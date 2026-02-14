<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:import-database',
    description: 'Import the database from the SQL dump file',
)]
class ImportDatabaseCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $params
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $projectDir = $this->params->get('kernel.project_dir');
        $sqlFile = $projectDir . '/data/innolearn_db-VF (2).sql'; // Correct filename

        if (!file_exists($sqlFile)) {
            $io->error('SQL file not found at: ' . $sqlFile);
            return Command::FAILURE;
        }

        $io->info('Reading SQL file...');
        $sql = file_get_contents($sqlFile);

        $connection = $this->entityManager->getConnection();
        
        $io->info('Importing database...');
        
        try {
            // Disable foreign key checks
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

            // 1. Extract, Drop & Recreate logic
            // Find all tables being created in the dump
            preg_match_all('/CREATE TABLE `?(\w+)`?/', $sql, $matches);
            $tablesInDump = array_unique($matches[1]);

            foreach ($tablesInDump as $tableName) {
                if (strtolower($tableName) === 'user') {
                    $io->note("Skipping DROP for 'user' table as requested.");
                    continue;
                }
                $io->text("Dropping table if exists: $tableName");
                $connection->executeStatement("DROP TABLE IF EXISTS `$tableName`");
            }

            // 2. Execute SQL dump
            $io->text("File size: " . strlen($sql) . " bytes");
            $io->text("First 20 bytes hex: " . bin2hex(substr($sql, 0, 20)));

            // Normalize line endings to \n
            $sql = str_replace("\r\n", "\n", $sql);
            $sql = str_replace("\r", "\n", $sql);
            
            // Split by ";\n" which is standard for dumps
            $statements = explode(";\n", $sql);
            
            if (count($statements) < 2) {
                $io->warning("Split by ;\\n yielded only " . count($statements) . " statements. Trying split by ; only.");
                $statements = explode(";", $sql);
            }
            
            $io->text("Found " . count($statements) . " statements to execute.");
            if (count($statements) > 0) {
                 $io->text("First statement preview: " . substr($statements[0], 0, 100));
            }

            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement) || str_starts_with($statement, '--') || str_starts_with($statement, '/*')) {
                    continue;
                }

                // Safety check: verify we are not dropping/creating user table here if regex missed it
                // (Double protection)
                if (preg_match('/(DROP|CREATE|TRUNCATE)\s+TABLE\s+`?user`?/i', $statement)) {
                     $io->warning("Skipping statement affecting 'user' table.");
                     continue;
                }
                
                 try {
                    $connection->executeStatement($statement);
                } catch (\Exception $e) {
                    // Start transaction/commit lines might fail if not in valid context or already active, log but continue or throw based on severity
                    // For a dump, usually we can ignore errors on specific lines like 'USE database' if we are already connected
                    if (!str_contains($e->getMessage(), 'already exists')) { 
                         $io->warning('Error executing statement: ' . substr($statement, 0, 50) . '... ' . $e->getMessage());
                    }
                }
            }

            $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
            
            $io->success('Database imported successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Import failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

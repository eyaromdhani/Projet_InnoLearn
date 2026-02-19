<?php
require 'vendor/autoload.php';
use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');
$conn = $entityManager->getConnection();
$sm = $conn->createSchemaManager();

$tables = $sm->listTableNames();
foreach ($tables as $table) {
    echo "Table: $table\n";
    $indexes = $sm->listTableIndexes($table);
    foreach ($indexes as $index) {
        echo "  Index: " . $index->getName() . " (" . implode(', ', $index->getColumns()) . ")\n";
    }
}

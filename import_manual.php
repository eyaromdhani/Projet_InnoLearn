<?php

$host = '127.0.0.1';
$port = '3306';
$dbname = 'innolearn_db';
$user = 'root';
$pass = '';

try {
    echo "Connecting to database...\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully.\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$sqlFile = __DIR__ . '/data/innolearn_db-VF (2).sql';
if (!file_exists($sqlFile)) {
    die("File not found: $sqlFile\n");
}

echo "Reading SQL file (" . filesize($sqlFile) . " bytes)...\n";
$sql = file_get_contents($sqlFile);

if (strlen($sql) < 100) {
    die("SQL file seems empty or too small.\n");
}

echo "First 50 bytes hex: " . bin2hex(substr($sql, 0, 50)) . "\n";

// Normalize
$sql = str_replace("\r\n", "\n", $sql);
$sql = str_replace("\r", "\n", $sql);

// Split
$statements = explode(";\n", $sql);

if (count($statements) < 2) {
    echo "Warning: Split by ;\\n yielded " . count($statements) . ". Trying explode by ;\n";
    $statements = explode(";", $sql);
}

echo "Found " . count($statements) . " statements.\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

// Drop tables logic (Extract from regex)
preg_match_all('/CREATE TABLE `?(\w+)`?/', $sql, $matches);
$tables = array_unique($matches[1]);
foreach ($tables as $table) {
    if (strtolower($table) === 'user') {
        echo "Skipping DROP user\n";
        continue;
    }
    echo "Dropping $table...\n";
    $pdo->exec("DROP TABLE IF EXISTS `$table`");
}

// Execute
$successCount = 0;
$errorCount = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt) || str_starts_with($stmt, '--') || str_starts_with($stmt, '/*')) {
        continue;
    }
    
    // Safety check user
    if (preg_match('/(DROP|CREATE|TRUNCATE)\s+TABLE\s+`?user`?/i', $stmt)) {
        echo "Skipping user tablestmt\n";
         continue;
    }

    try {
        $pdo->exec($stmt);
        $successCount++;
        if ($successCount % 50 === 0) echo ".";
    } catch (PDOException $e) {
        if (!str_contains($e->getMessage(), 'types exist')) { // Ignore specific benign errors if any
            echo "\nError executing:\n" . substr($stmt, 0, 100) . "...\nMessage: " . $e->getMessage() . "\n";
            $errorCount++;
        }
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');
echo "\nDone. Success: $successCount, Errors: $errorCount\n";

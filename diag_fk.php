<?php
$dsn = 'mysql:host=127.0.0.1;dbname=innolearn_db;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connecting success\n";

    echo "Checking for orphans...\n";
    $stmt = $pdo->query("SELECT id, id_etudiant FROM stagecondidature WHERE id_etudiant IS NOT NULL AND id_etudiant NOT IN (SELECT id FROM user)");
    $orphans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($orphans) {
        echo "Found orphans: " . count($orphans) . "\n";
        print_r($orphans);
    } else {
        echo "No orphans found.\n";
    }

    echo "Attempting to add FK...\n";
    $pdo->exec("ALTER TABLE stagecondidature ADD CONSTRAINT FK_D2E308D721A5CE76 FOREIGN KEY (id_etudiant) REFERENCES user (id)");
    echo "FK added successfully!\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

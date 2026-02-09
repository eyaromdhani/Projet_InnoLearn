<?php
$dsn = 'mysql:host=127.0.0.1;dbname=innolearn_db;charset=utf8mb4';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connecting success\n";

    // 1. Clean up id_etudiant
    echo "Cleaning up id_etudiant...\n";
    $pdo->exec("UPDATE stagecondidature SET id_etudiant = NULL WHERE id_etudiant IS NOT NULL AND id_etudiant NOT IN (SELECT id FROM user)");

    // 2. Add id_etudiant FK
    echo "Adding id_etudiant FK...\n";
    try {
        $pdo->exec("ALTER TABLE stagecondidature ADD CONSTRAINT FK_D2E308D721A5CE76 FOREIGN KEY (id_etudiant) REFERENCES user (id)");
        echo "FK_D2E308D721A5CE76 added.\n";
    } catch (Exception $e) {
        echo "FK_D2E308D721A5CE76 Error: " . $e->getMessage() . "\n";
    }

    // 3. Add id_offre FK (just in case)
    echo "Adding id_offre FK...\n";
    try {
        $pdo->exec("ALTER TABLE stagecondidature ADD CONSTRAINT FK_D2E308D74103C75F FOREIGN KEY (id_offre) REFERENCES offrestage (id)");
        echo "FK_D2E308D74103C75F added.\n";
    } catch (Exception $e) {
        echo "FK_D2E308D74103C75F Error: " . $e->getMessage() . "\n";
    }

    // 4. Listing current constraints
    echo "Current constraints for stagecondidature:\n";
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'stagecondidature' AND CONSTRAINT_SCHEMA = 'innolearn_db' AND REFERENCED_TABLE_NAME IS NOT NULL");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (PDOException $e) {
    echo "PDO ERROR: " . $e->getMessage() . "\n";
}

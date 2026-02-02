<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=innolearn_db', 'root', '');
    $stmt = $pdo->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo $row[0] . PHP_EOL;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

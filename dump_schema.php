<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=innolearn_db', 'root', '');
    $tables = ['book', 'cours', 'project', 'stagecondidature', 'book_categorie', 'cours_categorie'];
    $schema = [];
    foreach ($tables as $table) {
        $stmt = $pdo->query("DESCRIBE `$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $schema[$table] = $columns;
    }
    file_put_contents('schema.json', json_encode($schema, JSON_PRETTY_PRINT));
    echo "Schema dumped to schema.json";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

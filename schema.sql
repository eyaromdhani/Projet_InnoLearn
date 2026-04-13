-- Script de création des tables pour innolearn_db

CREATE TABLE IF NOT EXISTS formulaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS question (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contenu TEXT NOT NULL,
    reponse_correcte TEXT,
    points INT DEFAULT 1,
    type VARCHAR(50),
    formulaire_id INT,
    FOREIGN KEY (formulaire_id) REFERENCES formulaire(id) ON DELETE CASCADE
);

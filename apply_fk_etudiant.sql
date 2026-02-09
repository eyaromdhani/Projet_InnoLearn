ALTER TABLE stagecondidature DROP COLUMN IF EXISTS etudiant_id;
ALTER TABLE stagecondidature CHANGE id_etudiant id_etudiant INT DEFAULT NULL;
ALTER TABLE stagecondidature ADD CONSTRAINT FK_D2E308D721A5CE76 FOREIGN KEY (id_etudiant) REFERENCES user (id);
CREATE INDEX IDX_D2E308D721A5CE76 ON stagecondidature (id_etudiant);

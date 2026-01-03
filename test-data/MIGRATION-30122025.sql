-- 1. D'abord ajouter les colonnes comme NULLABLE
ALTER TABLE droits ADD utilisateur_id INT DEFAULT NULL;
ALTER TABLE droits ADD etudiant_id INT DEFAULT NULL;
ALTER TABLE droits ADD annee INT DEFAULT NULL;

ALTER TABLE payements_ecolages ADD utilisateur_id INT DEFAULT NULL;
ALTER TABLE payements_ecolages ADD annee INT DEFAULT NULL;

-- 2. Mettre à jour les données existantes avec des valeurs valides
-- Remplacez 1 par un ID utilisateur valide qui existe dans votre table utilisateur
UPDATE droits SET utilisateur_id = 1 WHERE utilisateur_id IS NULL;
UPDATE droits SET annee = 2024 WHERE annee IS NULL;

-- Pour etudiant_id, utilisez un ID existant de la table etudiants
UPDATE droits SET etudiant_id = (SELECT id FROM etudiants LIMIT 1) WHERE etudiant_id IS NULL;

-- Même chose pour payements_ecolages
UPDATE payements_ecolages SET utilisateur_id = 1 WHERE utilisateur_id IS NULL;
UPDATE payements_ecolages SET annee = 2024 WHERE annee IS NULL;

-- 3. Maintenant rendre les colonnes NOT NULL
ALTER TABLE droits ALTER COLUMN utilisateur_id SET NOT NULL;
ALTER TABLE droits ALTER COLUMN etudiant_id SET NOT NULL;
ALTER TABLE droits ALTER COLUMN annee SET NOT NULL;

ALTER TABLE payements_ecolages ALTER COLUMN utilisateur_id SET NOT NULL;
ALTER TABLE payements_ecolages ALTER COLUMN annee SET NOT NULL;

-- 4. Ajouter les contraintes de clé étrangère
ALTER TABLE droits ADD CONSTRAINT FK_7A9D4CEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE;
ALTER TABLE droits ADD CONSTRAINT FK_7A9D4CEDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiants (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE payements_ecolages ADD CONSTRAINT FK_A6D31440FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE;

-- 5. Créer les index
CREATE INDEX IDX_7A9D4CEFB88E14F ON droits (utilisateur_id);
CREATE INDEX IDX_7A9D4CEDDEAB1A3 ON droits (etudiant_id);
CREATE INDEX IDX_A6D31440FB88E14F ON payements_ecolages (utilisateur_id);

-- 6. Gérer la table niveau_etudiants - vérifier d'abord si l'index existe
DROP INDEX IF EXISTS IDX_70ADE61D5200282E;
ALTER TABLE niveau_etudiants DROP CONSTRAINT IF EXISTS niveau_etudiants_formation_id_fkey;
ALTER TABLE niveau_etudiants DROP COLUMN IF EXISTS formation_id;
ALTER TABLE niveau_etudiants ALTER COLUMN etudiant_id SET NOT NULL;





INSERT INTO role (id, name) VALUES (1, 'Admin');
INSERT INTO role (id, name) VALUES (2, 'Utilisateur');


INSERT INTO Status (id, name) VALUES (1, 'Actif');
INSERT INTO Status (id, name) VALUES (2, 'Inactif');


INSERT INTO Utilisateur (id, email, mdp, prenom, nom, role_id, status_id)
VALUES (
    1,
    'admin@gmail.com',
    '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi',
    'admin',
    'admin',
    1,
    1
);

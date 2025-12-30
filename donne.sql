
INSERT INTO role (id, name) VALUES (1, 'Admin');
INSERT INTO role (id, name) VALUES (2, 'Utilisateur');


INSERT INTO Status (id, name) VALUES (1, 'Actif');
INSERT INTO Status (id, name) VALUES (2, 'Inactif');



INSERT INTO Utilisateur (id, email, password, prenom, nom, is_active, is_admin)
VALUES (
    4,
    'admin@gmail.com',
    '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi',
    'admin',
    'admin',
    1,
    1
);

-- UPDATE utilisateur SET status_id = 2;

-- Table Propos avec id manuel
INSERT INTO propos (id, adresse, email, sexe)
VALUES 
(1, '123 Rue Analakely, Antananarivo', 'exemple1@gmail.com', 'M'),
(2, '456 Rue Isoraka, Antananarivo', 'exemple2@gmail.com', 'F');

-- Table Cin avec id manuel
INSERT INTO cin (id, numero, date_cin, lieu, ancien_date, nouveau_date)
VALUES
(1, 123456, '2020-01-15', 'Antananarivo', '2010-01-01', '2020-01-15'),
(2, 654321, '2019-06-20', 'Fianarantsoa', '2009-06-20', '2019-06-20');

-- Table Bacc avec id manuel
INSERT INTO bacc (id, numero, annee, serie)
VALUES
(1, 'BAC-2021-123456', 2021, 'C'),
(2, 'BAC-2020-654321', 2020, 'D');

-- Table Etudiants avec id manuel et relations
INSERT INTO etudiants (id, nom, prenom, date_naissance, lieu_naissance, cin_id, bacc_id, propos_id)
VALUES
(1, 'Rakoto', 'Jean', '2003-03-15', 'Antsirabe', 1, 1, 1),
(2, 'Rabe', 'Marie', '2002-07-22', 'Fianarantsoa', 2, 2, 2);


-- Insertion des types de formation avec id manuel
INSERT INTO type_formations (id, nom)
VALUES
(1, 'Académique'),
(2, 'Professionnel');


-- Insertion des formations avec id manuel et lien vers le type de formation
INSERT INTO formations (id, nom, type_formation_id)
VALUES 
(1, 'Académique', 1), -- Académique
(2, 'Atelier Luban', 2),          -- Professionnel
(3, 'Electrique Industrielle', 2), -- Académique
(4, 'Tecnologie Information', 2),-- Professionnel
(5, 'Maintenance automobile', 2);-- Professionnel

-- Insertion des données avec id manuel
INSERT INTO formation_etudiants (id, etudiants_id, formation_id, date_formation)
VALUES
(1, 1, 1, '2025-01-10'), 
(2, 2, 2, '2025-02-15'); 

-- Insertion des données d'écolage avec id manuel
INSERT INTO ecolages (id, formations_id, montant, date_ecolage)
VALUES
(1, 1, 0.0, '2025-01-05'),  -- Académique
(2, 2, 500.0, '2025-01-10'),  -- Atelier Luban
(3, 3, 800.0, '2025-02-15'),  -- Electrique Industrielle
(4, 4, 900.0, '2025-03-01'),  -- Tecnologie Information
(5, 5, 1000.0, '2025-03-10'); -- Maintenance automobile

INSERT INTO niveaux (nom, type, grade) VALUES
('Licence 1', 1, 1),
('Licence 2', 1, 2),
('Licence 3', 1, 3),
('Master 1', 1, 4),
('Master 2', 1, 5);

INSERT INTO mentions (id, nom) VALUES
(1, 'Telecommunications'),
(2, 'Genie Logiciel'),
(3, 'Electronique');


INSERT INTO niveau_etudiants (
    niveau_id,
    mention_id,
    etudiant_id,
    annee,
    date_insertion
) VALUES (
    1,
    1,
    1,
    2024,
    NOW()
);

INSERT INTO type_droits (id, nom) VALUES
(1, 'Pédagogique'),
(2, 'Administratif');

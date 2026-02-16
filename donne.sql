
\c digitalisation

\encoding UTF8

INSERT INTO role (id, name) VALUES (1, 'Admin');
INSERT INTO role (id, name) VALUES (2, 'Utilisateur');
INSERT INTO role (id, name) VALUES (3, 'Ecolage');


INSERT INTO Status (id, name) VALUES (1, 'Actif');
INSERT INTO Status (id, name) VALUES (2, 'Inactif');

-- INSERT INTO status_etudiants (id, name) VALUES (1, 'Passant');
-- INSERT INTO status_etudiants (id, name) VALUES (2, 'Redoublant');



INSERT INTO Utilisateur (id, email, mdp, prenom, nom, status_id, role_id)
VALUES (
    1,
    'admin@gmail.com',
    '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi',
    'admin',
    'admin',
    1,
    1
);

INSERT INTO Utilisateur (id, email, mdp, prenom, nom, status_id, role_id)
VALUES (
    2,
    'test@gmail.com',
    '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi',
    'test',
    'test',
    1,
    2
);
INSERT INTO Utilisateur (id, email, mdp, prenom, nom, status_id, role_id)
VALUES (
    3,
    'ecolage@gmail.com',
    '$2y$10$Djns8FgsL.xk2GBACEtJh.Hs1civTyvdGQ9s6gqbSgDN81QkOHvTi',
    'ecolage',
    'ecolage',
    1,
    3
);
-- UPDATE utilisateur SET status_id = 2;

-- Table Propos avec id manuel
INSERT INTO Sexes (id, nom) VALUES (1, 'Masculin');
INSERT INTO Sexes (id, nom) VALUES (2, 'Feminin');
-- INSERT INTO propos (id, adresse, email)
-- VALUES 
-- (1, '123 Rue Analakely, Antananarivo', 'exemple1@gmail.com'),
-- (2, '456 Rue Isoraka, Antananarivo', 'exemple2@gmail.com');

-- -- Table Cin avec id manuel
-- INSERT INTO cin (id, numero, date_cin, lieu, ancien_date, nouveau_date)
-- VALUES
-- (1, 123456, '2020-01-15', 'Antananarivo', '2010-01-01', '2020-01-15'),
-- (2, 654321, '2019-06-20', 'Fianarantsoa', '2009-06-20', '2019-06-20');

-- Table Bacc avec id manuel
-- INSERT INTO bacc (id, numero, annee, serie)
-- VALUES
-- (1, 'BAC-2021-123456', 2021, 'C'),
-- (2, 'BAC-2020-654321', 2020, 'D');

-- -- Table Etudiants avec id manuel et relations
-- INSERT INTO etudiants (id, nom, prenom, date_naissance, lieu_naissance, cin_id, bacc_id, propos_id,sexe_id)
-- VALUES
-- (1, 'Rakoto', 'Jean', '2003-03-15', 'Antsirabe', 1, 1, 1,1),
-- (2, 'Rabe', 'Marie', '2002-07-22', 'Fianarantsoa', 2, 2, 2,2);


-- Insertion des types de formation avec id manuel
INSERT INTO type_formations (id, nom)
VALUES
(1, 'Academique'),
(2, 'Professionnelle');


-- Insertion des formations avec id manuel et lien vers le type de formation
INSERT INTO formations (id, nom, type_formation_id)
VALUES 
(1, 'ACADEMIQUE', 1),
(2, 'PROFESSIONNELLE', 2),
(3, 'PROFESSIONNELLE LUBAN', 2), 
(4, 'MASTER RECHERCHE', 2),
(5, 'INSCRIPTION ANNULEE', 1);

INSERT INTO formations (id, nom, type_formation_id) VALUES (6, 'PROFESSIONNELLE EIE', 2);
INSERT INTO formations (id, nom, type_formation_id) VALUES (7, 'PROFESSIONNELLE EIE1', 2);
INSERT INTO formations (id, nom, type_formation_id) VALUES (8, 'PROFESSIONNELLE EIE2', 2);


INSERT INTO ecolages (formations_id, montant, date_ecolage)
VALUES
-- Professionnelle
(2, 700000, NOW()),

-- Professionnelle Luban
(3, 1200000, NOW());

INSERT INTO ecolages (formations_id, montant, date_ecolage)
VALUES (7, 1500000, NOW()),

-- Professionnelle Luban
(8, 1200000, NOW());


-- Insertion des données avec id manuel
-- INSERT INTO formation_etudiants (id, etudiant_id, formation_id, date_formation)
-- VALUES
-- (1, 1, 1, '2025-01-10'), 
-- (2, 2, 2, '2025-02-15'); 

-- -- Insertion des données d'écolage avec id manuel
-- INSERT INTO payments (
--     reference,
--     montant,
--     date_payment,
--     annee,
--     niveau_id,
--     etudiant_id,
--     type_id,
--     numero,
--     utilisateur_id
-- )
-- VALUES (
--     'PAY-2026-001',
--     150000.00,
--     NOW(),
--     2026,
--     3,      -- id du niveau
--     12,     -- id de l'étudiant
--     2,      -- id du type de droits
--     'REC-45987',
--     1       -- id de l'utilisateur
-- );


INSERT INTO niveaux (id, nom, type, grade) VALUES
(1,  'L1',   1, 1),
(2,  'L2',   1, 2),
(3,  'L3',   1, 3),
(4,  'M1',   1, 4),
(5,  'M2',   1, 5),
(6,  'LP1',  2, 1),
(7,  'LP2',  2, 2),
(8,  'LP3',  2, 3),
(9,  'MP1',  2, 4),
(10, 'MP2',  2, 5),
(11, 'LP1L', 3, 1),
(12, 'LP2L', 3, 2),
(13, 'LP3L', 3, 3),
(14, 'MVR',  4, 5),
(15, 'MRS',  5, 1);

INSERT INTO niveaux (id, nom, type, grade) VALUES
(16, 'MP2IEE', 6, 5);

INSERT INTO niveaux (id, nom, type, grade) VALUES
(17, 'MP2IEE1', 7, 5);

INSERT INTO niveaux (id, nom, type, grade) VALUES
(18, 'MP2IEE2', 8, 5);


-- INSERT INTO niveaux (nom, type, grade) VALUES
-- ('Licence 1', 1, 1),
-- ('Licence 2', 1, 2),
-- ('Licence 3', 1, 3),
-- ('Master 1', 1, 4),
-- ('Master 2', 1, 5);

-- INSERT INTO niveaux (nom, type, grade) VALUES
-- ('LP 1', 2, 1),
-- ('LP 2', 2, 2),
-- ('LP 3', 2, 3),
-- ('MP 1', 2, 4),
-- ('MP', 2, 5);

INSERT INTO public.mentions (id, nom, abr) VALUES
    (1,  'BTP', 'BTP'),
    (2,  'EN', 'EN'),
    (3,  'GC', 'GC'),
    (4,  'GE', 'GE'),
    (5,  'GER', 'GER'),
    (6,  'GGEO', 'GGEO'),
    (7,  'GMI', 'GMI'),
    (8,  'GPCI', 'GPCI'),
    (9,  'GPI', 'GPI'),
    (10, 'GST', 'GST'),
    (11, 'GSTI', 'GSTI'),
    (12, 'HYD', 'HYD'),
    (13, 'IGAT', 'IGAT'),
    (14, 'IMIN', 'IMIN'),
    (15, 'INSCRIPTION ANNULEE', 'INSCRIPTION ANNULEE'),
    (16, 'IPE', 'IPE'),
    (17, 'ISA', 'ISA'),
    (18, 'MTO', 'MTO'),
    (19, 'RENVOYEE', 'RENVOYEE'),
    (20, 'SIM', 'SIM'),
    (21, 'STI', 'STI'),
    (22, 'SUSPENDU', 'SUSPENDU'),
    (23, 'TCO', 'TCO'),
    (24, 'UAGC', 'UAGC');

INSERT INTO public.mentions (id, nom, abr) VALUES
    (25, 'EIE', 'EIE');

 INSERT INTO niveau_etudiants (
     niveau_id,
     mention_id,
     etudiant_id,
     annee,
     date_insertion,
     status_etudiant_id
 ) VALUES (
     4,
     2,
     4206,
     2025,
     NOW(),
     1
 );

INSERT INTO type_droits (id, nom) VALUES 
(1, 'Pédagogique'),
(2, 'Administratif'),
(3, 'Ecolage'),
(4, 'Agence Comptable'),
(5, 'Pédagogique-Administratif'),
(6, 'Sélection');
INSERT INTO type_droits (id, nom) VALUES 
(7, 'Sélection dossier'),
(8, 'Droit'),
(9, 'Droit selection'),
(10, 'LT'),
(11, 'RAT'),
(12, 'Rattrapage');



INSERT INTO nationalites (id, nom, type) VALUES
(1, 'MALAGASY', 1);

-- Insertion des autres nationalités (type = 2)
INSERT INTO nationalites (id, nom, type) VALUES
(2,  'CAMEROUNAISE', 2),
(3,  'CHINOISE', 2),
(4,  'COMORIENNE', 2),
(5,  'CONGOLAISE', 2),
(6,  'DJIBOUTIENNE', 2),
(7,  'FRANCAISE', 2),
(8,  'GABONAISE', 2),
(9,  'GHANEENNE', 2),
(10, 'GUINEENNE', 2),
(11, 'NIGERIENNE', 2),
(12, 'SENEGALAISE', 2),
(13, 'TCHADIENNE', 2);


INSERT INTO status_etudiants (id, name) VALUES
(1, 'PASSANT'),
(2, 'REDOUBLANT'),
(3, 'SUSPENDU'),
(4, 'INSCRIPTION ANNULEE');

andriamboavonjy ianjatiana rico

-- 1. Insertion des mentions (nécessaire pour les parcours et niveaux)
INSERT INTO mentions (id, nom) VALUES
(1, 'Informatique'),
(2, 'Électronique'),
(3, 'Mécanique'),
(4, 'Gestion');

-- 2. Insertion des niveaux académiques
INSERT INTO niveaux (id, nom, type, grade) VALUES
(1, 'L1', 1, 1),  -- Licence 1ère année
(2, 'L2', 1, 2),  -- Licence 2ème année
(3, 'L3', 1, 3),  -- Licence 3ème année
(4, 'M1', 2, 4),  -- Master 1ère année
(5, 'M2', 2, 5);  -- Master 2ème année

-- 3. Insertion des parcours (spécialisations)
INSERT INTO parcours (id, mention_id, nom) VALUES
(1, 1, 'Développement Web'),
(2, 1, 'Réseaux et Sécurité'),
(3, 2, 'Électronique Industrielle'),
(4, 3, 'Maintenance Automobile'),
(5, 4, 'Gestion des Projets');

-- 4. Lier les étudiants à leurs niveaux par année
INSERT INTO niveau_etudiants (id, etudiant_id, niveau_id, mention_id, formation_id, annee, date_insertion) VALUES
(1, 1, 1, 1, 1, 2024, '2024-09-01'),  -- Rakoto Jean en L1 Informatique, formation Académique
(2, 1, 2, 1, 1, 2025, '2025-09-01'),  -- Rakoto Jean en L2 Informatique, formation Académique
(3, 2, 3, 3, 2, 2024, '2024-09-01'),  -- Rabe Marie en L3 Mécanique, Atelier Luban
(4, 2, 4, 3, 2, 2025, '2025-09-01');  -- Rabe Marie en M1 Mécanique, Atelier Luban

-- 5. Insérer les types de droits (pour les écolages)
INSERT INTO type_droits (id, nom) VALUES
(1, 'Écolage'),
(2, 'Frais dinscription'),
(3, 'Frais administratif');

-- 6. Insérer les droits d'écolage théoriques par formation et par année
INSERT INTO droits (id, type_droit_id, reference, date_versement, montant) VALUES
-- Pour la formation Académique (Licence)
(1, 1, 'ECOL-2024-ACAD', '2024-09-01', 1200000),  -- Écolage 2024
(2, 1, 'ECOL-2025-ACAD', '2025-09-01', 1300000),  -- Écolage 2025

-- Pour Atelier Luban
(3, 1, 'ECOL-2024-LUBAN', '2024-09-01', 1500000),
(4, 1, 'ECOL-2025-LUBAN', '2025-09-01', 1600000),

-- Frais d'inscription
(5, 2, 'INS-2024', '2024-09-01', 50000),
(6, 2, 'INS-2025', '2025-09-01', 55000);

-- 7. Insérer les paiements réels des étudiants
INSERT INTO payements_ecolages (id, etudiant_id, reference, datepayements, montant, tranche) VALUES
-- Rakoto Jean (Académique)
(1, 1, 'PAY-2024-001', '2024-09-15', 600000, 1),   -- 1ère tranche 2024
(2, 1, 'PAY-2024-002', '2024-11-20', 600000, 2),   -- 2ème tranche 2024
(3, 1, 'PAY-2025-001', '2025-09-10', 650000, 1),   -- 1ère tranche 2025
(4, 1, 'PAY-2025-002', '2025-11-15', 400000, 2),   -- 2ème tranche 2025 (partiel)

-- Rabe Marie (Atelier Luban)
(5, 2, 'PAY-2024-003', '2024-10-01', 750000, 1),   -- 1ère tranche 2024
(6, 2, 'PAY-2024-004', '2025-01-15', 750000, 2),   -- 2ème tranche 2024
(7, 2, 'PAY-2025-003', '2025-10-05', 800000, 1),   -- 1ère tranche 2025
(8, 2, 'PAY-2025-004', '2025-12-20', 800000, 2);   -- 2ème tranche 2025

-- 8. Mettre à jour les écolages avec des montants réalistes
UPDATE ecolages SET 
    montant = CASE 
        WHEN id = 1 THEN 1200000  -- Académique
        WHEN id = 2 THEN 1500000  -- Atelier Luban
        WHEN id = 3 THEN 1800000  -- Electrique Industrielle
        WHEN id = 4 THEN 2000000  -- Tecnologie Information
        WHEN id = 5 THEN 2200000  -- Maintenance automobile
    END,
    date_ecolage = '2024-09-01';

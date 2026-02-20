INSERT INTO niveau_etudiants (
    niveau_id,
    mention_id,
    etudiant_id,
    status_etudiant_id,
    annee,
    date_insertion,
    matricule
) VALUES (
    14,              -- niveau_id
    2,              -- mention_id
    10626,             -- etudiant_id
    null,              -- status_etudiant_id
    2025,           -- annee
    NOW(),           -- date_insertion
    'MAT2025-001'   -- matricule
);
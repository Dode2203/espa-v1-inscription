SELECT ne.etudiant_id
FROM niveau_etudiants ne
WHERE ne.deleted_at IS NULL and annee = 2026 and niveau_id is not null

AND NOT EXISTS (
    SELECT 1
    FROM inscrits i
    WHERE i.etudiant_id = ne.etudiant_id
    AND i.deleted_at IS NULL
);
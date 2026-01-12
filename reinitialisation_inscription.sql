delete from inscrits;
delete from droits;
delete from ecolages;
delete from niveau_etudiants;
INSERT INTO niveau_etudiants (
    niveau_id,
    mention_id,
    etudiant_id,
    annee,
    date_insertion,
    status_etudiant_id
) VALUES (
    1,
    1,
    1,
    2024,
    NOW(),
    1
);




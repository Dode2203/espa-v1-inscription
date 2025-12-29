-- 1. Ajouter la relation manquante entre etudiants et niveau_etudiants
ALTER TABLE niveau_etudiants 
ADD COLUMN etudiant_id INT REFERENCES etudiants(id);

-- 2. Ajouter la relation entre niveau_etudiants et formations (optionnel mais utile)
ALTER TABLE niveau_etudiants 
ADD COLUMN formation_id INT REFERENCES formations(id);

-- 3. Vérifier et compléter les tables manquantes

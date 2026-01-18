CREATE DATABASE mabagnole;
use mabagnole;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,users
    role ENUM('client', 'admin') NOT NULL
);
CREATE TABLE categories (
    id_c INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    `description` TEXT
);
CREATE TABLE vehicule (
    id_v INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(150) NOT NULL UNIQUE,
    prix_jours VARCHAR(255) NOT NULL,
    disponibilite bool default 1 NOT NULL,
    image varchar(200),
    c_id INT NOT NULL,
	FOREIGN KEY (c_id) REFERENCES categories(id_c)
);

CREATE TABLE reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    lieu_prise VARCHAR(100),
    lieu_retour VARCHAR(100),
    user_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicule(id_v)
);
CREATE TABLE avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    note INT NOT NULL CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE,
    user_id INT NOT NULL,
    vehicule_id INT NOT NULL,
	FOREIGN KEY (user_id) REFERENCES users(id),
	FOREIGN KEY (vehicule_id) REFERENCES vehicule(id_v)
);
alter table users 
add column approuve boolean default false;

CREATE VIEW ListeVehicules AS
SELECT 
    v.*, 
    c.nom AS categorie_nom, 
    AVG(a.note) AS note_moyenne
FROM vehicule v
LEFT JOIN categories c ON v.c_id = c.id_c
LEFT JOIN avis a ON v.id_v = a.vehicule_id
GROUP BY v.id_v;


DELIMITER //
CREATE PROCEDURE AjouterReservation(
    IN p_debut DATE,
    IN p_fin DATE,
    IN p_prise VARCHAR(100),
    IN p_retour VARCHAR(100),
    IN p_user INT,
    IN p_vehicule INT
)
BEGIN
    INSERT INTO reservations 
    (date_debut, date_fin, lieu_prise, lieu_retour, user_id, vehicule_id)
    VALUES 
    (p_debut, p_fin, p_prise, p_retour, p_user, p_vehicule);
END //

DELIMITER ;








select u.non_user, sum(t.montant) as depense from user u 
left join transaction_ t on u.id_user = t.user_id 
where t.statut = 'depense' and t.date between hhhhj and fdghsjk
group by u.id_user 
having depense=(select max(depense) from 
(select sum(montant) from 
transaction_ where t.statut = 'depense' and t.date between hhhhj and fdghsjk group by id_user )as table__)


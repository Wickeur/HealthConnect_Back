-- Création de la base de données (facultatif, dépend de votre configuration actuelle)
-- CREATE DATABASE IF NOT EXISTS healthconnect;
-- USE healthconnect;

-- Création de la table Roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(255) NOT NULL
);

-- Insertion de données fictives dans la table Roles
INSERT INTO roles (label) VALUES
('Admin'),
('Doctor'),
('Patient');

-- Création de la table Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(255) NOT NULL,
    mail VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    idRole INT,
    FOREIGN KEY (idRole) REFERENCES roles(id)
);

-- Insertion de données fictives dans la table Users
INSERT INTO users (pseudo, mail, password, idRole) VALUES
('JohnDoe', 'johndoe@example.com', 'hashed_password_here', 1),
('JaneDoe', 'janedoe@example.com', 'hashed_password_here', 2);

-- Création de la table Medical Files
CREATE TABLE IF NOT EXISTS medical_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUser INT,
    comment TEXT,
    content TEXT,
    FOREIGN KEY (idUser) REFERENCES users(id)
);

-- Insertion de données fictives dans la table Medical Files
INSERT INTO medical_files (idUser, comment, content) VALUES
(1, 'No significant issues.', 'Patient is healthy.'),
(2, 'Requires follow up.', 'Patient has shown signs of improvement.');

-- Création de la table RDVs
CREATE TABLE IF NOT EXISTS rdvs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    idUserClient INT,
    idUserMedecin INT,
    date DATE,
    timeStart TIME,
    timeEnd TIME,
    FOREIGN KEY (idUserClient) REFERENCES users(id),
    FOREIGN KEY (idUserMedecin) REFERENCES users(id)
);

-- Insertion de données fictives dans la table RDVs
INSERT INTO rdvs (idUserClient, idUserMedecin, date, timeStart, timeEnd) VALUES
(1, 2, '2024-01-15', '09:00:00', '09:30:00'),
(2, 1, '2024-01-16', '10:00:00', '10:30:00');

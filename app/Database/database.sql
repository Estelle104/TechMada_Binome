CREATE DATABASE codeigniter;
USE codeigniter;

CREATE TABLE etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
);

CREATE TABLE produit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prix DECIMAL(10, 2) NOT NULL
);


INSERT INTO etudiant (nom, prenom, email) VALUES ('Doe', 'John', 'john.doe@example.com');
INSERT INTO etudiant (nom, prenom, email) VALUES ('Smith', 'Jane', 'jane.smith@example.com');
INSERT INTO etudiant (nom, prenom, email) VALUES ('Johnson', 'Bob', 'bob.johnson@example.com');

INSERT INTO produit (nom, prix) VALUES ('Produit 1', 19.99);
INSERT INTO produit (nom, prix) VALUES ('Produit 2', 29.99);
INSERT INTO produit (nom, prix) VALUES ('Produit 3', 39.99);
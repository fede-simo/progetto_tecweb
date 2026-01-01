DROP TABLE IF EXISTS Recensione;
DROP TABLE IF EXISTS Acquisto;
DROP TABLE IF EXISTS Corso;
DROP TABLE IF EXISTS Utente;


CREATE TABLE Utente (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    email VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN NOT NULL DEFAULT(FALSE),
    dataDiNascita DATE NOT NULL,
    PRIMARY KEY (id)
);


CREATE TABLE Corso(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(100) NOT NULL,
    categoria VARCHAR(30) NOT NULL,
    durata INT NOT NULL, 
    costo INT NOT NULL,
    modalita ENUM('aula', 'online_live', 'online_reg') NOT NULL,
    breve_desc VARCHAR(200) NOT NULL,
    desc_completa VARCHAR(10000) NOT NULL,
    PRIMARY KEY(id)
);


CREATE TABLE Acquisto(
    id_user INT UNSIGNED NOT NULL,
    id_corso INT UNSIGNED NOT NULL,
    data DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES Utente(id) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    PRIMARY KEY (id_user, id_corso)
);


CREATE TABLE Recensione(
    id_user INT UNSIGNED NOT NULL,
    id_corso INT UNSIGNED NOT NULL,
    rating DECIMAL(2,1) NOT NULL,
    descrizione VARCHAR(1000),
    FOREIGN KEY (id_user) REFERENCES Utente(id) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    PRIMARY KEY(id_user, id_corso)
);


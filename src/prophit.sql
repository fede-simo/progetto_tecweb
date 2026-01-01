DROP TABLE IF EXISTS Utente;
DROP TABLE IF EXISTS Corso;
DROP TABLE IF EXISTS Acquisto;
DROP TABLE IF EXISTS Recensione;


CREATE TABLE Utente (
    id UNSIGNED INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    email VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN NOT NULL DEFAULT(FALSE),
    dataDiNascita DATE NOT NULL,
    PRIMARY KEY (id)
);


CREATE TYPE mode AS ENUM('aula', 'online_live', 'online_reg');

CREATE TABLE Corso(
    id UNSIGNED INT NOT NULL,
    titolo VARCHAR(100) NOT NULL,
    categoria VARCHAR(30) NOT NULL,
    durata INT NOT NULL, 
    costo INT NOT NULL,
    modalita mode NOT NULL,
    breve_desc VARCHAR(200) NOT NULL,
    desc_completa VARCHAR(10000) NOT NULL,
    PRIMARY KEY(id)
);


CREATE TABLE Acquisto(
    id_user INT NOT NULL,
    id_corso INT NOT NULL,
    data DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES Utente(id) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    PRIMARY KEY (id_user, id_corso)
);


CREATE TABLE Recensione(
    id_user INT NOT NULL,
    id_corso INT NOT NULL,
    rating REAL NOT NULL,
    desc VARCHAR(1000),
    FOREIGN KEY (id_user) REFERENCES Acquisto(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Acquisto(id_corso) ON DELETE CASCADE,
    PRIMARY KEY(id_user, id_corso);
)


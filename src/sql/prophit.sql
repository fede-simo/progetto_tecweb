DROP TABLE IF EXISTS Recensione;
DROP TABLE IF EXISTS Acquisto;
DROP TABLE IF EXISTS Corso;
DROP TABLE IF EXISTS Utente;


CREATE TABLE Utente (
    username VARCHAR(30) NOT NULL,
    nome VARCHAR(30) NOT NULL,
    cognome VARCHAR(30) NOT NULL,
    password VARCHAR(255) NOT NULL,
    isAdmin BOOLEAN NOT NULL DEFAULT(FALSE),
    data_di_nascita DATE NOT NULL,
    PRIMARY KEY (username)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;


CREATE TABLE Corso(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titolo VARCHAR(100) NOT NULL,
    immagine VARCHAR(100) NOT NULL,
    categoria VARCHAR(30) NOT NULL,
    durata INT NOT NULL, 
    costo DECIMAL(9,2) NOT NULL,
    modalita ENUM ('In aula', 'Online live', 'Online registrata') NOT NULL,
    breve_desc VARCHAR(200) NOT NULL,
    desc_completa VARCHAR(10000) NOT NULL,
    PRIMARY KEY(id)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci; 


CREATE TABLE Acquisto(
    id_user VARCHAR(30) NOT NULL,
    id_corso INT UNSIGNED NOT NULL,
    data DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES Utente(username) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    PRIMARY KEY (id_user, id_corso)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;


CREATE TABLE Recensione(
    id_user VARCHAR(30) NOT NULL,
    id_corso INT UNSIGNED NOT NULL,
    rating DECIMAL(3,1) NOT NULL,
    descrizione VARCHAR(1000),
    FOREIGN KEY (id_user) REFERENCES Utente(username) ON DELETE CASCADE,
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    PRIMARY KEY(id_user, id_corso)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;


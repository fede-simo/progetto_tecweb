DROP TABLE IF EXISTS Recensione;
DROP TABLE IF EXISTS Acquisto;
DROP TABLE IF EXISTS Contatto;
DROP TABLE IF EXISTS CorsoCategoria;
DROP TABLE IF EXISTS Categoria;
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

CREATE TABLE Categoria(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE,
    PRIMARY KEY(id)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE CorsoCategoria(
    id_corso INT UNSIGNED NOT NULL,
    id_categoria INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_corso, id_categoria),
    FOREIGN KEY (id_corso) REFERENCES Corso(id) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id) ON DELETE CASCADE
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


CREATE TABLE Recensione (
    id INT UNSIGNED AUTO_INCREMENT,
    id_user VARCHAR(30) NOT NULL,
    id_corso INT UNSIGNED NOT NULL,
    rating DECIMAL(2,1) NOT NULL,
    descrizione VARCHAR(1000),
    PRIMARY KEY (id),
    UNIQUE (id_user, id_corso),
    FOREIGN KEY (id_user, id_corso)
        REFERENCES Acquisto(id_user, id_corso)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE Contatto(
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    oggetto VARCHAR(150) NOT NULL,
    messaggio VARCHAR(2000) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id)
) ENGINE=InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

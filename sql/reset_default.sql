DROP DATABASE IF EXISTS __DB_NAME__;
CREATE DATABASE __DB_NAME__ CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE USER IF NOT EXISTS '__DB_USER__'@'localhost' IDENTIFIED BY '__DB_PASS__';
ALTER USER '__DB_USER__'@'localhost' IDENTIFIED BY '__DB_PASS__';
GRANT ALL PRIVILEGES ON __DB_NAME__.* TO '__DB_USER__'@'localhost';
FLUSH PRIVILEGES;

USE __DB_NAME__;

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

INSERT INTO Utente (username, nome, cognome, password, isAdmin, data_di_nascita) VALUES
('admin', 'Admin', 'Prophit', '$2y$12$3bH8usFSfx.uZUKalInpXOr68Mj86rj0cyZPPDsVZJ8clY7H8Pf66', TRUE, '1990-01-01'),
('user', 'User', 'Standard', '$2y$12$10cYxTeXdGuzHPMGtcosMeVRm.LAn.kP5jTGpc4nlAKjeHkZ2a37.', FALSE, '2000-01-01');

INSERT INTO Categoria (id, nome) VALUES
(1, 'Investimenti'),
(2, 'Risparmio'),
(3, 'Previdenza');

INSERT INTO Corso (id, titolo, immagine, categoria, durata, costo, modalita, breve_desc, desc_completa) VALUES
(1, 'Analisi di bilancio per Fuffaguru', '../../img/foto-corso-1.jpg', 'Investimenti', 14, 360, 'Online live', 'Leggere i numeri giusti per capire se un titolo ha senso.', 'Impari a leggere stato patrimoniale e conto economico senza farti incantare dalla fuffa. Esempi pratici e check rapidi per valutare aziende reali.'),
(2, 'ETF senza stress', '../../img/foto-corso-2.jpg', 'Investimenti', 10, 280, 'Online registrata', 'Come scegliere ETF coerenti con obiettivi e rischio.', 'Dalla composizione ai costi, con criteri semplici per creare un portafoglio solido e ripetibile nel tempo.'),
(3, 'Azioni da bar: strategie long-term', '../../img/foto-corso-1.jpg', 'Investimenti', 16, 420, 'In aula', 'Focus su metodo, pazienza e numeri misurabili.', 'Un percorso pratico per impostare una strategia di lungo periodo, evitando mode e scelte impulsive.'),
(4, 'Analisi tecnica minimal', '../../img/foto-corso-2.jpg', 'Investimenti', 12, 320, 'Online live', 'Indicatori essenziali per leggere trend e livelli.', 'Usi pochi strumenti ma buoni: trendline, supporti e volumi. Meno fumo, piu disciplina.'),
(5, 'Budget Zen', '../../img/foto-corso-1.jpg', 'Risparmio', 8, 190, 'Online registrata', 'Metodi semplici per tenere le spese sotto controllo.', 'Costruisci un budget che funziona davvero e smetti di vivere a fine mese con l ansia.'),
(6, 'Spese invisibili: tagli leggeri', '../../img/foto-corso-2.jpg', 'Risparmio', 6, 160, 'Online registrata', 'Ridurre costi senza stravolgere lo stile di vita.', 'Tecniche pratiche per individuare uscite inutili e liberare liquidita senza sacrifici.'),
(7, 'Fondo emergenza in 30 giorni', '../../img/foto-corso-1.jpg', 'Risparmio', 7, 210, 'Online live', 'Costruire un cuscinetto con piccoli passi.', 'Definisci obiettivo, priorita e automatismi per accumulare in modo costante.'),
(8, 'Obiettivi a breve: metodo envelope', '../../img/foto-corso-2.jpg', 'Risparmio', 9, 220, 'In aula', 'Gestione pratica con categorie e limiti chiari.', 'Impari a usare il metodo delle buste per pianificare spese e obiettivi in modo sostenibile.'),
(9, 'Pensione chiara', '../../img/foto-corso-1.jpg', 'Previdenza', 10, 250, 'Online live', 'Capire il sistema e calcolare una stima realistica.', 'Panoramica su contributi, eta pensionabile e strumenti di previdenza complementare.'),
(10, 'Fondi pensione senza paura', '../../img/foto-corso-2.jpg', 'Previdenza', 12, 300, 'Online registrata', 'Tipi di fondi, costi e vantaggi fiscali.', 'Confronto tra fondi aperti, chiusi e PIP con esempi pratici di scelta.'),
(11, 'PIR e strumenti previdenziali', '../../img/foto-corso-1.jpg', 'Previdenza', 11, 280, 'Online live', 'Strumenti utili e limiti da conoscere.', 'Guida chiara a PIR, previdenza complementare e opzioni per diversificare.'),
(12, 'TFR e scelte intelligenti', '../../img/foto-corso-2.jpg', 'Previdenza', 9, 230, 'In aula', 'Cosa fare con il TFR e perche.', 'Valuti pro e contro tra azienda e fondo, con esempi numerici semplici.');

INSERT INTO CorsoCategoria (id_corso, id_categoria) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 2),
(6, 2),
(7, 2),
(8, 2),
(9, 3),
(10, 3),
(11, 3),
(12, 3);

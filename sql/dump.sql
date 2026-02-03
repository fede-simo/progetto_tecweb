INSERT INTO Categoria (id, nome) VALUES
(1, 'Investimenti'),
(2, 'Risparmio'),
(3, 'Previdenza');

INSERT INTO Corso (id, titolo, immagine, categoria, durata, costo, modalita, breve_desc, desc_completa) VALUES
(1, 'Analisi di bilancio', './img/foto-corso-1.jpg', 'Investimenti', 14, 360, 'Online live', 'Leggere i numeri giusti per capire se un titolo ha senso.', 'Impari a leggere stato patrimoniale e conto economico senza farti incantare dalla fuffa. Esempi pratici e controlli rapidi per valutare aziende reali.'),
(2, 'ETF senza sforzi', './img/foto-corso-2.jpg', 'Investimenti', 10, 280, 'Online registrata', 'Come scegliere ETF coerenti con obiettivi e rischio.', 'Dalla composizione ai costi, con criteri semplici per creare un portafoglio solido e ripetibile nel tempo.'),
(3, 'Azioni da bar: strategie long-term', './img/foto-corso-3.jpg', 'Investimenti', 16, 420, 'In aula', 'Focus su metodo, pazienza e numeri misurabili.', 'Un percorso pratico per impostare una strategia di lungo periodo, evitando mode e scelte impulsive.'),
(4, 'Analisi tecnica minimal', './img/foto-corso-4.jpg', 'Investimenti', 12, 320, 'Online live', 'Indicatori essenziali per leggere andamenti e livelli.', 'Usi pochi strumenti ma buoni: andamenti, supporti e volumi. Meno fumo, piu disciplina.'),
(5, 'Budget Zen', './img/foto-corso-5.jpg', 'Risparmio', 8, 190, 'Online registrata', 'Metodi semplici per tenere le spese sotto controllo.', 'Crea un budget che funziona davvero e smetti di vivere a fine mese con l''ansia.'),
(6, 'Spese invisibili: tagli leggeri', './img/foto-corso-6.jpg', 'Risparmio', 6, 160, 'Online registrata', 'Ridurre costi senza stravolgere lo stile di vita.', 'Tecniche pratiche per individuare uscite inutili e liberare liquidità senza sacrifici.'),
(7, 'Fondo emergenza in 30 giorni', './img/foto-corso-7.jpg', 'Risparmio', 7, 210, 'Online live', 'Costruire un cuscinetto con piccoli passi.', 'Definisci obiettivo, priorita e automatismi per accumulare in modo costante.'),
(8, 'Obiettivi a breve: metodo envelope', './img/foto-corso-8.jpg', 'Risparmio', 9, 220, 'In aula', 'Gestione pratica con categorie e limiti chiari.', 'Impari a usare il metodo delle buste per pianificare spese e obiettivi in modo sostenibile.'),
(9, 'Pensione chiara', './img/foto-corso-9.jpg', 'Previdenza', 10, 250, 'Online live', 'Capire il sistema e calcolare una stima realistica.', 'Panoramica su contributi, eta pensionabile e strumenti di previdenza complementare.'),
(10, 'Fondi pensione senza paura', './img/foto-corso-10.jpeg', 'Previdenza', 12, 300, 'Online registrata', 'Tipi di fondi, costi e vantaggi fiscali.', 'Confronto tra fondi aperti, chiusi e PIP con esempi pratici di scelta.'),
(11, 'PIR e strumenti previdenziali', './img/foto-corso-11.jpg', 'Previdenza', 11, 280, 'Online live', 'Strumenti utili e limiti da conoscere.', 'Guida chiara a Piani Individuali di Risparmio, previdenza complementare e opzioni per diversificare.'),
(12, 'TFR e scelte intelligenti', './img/foto-corso-12.jpg', 'Previdenza', 9, 230, 'In aula', 'Cosa fare con il TFR e perché.', 'Valuti pro e contro tra azienda e fondo, con esempi numerici semplici.');

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

INSERT INTO Utente (username, nome, cognome, password, isAdmin, data_di_nascita) VALUES
('user', 'User', 'Standard', '$2y$10$RiIo3BfN4RfdOt3i6k6aMeDcnMskmDAo6zSRPvxDugcGM5aa3LlZm', FALSE, '2000-01-01'),
('admin', 'Admin', 'Prophit', '$2y$10$3yiDj8iY4MojUltGEzJZv.0JEaUyhOYyx8XV0hk3O96ZrPvpIiLSy', TRUE, '1990-01-01');


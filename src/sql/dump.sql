INSERT INTO Utente (username, nome, cognome, password, isAdmin, data_di_nascita) VALUES
('user', 'user', 'user', '$2y$10$zmpEFyW/0mGMKZr90TTMMOGytFEMoiOZCieHp8Wie/IfKA9qi20Hm', FALSE, '2000-01-01'),
('admin', 'admin', 'admin', '$2y$10$imrp0lNBcklXR4xDyp9MxOP33KeZVEQdESdNdUq4m9p/7zrf.4n8e', TRUE, '2000-01-01');

INSERT INTO Corso (id, titolo, immagine, categoria, durata, costo, modalita, breve_desc, desc_completa) VALUES
(1, 'Finanza Operativa', '../../img/foto-corso-1.jpg', 'Altro', 16, 490, 'Online live', 'Basi pratiche: margini, cassa, KPI e lettura dei numeri essenziali.', 'Palle.'),
(2, 'Budget And Cash Flow', '../../img/foto-corso-2.jpg', 'Altro', 12, 390, 'In aula', 'Impostazione budget, forecast e piano di cassa con esempi semplici.', 'Palle.'),
(3, 'Finanza Operativa', '../../img/foto-corso-1.jpg', 'Altro', 16, 490, 'Online live', 'Basi pratiche: margini, cassa, KPI e lettura dei numeri essenziali.', 'Palle.'),
(4, 'Finanza Operativa', '../../img/foto-corso-1.jpg', 'Altro', 16, 490, 'Online live', 'Basi pratiche: margini, cassa, KPI e lettura dei numeri essenziali.', 'Palle.'),
(5, 'Criptovalute per Principianti', '../../img/foto-corso-1.jpg', 'Cripto', 10, 299, 'Online registrata', 'Introduzione al mondo delle criptovalute: cosa sono, come funzionano e come iniziare.', 'Palle.'),
(6, 'Criptovalute avanzate', '../../img/foto-corso-1.jpg', 'Cripto', 14, 399, 'In aula', 'Strategie di investimento, gestione del rischio e analisi del mercato delle criptovalute.', 'Palle.');
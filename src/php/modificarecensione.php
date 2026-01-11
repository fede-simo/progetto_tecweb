<?php

require_once "helpers.php";
require_once "dbConnection.php";

session_start();

$paginaHTML = file_get_contents('../html/dettagliocorso.html');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $paginaHTML = str_replace('{titolo}', 'Corso non trovato', $paginaHTML);
    $paginaHTML = str_replace('{immagine}', '../../img/foto-corso-1.jpg', $paginaHTML);
    $paginaHTML = str_replace('{breve_desc}', 'Il corso richiesto non esiste.', $paginaHTML);
    $paginaHTML = str_replace('{prezzo}', '--', $paginaHTML);
    $paginaHTML = str_replace('{tipologia}', '--', $paginaHTML);
    $paginaHTML = str_replace('{durata}', '--', $paginaHTML);
    $paginaHTML = str_replace('{desc_completa}', 'Nessun dettaglio disponibile.', $paginaHTML);
    replaceContent("azione-corso", '', $paginaHTML);
    replaceContent("recensioni", '<p>Nessuna recensione disponibile.</p>', $paginaHTML);
    replaceContent("form-recensione", '', $paginaHTML);
    echo $paginaHTML;
    exit();
}



?>
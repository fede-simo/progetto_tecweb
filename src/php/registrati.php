<?php

function stickyForm($paginaHTML, $campi) {
    foreach ($campi as $campo) {
        $bookmark = "<!--{start-form-".$campo."}-->";
        $paginaHTML = str_replace($bookmark, 'value="'.htmlspecialchars($_POST[$campo]).'"', $paginaHTML);
    }
    return $paginaHTML;
}

require_once "dbConnection.php";

$paginaHTML = file_get_contents('/progetto_tecweb/src/html/registrati.html');



session_start();

if (isset($_SESSION["user"])) {
    header("Location: areapersonale.html");
    exit();
}



$campi = ['nome', 'cognome', 'username', 'data-di-nascita', 'password'];
$stickyCampi = ['nome', 'cognome', 'username', 'data-di-nascita'];

foreach ($campi as $campo) {
    if (empty($_POST[$campo])) {
        die("Campo mancante: $campo");
    }
}



try {
    $connessione = new DB\DBAccess();

    $conn = $connessione->openConnection();

    

    $connessione->closeConnection();

} catch (Exception $e) {
    $paginaHTML .= "<p>Si è verificato un errore. Riprova più tardi.</p>";
    echo $paginaHTML;
    exit();
}



?>
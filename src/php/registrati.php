<?php

function stickyForm($paginaHTML, $campi) : string {
    foreach ($campi as $campo) {
        if (isset($_POST[$campo])) {
            $value = htmlspecialchars($_POST[$campo], ENT_QUOTES);
            $paginaHTML = str_replace("{".$campo."}", $value, $paginaHTML);
        } else {
            $paginaHTML = str_replace("{".$campo."}", "", $paginaHTML);
        }
    }
    return $paginaHTML;
}

require_once "dbConnection.php";

$paginaHTML = file_get_contents("../html/registrati.html");


session_start();

if (isset($_SESSION["user"])) {
    header("Location: areapersonale.html");
    exit();
}





$campi = ['nome', 'cognome', 'username', 'data-di-nascita', 'password'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campi as $campo) {
        if (empty($_POST[$campo])) {
            stickyForm($paginaHTML, $campi);
            echo $paginaHTML;
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
} else {
    $paginaHTML = stickyForm($paginaHTML, $campi);
    echo $paginaHTML;
}


?>
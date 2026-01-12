<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ./accedi.php');
    exit();
}

$paginaHTML = file_get_contents('./html/modificarecensione.html');

$err = '';
$recensione = "";


$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo $paginaHTML;
    exit();
}

/*
provato a farlo con le robe gia impostate ma non va :(
try {
    $connessione = new DB\DBAccess();
    $conn = $connessione->openConnection();
    if (!$conn) throw new Exception('Connessione al database non riuscita.');

    $recensione = $connessione->getRecensioneById($id);
    $connessione->closeConnection();


} catch (Exception $e){
    echo $e->getMessage();
}*/




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $rating = isset($_POST['rating']) ? trim($_POST['rating']) : "";
    $descrizione = isset($_POST['descrizione']) ? trim($_POST['descrizione']) : "";

    if ($rating !== "") {

        try {
            $connessione = new DB\DBAccess();
            $conn = $connessione->openConnection();
            if (!$conn) throw new Exception('Connessione al database non riuscita.');

            $success = $connessione->modificaRecensione($_SESSION['user'], $id, $rating, $descrizione);
            $connessione->closeConnection();

            if ($success) {
                header("Location: areapersonale.php");
                exit();
            }
        } catch (Exception $e){
            echo $e->getMessage();
        }
    } else {
        replaceContent("errore", $err, $paginaHTML);
    }
} else {
    
}

$form = '<form action="./modificarecensione.php?id='. urlencode($id) .'" method="POST" class="register-form">  ';
replaceContent('azione-form', $form, $paginaHTML);

$paginaHTML = str_replace("{rating}",htmlspecialchars($recensione['rating']), $paginaHTML);
$paginaHTML = str_replace("{descrizione}",htmlspecialchars($recensione['descrizione']), $paginaHTML);

echo $paginaHTML;





?>
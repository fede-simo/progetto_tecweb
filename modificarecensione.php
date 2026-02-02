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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        header("Location: ./areapersonale.php");
        exit();
    }

    try {
        $connessione = new DB\DBAccess();
        $conn = $connessione->openConnection();
        if (!$conn) {
            throw new RuntimeException('DB connection failed');
        }

        $recensione = $connessione->getRecensioneById($id);
        $connessione->closeConnection();
    } catch (Throwable $e) {
        error_log($e->__toString());
        throw $e;
    }

    $paginaHTML = str_replace("{rating}",htmlspecialchars($recensione['rating']), $paginaHTML);
    $paginaHTML = str_replace("{descrizione}",htmlspecialchars($recensione['descrizione']), $paginaHTML);
    $paginaHTML = str_replace("{id}", $id, $paginaHTML);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "din";

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
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

echo $paginaHTML;

?>
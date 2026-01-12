<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: accedi.php');
    exit();
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;


if (!isset($_SESSION['user'])) {
    header("Location: accedi.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id <= 0) {
    header("Location: areapersonale.php");
    exit();
}


try {
    $connessione = new DB\DBAccess();
    $conn = $connessione->openConnection();

    if (!$conn) {
        throw new Exception('Connessione al database non riuscita.');
    }

    $connessione->eliminaRecensione($id, $_SESSION['user']);
    $connessione->closeConnection();

} catch (Throwable $e) {
}

header("Location: areapersonale.php");
exit();

?>
<?php

require_once "dbConnection.php";
require_once "helpers.php";

session_start();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// per debug
if ($id <= 0) {
    echo $id;
    exit();
}

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
} catch (Throwable $e) {
    echo 'errore nella query';
}

header("Location: areapersonale.php");
exit();

?>
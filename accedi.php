<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

$paginaHTML = file_get_contents('./html/accedi.html');
$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : "";
    $password = isset($_POST['password']) ? $_POST['password'] : "";

    if ($username === "" || $password === "") {
        $err = '<p class="errore-registrazione">Inserisci <span lang="en">username</span> e <span lang="en">password</span>.</p>';
        replaceContent("errore-login", $err, $paginaHTML);
        echo $paginaHTML;
        exit();
    }

    try {
        $connessione = new DB\DBAccess();
        $conn = $connessione->openConnection();

        if (!$conn) {
            throw new RuntimeException('DB connection failed');
        }

        $utente = $connessione->getUtenteByUsername($username);
        $connessione->closeConnection();

        if ($utente && password_verify($password, $utente['password'])) {
            session_start();
            $_SESSION['user'] = $utente['username'];
            $_SESSION['is_admin'] = (bool) $utente['isAdmin'];
            header("Location: ./areapersonale.php");
            exit();
        }

        $err = '<p class="errore-registrazione">Credenziali non valide.</p>';
        replaceContent("errore-login", $err, $paginaHTML);
        echo $paginaHTML;
        exit();
    } catch (Throwable $e) {
        error_log($e->__toString());
        throw $e;
    }
}

echo $paginaHTML;

?>

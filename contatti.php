<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

$paginaHTML = file_get_contents('./html/contatti.html');
$messaggio = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $oggetto = isset($_POST['oggetto']) ? trim($_POST['oggetto']) : '';
    $testo = isset($_POST['messaggio']) ? trim($_POST['messaggio']) : '';

    if ($nome === '' || $email === '' || $oggetto === '' || $testo === '') {
        $messaggio = '<p class="errore-registrazione">Compila tutti i campi.</p>';
    } else {
        try {
            $connessione = new DB\DBAccess();
            $conn = $connessione->openConnection();
            if ($conn) {
                $ok = $connessione->addContatto($nome, $email, $oggetto, $testo);
                $messaggio = $ok ? '<p class="">Messaggio inviato.</p>' : '<p class="errore">Errore durante l\'invio.</p>';
                $connessione->closeConnection();
            } else {
                $messaggio = '<p class="errore">Connessione al <span lang="en">database</span> non riuscita.</p>';
            }
        } catch (Throwable $e) {
            //$messaggio = '<p class="errore">Errore interno: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '</p>';
            header("location: ./html/500.html");
            exit();
        }
    }
}

if ($messaggio !== "") {
    replaceContent("contatti-message", $messaggio, $paginaHTML);
}

echo $paginaHTML;

?>

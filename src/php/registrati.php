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


require_once "helpers.php";
require_once "dbConnection.php";

$paginaHTML = file_get_contents('../html/registrati.html');


session_start();

if (isset($_SESSION['user'])) {
    header("Location: ../php/areapersonale.php");
    exit();
}

$nome = '';
$cognome = '';
$dataNascita = '';
$username = '';

$campi = ['nome', 'cognome', 'username', 'data_di_nascita'];
$err = "";

function pulisciInput($value){
 	$value = trim($value);
  	$value = strip_tags($value);
	$value = htmlentities($value);
  	return $value;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $connessione = new DB\DBAccess();

        $conn = $connessione->openConnection();
        if (!$conn) {
            $err .= '<p class="errore-registrazione">Connessione al database non riuscita.</p>';
            replaceContent("errore-registrazione", $err, $paginaHTML);
            $paginaHTML = stickyForm($paginaHTML, $campi);
            echo $paginaHTML;
            exit();
        }

        $totaleUtenti = $connessione->countUtenti();
        $isAdmin = ($totaleUtenti === 0);
        $isAdmin = false;

        $result = $connessione->registraUtente(
            $_POST['username'],
            $_POST['nome'],
            $_POST['cognome'],
            $hash,
            $isAdmin,
            $_POST['data_di_nascita'],          
            $err
        );

        $connessione->closeConnection();

        if ($result) {
            $_SESSION['user'] = $_POST['username'];
            $_SESSION['is_admin'] = $isAdmin;
            header("Location: ../php/areapersonale.php");
            exit();
        } else {
            replaceContent("errore-registrazione", $err, $paginaHTML);
            $paginaHTML = stickyForm($paginaHTML, $campi);
            echo $paginaHTML;
            exit();
        }
            

    } catch (Throwable $e) {
        $err .= '<p class="errore-registrazione">Errore durante la registrazione.</p>';
        replaceContent("errore-registrazione", $err, $paginaHTML);
        $paginaHTML = stickyForm($paginaHTML, $campi);
        echo $paginaHTML;
        exit();
    }
} else {
    $paginaHTML = stickyForm($paginaHTML, $campi);
    echo $paginaHTML;
}


?>

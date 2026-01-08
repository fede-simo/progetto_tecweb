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


function fieldsRestriction($campo, &$err) : bool {
    if ($campo === 'nome' || $campo === 'cognome') {
        if (!preg_match('/^[a-zA-ZÀ-ÿ\' -]{2,30}$/u', $_POST[$campo])) {
            $err .= '<p>Il nome e il cognome non possono contenere numeri o caratteri speciali e devono essere lunghi da 2 a 30 caratteri.</p>';
            return false;
        }
    } elseif ($campo === 'username') {
        if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $_POST[$campo])) {
            $err .= '<p>Lo username deve essere composto da 5 a 20 caratteri alfanumerici o underscore.</p>';
            return false;
        }
    } elseif ($campo === 'password') {
        if (strlen($_POST[$campo]) < 8 || !preg_match('/[a-z]/', $_POST[$campo]) || !preg_match('/[A-Z]/', $_POST[$campo]) || !preg_match('/[0-9]/', $_POST[$campo]) || !preg_match('/[!@#$%^&*(),.?":{}|<>]/', $_POST[$campo])) {
            $err .= '<p>La password deve essere lunga almeno 8 caratteri, contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.</p>';
            return false;
        }
    } elseif ($campo === 'data_di_nascita') {
        $data = DateTime::createFromFormat('Y-m-d', $_POST[$campo]);
        $now = new DateTime();
        if (!$data || $data > $now || $now->diff($data)->y < 18) {
            $err .= '<p>Per registrati a Prophit devi essere maggiorenne e la data di nascita deve essere valida.</p>';
            return false;
        }
    } 
    return true;
}

require_once "helpers.php";
require_once "dbConnection.php";

$paginaHTML = file_get_contents("../html/registrati.html");


session_start();

if (isset($_SESSION["user"])) {
    header("Location: areapersonale.php");
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
    foreach ($campi as $campo) {
        if (isset($_POST[$campo])) {
            $_POST[$campo] = trim($_POST[$campo]);

            if (empty($_POST[$campo])) {
                $err .= '<p>Tutti i campi sono obbligatori.</p>';
                break;
            } else fieldsRestriction($campo, $err);
        }
    }
    if (empty($err)) {
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
        if ($password === '' || $passwordConfirm === '') {
            $err .= '<p>Tutti i campi sono obbligatori.</p>';
        } elseif ($password !== $passwordConfirm) {
            $err .= '<p>Le password non coincidono.</p>';
        } else {
            $_POST['password'] = $password;
            fieldsRestriction('password', $err);
        }
    }
    if (!empty($err)) {
        replaceContent("errore-registrazione", $err, $paginaHTML);
        $paginaHTML = stickyForm($paginaHTML, $campi);
        echo $paginaHTML;
        exit();
    }

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

        $result = $connessione->registraUtente(
            $_POST['nome'],
            $_POST['cognome'],
            $_POST['username'],
            $_POST['data_di_nascita'],
            $hash,
            $isAdmin,
            $err
        );

        $connessione->closeConnection();


        if ($result) {
            $_SESSION['user'] = $_POST['username'];
            $_SESSION['is_admin'] = $isAdmin;
            header("Location: /src/php/areapersonale.php");
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
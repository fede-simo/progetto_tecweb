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

function emptyFields($campo) : bool {
    if (empty($_POST[$campo])) return true;
    else return false;
}

function fieldsRestriction($campo, $err) : bool {
    if ($campo === 'nome' || $campo === 'cognome') {
        if (!preg_match('/^[a-zA-ZÀ-ÿ\' -]{2,30}$/u', $_POST[$campo])) {
            $err .= "<p>Il nome e il cognome non possono contenere numeri o caratteri speciali e devono essere lunghi massimo 30 caratteri.</p>";
            return false;
        }
    } elseif ($campo === 'username') {
        if (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $_POST[$campo])) {
            $err .= "<p>Lo username deve essere composto da 5 a 20 caratteri alfanumerici o underscore.</p>";
            return false;
        }
    } elseif ($campo === 'password') {
        if (strlen($_POST[$campo]) < 8 || !preg_match('/[a-z]/', $_POST[$campo]) || !preg_match('/[A-Z]/', $_POST[$campo]) || !preg_match('/[0-9]/', $_POST[$campo]) || !preg_match('/[\W]/', $_POST[$campo])) {
            $err .= "<p>La password deve essere lunga almeno 8 caratteri, contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.</p>";
            return false;
        }
    } elseif ($campo === 'data-di-nascita') {
        $data = DateTime::createFromFormat('Y-m-d', $_POST[$campo]);
        $now = new DateTime();
        if (!$data || $data > $now || $now->diff($data)->y < 18) {
            $err .= "<p>Devi essere maggiorenne per registrarti e la data di nascita deve essere valida.</p>";
            return false;
        }
    } 
    return true;
}

require_once "dbConnection.php";

$paginaHTML = file_get_contents("../html/registrati.html");


session_start();

if (isset($_SESSION["user"])) {
    header("Location: areapersonale.html");
    exit();
}





$campi = ['nome', 'cognome', 'username', 'data-di-nascita', 'password'];
$err = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campi as $campo) {
        if (isset($_POST[$campo])) {
            $_POST[$campo] = trim($_POST[$campo]);
            if (emptyFields($campo)) {
                $err .= "<p>Il campo " . htmlspecialchars($campo) . " è obbligatorio.</p>";
            } elseif (!fieldsRestriction($campo, $err)) {
                $err .= "<p>Il campo " . htmlspecialchars($campo) . " non rispetta le restrizioni.</p>";
            }

        }
    }
    if (!empty($err)) {
        $paginaHTML .= $err;
        $paginaHTML = stickyForm($paginaHTML, $campi);
        echo $paginaHTML;
        exit();
    }

    try {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        echo $hash;
        $connessione = new DB\DBAccess();

        $conn = $connessione->openConnection();

        $result = $connessione->registerUser(
            $_POST['nome'],
            $_POST['cognome'],
            $_POST['username'],
            $_POST['data-di-nascita'],
            $_POST['password'],
            $err
        );

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
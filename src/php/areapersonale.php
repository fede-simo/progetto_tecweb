<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../php/accedi.php");
    exit();
}

$paginaHTML = file_get_contents('../html/areapersonale.html');
$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES);
$paginaHTML = str_replace('{username}', $username, $paginaHTML);

require_once "dbConnection.php";
require_once "helpers.php";


if (!empty($_SESSION['is_admin'])) {
    header("Location: ../php/admin.php");
    exit();
}
 
$corsiHtml = '';
$corsi = [];
$noCorsi ='
    <section class="welcome-form">
        <h4 class="viz-msg">Non hai ancora acquistato alcun corso, <a href="../php/corsi.php">rimedia</a>.</h4>
    </section>';

$recensioni = [];
$recensioniHtml = '';
$noRecensioni ='
    <section class="welcome-form">
        <h4 class="viz-msg">Non hai ancora pubblicato alcuna recensione.</h4>
    </section>';


try {
    $db = new DB\DBAccess();
    $conn = $db->openConnection();

    if ($conn) {
        try {
            $corsi = $db->getCorsiByUser($_SESSION['user']);
        } catch (Throwable $e) {
            $corsi = [];
        }

        try {
            $recensioni = $db->getRecensioniByUser($_SESSION['user']);
        } catch (Throwable $e) {
            $recensioni = [];
        }

        $db->closeConnection();
    }
} catch (Throwable $e) {
    $corsiHtml .= '<p>Si è verificato un errore. Riprova più tardi.</p>';
    // TODO: POSSIBILE PAG 500 O 404??
}


if (!empty($corsi)) {    
    foreach ($corsi as $corso) {
        $modalita = allyModCorso($corso['modalita']);
        $corsiHtml .=
        '<tr>
            <th scope="row"><a href="../php/dettagliocorso.php?id=' . urlencode($corso['id']) . '" class="corso-link-tabella"><strong>' . htmlspecialchars($corso['titolo']) . '</strong></a></th>
            <td data-title="Categoria">' . htmlspecialchars($corso['categoria']) . '</td>
            <td data-title="Durata">' . htmlspecialchars($corso['durata']) . ' ore</td>
            <td data-title="Prezzo">€ ' . htmlspecialchars($corso['costo']) . '</td>
            <td data-title="Modalità">' . $modalita . '</td>
            <td data-title="Data acquisto">' . htmlspecialchars($corso['data_acquisto']) . '</td>
        </tr>';
    }
    replaceContent("miei-corsi", $corsiHtml, $paginaHTML);
} else {
    replaceContent("miei-corsi-section", $noCorsi, $paginaHTML);
    replaceContent("tabella-corsi-acquistati", "", $paginaHTML);
}

if (!empty($recensioni)) {   
    foreach ($recensioni as $recensione) {
        $recensioniHtml .=
        '<tr>
            <th scope="row"><a href="../php/dettagliocorso.php?id=' . urlencode($recensione['id_corso']) . '" class="corso-link-tabella"><strong>' . htmlspecialchars($recensione['titolo']) . '</strong></a></th>
            <td data-title="Voto">' . htmlspecialchars($recensione['rating']) . '</td>
            <td data-title="Descrizione">' . htmlspecialchars($recensione['descrizione']) . '</td>
            <td data-title=""><button type="button" class="btn-modify">Modifica</button></td>
            <td data-title=""><button type="button" class="btn-danger">Elimina</button></td>
        </tr>';
    }
    replaceContent("mie-recensioni", $recensioniHtml, $paginaHTML);
} else {
    replaceContent("mie-recensioni-section", $noRecensioni, $paginaHTML);
    replaceContent("tabella-recensioni", "", $paginaHTML);
}

echo $paginaHTML;

?>

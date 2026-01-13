<?php

session_start();


if (!isset($_SESSION['user'])) {
    header("Location: ./accedi.php");
    exit();
}


$paginaHTML = file_get_contents('./html/areapersonale.html');
$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES);
$paginaHTML = str_replace('{username}', $username, $paginaHTML);

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";


if (!empty($_SESSION['is_admin'])) {
    header("Location: ./admin.php");
    exit();
}
 
$corsiHtml = '';
$corsi = [];
$noCorsi ='
    <section class="welcome-form">
        <h4 class="viz-msg">Non hai ancora acquistato alcun corso, <a href="./corsi.php">rimedia</a>.</h4>
    </section>';

$recensioni = [];
$recensioniHtml = '';
$noRecensioni ='
    <section class="welcome-form">
        <h4 class="viz-msg">Non hai alcuna recensione pubblicata.</h4>
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
            <th scope="row"><a href="./dettagliocorso.php?id=' . urlencode($corso['id']) . '" class="corso-link-tabella"><strong>' . htmlspecialchars($corso['titolo']) . '</strong></a></th>
            <td data-title="Categoria">' . htmlspecialchars($corso['categoria']) . '</td>
            <td data-title="Durata">' . htmlspecialchars($corso['durata']) . ' ore</td>
            <td data-title="Prezzo">€ ' . htmlspecialchars($corso['costo']) . '</td>
            <td data-title="Modalità">' . $modalita . '</td>
            <td data-title="Data acquisto">' . htmlspecialchars($corso['data_acquisto']) . '</td>
        </tr>';
    }
    replaceContent("miei-corsi-table", $corsiHtml, $paginaHTML);
} else {
    replaceContent("miei-corsi-section", $noCorsi, $paginaHTML);
}

if (!empty($recensioni)) {   
    foreach ($recensioni as $recensione) {
        // se vuole modificare la recensione, cancella e ne fa un'altra
        $recensioniHtml .=
        '<tr>
            <th scope="row"><a href="./dettagliocorso.php?id=' . urlencode($recensione['id_corso']) . '" class="corso-link-tabella"><strong>' . htmlspecialchars($recensione['titolo']) . '</strong></a></th>
            <td data-title="Voto">' . htmlspecialchars($recensione['rating']) . '</td>
            <td data-title="Descrizione">' . htmlspecialchars($recensione['descrizione']) . '</td>
            <td data-title=""><form action="./modificarecensione.php" method="GET">
                <input type="hidden" name="id" value="' . $recensione['id'] . '">
                <button type="submit" class="action-btn">
                    MODIFICA
                </button>
            </form></td>
            <td data-title=""><form action="./eliminarecensione.php?id=' . urlencode($recensione['id']) . '" method="POST"
                onsubmit="return confirm(\'Sei sicuro di voler eliminare questa recensione?\');">
                <button type="submit" class="action-btn action-btn-danger">
                    ELIMINA
                </button>
            </form></td>
        </tr>';
    }
    replaceContent("mie-recensioni-table", $recensioniHtml, $paginaHTML);
} else {
    replaceContent("mie-recensioni-section", $noRecensioni, $paginaHTML);
}

echo $paginaHTML;

?>

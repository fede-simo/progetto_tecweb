<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

session_start();

$paginaHTML = file_get_contents('./html/dettagliocorso.html');

if (!isset($_SESSION['user'])) $paginaHTML = str_replace('{azione-titolo-corso}', 'Prenota ora', $paginaHTML);

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    $paginaHTML = str_replace('{titolo}', 'Corso non trovato', $paginaHTML);
    $paginaHTML = str_replace('{immagine}', './img/foto-corso-1.jpg', $paginaHTML);
    $paginaHTML = str_replace('{breve_desc}', 'Il corso richiesto non esiste.', $paginaHTML);
    $paginaHTML = str_replace('{categoria}', '--', $paginaHTML);
    $paginaHTML = str_replace('{prezzo}', '--', $paginaHTML);
    $paginaHTML = str_replace('{tipologia}', '--', $paginaHTML);
    $paginaHTML = str_replace('{durata}', '--', $paginaHTML);
    $paginaHTML = str_replace('{desc_completa}', 'Nessun dettaglio disponibile.', $paginaHTML);
    replaceContent("azione-corso", '', $paginaHTML);
    replaceContent("recensioni", '<p>Nessuna recensione disponibile.</p>', $paginaHTML);
    replaceContent("form-recensione", '', $paginaHTML);
    echo $paginaHTML;
    exit();
}

try {
    $connessione = new DB\DBAccess();
    $conn = $connessione->openConnection();
    if (!$conn) {
        throw new Exception('Connessione al database non riuscita.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        if (!isset($_SESSION['user'])) {
            header("Location: ./accedi.php");
            exit();
        }
        if ($action === 'acquista') {
            $connessione->acquistaCorso($_SESSION['user'], $id);
        } elseif ($action === 'elimina') {
            $connessione->eliminaAcquisto($_SESSION['user'], $id);
        } elseif ($action === 'recensisci') {
            $rating = isset($_POST['rating']) ? (float) $_POST['rating'] : 0.0;
            $descrizione = isset($_POST['descrizione']) ? trim($_POST['descrizione']) : '';
            if ($rating > 0 && $descrizione !== '') {
                $connessione->addRecensione($_SESSION['user'], $id, $rating, $descrizione);
            }
        }
    }

    $corso = $connessione->getCorsoById($id);
    $recensioni = $connessione->getRecensioniByCorso($id);
    $haAcquistato = isset($_SESSION['user']) ? $connessione->hasAcquisto($_SESSION['user'], $id) : false;
    $haRecensito = isset($_SESSION['user']) ? $connessione->hasRecensione($_SESSION['user'], $id) : false;
    $connessione->closeConnection();
} catch (Throwable $e) {
    $paginaHTML = str_replace('{titolo}', 'Errore', $paginaHTML);
    $paginaHTML = str_replace('{immagine}', './img/foto-corso-1.jpg', $paginaHTML);
    $paginaHTML = str_replace('{breve_desc}', 'Si è verificato un errore. Riprova più tardi.', $paginaHTML);
    $paginaHTML = str_replace('{categoria}', '--', $paginaHTML);
    $paginaHTML = str_replace('{prezzo}', '--', $paginaHTML);
    $paginaHTML = str_replace('{tipologia}', '--', $paginaHTML);
    $paginaHTML = str_replace('{durata}', '--', $paginaHTML);
    $paginaHTML = str_replace('{desc_completa}', 'Nessun dettaglio disponibile.', $paginaHTML);
    replaceContent("azione-corso", '', $paginaHTML);
    replaceContent("recensioni", '<p>Nessuna recensione disponibile.</p>', $paginaHTML);
    replaceContent("form-recensione", '', $paginaHTML);
    echo $paginaHTML;
    exit();
}

if (!$corso) {
    $paginaHTML = str_replace('{titolo}', 'Corso non trovato', $paginaHTML);
    $paginaHTML = str_replace('{immagine}', './img/foto-corso-1.jpg', $paginaHTML);
    $paginaHTML = str_replace('{breve_desc}', 'Il corso richiesto non esiste.', $paginaHTML);
    $paginaHTML = str_replace('{categoria}', '--', $paginaHTML);
    $paginaHTML = str_replace('{prezzo}', '--', $paginaHTML);
    $paginaHTML = str_replace('{tipologia}', '--', $paginaHTML);
    $paginaHTML = str_replace('{durata}', '--', $paginaHTML);
    $paginaHTML = str_replace('{desc_completa}', 'Nessun dettaglio disponibile.', $paginaHTML);
    replaceContent("azione-corso", '', $paginaHTML);
    replaceContent("recensioni", '<p>Nessuna recensione disponibile.</p>', $paginaHTML);
    replaceContent("form-recensione", '', $paginaHTML);
    echo $paginaHTML;
    exit();
}

$paginaHTML = str_replace('{titolo}', htmlspecialchars($corso['titolo']), $paginaHTML);
$paginaHTML = str_replace('{immagine}', htmlspecialchars($corso['immagine']), $paginaHTML);
$paginaHTML = str_replace('{breve_desc}', htmlspecialchars($corso['breve_desc']), $paginaHTML);
$paginaHTML = str_replace('{categoria}', htmlspecialchars($corso['categoria']), $paginaHTML);
$paginaHTML = str_replace('{prezzo}', htmlspecialchars($corso['costo']), $paginaHTML);
$paginaHTML = str_replace('{tipologia}', htmlspecialchars($corso['modalita']), $paginaHTML);
$paginaHTML = str_replace('{durata}', htmlspecialchars($corso['durata']), $paginaHTML);
$paginaHTML = str_replace('{desc_completa}', nl2br(htmlspecialchars($corso['desc_completa'])), $paginaHTML);

$azioneHtml = '';
if (empty($_SESSION['is_admin'])) {
    if (!isset($_SESSION['user'])) {
        $azioneHtml = '<a href="../accedi.php" class="default-form-login-link">Accedi per acquistare</a>';
    } elseif ($haAcquistato) {
        $paginaHTML = str_replace('{azione-titolo-corso}', 'Disiscriviti', $paginaHTML);
        $azioneHtml = '<form action="../dettagliocorso.php?id=' . urlencode($id) . '" method="POST"><input type="hidden" name="action" value="elimina"><button type="submit" class="default-form-confirm-button">Disiscriviti</button></form>';
    } else {
        $paginaHTML = str_replace('{azione-titolo-corso}', 'Prenota ora', $paginaHTML);
        $azioneHtml = '<form action="../dettagliocorso.php?id=' . urlencode($id) . '" method="POST"><input type="hidden" name="action" value="acquista"><button type="submit" class="default-form-confirm-button">Compra gratis</button></form>';
    }
} else {
    $paginaHTML = str_replace('{azione-titolo-corso}', 'Prenota ora', $paginaHTML);
}

if (!empty($azioneHtml)) {
    replaceContent("azione-corso", $azioneHtml, $paginaHTML);
} else {
    replaceContent("azione-corso", '', $paginaHTML);
}

$recensioniHtml = '';
if (!empty($recensioni)) {
    foreach ($recensioni as $recensione) {
        $recensioniHtml .= '<article class="corso-recensione"><h3>' . htmlspecialchars($recensione['id_user']) . ' - ' . htmlspecialchars($recensione['rating']) . '/5</h3><p>' . htmlspecialchars($recensione['descrizione']) . '</p></article>';
    }
} else {
    $recensioniHtml = '<p>Nessuna recensione disponibile.</p>';
}
replaceContent("recensioni", $recensioniHtml, $paginaHTML);

$formRecensione = '';
if (isset($_SESSION['user']) && empty($_SESSION['is_admin']) && $haAcquistato && !$haRecensito) {
    $formRecensione = '
        <section class="modifica-recensione-section">
            <form action="../dettagliocorso.php?id=' . urlencode($id) . '" method="POST" id="modifica-recensione-form" class="default-form">       
            <input type="hidden" name="action" value="recensisci">
            <h1>Aggiungi una recensione:</h1>
                <fieldset class="default-form-fieldset"> 
                    <legend class="recensione-legend">Recensione</legend>    

                        <div class="default-form-group">
                            <label class="default-form-label" for="rating">Voto (1-5)</label>
                            <input class="default-form-field" type="number" id="rating" name="rating" min="1" max="5" step="0.5" required>
                        </div>

                        <div class="default-form-group">
                            <label class="default-form-label" for="descrizione">Recensione</label>
                            <textarea class="default-form-field" id="descrizione" name="descrizione" rows="4" required></textarea>
                        </div>
                </fieldset>      

                <button type="submit" class="default-form-confirm-button">Invia recensione</button>

                <div class="default-form-error">
                <!--{start-errore}-->
                
                <!--{end-errore}-->
                </div>
            </form>
        </section>';
} elseif (!isset($_SESSION['user'])) {
    $formRecensione = '<a href="../accedi.php" class="default-form-login-link">Accedi per pubblicare una recensione.</a>';
}
replaceContent("form-recensione", $formRecensione, $paginaHTML);

echo $paginaHTML;

?>

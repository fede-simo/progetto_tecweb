<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /src/php/accedi.php");
    exit();
}

$paginaHTML = file_get_contents('../html/areapersonale.html');
$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES);
$paginaHTML = str_replace('{username}', $username, $paginaHTML);

require_once "dbConnection.php";
require_once "helpers.php";

$adminLink = '';
if (!empty($_SESSION['is_admin'])) {
    $adminLink = '<a href="/src/php/admin.php" class="confirm-registration">Pannello admin</a>';
}
replaceContent("admin-link", $adminLink, $paginaHTML);

$corsiHtml = '';
try {
    $connessione = new DB\DBAccess();
    $conn = $connessione->openConnection();
    if ($conn) {
        $corsi = $connessione->getCorsiByUser($_SESSION['user']);
        $connessione->closeConnection();
    } else {
        $corsi = [];
    }
} catch (Throwable $e) {
    $corsi = [];
}

if (!empty($corsi)) {
    foreach ($corsi as $corso) {
        $corsiHtml .=
        '<div class="corsi">
            <img src="' . htmlspecialchars($corso['immagine']) . '" class="img-corso" alt="">
            <dt class="titolo-corso"><a href="/src/php/dettagliocorso.php?id=' . urlencode($corso['id']) . '" class="corso-link"><strong>' . htmlspecialchars($corso['titolo']) . '</strong></a></dt>
            <dd>
                <ul class="lista-info-corso">
                    <li class="categoria-corso">' . htmlspecialchars($corso['categoria']) . '</li>
                    <li class="durata-corso">' . htmlspecialchars($corso['durata']) . ' ore</li>
                    <li class="prezzo-corso">€ ' . htmlspecialchars($corso['costo']) . '</li>
                    <li class="locazione-corso" lang="en">' . htmlspecialchars($corso['modalita']) . '</li>
                </ul>
            </dd>
            <h6 class="descrizione-corso">' . htmlspecialchars($corso['breve_desc']) . '</h6>
        </div>';
    }
} else {
    $corsiHtml = '<p>Non hai ancora corsi acquistati.</p>';
}

replaceContent("miei-corsi", $corsiHtml, $paginaHTML);

echo $paginaHTML;

?>

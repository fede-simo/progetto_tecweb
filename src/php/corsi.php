<?php

require_once "dbConnection.php";

$paginaHTML = file_get_contents('../html/corsi.html');
$corsi = "";
$corsiUpdated = "";

//echo $paginaHTML;



try {
    $connessione = new DB\DBAccess();

    $conn = $connessione->openConnection();

    isset($_GET['categoria']) ? $categoria = $_GET['categoria'] : $categoria = 'all';

    $corsi = $connessione->getCorsi($categoria);

    $connessione->closeConnection();

} catch (Exception $e) {
    $paginaHTML .= "<p>Si è verificato un errore. Riprova più tardi.</p>";
    echo $paginaHTML;
    exit();
}

//variabile per fare in modo che il form mostri l'impostazione giusta

$formCategoria = 'value="'.$categoria.'"';

// aggiornamento corsi per categoria

/*
SESSION START

session_start();

$isLogged = isset($_SESSION['user_id']);

*/

if (!empty($corsi)) {

    foreach ($corsi as $corso) {
        $corsiUpdated .= 
        '<div class="corsi">
            <img src="' . htmlspecialchars($corso['immagine']) . '" class="img-corso">
            <dt class="titolo-corso"><a href="./corso1.html" class="corso-link"><strong>' . htmlspecialchars($corso['titolo']) . '</strong></a></dt>
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
    $corsiUpdated = '<p>Al momento non sono presenti corsi per la categoria selezionata. Riprova più tardi.</p>';
}

$formCategoria = '
    <option value="all"' . ($categoria === 'all' ? ' selected' : '') . '>Tutti</option>
    <option value="finanza"' . ($categoria === 'finanza' ? ' selected' : '') . '>Finanza</option>
    <option value="cripto"' . ($categoria === 'cripto' ? ' selected' : '') . '>Cripto</option>
    <option value="altro"' . ($categoria === 'altro' ? ' selected' : '') . '>Altro</option>';

$connessione->replaceContent("select-categoria", $formCategoria, $paginaHTML);


$connessione->replaceContent("section-corsi", $corsiUpdated, $paginaHTML);

echo $paginaHTML;

?>

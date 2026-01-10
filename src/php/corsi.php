<?php

require_once "helpers.php";
require_once "dbConnection.php";

$paginaHTML = file_get_contents('../html/corsi.html');
$corsi = "";
$corsiUpdated = "";


try {
    $connessione = new DB\DBAccess();

    $conn = $connessione->openConnection();

    $rawCategoria = isset($_GET['categoria']) ? trim(urldecode($_GET['categoria'])) : 'all';
    $categoryMap = [
        'investimenti' => 'Investimenti',
        'risparmio' => 'Risparmio',
        'previdenza' => 'Previdenza',
        'all' => 'all'
    ];
    $rawKey = strtolower($rawCategoria);
    $categoria = $categoryMap[$rawKey] ?? 'all';

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


if (!empty($corsi)) {

    foreach ($corsi as $corso) {
        $modalita = allyModCorso(htmlspecialchars($corso['modalita']));
        $corsiUpdated .= 
        '<div class="corsi">
            <img src="' . htmlspecialchars($corso['immagine']) . '" class="img-corso">
            <dt class="titolo-corso"><a href="./dettagliocorso.php?id=' . urlencode($corso['id']) . '" class="corso-link"><strong>' . htmlspecialchars($corso['titolo']) . '</strong></a></dt>
                <dd>
                <ul class="lista-info-corso">
                    <li class="categoria-corso">' . htmlspecialchars($corso['categoria']) . '</li>
                    <li class="durata-corso">' . htmlspecialchars($corso['durata']) . ' ore</li>
                    <li class="prezzo-corso">€ ' . htmlspecialchars($corso['costo']) . '</li>
                    <li class="locazione-corso">' . $modalita . '</li>
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
    <option value="Investimenti"' . ($categoria === 'Investimenti' ? ' selected' : '') . '>Investimenti</option>
    <option value="Risparmio"' . ($categoria === 'Risparmio' ? ' selected' : '') . '>Risparmio</option>
    <option value="Previdenza"' . ($categoria === 'Previdenza' ? ' selected' : '') . '>Previdenza</option>';

replaceContent("select-categoria", $formCategoria, $paginaHTML);

replaceContent("section-corsi", $corsiUpdated, $paginaHTML);

echo $paginaHTML;

?>

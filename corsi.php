<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

$paginaHTML = file_get_contents('./html/corsi.html');
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
    /*
    $paginaHTML .= "<p class=\"errore\">Si è verificato un errore. Riprova più tardi.</p>";
    echo $paginaHTML;*/
    header("location: ./html/500.html");
    exit();
}

//variabile per fare in modo che il form mostri l'impostazione giusta

$formCategoria = 'value="'.$categoria.'"';

// aggiornamento corsi per categoria


if (!empty($corsi)) {

    foreach ($corsi as $corso) {
        $corsiUpdated .= 
        '<div class="default-container">
            <img src="' . htmlspecialchars($corso['immagine']) . '" class="img-container" alt="">
            <h3 class="titolo-container"><a href="./dettagliocorso.php?id=' . urlencode($corso['id']) . '" class="container-link">' . allyModTesto(htmlspecialchars($corso['titolo'])) . '</a></h3>
            <p class="descrizione-container">' . allyModTesto(htmlspecialchars($corso['breve_desc'])) . '<p>
            <ul class="lista-info-corso">
                <li>' . htmlspecialchars($corso['categoria']) . '</li>
                <li>' . htmlspecialchars($corso['durata']) . ' ore</li>
                <li>€ ' . htmlspecialchars($corso['costo']) . '</li>
                <li>' . allyModCorso(htmlspecialchars($corso['modalita'])) . '</li>
            </ul>
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

<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

$paginaHTML = file_get_contents('./html/corsi.html');
$corsi = "";
$corsiUpdated = "";


try {
    $connessione = new DB\DBAccess();

    $conn = $connessione->openConnection();
    if (!$conn) {
        throw new RuntimeException('DB connection failed');
    }
    
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

} catch (Throwable $e) {
    error_log($e->__toString());
    throw $e;
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
                <li><span class="visually-hidden">Categoria: </span>' . htmlspecialchars($corso['categoria']) . '</li>
                <li><span class="visually-hidden">Durata: </span>' . htmlspecialchars($corso['durata']) . ' ore</li>
                <li><span class="visually-hidden">Costo: </span>€ ' . htmlspecialchars($corso['costo']) . '</li>
                <li><span class="visually-hidden">Modalità: </span>' . allyModCorso(htmlspecialchars($corso['modalita'])) . '</li>
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

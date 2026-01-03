<?php


$paginaHTML = file_get_contents('../html/corsi.html');
$corsi = "";

require_once "dbConnection.php";

$connessione = new DBAccess();
$conn = $connessione->openConnection();

$categoria = $_GET['categoria'] ?? 'all';

// DEBUG (temporaneo)
$paginaHTML .= "<!-- categoria: $categoria -->";

echo $paginaHTML;

?>

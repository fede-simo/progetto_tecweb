<?php

require_once "./php/dbConnection.php";
require_once "./php/helpers.php";

session_start();

if (!isset($_SESSION['user']) || empty($_SESSION['is_admin'])) {
    header("Location: accedi.php");
    exit();
}

$paginaHTML = file_get_contents('./html/admin.html');
$username = htmlspecialchars($_SESSION['user'], ENT_QUOTES);
$paginaHTML = str_replace('{username}', $username, $paginaHTML);
$messaggio = "";
$confirmHtml = "";

try {
    $connessione = new DB\DBAccess();
    $conn = $connessione->openConnection();

    if (!$conn) {
        throw new Exception('Connessione al database non riuscita.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        if ($action === 'preview-admin') {
            $selected = isset($_POST['admin_users']) ? (array) $_POST['admin_users'] : [];
            $selected = array_map('strval', $selected);
            $utentiCorrenti = $connessione->getUtenti();
            $changes = [];
            foreach ($utentiCorrenti as $utente) {
                $isAdminNow = (bool) $utente['isAdmin'];
                $shouldBeAdmin = in_array($utente['username'], $selected, true);
                if ($isAdminNow !== $shouldBeAdmin) {
                    $changes[] = [
                        'username' => $utente['username'],
                        'new_admin' => $shouldBeAdmin
                    ];
                }
            }
            if (empty($changes)) {
                $messaggio = '<p>Nessun cambiamento da confermare.</p>';
            } else {
                $confirmHtml = '<div class="default-form"><h2>Conferma cambiamenti</h2><ul>';
                foreach ($changes as $change) {
                    $confirmHtml .= '<li>' . htmlspecialchars($change['username']) . ' => ' . ($change['new_admin'] ? 'admin' : 'utente') . '</li>';
                }
                $confirmHtml .= '</ul><form action="./admin.php" method="POST">';
                $confirmHtml .= '<input type="hidden" name="action" value="apply-admin">';
                foreach ($changes as $change) {
                    $confirmHtml .= '<input type="hidden" name="change_user[]" value="' . htmlspecialchars($change['username']) . '">';
                    $confirmHtml .= '<input type="hidden" name="change_admin[]" value="' . ($change['new_admin'] ? '1' : '0') . '">';
                }
                $confirmHtml .= '<button type="submit" class="default-form-confirm-button">Conferma</button></form></div>';
            }
        } elseif ($action === 'apply-admin') {
            $users = isset($_POST['change_user']) ? (array) $_POST['change_user'] : [];
            $admins = isset($_POST['change_admin']) ? (array) $_POST['change_admin'] : [];
            $okAll = true;
            foreach ($users as $i => $user) {
                $isAdmin = isset($admins[$i]) && $admins[$i] === '1';
                $ok = $connessione->setAdmin($user, $isAdmin);
                if (!$ok) {
                    $okAll = false;
                }
            }
            $messaggio = $okAll ? '<p>Ruoli aggiornati.</p>' : '<p class="errore">Errore durante l\'aggiornamento dei ruoli.</p>';
        } elseif ($action === 'add-corso') {
            $titolo = isset($_POST['titolo']) ? trim($_POST['titolo']) : '';
            $categorie = isset($_POST['categorie']) ? (array) $_POST['categorie'] : [];
            $durata = isset($_POST['durata']) ? (int) $_POST['durata'] : 0;
            $costo = isset($_POST['costo']) ? (float) $_POST['costo'] : 0.0;
            $modalita = isset($_POST['modalita']) ? trim($_POST['modalita']) : '';
            $breveDesc = isset($_POST['breve_desc']) ? trim($_POST['breve_desc']) : '';
            $descCompleta = isset($_POST['desc_completa']) ? trim($_POST['desc_completa']) : '';

            $immaginePath = '';
            if (isset($_FILES['immagine']) && $_FILES['immagine']['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES['immagine']['tmp_name'];
                $originalName = basename($_FILES['immagine']['name']);
                $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png'];
                if (in_array($extension, $allowed, true)) {
                    $uploadDir = __DIR__ . '/img';
                    if (!is_dir($uploadDir)) {
                        $messaggio = '<p class="errore">Cartella immagini non trovata.</p>';
                    }
                    $fileName = 'corso_' . uniqid('', true) . '.' . $extension;
                    $destPath = $uploadDir . '/' . $fileName;
                    if (empty($messaggio) && move_uploaded_file($tmpName, $destPath)) {
                        $immaginePath = '/img/' . $fileName;
                    }
                }
            }

            if ($immaginePath === '') {
                $messaggio = '<p class="errore">Carica un\'immagine valida (jpg o png).</p>';
            } elseif ($titolo === '' || empty($categorie) || $durata <= 0 || $modalita === '' || $breveDesc === '' || $descCompleta === '') {
                $messaggio = '<p class="errore">Compila tutti i campi obbligatori.</p>';
            } elseif (fmod($costo, 5) !== 0.0) {
                $messaggio = '<p class="errore">Il costo deve essere un multiplo di 5.</p>';
            } else {
                $ok = $connessione->addCorso($titolo, $immaginePath, $categorie, $durata, $costo, $modalita, $breveDesc, $descCompleta);
                $messaggio = $ok ? '<p>Corso aggiunto.</p>' : '<p class="errore">Errore durante l\'inserimento del corso.</p>';
            }
        }
    }

    $utenti = $connessione->getUtenti();
    $categorie = $connessione->getCategorie();
    $contatti = $connessione->getContatti();
    $acquisti = $connessione->getAcquisti();
    $connessione->closeConnection();
} catch (Throwable $e) {
    $messaggio = '<p class="errore">Errore interno.</p>';
    $utenti = [];
    $categorie = [];
    $contatti = [];
    $acquisti = [];
}

$lista = '<form action="./admin.php" method="POST" id="utenti-form">';
$lista .= '<input type="hidden" name="action" value="preview-admin">';
$lista .= '<p id="sum-users" class="visually-hidden">La tabella, ordinata per colonne, mostra tutti gli utenti registrati, con l\'indicazione se sono amministratori o meno, e permette di modificare i loro ruoli.</p>';
$lista .= '<table class="tabella-default" aria-describedby="sum-users">
            <caption class="table-caption">Utenti</caption>
                <thead>
                    <tr>
                    <th scope="col" lang="en">Admin</th>
                    <th scope="col" lang="en">Username</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Cognome</th>
                    <th scope="col">Data</th>
                    </tr>
                </thead>
                <tbody>';
foreach ($utenti as $utente) {
    $isAdmin = !empty($utente['isAdmin']);
    $mark = $isAdmin ? 'V' : 'X';
    $lista .= '<tr data-utente="' . htmlspecialchars($utente['username']) . '" data-data="' . htmlspecialchars($utente['data_di_nascita']) . '">';
    $lista .= '<td><input type="checkbox" name="admin_users[]" value="' . htmlspecialchars($utente['username']) . '"' . ($isAdmin ? ' checked' : '') . '> ' . $mark . '</td>';
    $lista .= '<th scope="row">' . htmlspecialchars($utente['username']) . '</th>';
    $lista .= '<td>' . htmlspecialchars($utente['nome']) . '</td>';
    $lista .= '<td>' . htmlspecialchars($utente['cognome']) . '</td>';
    $lista .= '<td>' . htmlspecialchars($utente['data_di_nascita']) . '</td>';
    $lista .= '</tr>';
}
$lista .= '</tbody></table>';
$lista .= '<button type="submit" class="default-form-confirm-button">Anteprima cambiamenti</button>';
$lista .= '</form>';

replaceContent("utenti-list", $lista, $paginaHTML);

if ($messaggio !== "") {
    replaceContent("admin-message", $messaggio, $paginaHTML);
}

replaceContent("admin-confirm", $confirmHtml, $paginaHTML);

$categorieHtml = '';
if (!empty($categorie)) {
    $categorieHtml .= '<select id="categorie" name="categorie[]" required>';
    $categorieHtml .= '<option value="" disabled selected>Seleziona categoria</option>';
    foreach ($categorie as $cat) {
        $categorieHtml .= '<option value="' . htmlspecialchars($cat['id']) . '">' . htmlspecialchars($cat['nome']) . '</option>';
        //$categorieHtml .= '<tr><td><label><input type="radio" name="categorie[]" value="' . htmlspecialchars($cat['nome']) . '"> ' . htmlspecialchars($cat['nome']) . '</label></td></tr>';
    }
    $categorieHtml .= '</select>';
}
replaceContent("categorie-list", $categorieHtml, $paginaHTML);

$contattiHtml = '';
if (!empty($contatti)) {
    $contattiHtml .= '<ul>';
    foreach ($contatti as $contatto) {
        $contattiHtml .= '<li><strong>' . htmlspecialchars($contatto['oggetto']) . '</strong> - ' . htmlspecialchars($contatto['nome']) . ' (' . htmlspecialchars($contatto['email']) . ')<br>' . htmlspecialchars($contatto['messaggio']) . '<br><small>' . htmlspecialchars($contatto['created_at']) . '</small></li>';
    }
    $contattiHtml .= '</ul>';
} else {
    $contattiHtml = '<p>Nessun messaggio.</p>';
}
replaceContent("contatti-list", $contattiHtml, $paginaHTML);

$messaggiCount = '';
if (!empty($contatti)) {
    $messaggiCount = ' (' . count($contatti) . ')';
}
replaceContent("messaggi-count", $messaggiCount, $paginaHTML);

$acquistiHtml = '';
if (!empty($acquisti)) {
    $acquistiHtml .= '<p id="sum" class="visually-hidden">La tabella, ordinata per colonne, mostra tutti gli acquisti effettuati dagli utenti, con le rispettive date.</p>
                        <table class="tabella-default" aria-describedby="sum">
                            <caption class="table-caption">Acquisti</caption>
                            <thead>
                                <tr>
                                    <th scope="col">Utente</th>
                                    <th scope="col">Corso</th>
                                    <th scope="col">Data</th>
                                </tr>
                            </thead>
                            <tbody>';
    foreach ($acquisti as $acquisto) {
        $acquistiHtml .= '<tr data-acquisto="' . htmlspecialchars($acquisto['id_user']) . '">';
        $acquistiHtml .= '<th scope="row">' . htmlspecialchars($acquisto['id_user']) . '</th>';
        $acquistiHtml .= '<td>' . htmlspecialchars($acquisto['titolo']) . '</td>';
        $acquistiHtml .= '<td>' . htmlspecialchars($acquisto['data']) . '</td>';
        $acquistiHtml .= '</tr>';
    }
    $acquistiHtml .= '</tbody></table>';
} else {
    $acquistiHtml = '<p>Nessun acquisto.</p>';
}
replaceContent("acquisti-list", $acquistiHtml, $paginaHTML);

echo $paginaHTML;

?>

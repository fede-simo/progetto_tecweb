<?php 

namespace DB;

class DBAccess {

	private const HOST_DB = "127.0.0.1";
	private const DATABASE_NAME = "fsimonet"; // chiamato così per facilitare il lavoro sul server dell'uni, se dovete testare in locale cambiatelo con il vostro nome utente
	private const USERNAME = "fsimonet";
	private const PASSWORD = "oXa1ohjooxeehiTh";

	private $connection;

	public function openConnection() {

		//mysqli_report(MYSQLI_REPORT_ERROR); //solo in fase di debug poi da togliere per vedere gli errori nel browser

		$this->connection = \mysqli_connect(self::HOST_DB, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);

        if (\mysqli_connect_errno()) { //se restituisce una stringa c'e errore (nella creazione della connessione)
			return false;
		} else {
			return true;
		}
	}

	public function closeConnection(): void {
		\mysqli_close($this->connection);
	}


	public function getCorsi($categoria): array {

		$query = "SELECT DISTINCT c.* FROM Corso c";	
		

		if ($categoria !== 'all') {
			$query .= " INNER JOIN CorsoCategoria cc ON cc.id_corso = c.id INNER JOIN Categoria cat ON cat.id = cc.id_categoria WHERE cat.nome = ?";
		}

		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}

		if ($categoria !== 'all') {
			$stmt->bind_param("s", $categoria);
		}

		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);	
			}
		} else {
			$stmt->bind_result($id, $titolo, $immagine, $categoriaRes, $durata, $costo, $modalita, $breveDesc, $descCompleta);
			while ($stmt->fetch()) {
				$rows[] = [
					'id' => $id,
					'titolo' => $titolo,
					'immagine' => $immagine,
					'categoria' => $categoriaRes,
					'durata' => $durata,
					'costo' => $costo,
					'modalita' => $modalita,
					'breve_desc' => $breveDesc,
					'desc_completa' => $descCompleta
				];
			}
		}

		$stmt->close();
		return $rows;
	}

	public function getCorsoById($id): ?array {
		$query = "SELECT * FROM Corso WHERE id = ?";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return null;
		}
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$row = null;
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$row = $result->fetch_assoc();
			}
		} else {
			$stmt->bind_result($rid, $titolo, $immagine, $categoria, $durata, $costo, $modalita, $breveDesc, $descCompleta);
			if ($stmt->fetch()) {
				$row = [
					'id' => $rid,
					'titolo' => $titolo,
					'immagine' => $immagine,
					'categoria' => $categoria,
					'durata' => $durata,
					'costo' => $costo,
					'modalita' => $modalita,
					'breve_desc' => $breveDesc,
					'desc_completa' => $descCompleta
				];
			}
		}
		$stmt->close();
		return $row;
	}

	public function getCategorie(): array {
		$query = "SELECT id, nome FROM Categoria ORDER BY nome";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($id, $nome);
			while ($stmt->fetch()) {
				$rows[] = [
					'id' => $id,
					'nome' => $nome
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function addCorso($titolo, $immagine, $categorie, $durata, $costo, $modalita, $breveDesc, $descCompleta): bool {
		if (empty($categorie)) {
			return false;
		}
		$categoriaPrincipale = $categorie[0];
		$this->connection->begin_transaction();
		$query = "INSERT INTO Corso(titolo, immagine, categoria, durata, costo, modalita, breve_desc, desc_completa) VALUES (?,?,?,?,?,?,?,?)";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			$this->connection->rollback();
			return false;
		}
		$stmt->bind_param("sssidsss", $titolo, $immagine, $categoriaPrincipale, $durata, $costo, $modalita, $breveDesc, $descCompleta);
		$ok = $stmt->execute();
		$corsoId = $this->connection->insert_id;
		$stmt->close();
		if (!$ok) {
			$this->connection->rollback();
			return false;
		}

		$queryCat = "SELECT id FROM Categoria WHERE nome = ?";
		$stmtCat = $this->connection->prepare($queryCat);
		if ($stmtCat === false) {
			$this->connection->rollback();
			return false;
		}

		$queryLink = "INSERT INTO CorsoCategoria(id_corso, id_categoria) VALUES (?, ?)";
		$stmtLink = $this->connection->prepare($queryLink);
		if ($stmtLink === false) {
			$stmtCat->close();
			$this->connection->rollback();
			return false;
		}

		foreach ($categorie as $nomeCategoria) {
			$stmtCat->bind_param("s", $nomeCategoria);
			$stmtCat->execute();
			$idCategoria = null;
			if (method_exists($stmtCat, 'get_result')) {
				$resCat = $stmtCat->get_result();
				if ($resCat && $rowCat = $resCat->fetch_assoc()) {
					$idCategoria = (int) $rowCat['id'];
				}
			} else {
				$stmtCat->bind_result($idCategoria);
				$stmtCat->fetch();
			}
			if ($idCategoria) {
				$stmtLink->bind_param("ii", $corsoId, $idCategoria);
				$stmtLink->execute();
			}
		}

		$stmtCat->close();
		$stmtLink->close();
		$this->connection->commit();
		return true;
	}

	public function getCorsiByUser($username): array {
		$query = "SELECT c.* FROM Corso c INNER JOIN Acquisto a ON a.id_corso = c.id WHERE a.id_user = ? ORDER BY c.titolo";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($id, $titolo, $immagine, $categoria, $durata, $costo, $modalita, $breveDesc, $descCompleta);
			while ($stmt->fetch()) {
				$rows[] = [
					'id' => $id,
					'titolo' => $titolo,
					'immagine' => $immagine,
					'categoria' => $categoria,
					'durata' => $durata,
					'costo' => $costo,
					'modalita' => $modalita,
					'breve_desc' => $breveDesc,
					'desc_completa' => $descCompleta
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function hasAcquisto($username, $idCorso): bool {
		$query = "SELECT 1 FROM Acquisto WHERE id_user = ? AND id_corso = ?";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$stmt->bind_param("si", $username, $idCorso);
		$stmt->execute();
		$found = false;
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			$found = $result && $result->num_rows > 0;
		} else {
			$stmt->store_result();
			$found = $stmt->num_rows > 0;
		}
		$stmt->close();
		return $found;
	}

	public function acquistaCorso($username, $idCorso): bool {
		$query = "INSERT INTO Acquisto(id_user, id_corso, data) VALUES (?, ?, CURDATE())";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$stmt->bind_param("si", $username, $idCorso);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}

	public function eliminaAcquisto($username, $idCorso): bool {
		$query = "DELETE FROM Acquisto WHERE id_user = ? AND id_corso = ?";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$stmt->bind_param("si", $username, $idCorso);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}

	public function getRecensioniByCorso($idCorso): array {
		$query = "SELECT r.id_user, r.rating, r.descrizione FROM Recensione r WHERE r.id_corso = ? ORDER BY r.id_user";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		$stmt->bind_param("i", $idCorso);
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($user, $rating, $descrizione);
			while ($stmt->fetch()) {
				$rows[] = [
					'id_user' => $user,
					'rating' => $rating,
					'descrizione' => $descrizione
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function getAcquisti(): array {
		$query = "SELECT a.id_user, a.id_corso, a.data, c.titolo FROM Acquisto a INNER JOIN Corso c ON c.id = a.id_corso ORDER BY a.data DESC";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($user, $idCorso, $data, $titolo);
			while ($stmt->fetch()) {
				$rows[] = [
					'id_user' => $user,
					'id_corso' => $idCorso,
					'data' => $data,
					'titolo' => $titolo
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function addRecensione($username, $idCorso, $rating, $descrizione): bool {
		$query = "INSERT INTO Recensione(id_user, id_corso, rating, descrizione) VALUES (?,?,?,?)
			ON DUPLICATE KEY UPDATE rating = VALUES(rating), descrizione = VALUES(descrizione)";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$stmt->bind_param("sids", $username, $idCorso, $rating, $descrizione);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}

	public function addContatto($nome, $email, $oggetto, $messaggio): bool {
		$query = "INSERT INTO Contatto(nome, email, oggetto, messaggio) VALUES (?,?,?,?)";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$stmt->bind_param("ssss", $nome, $email, $oggetto, $messaggio);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}

	public function getContatti(): array {
		$query = "SELECT id, nome, email, oggetto, messaggio, created_at FROM Contatto ORDER BY created_at DESC";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($id, $nome, $email, $oggetto, $messaggio, $createdAt);
			while ($stmt->fetch()) {
				$rows[] = [
					'id' => $id,
					'nome' => $nome,
					'email' => $email,
					'oggetto' => $oggetto,
					'messaggio' => $messaggio,
					'created_at' => $createdAt
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function getUtenteByUsername($username): ?array {
		$query = "SELECT * FROM Utente WHERE username = ?";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return null;
		}
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$row = null;
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$row = $result->fetch_assoc();
			}
		} else {
			$stmt->bind_result($u, $n, $c, $p, $isAdmin, $data);
			if ($stmt->fetch()) {
				$row = [
					'username' => $u,
					'nome' => $n,
					'cognome' => $c,
					'password' => $p,
					'isAdmin' => $isAdmin,
					'data_di_nascita' => $data
				];
			}
		}
		$stmt->close();
		return $row;
	}

	public function countUtenti(): int {
		$query = "SELECT COUNT(*) AS totale FROM Utente";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return 0;
		}
		$stmt->execute();
		$totale = 0;
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $row = $result->fetch_assoc()) {
				$totale = (int) $row['totale'];
			}
		} else {
			$stmt->bind_result($totale);
			$stmt->fetch();
		}
		$stmt->close();
		return $totale;
	}

	public function getUtenti($search = "", $order = "nome"): array {
		$allowedOrder = ["nome", "data"];
		$orderBy = in_array($order, $allowedOrder, true) ? $order : "nome";
		$orderSql = $orderBy === "data" ? "data_di_nascita" : "username";
		$query = "SELECT username, nome, cognome, isAdmin, data_di_nascita FROM Utente";
		if ($search !== "") {
			$query .= " WHERE username LIKE ? OR nome LIKE ? OR cognome LIKE ?";
		}
		$query .= " ORDER BY " . $orderSql;
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return [];
		}
		if ($search !== "") {
			$like = "%" . $search . "%";
			$stmt->bind_param("sss", $like, $like, $like);
		}
		$stmt->execute();
		$rows = [];
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			if ($result && $result->num_rows > 0) {
				$rows = $result->fetch_all(MYSQLI_ASSOC);
			}
		} else {
			$stmt->bind_result($username, $nome, $cognome, $isAdmin, $dataNascita);
			while ($stmt->fetch()) {
				$rows[] = [
					'username' => $username,
					'nome' => $nome,
					'cognome' => $cognome,
					'isAdmin' => $isAdmin,
					'data_di_nascita' => $dataNascita
				];
			}
		}
		$stmt->close();
		return $rows;
	}

	public function setAdmin($username, $isAdmin): bool {
		$query = "UPDATE Utente SET isAdmin = ? WHERE username = ?";
		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			return false;
		}
		$isAdminInt = $isAdmin ? 1 : 0;
		$stmt->bind_param("is", $isAdminInt, $username);
		$ok = $stmt->execute();
		$stmt->close();
		return $ok;
	}

	public function registraUtente($username, $nome, $cognome, $passwordHash, $isAdmin, $data_di_nascita, &$err): bool {

		$query = "SELECT * FROM Utente WHERE username = ?";

		$stmt = $this->connection->prepare($query);
		if ($stmt === false) {
			$err .= '<p class="errore-registrazione">Errore nella preparazione della query.</p>';
			return false;
		}
		$stmt->bind_param("s", $username);
		$stmt->execute();
		if (method_exists($stmt, 'get_result')) {
			$result = $stmt->get_result();
			$hasUser = $result && $result->num_rows > 0;
		} else {
			$stmt->store_result();
			$hasUser = $stmt->num_rows > 0;
		}
		if($hasUser) {
			$err .= '<p class="errore-registrazione">Username già in uso.</p>';
			$stmt->close();
			return false;	
		}
		$stmt->close();
		
		$queryInsert = "INSERT INTO Utente(username, nome, cognome, password, isAdmin, data_di_nascita) VALUES (?,?,?,?,?,?)"; 

		$stmt = $this->connection->prepare($queryInsert);
		if ($stmt === false) {
			$err .= '<p class="errore-registrazione">Errore nella preparazione dell\'inserimento.</p>';
			return false;
		}
		$isAdminInt = $isAdmin ? 1 : 0;
		$stmt->bind_param("ssssis", $username, $nome, $cognome, $passwordHash,  $isAdminInt, $data_di_nascita);
		
		if (!$stmt->execute()) {
			$err .= '<p class="errore-registrazione">Errore nell\'inserimento.</p>';
			$stmt->close();
			return false;
		}

		$stmt->close();
		return true;		
	}	
}

?>

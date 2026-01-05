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

		$this->connection = mysqli_connect(self::HOST_DB, self::USERNAME, self::PASSWORD, self::DATABASE_NAME);

        if (mysqli_connect_errno()) { //se restituisce una stringa c'e errore (nella creazione della connessione)
			return false;
		} else {
			return true;
		}
	}

	public function closeConnection(): void {
		mysqli_close($this->connection);
	}


	public function replaceContent($bookmark, $newContent, &$paginaHTML): void{
		$start = "<!--{start-".$bookmark."}-->";
		$end = "<!--{end-".$bookmark."}-->";

		$startPos = strpos($paginaHTML, $start);
		$endPos = strpos($paginaHTML, $end);

		if ($startPos === false || $endPos === false) {
			return;
		}

		$contentStart = $startPos + strlen($start);
		$contentLength = $endPos - $contentStart;

		$paginaHTML = substr_replace(
			$paginaHTML,
			$newContent,
			$contentStart,
			$contentLength
		);

	}

	public function getCorsi($categoria): array {

		$query = "SELECT * FROM Corso";	
		

		if ($categoria !== 'all') {
			$query .= " WHERE categoria = ?";
		}

		$stmt = $this->connection->prepare($query);

		if ($categoria !== 'all') {
			$stmt->bind_param("s", $categoria);
		}

		$stmt->execute();
		$result = $stmt->get_result();


		if($result && $result->num_rows > 0) {
			$rows = $result->fetch_all(MYSQLI_ASSOC);	
		}

		$stmt->close();
		return $rows;

	}

	public function registraUtente($nome, $cognome, $username, $data_di_nascita, $passwordHash, &$err): bool {

		$query = "SELECT * FROM Utente WHERE username = ?";

		$stmt = $this->connection->prepare($query);
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result && $result->num_rows > 0) {
			$err .= "<p>Username già in uso.</p>";
			$stmt->close();
			return false;	
		}
		$stmt->close();
		
		$queryInsert = "INSERT INTO Utente(nome, cognome, username, dataDiNascita, password) VALUES (?,?,?,?,?)"; 

		$stmt = $this->connection->prepare($queryInsert);
		$stmt->bind_param("sssss", $nome, $cognome, $username, $data_di_nascita, $passwordHash);
		$stmt->execute();
		$result = $stmt->get_result();

		$stmt->close();

		return $result ? true : false;
		
		
	}

	
}

?>
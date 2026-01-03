<?php 

namespace DB;

class DBAccess {

	private const HOST_DB = "localhost";
	private const DATABASE_NAME = "fsimonet";
	private const USERNAME = "fsimonet";
	private const PASSWORD = "";

	private $connection;

	public function openConnection() {

		//mysqli_report(MYSQLI_REPORT_ERROR); //solo in fase di debug poi da togliere per vedere gli errori nel browser

		$this->connection = mysqli_connect("127.0.0.1", DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);

        if (mysqli_connect_errno()) { //se restituisce una stringa c'e errore (nella creazione della connessione)
			return false;
		} else {
			return true;
		}
	}

	public function closeConnection() {
		mysqli_close($this->connection);
	}

    public function prova() {
		$result = mysqli_query($this->connection, "SELECT * FROM Utente");

        while($row = mysqli_fetch_assoc($result)) {
            echo $row['nome'] . "<br>";
        }
	}

	public function getCorsi($categoria) {

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

	public function registraUtente($nome, $cognome, $email, $username, $passwordHash) {
		
		$queryInsert = "INSERT INTO Utente(nome, cognome, email, username, passwordHash) VALUES (\"$nome\", \"$cognome\", \"$email\", \"$username\", \"$passwordHash\")"; 
		$queryResult = mysqli_query($this->connection, $queryInsert) or die("Error in dbConnection: ". mysqli_error($this->connection));
		
		if(mysqli_affected_rows($this->connection)>0) {
			return true;
		} else {
			return false;			
		}
	}

	
}

?>
<?php
class DatabaseHandler {
	private $host = "localhost";
	private $username = "root";
	private $password = "";
	private $database = "JJUniversityDB";
	public $con;

	public function __construct() {
		$this->con = new mysqli($this->host, $this->username, $this->password, $this->database);
		if ($this->con->connect_error) {
			die("Connection failed: " . $this->con->connect_error);}
		}

	public function executeSelectQuery($sql) {
		$result = $this->con->query($sql);
		if ($result === false) {
			echo "Error: " . $this->con->error;
			return false;}
		$data = [];
		while ($row = $result->fetch_assoc()) {
			$data[] = $row;}
		return $data;}

	public function executeQuery($sql) {
		if ($this->con->query($sql) === TRUE) {
			return true;}
		else {
			echo "Error: " . $this->con->error;
			return false;}
    		}

	public function __destruct() {
		if ($this->con) {
			$this->con->close();}
		}
	}
?>

<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "irrigation";

    private function __construct() {
        // Create connection
        $this->conn = new mysqli($this->host, $this->username, $this->password);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        // Initialize Database
        $this->initializeDatabase();
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    private function initializeDatabase() {
        // Create Database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS " . $this->dbname;
        if ($this->conn->query($sql) === TRUE) {
            $this->conn->select_db($this->dbname);
            $this->createTablesAndSeed();
        } else {
            die("Error creating database: " . $this->conn->error);
        }
    }

    private function createTablesAndSeed() {
        $queriesFile = __DIR__ . '/queries';
        if (!file_exists($queriesFile)) {
            // If queries file doesn't exist, we can't do anything
            return;
        }

        $sqlContent = file_get_contents($queriesFile);
        
        // Remove comments
        $sqlContent = preg_replace('/--.*$/m', '', $sqlContent);
        
        // Split by semicolon
        $queries = explode(';', $sqlContent);

        foreach ($queries as $query) {
            $query = trim($query);
            if (empty($query)) continue;

            // Modify CREATE TABLE to CREATE TABLE IF NOT EXISTS
            if (stripos($query, 'CREATE TABLE') === 0) {
                $query = preg_replace('/CREATE TABLE/i', 'CREATE TABLE IF NOT EXISTS', $query, 1);
                $this->conn->query($query);
            }
            // Modify INSERT INTO to INSERT IGNORE INTO for idempotency
            elseif (stripos($query, 'INSERT INTO') === 0) {
                $query = preg_replace('/INSERT INTO/i', 'INSERT IGNORE INTO', $query, 1);
                $this->conn->query($query);
            }
            // Execute other queries (like creating DB if it was in the file, though we handled it)
            else {
                $this->conn->query($query);
            }
        }
    }
    
    // Set charset to UTF-8
    public function setCharset($charset) {
        $this->conn->set_charset($charset);
    }
}
?>

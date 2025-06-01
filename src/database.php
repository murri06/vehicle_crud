<?php
// database.php - Database connection and table creation

class Database {
    private $host = 'db';
    private $db_name = 'vehicle_crud';
    private $username = 'appuser';
    private $password = 'apppassword';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
            die;
        }
        return $this->conn;
    }

    public function createTable() {
        $query = "CREATE TABLE IF NOT EXISTS vehicle_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";


        try {
            $this->conn->exec($query);
            return true;
        } catch(PDOException $exception) {
            echo "Error creating table: " . $exception->getMessage();
            return false;
        }
    }
}

$database = new Database();
$db = $database->getConnection();
$database->createTable();
$stmt = $db->prepare("SELECT id FROM `users` WHERE username = 'admin'");
$stmt->execute();
$admin = $stmt->fetch();

if(!$admin){
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT IGNORE INTO `users` (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', 'admin@example.com', $adminPassword, 'admin']);
}

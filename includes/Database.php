<?php
require_once __DIR__ . '/../config/database.php';

class Database {
    private $conn;
    private static $instance = null;
    private $lastError = null;

    private function __construct() {
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }

            // Set charset to UTF-8
            $this->conn->set_charset("utf8mb4");
            
            // Set strict mode
            $this->conn->query("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
            
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Database connection error: " . $e->getMessage());
        }
    }

    // Singleton pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Get connection
    public function getConnection() {
        return $this->conn;
    }

    // Get last error
    public function getLastError() {
        return $this->lastError;
    }

    // Sanitize input
    public function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return $this->conn->real_escape_string(trim($input));
    }

    // Insert contact message with validation
    public function insertContact($name, $email, $message) {
        try {
            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format");
            }

            // Validate name (only letters and spaces)
            if (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
                throw new Exception("Name can only contain letters and spaces");
            }

            // Validate message length
            if (strlen($message) < 10 || strlen($message) > 1000) {
                throw new Exception("Message must be between 10 and 1000 characters");
            }

            // Sanitize inputs
            $name = $this->sanitize($name);
            $email = $this->sanitize($email);
            $message = $this->sanitize($message);

            // Prepare statement
            $stmt = $this->conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            // Bind parameters
            $stmt->bind_param("sss", $name, $email, $message);

            // Execute
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $stmt->close();
            return true;

        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Contact insert error: " . $e->getMessage());
            return false;
        }
    }

    // Get all services
    public function getServices() {
        try {
            $result = $this->conn->query("SELECT * FROM services ORDER BY id");
            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Get services error: " . $e->getMessage());
            return false;
        }
    }

    // Get recent contacts with pagination
    public function getRecentContacts($page = 1, $perPage = 10) {
        try {
            $offset = ($page - 1) * $perPage;
            $result = $this->conn->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT $offset, $perPage");
            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Get recent contacts error: " . $e->getMessage());
            return false;
        }
    }

    // Get total number of contacts
    public function getTotalContacts() {
        try {
            $result = $this->conn->query("SELECT COUNT(*) as total FROM contacts");
            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result->fetch_assoc()['total'];
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Get total contacts error: " . $e->getMessage());
            return false;
        }
    }

    // Close connection
    public function close() {
        if ($this->conn) {
            $this->conn->close();
            self::$instance = null;
        }
    }

    // Destructor
    public function __destruct() {
        $this->close();
    }
}
?> 
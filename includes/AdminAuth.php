<?php
require_once __DIR__ . '/Database.php';

class AdminAuth {
    private $db;
    private $session_name = 'admin_session';
    private $session_lifetime = 3600; // 1 hour

    public function __construct() {
        $this->db = Database::getInstance();
        session_set_cookie_params($this->session_lifetime, '/', '', true, true);
        session_name($this->session_name);
        session_start();
    }

    public function login($username, $password) {
        try {
            // Check if account is locked
            $stmt = $this->db->getConnection()->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                error_log("User not found: " . $username);
                return false;
            }

            $user = $result->fetch_assoc();

            // Check if account is locked
            if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
                error_log("Account locked for user: " . $username . " until: " . $user['locked_until']);
                throw new Exception("Account is locked. Try again later.");
            }

            // Verify password
            error_log("Verifying password for user: " . $username);
            if (!password_verify($password, $user['password'])) {
                error_log("Password verification failed for user: " . $username);
                $this->incrementLoginAttempts($username);
                return false;
            }

            error_log("Password verification successful for user: " . $username);

            // Reset login attempts on successful login
            $this->resetLoginAttempts($username);

            // Update last login
            $stmt = $this->db->getConnection()->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();

            // Set session
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_last_activity'] = time();

            return true;

        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_destroy();
        return true;
    }

    public function isLoggedIn() {
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_last_activity'])) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['admin_last_activity'] > $this->session_lifetime) {
            $this->logout();
            return false;
        }

        $_SESSION['admin_last_activity'] = time();
        return true;
    }

    private function incrementLoginAttempts($username) {
        $stmt = $this->db->getConnection()->prepare("UPDATE admin_users SET login_attempts = login_attempts + 1 WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Check if account should be locked
        $stmt = $this->db->getConnection()->prepare("SELECT login_attempts FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $locked_until = date('Y-m-d H:i:s', time() + LOGIN_TIMEOUT);
            $stmt = $this->db->getConnection()->prepare("UPDATE admin_users SET locked_until = ? WHERE username = ?");
            $stmt->bind_param("ss", $locked_until, $username);
            $stmt->execute();
            error_log("Account locked for user: " . $username . " until: " . $locked_until);
        }
    }

    private function resetLoginAttempts($username) {
        $stmt = $this->db->getConnection()->prepare("UPDATE admin_users SET login_attempts = 0, locked_until = NULL WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: /admin/login.php");
            exit();
        }
    }
}
?> 
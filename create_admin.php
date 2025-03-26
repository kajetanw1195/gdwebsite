<?php
require_once 'includes/Database.php';

$db = Database::getInstance();
$username = 'admin';
$password = 'Admin123!';
$email = 'admin@golebidwor.pl';

$hashed_password = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

$stmt = $db->getConnection()->prepare("INSERT INTO admin_users (username, password, email) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE password = ?, login_attempts = 0, locked_until = NULL");
$stmt->bind_param("ssss", $username, $hashed_password, $email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin user created/updated successfully!\n";
    echo "Username: " . $username . "\n";
    echo "Password: " . $password . "\n";
} else {
    echo "Error: " . $stmt->error . "\n";
}

$stmt->close();
?> 
<?php
$host = 'localhost';
$db = 'golebi_dwor';
$user = 'golebi_dwor';
$pass = 'golebi_dwor123';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Błąd połączenia: " . $conn->connect_error);
}

$name = 'Test User';
$email = 'test@example.com';
$message = 'To jest testowa wiadomość';

$stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
  echo "Testowy wpis dodany!";
} else {
  echo "Błąd: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

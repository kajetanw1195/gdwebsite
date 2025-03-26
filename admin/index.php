<?php
require_once '../includes/AdminAuth.php';
require_once '../includes/Database.php';

$auth = new AdminAuth();
$auth->requireLogin();

$db = Database::getInstance();
$recentContacts = $db->getRecentContacts(1, 5);
$totalContacts = $db->getTotalContacts();
$services = $db->getServices();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0e9a3f7.js" crossorigin="anonymous"></script>
    <style>
        body {
            font-family: 'Raleway', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #3e4e50;
            color: white;
            padding: 1rem;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 0.5rem;
        }
        .nav-item:hover {
            background-color: #2c3a3c;
        }
        .nav-item i {
            margin-right: 0.5rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .stat-card h3 {
            margin: 0;
            color: #3e4e50;
        }
        .stat-card p {
            margin: 0.5rem 0 0;
            font-size: 2rem;
            color: #2c3a3c;
        }
        .recent-contacts {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .contact-item {
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .contact-item:last-child {
            border-bottom: none;
        }
        .contact-name {
            font-weight: bold;
            color: #3e4e50;
        }
        .contact-email {
            color: #666;
            font-size: 0.9rem;
        }
        .contact-message {
            margin-top: 0.5rem;
            color: #333;
        }
        .contact-date {
            font-size: 0.8rem;
            color: #999;
        }
        .logout-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2>Panel Admin</h2>
            <nav>
                <a href="index.php" class="nav-item">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="contacts.php" class="nav-item">
                    <i class="fas fa-envelope"></i> Wiadomości
                </a>
                <a href="services.php" class="nav-item">
                    <i class="fas fa-concierge-bell"></i> Usługi
                </a>
                <a href="settings.php" class="nav-item">
                    <i class="fas fa-cog"></i> Ustawienia
                </a>
            </nav>
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Dashboard</h1>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Wyloguj
                </a>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Wiadomości</h3>
                    <p><?php echo $totalContacts; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Usługi</h3>
                    <p><?php echo count($services); ?></p>
                </div>
            </div>

            <div class="recent-contacts">
                <h2>Najnowsze wiadomości</h2>
                <?php if ($recentContacts): ?>
                    <?php foreach ($recentContacts as $contact): ?>
                        <div class="contact-item">
                            <div class="contact-name"><?php echo htmlspecialchars($contact['name']); ?></div>
                            <div class="contact-email"><?php echo htmlspecialchars($contact['email']); ?></div>
                            <div class="contact-message"><?php echo htmlspecialchars($contact['message']); ?></div>
                            <div class="contact-date"><?php echo date('d.m.Y H:i', strtotime($contact['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Brak wiadomości.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html> 
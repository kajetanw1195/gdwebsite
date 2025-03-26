<?php
require_once '../includes/AdminAuth.php';

$auth = new AdminAuth();
$auth->logout();

header('Location: login.php');
exit();
?>
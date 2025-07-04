<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: usuarios.php?msg=erro');
    exit;
}

$id = intval($_GET['id']);
$status = intval($_GET['status']) === 1 ? 1 : 0;

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare('UPDATE usuarios SET status = :status WHERE id = :id');
$success = $stmt->execute([':status' => $status, ':id' => $id]);
$db->closeConnection();

if ($success) {
    header('Location: usuarios.php?msg=status');
} else {
    header('Location: usuarios.php?msg=erro');
}
exit;
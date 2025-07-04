<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header('Location: estudantes.php?msg=erro');
    exit;
}

$id = intval($_GET['id']);
$status = intval($_GET['status']) === 1 ? 1 : 0;

$db = new Database();
$conn = $db->getConnection();

// Primeiro, obter o usuario_id do estudante
$stmt = $conn->prepare('SELECT usuario_id FROM estudantes WHERE id = ?');
$stmt->execute([$id]);
$estudante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudante) {
    $db->closeConnection();
    header('Location: estudantes.php?msg=erro');
    exit;
}

// Atualizar o status do usuário
$stmt = $conn->prepare('UPDATE usuarios SET status = :status WHERE id = :id');
$success = $stmt->execute([':status' => $status, ':id' => $estudante['usuario_id']]);
$db->closeConnection();

if ($success) {
    header('Location: estudantes.php?msg=status');
} else {
    header('Location: estudantes.php?msg=erro');
}
exit;
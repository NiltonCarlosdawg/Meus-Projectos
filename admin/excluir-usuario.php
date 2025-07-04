<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: usuarios.php?msg=erro');
    exit;
}

$id = intval($_GET['id']);

$db = new Database();
$conn = $db->getConnection();

// Verificar se o usuário é orientador ou coorientador e se há estudantes vinculados
$stmt = $conn->prepare('SELECT tipo_usuario FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verificar se há estudantes vinculados como usuario_id
$stmtEstudante = $conn->prepare('SELECT COUNT(*) as total FROM estudantes WHERE usuario_id = ?');
$stmtEstudante->execute([$id]);
$estudante = $stmtEstudante->fetch(PDO::FETCH_ASSOC);
if ($estudante && $estudante['total'] > 0) {
    $db->closeConnection();
    header('Location: usuarios.php?msg=erro_vinculo');
    exit;
}

// Verificar vínculos como orientador ou coorientador
if ($user && in_array($user['tipo_usuario'], ['orientador', 'coorientador'])) {
    $campo = $user['tipo_usuario'] === 'orientador' ? 'orientador_id' : 'coorientador_id';
    $stmtVinculo = $conn->prepare("SELECT COUNT(*) as total FROM estudantes WHERE $campo = ?");
    $stmtVinculo->execute([$id]);
    $vinculo = $stmtVinculo->fetch(PDO::FETCH_ASSOC);
    if ($vinculo && $vinculo['total'] > 0) {
        $db->closeConnection();
        header('Location: usuarios.php?msg=erro_vinculo');
        exit;
    }
}

$stmt = $conn->prepare('DELETE FROM usuarios WHERE id = ?');
$success = $stmt->execute([$id]);
$db->closeConnection();

if ($success) {
    header('Location: usuarios.php?msg=excluido');
} else {
    header('Location: usuarios.php?msg=erro');
}
exit;
<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Redirecionar para a página de login se não estiver autenticado
if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

// Redirecionar para o dashboard específico com base no tipo de usuário
$userType = getUserType();
switch ($userType) {
    case 'admin':
        header('Location: ' . BASE_URL . '/dashboard/admin/index.php');
        break;
    case 'orientador':
        header('Location: ' . BASE_URL . '/dashboard/orientador/index.php');
        break;
    case 'aluno':
        header('Location: ' . BASE_URL . '/dashboard/aluno/index.php');
        break;
    case 'professor':
        header('Location: ' . BASE_URL . '/dashboard/professor/index.php');
        break;
    default:
        // Se o tipo de usuário não for reconhecido, fazer logout
        session_destroy();
        header('Location: ' . BASE_URL . '/login.php');
        break;
}
exit();
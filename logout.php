<?php
require_once 'config/config.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login
header('Location: ' . BASE_URL . '/login.php');
exit();
?>
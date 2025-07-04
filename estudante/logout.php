<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Destruir todas as variáveis de sessão
$_SESSION = array();

// Destruir a sessão
session_destroy();

redirect('/');
?>
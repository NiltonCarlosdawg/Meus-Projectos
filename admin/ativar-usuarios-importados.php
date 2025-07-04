<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['importacao_id'])) {
    $importacao_id = intval($_POST['importacao_id']);
    $db = new Database();
    $conn = $db->getConnection();

    // Buscar o nome do arquivo importado a partir do log
    $stmt = $conn->prepare("SELECT descricao FROM user_logs WHERE id = ? AND acao LIKE '%importação%'");
    $stmt->execute([$importacao_id]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($log) {
        $arquivo_nome = $log['descricao'];
        // Ativar todos os usuários importados com base no campo 'arquivo_importado' (ajuste conforme sua estrutura)
        // Não é possível ativar usuários importados em massa pois não há vínculo direto entre usuários e o arquivo de importação.
        $_SESSION['error'] = "Não é possível ativar usuários importados em massa pois não há vínculo direto entre usuários e o arquivo de importação. Consulte o administrador do sistema para ajustar a estrutura do banco de dados.";
    } else {
        $_SESSION['error'] = "Importação não encontrada.";
    }
    $db->closeConnection();
    header('Location: ' . BASE_URL . '/admin/dashboard/admin/');
    exit;
} else {
    $_SESSION['error'] = "Requisição inválida.";
    header('Location: ' . BASE_URL . '/admin/dashboard/admin/');
    exit;
}
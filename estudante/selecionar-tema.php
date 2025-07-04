<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

// Log para debug
error_log('Recebendo requisição POST: ' . print_r($_POST, true));

// Verificar se um tema foi selecionado
if (!isset($_POST['tema_id']) || empty($_POST['tema_id'])) {
    error_log('Tema ID não encontrado ou vazio');
    setFlashMessage('error', 'É necessário selecionar um tema.');
    redirect('/estudante/temas-disponiveis.php');
}

$tema_id = $_POST['tema_id'];
$estudante_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

// Iniciar transação
$conn->beginTransaction();

try {
    // Verificar se o tema ainda está disponível
    $stmt = $conn->prepare("SELECT * FROM temas_tfc WHERE id = ? AND status IN ('disponivel', 'publicado') AND (data_limite_escolha >= CURDATE() OR data_limite_escolha IS NULL)");
    $stmt->execute([$tema_id]);
    $tema = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tema) {
        throw new Exception('O tema selecionado não está mais disponível.');
    }

    // Verificar se o estudante já tem um projeto ativo
    $stmt = $conn->prepare("SELECT * FROM projetos WHERE estudante_id = ? AND status != 'concluido' ORDER BY data_cadastro DESC LIMIT 1");
    $stmt->execute([$estudante_id]);
    $projeto_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$projeto_existente) {
        // Criar um novo projeto
        $stmt = $conn->prepare("INSERT INTO projetos (estudante_id, status, data_cadastro) VALUES (?, 'em_andamento', NOW())");
        $stmt->execute([$estudante_id]);
        $projeto_id = $conn->lastInsertId();
    } else {
        $projeto_id = $projeto_existente['id'];
    }

    // Verificar se já existe uma inscrição para este tema
    $stmt = $conn->prepare("SELECT * FROM inscricoes_tema WHERE estudante_id = ? AND tema_id = ?");
    $stmt->execute([$estudante_id, $tema_id]);
    $inscricao_existente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$inscricao_existente) {
        // Registrar a inscrição no tema
        $stmt = $conn->prepare("INSERT INTO inscricoes_tema (estudante_id, tema_id, data_inscricao, status) VALUES (?, ?, NOW(), 'pendente')");
        $stmt->execute([$estudante_id, $tema_id]);
    }

    // Atualizar o status do tema para 'em_andamento'
    $stmt = $conn->prepare("UPDATE temas_tfc SET status = 'em_andamento' WHERE id = ?");
    $stmt->execute([$tema_id]);

    $conn->commit();
    setFlashMessage('success', 'Tema selecionado com sucesso!');
    redirect('/estudante/index.php');

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    setFlashMessage('error', 'Erro ao selecionar o tema: ' . $e->getMessage());
    redirect('/estudante/temas-disponiveis.php');
} finally {
    $db->closeConnection();
}
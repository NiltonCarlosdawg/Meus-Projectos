<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Método inválido.');
    redirect('/estudante/');
}

$db = new Database();
$conn = $db->getConnection();

// Validar dados do formulário
$titulo = trim($_POST['titulo']);
$descricao = trim($_POST['descricao']);
$tema_id = isset($_POST['tema_id']) ? (int)$_POST['tema_id'] : null;

if (empty($titulo) || empty($descricao)) {
    setFlashMessage('error', 'Por favor, preencha todos os campos obrigatórios.');
    redirect('/estudante/');
}

// Processar upload do arquivo
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    setFlashMessage('error', 'Erro no upload do arquivo.');
    redirect('/estudante/');
}

$arquivo = $_FILES['arquivo'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

if ($extensao !== 'pdf') {
    setFlashMessage('error', 'Apenas arquivos PDF são permitidos.');
    redirect('/estudante/');
}

// Criar diretório para o estudante se não existir
$upload_dir = '../uploads/estudantes/' . $_SESSION['user_id'];
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Gerar nome único para o arquivo
$nome_arquivo = 'proposta_' . time() . '.pdf';
$caminho_arquivo = $upload_dir . '/' . $nome_arquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
    setFlashMessage('error', 'Erro ao salvar o arquivo.');
    redirect('/admin/dashboard/estudante/');
}

try {
    // Iniciar transação
    $conn->beginTransaction();

    // Inserir projeto
    $stmt = $conn->prepare("INSERT INTO projetos (titulo, descricao, estudante_id, tema_id, status, data_cadastro) VALUES (?, ?, ?, ?, 'pendente', NOW())");
    $stmt->execute([$titulo, $descricao, $_SESSION['user_id'], $tema_id]);
    $projeto_id = $conn->lastInsertId();

    // Inserir documento
    $stmt = $conn->prepare("INSERT INTO documentos (projeto_id, nome, tipo, arquivo, data_upload) VALUES (?, ?, 'proposta', ?, NOW())");
    $stmt->execute([$projeto_id, 'Proposta de Projeto', $nome_arquivo]);

    // Se houver tema selecionado, atualizar seu status
    if ($tema_id) {
        $stmt = $conn->prepare("UPDATE temas_tfc SET status = 'em_uso' WHERE id = ?");
        $stmt->execute([$tema_id]);
    }

    $conn->commit();
    setFlashMessage('success', 'Proposta enviada com sucesso! Aguarde a análise do orientador.');
} catch (Exception $e) {
    $conn->rollBack();
    unlink($caminho_arquivo); // Remover arquivo em caso de erro
    setFlashMessage('error', 'Erro ao salvar a proposta. Por favor, tente novamente.');
}

$db->closeConnection();
redirect('/admin/dashboard/estudante/');
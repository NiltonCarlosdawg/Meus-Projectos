<?php
require_once '../../../config/config.php';
require_once '../../../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Método inválido.');
    redirect('/admin/dashboard/estudante/');
}

$db = new Database();
$conn = $db->getConnection();

// Validar tipo do relatório
$tipo = $_POST['tipo'];
if (!in_array($tipo, ['preliminar', 'final'])) {
    setFlashMessage('error', 'Tipo de relatório inválido.');
    redirect('/admin/dashboard/estudante/');
}

// Buscar projeto do aluno
$stmt = $conn->prepare("SELECT id FROM projetos WHERE estudante_id = ? ORDER BY data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projeto) {
    setFlashMessage('error', 'Nenhum projeto encontrado.');
    redirect('/admin/dashboard/estudante/');
}

// Processar upload do arquivo
if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    setFlashMessage('error', 'Erro no upload do arquivo.');
    redirect('/admin/dashboard/estudante/');
}

$arquivo = $_FILES['arquivo'];
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

if ($extensao !== 'pdf') {
    setFlashMessage('error', 'Apenas arquivos PDF são permitidos.');
    redirect('/admin/dashboard/estudante/');
}

// Criar diretório para o estudante se não existir
$upload_dir = '../../../uploads/estudantes/' . $_SESSION['user_id'];
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Gerar nome único para o arquivo
$nome_arquivo = 'relatorio_' . $tipo . '_' . time() . '.pdf';
$caminho_arquivo = $upload_dir . '/' . $nome_arquivo;

if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
    setFlashMessage('error', 'Erro ao salvar o arquivo.');
    redirect('/admin/dashboard/estudante/');
}

try {
    // Inserir documento
    $stmt = $conn->prepare("INSERT INTO documentos (projeto_id, nome, tipo, arquivo, data_upload) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $projeto['id'],
        $tipo === 'preliminar' ? 'Relatório Preliminar' : 'Relatório Final',
        'relatorio_' . $tipo,
        $nome_arquivo
    ]);

    // Atualizar status do projeto se for relatório final
    if ($tipo === 'final') {
        $stmt = $conn->prepare("UPDATE projetos SET status = 'em_revisao' WHERE id = ?");
        $stmt->execute([$projeto['id']]);
    }

    setFlashMessage('success', 'Relatório enviado com sucesso!');
} catch (Exception $e) {
    unlink($caminho_arquivo); // Remover arquivo em caso de erro
    setFlashMessage('error', 'Erro ao salvar o relatório. Por favor, tente novamente.');
}

$db->closeConnection();
redirect('/admin/dashboard/estudante/');
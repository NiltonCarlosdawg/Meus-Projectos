<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    setMessage('error', 'Acesso não autorizado.');
    redirect('/estudante/entregas-progressivas.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setMessage('error', 'Método não permitido.');
    redirect('/estudante/entregas-progressivas.php');
}

// Validar se os campos necessários foram enviados
if (!isset($_FILES['documento']) || !isset($_POST['tipo'])) {
    setMessage('error', 'Todos os campos são obrigatórios.');
    redirect('/estudante/entregas-progressivas.php');
}

$tiposPermitidos = ['capitulo1', 'capitulo2', 'capitulo3', 'versao_parcial'];
if (!in_array($_POST['tipo'], $tiposPermitidos)) {
    setMessage('error', 'Tipo de documento inválido.');
    redirect('/estudante/entregas-progressivas.php');
}

$arquivo = $_FILES['documento'];
$tiposArquivoPermitidos = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
$extensoesPermitidas = ['pdf', 'doc', 'docx'];

// Validar tipo do arquivo
$extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
if (!in_array($extensao, $extensoesPermitidas)) {
    setMessage('error', 'Tipo de arquivo não permitido. Use apenas PDF, DOC ou DOCX.');
    redirect('/estudante/entregas-progressivas.php');
}

// Validar tamanho do arquivo (máximo 10MB)
if ($arquivo['size'] > 10 * 1024 * 1024) {
    setMessage('error', 'O arquivo é muito grande. Tamanho máximo permitido: 10MB.');
    redirect('/estudante/entregas-progressivas.php');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar o projeto atual do estudante
$stmt = $conn->prepare("SELECT id FROM projetos WHERE estudante_id = ? ORDER BY data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projeto) {
    setMessage('error', 'Nenhum projeto encontrado.');
    redirect('/estudante/entregas-progressivas.php');
}

// Criar diretório para o estudante se não existir
$diretorioUpload = "../uploads/estudantes/{$_SESSION['user_id']}/";
if (!file_exists($diretorioUpload)) {
    mkdir($diretorioUpload, 0777, true);
}

// Gerar nome único para o arquivo
$nomeArquivo = uniqid() . '_' . $arquivo['name'];
$caminhoCompleto = $diretorioUpload . $nomeArquivo;

// Tentar mover o arquivo
if (!move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
    setMessage('error', 'Erro ao fazer upload do arquivo.');
    redirect('/estudante/entregas-progressivas.php');
}

try {
    $conn->beginTransaction();

    // Inserir registro do documento no banco de dados
    $stmt = $conn->prepare("INSERT INTO documentos (projeto_id, tipo, nome, caminho, data_upload) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $projeto['id'],
        $_POST['tipo'],
        $arquivo['name'],
        $nomeArquivo
    ]);

    // Se for um capítulo, atualizar ou inserir na tabela de capítulos
    if (strpos($_POST['tipo'], 'capitulo') === 0) {
        $numeroCapitulo = substr($_POST['tipo'], -1);
        
        // Verificar se já existe um registro para este capítulo
        $stmt = $conn->prepare("SELECT id FROM capitulos WHERE projeto_id = ? AND numero_capitulo = ?");
        $stmt->execute([$projeto['id'], $numeroCapitulo]);
        $capituloExistente = $stmt->fetch(PDO::FETCH_ASSOC);

        // Definir título padrão baseado no número do capítulo
        $tituloCapitulo = "Capítulo " . $numeroCapitulo;

        if ($capituloExistente) {
            // Atualizar status e arquivo do capítulo existente
            $stmt = $conn->prepare("UPDATE capitulos SET status = 'pendente', arquivo_path = ?, data_atualizacao = NOW() WHERE id = ?");
            $stmt->execute([$nomeArquivo, $capituloExistente['id']]);
        } else {
            // Inserir novo registro de capítulo com todos os campos obrigatórios
            $stmt = $conn->prepare("INSERT INTO capitulos (projeto_id, numero_capitulo, titulo, arquivo_path, status, data_submissao, data_atualizacao) VALUES (?, ?, ?, ?, 'pendente', NOW(), NOW())");
            $stmt->execute([$projeto['id'], $numeroCapitulo, $tituloCapitulo, $nomeArquivo]);
        }
    }

    $conn->commit();
    setMessage('success', 'Documento enviado com sucesso!');
} catch (PDOException $e) {
    $conn->rollBack();
    unlink($caminhoCompleto);
    
    // Log do erro para debug
    error_log('Erro no upload de documento: ' . $e->getMessage());
    
    // Mensagem de erro mais específica baseada no código de erro SQL
    if ($e->getCode() == '23000') { // Violação de chave única/estrangeira
        setMessage('error', 'Erro: Este documento já foi registrado ou o projeto não existe mais.');
    } else {
        setMessage('error', 'Erro ao registrar o documento. Por favor, tente novamente mais tarde.');
    }
} catch (Exception $e) {
    $conn->rollBack();
    unlink($caminhoCompleto);
    error_log('Erro inesperado no upload: ' . $e->getMessage());
    setMessage('error', 'Ocorreu um erro inesperado. Por favor, tente novamente.');
}

$db->closeConnection();
redirect('/estudante/entregas-progressivas.php');
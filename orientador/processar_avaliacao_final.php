<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SESSION['tipo_usuario'] !== 'orientador') {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método não permitido');
    }

    $projeto_id = filter_input(INPUT_POST, 'projeto_id', FILTER_VALIDATE_INT);
    $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_FLOAT);
    $parecer = filter_input(INPUT_POST, 'parecer', FILTER_SANITIZE_STRING);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $orientador_id = $_SESSION['user_id'];

    if (!$projeto_id || !$nota || !$parecer || !$status) {
        throw new Exception('Dados inválidos');
    }

    // Verificar se o projeto pertence ao orientador
    $stmt = $pdo->prepare("SELECT id FROM projetos WHERE id = ? AND orientador_id = ?");
    $stmt->execute([$projeto_id, $orientador_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Projeto não encontrado ou não autorizado');
    }

    // Verificar se já existe uma avaliação final
    $stmt = $pdo->prepare("SELECT id FROM avaliacoes_finais WHERE projeto_id = ?");
    $stmt->execute([$projeto_id]);
    if ($stmt->fetch()) {
        throw new Exception('Já existe uma avaliação final para este projeto');
    }

    // Iniciar transação
    $pdo->beginTransaction();

    // Inserir avaliação final
    $stmt = $pdo->prepare("
        INSERT INTO avaliacoes_finais 
        (projeto_id, nota_final, observacoes, status_defesa, data_avaliacao) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    $stmt->execute([
        $projeto_id,
        $nota,
        $parecer,
        $status
    ]);

    // Atualizar status do projeto
    $novo_status = $status === 'aprovado' ? 'concluido' : 'reprovado';
    $stmt = $pdo->prepare("UPDATE projetos SET status = ? WHERE id = ?");
    $stmt->execute([$novo_status, $projeto_id]);

    // Atualizar status da defesa
    $stmt = $pdo->prepare("UPDATE defesas SET status = ? WHERE projeto_id = ?");
    $stmt->execute([$status, $projeto_id]);

    // Registrar log
    $stmt = $pdo->prepare("
        INSERT INTO logs_atividades 
        (usuario_id, tipo_atividade, descricao, data_registro) 
        VALUES (?, 'avaliacao_final', ?, NOW())
    ");
    $stmt->execute([
        $orientador_id,
        "Registrou avaliação final para o projeto ID: $projeto_id"
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Avaliação registrada com sucesso'
    ]);

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
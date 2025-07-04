<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new Database();
    $conn = $db->getConnection();

    $destinatario_id = filter_input(INPUT_POST, 'destinatario_id', FILTER_SANITIZE_NUMBER_INT);
    $assunto = filter_input(INPUT_POST, 'assunto', FILTER_SANITIZE_SPECIAL_CHARS);
    $mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($destinatario_id && $assunto && $mensagem) {
        try {
            $stmt = $conn->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, assunto, mensagem, data_envio) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $destinatario_id, $assunto, $mensagem]);

            $_SESSION['success'] = 'Mensagem enviada com sucesso!';
        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erro ao enviar mensagem. Por favor, tente novamente.';
        }
    } else {
        $_SESSION['error'] = 'Por favor, preencha todos os campos.';
    }

    $db->closeConnection();
}

header('Location: mensagem.php');
exit;
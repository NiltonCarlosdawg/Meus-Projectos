<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

/**
 * Registra uma ação do usuário no log
 */
function registrarLog($usuario_id, $acao, $descricao = '') {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO user_logs (usuario_id, acao, descricao, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $usuario_id,
        $acao,
        $descricao,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    ]);
    
    $db->closeConnection();
}

/**
 * Registra uma ação do usuário no sistema
 */
function logUserAction($acao, $descricao = '') {
    if (isset($_SESSION['user_id'])) {
        registrarLog($_SESSION['user_id'], $acao, $descricao);
    }
}


/**
 * Registra alteração de senha no histórico
 */
function registrarAlteracaoSenha($usuario_id, $senha_antiga) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("INSERT INTO historico_senhas (usuario_id, senha_antiga) VALUES (?, ?)");
    $stmt->execute([$usuario_id, $senha_antiga]);
    
    $db->closeConnection();
}

/**
 * Reseta a senha de um usuário
 */
function resetarSenha($usuario_id, $nova_senha) {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $conn->beginTransaction();
        
        // Buscar senha atual
        $stmt = $conn->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario) {
            // Registrar senha antiga no histórico
            registrarAlteracaoSenha($usuario_id, $usuario['senha']);
            
            // Atualizar senha
            $stmt = $conn->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
            $stmt->execute([password_hash($nova_senha, PASSWORD_DEFAULT), $usuario_id]);
            
            // Registrar ação no log
            registrarLog($usuario_id, 'reset_senha', 'Senha resetada pelo administrador');
            
            $conn->commit();
            return true;
        }
        
        return false;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    } finally {
        $db->closeConnection();
    }
}

/**
 * Importa usuários em massa a partir de um arquivo CSV
 */
function importarUsuarios($arquivo, $admin_id) {
    $db = new Database();
    $conn = $db->getConnection();
    
    try {
        $conn->beginTransaction();
        
        // Registrar início da importação
        $stmt = $conn->prepare("INSERT INTO importacao_usuarios (arquivo_nome, usuario_id) VALUES (?, ?)");
        $stmt->execute([$arquivo['name'], $admin_id]);
        $importacao_id = $conn->lastInsertId();
        
        // Processar arquivo CSV
        $handle = fopen($arquivo['tmp_name'], 'r');
        $header = fgetcsv($handle);
        $total = 0;
        $processados = 0;
        $erros = 0;
        $log_erro = '';
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $total++;
            
            try {
                $nome = $data[0];
                $email = $data[1];
                $tipo_usuario = $data[2];
                $senha = password_hash(uniqid(), PASSWORD_DEFAULT); // Senha temporária
                
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario) VALUES (?, ?, ?, ?)");
                $stmt->execute([$nome, $email, $senha, $tipo_usuario]);
                $processados++;
                
                registrarLog($admin_id, 'importacao_usuario', "Usuário {$email} importado com sucesso");
            } catch (Exception $e) {
                $erros++;
                $log_erro .= "Linha {$total}: {$e->getMessage()}\n";
            }
        }
        
        fclose($handle);
        
        // Atualizar status da importação
        $stmt = $conn->prepare("UPDATE importacao_usuarios SET status = ?, total_registros = ?, registros_processados = ?, registros_com_erro = ?, log_erro = ? WHERE id = ?");
        $stmt->execute([
            $erros > 0 ? 'erro' : 'concluido',
            $total,
            $processados,
            $erros,
            $log_erro,
            $importacao_id
        ]);
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    } finally {
        $db->closeConnection();
    }
}
<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

// Processar importação em massa
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    if (importarUsuarios($_FILES['csv_file'], $_SESSION['user_id'])) {
        $success = 'Arquivo processado com sucesso!';
    } else {
        $error = 'Erro ao processar arquivo.';
    }
}

// Processar reset de senha
if (isset($_POST['reset_senha']) && isset($_POST['usuario_id'])) {
    $nova_senha = uniqid();
    if (resetarSenha($_POST['usuario_id'], $nova_senha)) {
        $success = "Senha resetada com sucesso! Nova senha: {$nova_senha}";
    } else {
        $error = 'Erro ao resetar senha.';
    }
}

// Processar alteração de status
if (isset($_POST['alterar_status']) && isset($_POST['usuario_id'])) {
    try {
        $stmt = $conn->prepare("UPDATE usuarios SET status = NOT status WHERE id = ? AND id != ?");
        $stmt->execute([$_POST['usuario_id'], $_SESSION['user_id']]);
        if ($stmt->rowCount() > 0) {
            registrarLog($_SESSION['user_id'], 'alterar_status', "Status do usuário {$_POST['usuario_id']} alterado");
            $success = 'Status do usuário alterado com sucesso!';
        }
    } catch (Exception $e) {
        $error = 'Erro ao alterar status do usuário.';
    }
}

// Buscar histórico de importações
$stmt = $conn->prepare("
    SELECT i.*, u.nome as admin_nome 
    FROM importacao_usuarios i 
    INNER JOIN usuarios u ON i.usuario_id = u.id 
    ORDER BY i.data_importacao DESC LIMIT 10
");
$stmt->execute();
$importacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar usuários
$stmt = $conn->prepare("
    SELECT u.*, 
           (SELECT COUNT(*) FROM user_logs WHERE usuario_id = u.id) as total_logs
    FROM usuarios u 
    ORDER BY u.nome
");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-graduation-cap me-2"></i><?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard/admin/">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/gerenciar-usuarios.php">
                            <i class="fas fa-users me-1"></i>Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">
                            <i class="fas fa-sign-out-alt me-1"></i>Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gerenciar Usuários</h2>
            <div>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#importarModal">
                    <i class="fas fa-file-import me-1"></i>Importar Usuários
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#usuarioModal">
                    <i class="fas fa-user-plus me-1"></i>Novo Usuário
                </button>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabela de Usuários -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Logs</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?php echo $usuario['nome']; ?></td>
                                    <td><?php echo $usuario['email']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $usuario['tipo_usuario'] === 'admin' ? 'danger' : 
                                                ($usuario['tipo_usuario'] === 'professor' ? 'primary' : 'success');
                                        ?>">
                                            <?php echo ucfirst($usuario['tipo_usuario']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $usuario['status'] ? 'success' : 'danger'; ?>">
                                            <?php echo $usuario['status'] ? 'Ativo' : 'Inativo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo $usuario['total_logs']; ?> logs</span>
                                    </td>
                                    <td>
                                        <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                            <button class="btn btn-sm btn-warning me-1" onclick="resetarSenha(<?php echo $usuario['id']; ?>)" title="Resetar Senha">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary me-1" onclick="editarUsuario(<?php echo $usuario['id']; ?>)" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                                <button type="submit" name="alterar_status" class="btn btn-sm btn-<?php echo $usuario['status'] ? 'danger' : 'success'; ?>" title="<?php echo $usuario['status'] ? 'Desativar' : 'Ativar'; ?>" onclick="return confirm('Tem certeza?')">
                                                    <i class="fas fa-<?php echo $usuario['status'] ? 'ban' : 'check'; ?>"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Histórico de Importações -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Histórico de Importações</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Arquivo</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Processados</th>
                                <th>Erros</th>
                                <th>Data</th>
                                <th>Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($importacoes as $importacao): ?>
                                <tr>
                                    <td><?php echo $importacao['arquivo_nome']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $importacao['status'] === 'concluido' ? 'success' : 
                                                ($importacao['status'] === 'erro' ? 'danger' : 'warning');
                                        ?>">
                                            <?php echo ucfirst($importacao['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $importacao['total_registros']; ?></td>
                                    <td><?php echo $importacao['registros_processados']; ?></td>
                                    <td><?php echo $importacao['registros_com_erro']; ?></td>
                                    <td><?php echo formatDate($importacao['data_importacao']); ?></td>
                                    <td><?php echo $importacao['admin_nome']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Importação -->
    <div class="modal fade" id="importarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Importar Usuários</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Arquivo CSV</label>
                            <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            O arquivo CSV deve conter as colunas: Nome, Email, Tipo de Usuário
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script>
    function resetarSenha(id) {
        if (confirm('Tem certeza que deseja resetar a senha deste usuário?')) {
            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="usuario_id" value="${id}">
                <input type="hidden" name="reset_senha" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function editarUsuario(id) {
        // Implementar edição de usuário
        alert('Funcionalidade em desenvolvimento');
    }
    </script>
</body>
</html>
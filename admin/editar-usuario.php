<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
<?php

// Verificar se o usuário está logado e tem permissão de administrador
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Verificar se foi fornecido um ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'ID de usuário inválido.';
    header('Location: usuarios.php');
    exit();
}

$userId = $_GET['id'];
$db = new Database();
$conn = $db->getConnection();

// Buscar informações do usuário
$stmt = $conn->prepare('SELECT * FROM usuarios WHERE id = ?');
$stmt->execute([$userId]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    $_SESSION['error'] = 'Usuário não encontrado.';
    header('Location: usuarios.php');
    exit();
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $tipo_usuario = $_POST['tipo_usuario'];
    $status = isset($_POST['status']) ? 1 : 0;
    $nova_senha = trim($_POST['nova_senha']);

    // Validar campos
    $errors = [];
    if (empty($nome)) $errors[] = 'O nome é obrigatório.';
    if (empty($email)) $errors[] = 'O email é obrigatório.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';

    // Verificar se o email já existe (exceto para o usuário atual)
    $stmt = $conn->prepare('SELECT id FROM usuarios WHERE email = ? AND id != ?');
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        $errors[] = 'Este email já está em uso.';
    }

    if (empty($errors)) {
        try {
            $conn->beginTransaction();

            // Atualizar informações básicas
            $sql = 'UPDATE usuarios SET nome = ?, email = ?, tipo_usuario = ?, status = ? WHERE id = ?';
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nome, $email, $tipo_usuario, $status, $userId]);

            // Atualizar senha se fornecida
            if (!empty($nova_senha)) {
                $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE usuarios SET senha = ? WHERE id = ?');
                $stmt->execute([$senha_hash, $userId]);
            }

            $conn->commit();
            $_SESSION['success'] = 'Usuário atualizado com sucesso!';
            header('Location: usuarios.php');
            exit();
        } catch (Exception $e) {
            $conn->rollBack();
            $errors[] = 'Erro ao atualizar usuário: ' . $e->getMessage();
        }
    }
}

$pageTitle = 'Editar Usuário';
include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Editar Usuário</h1>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" 
                                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                            <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                                <option value="admin" <?php echo $usuario['tipo_usuario'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                <option value="orientador" <?php echo $usuario['tipo_usuario'] === 'orientador' ? 'selected' : ''; ?>>Orientador</option>
                                <option value="estudante" <?php echo $usuario['tipo_usuario'] === 'estudante' ? 'selected' : ''; ?>>Estudante</option>
                                <option value="professor" <?php echo $usuario['tipo_usuario'] === 'professor' ? 'selected' : ''; ?>>Professor</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nova_senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
                            <input type="password" class="form-control" id="nova_senha" name="nova_senha">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" 
                                   <?php echo $usuario['status'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Usuário Ativo</label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="usuarios.php" class="btn btn-secondary me-md-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
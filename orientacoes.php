<?php
require_once 'config/config.php';
require_once 'config/database.php';

if (!isLoggedIn() || !isProfessor()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

// Processar exclusão de orientação
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM orientacoes WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            setFlashMessage('Orientação excluída com sucesso!');
        }
        redirect('/orientacoes.php');
    } catch(PDOException $e) {
        $error = 'Erro ao excluir orientação.';
    }
}

// Processar adição/edição de orientação
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $projeto_id = (int)$_POST['projeto_id'];
    $data_orientacao = $_POST['data_orientacao'];
    $descricao = sanitizeInput($_POST['descricao']);
    $status = sanitizeInput($_POST['status']);
    $observacoes = sanitizeInput($_POST['observacoes']);

    if (empty($projeto_id) || empty($data_orientacao) || empty($descricao)) {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    } else {
        try {
            if ($id) { // Editar orientação
                $stmt = $conn->prepare("UPDATE orientacoes SET projeto_id = ?, data_orientacao = ?, descricao = ?, status = ?, observacoes = ? WHERE id = ?");
                $stmt->execute([$projeto_id, $data_orientacao, $descricao, $status, $observacoes, $id]);
                $success = 'Orientação atualizada com sucesso!';
            } else { // Adicionar orientação
                $stmt = $conn->prepare("INSERT INTO orientacoes (projeto_id, data_orientacao, descricao, status, observacoes) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$projeto_id, $data_orientacao, $descricao, $status, $observacoes]);
                $success = 'Orientação agendada com sucesso!';
            }
        } catch(PDOException $e) {
            $error = 'Erro ao salvar orientação.';
        }
    }
}

// Buscar orientação para edição
$orientacao_edicao = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM orientacoes WHERE id = ?");
    $stmt->execute([$id]);
    $orientacao_edicao = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Buscar projetos do professor
$stmt = $conn->prepare("
    SELECT p.*, u.nome as aluno_nome 
    FROM projetos p 
    JOIN usuarios u ON p.aluno_id = u.id 
    WHERE p.orientador_id = ? AND p.status = 'em_andamento'
    ORDER BY p.titulo
");
$stmt->execute([$_SESSION['user_id']]);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar orientações
$stmt = $conn->prepare("
    SELECT o.*, p.titulo as projeto_titulo, u.nome as aluno_nome 
    FROM orientacoes o 
    JOIN projetos p ON o.projeto_id = p.id 
    JOIN usuarios u ON p.aluno_id = u.id 
    WHERE p.orientador_id = ? 
    ORDER BY o.data_orientacao DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orientacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orientações - <?php echo APP_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
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
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/orientacoes.php">Orientações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/logout.php">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Gestão de Orientações</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#orientacaoModal">
                <i class="fas fa-plus me-2"></i>Nova Orientação
            </button>
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

        <!-- Tabela de Orientações -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Aluno</th>
                                <th>Projeto</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orientacoes as $orientacao): ?>
                                <tr>
                                    <td><?php echo formatDate($orientacao['data_orientacao'], 'd/m/Y H:i'); ?></td>
                                    <td><?php echo $orientacao['aluno_nome']; ?></td>
                                    <td><?php echo $orientacao['projeto_titulo']; ?></td>
                                    <td><?php echo $orientacao['descricao']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $orientacao['status'] === 'agendada' ? 'warning' : 
                                                ($orientacao['status'] === 'realizada' ? 'success' : 'danger');
                                        ?>">
                                            <?php echo ucfirst($orientacao['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="?edit=<?php echo $orientacao['id']; ?>" class="btn btn-sm btn-primary me-1" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $orientacao['id']; ?>" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirmarExclusao('Tem certeza que deseja excluir esta orientação?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal de Orientação -->
    <div class="modal fade" id="orientacaoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $orientacao_edicao ? 'Editar Orientação' : 'Nova Orientação'; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validarFormulario(this);">
                    <div class="modal-body">
                        <?php if ($orientacao_edicao): ?>
                            <input type="hidden" name="id" value="<?php echo $orientacao_edicao['id']; ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="projeto_id" class="form-label">Projeto</label>
                            <select class="form-select" id="projeto_id" name="projeto_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($projetos as $projeto): ?>
                                    <option value="<?php echo $projeto['id']; ?>" <?php echo $orientacao_edicao && $orientacao_edicao['projeto_id'] == $projeto['id'] ? 'selected' : ''; ?>>
                                        <?php echo $projeto['titulo'] . ' - ' . $projeto['aluno_nome']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="data_orientacao" class="form-label">Data e Hora</label>
                            <input type="datetime-local" class="form-control" id="data_orientacao" name="data_orientacao" value="<?php echo $orientacao_edicao ? date('Y-m-d\TH:i', strtotime($orientacao_edicao['data_orientacao'])) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" required><?php echo $orientacao_edicao ? $orientacao_edicao['descricao'] : ''; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="agendada" <?php echo $orientacao_edicao && $orientacao_edicao['status'] === 'agendada' ? 'selected' : ''; ?>>Agendada</option>
                                <option value="realizada" <?php echo $orientacao_edicao && $orientacao_edicao['status'] === 'realizada' ? 'selected' : ''; ?>>Realizada</option>
                                <option value="cancelada" <?php echo $orientacao_edicao && $orientacao_edicao['status'] === 'cancelada' ? 'selected' : ''; ?>>Cancelada</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo $orientacao_edicao ? $orientacao_edicao['observacoes'] : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>

    <?php if ($orientacao_edicao): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var orientacaoModal = new bootstrap.Modal(document.getElementById('orientacaoModal'));
            orientacaoModal.show();
        });
    </script>
    <?php endif; ?>
</body>
</html>
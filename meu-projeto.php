<?php
require_once 'config/config.php';
require_once 'config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

// Buscar projeto do aluno
$stmt = $conn->prepare("
    SELECT p.*, u.nome as orientador_nome 
    FROM projetos p 
    LEFT JOIN usuarios u ON p.orientador_id = u.id 
    WHERE p.aluno_id = ? 
    ORDER BY p.data_cadastro DESC LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar orientações do projeto
$orientacoes = [];
if ($projeto) {
    $stmt = $conn->prepare("
        SELECT * FROM orientacoes 
        WHERE projeto_id = ? 
        ORDER BY data_orientacao DESC
    ");
    $stmt->execute([$projeto['id']]);
    $orientacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Buscar documentos do projeto
    $stmt = $conn->prepare("
        SELECT * FROM documentos 
        WHERE projeto_id = ? 
        ORDER BY data_upload DESC
    ");
    $stmt->execute([$projeto['id']]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Processar upload de documento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $arquivo = $_FILES['documento'];
    $tipo = sanitizeInput($_POST['tipo']);
    
    if ($arquivo['error'] === 0) {
        $nome_arquivo = uniqid() . '_' . sanitizeInput($arquivo['name']);
        $caminho = 'uploads/' . $nome_arquivo;
        
        if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
            try {
                $stmt = $conn->prepare("INSERT INTO documentos (projeto_id, nome, tipo, caminho) VALUES (?, ?, ?, ?)");
                $stmt->execute([$projeto['id'], $arquivo['name'], $tipo, $caminho]);
                $success = 'Documento enviado com sucesso!';
                redirect('/meu-projeto.php');
            } catch(PDOException $e) {
                $error = 'Erro ao salvar documento no banco de dados.';
                unlink($caminho); // Remove o arquivo se houver erro no banco
            }
        } else {
            $error = 'Erro ao fazer upload do arquivo.';
        }
    } else {
        $error = 'Erro no arquivo enviado.';
    }
}

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Projeto - <?php echo APP_NAME; ?></title>
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
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/meu-projeto.php">Meu Projeto</a>
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

        <?php if ($projeto): ?>
            <!-- Detalhes do Projeto -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Detalhes do Projeto</h5>
                </div>
                <div class="card-body">
                    <h4><?php echo $projeto['titulo']; ?></h4>
                    <p class="text-muted mb-2">Orientador: <?php echo $projeto['orientador_nome'] ?: 'Não atribuído'; ?></p>
                    <p class="mb-2">Status: 
                        <span class="badge bg-<?php 
                            echo $projeto['status'] === 'em_andamento' ? 'warning' : 
                                ($projeto['status'] === 'concluido' ? 'success' : 'danger');
                        ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $projeto['status'])); ?>
                        </span>
                    </p>
                    <p class="mb-0">Descrição:</p>
                    <p class="mb-0"><?php echo $projeto['descricao']; ?></p>
                </div>
            </div>

            <!-- Upload de Documentos -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Documentos</h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($documentos)): ?>
                        <p class="text-muted">Nenhum documento enviado.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Tipo</th>
                                        <th>Data de Upload</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documentos as $documento): ?>
                                        <tr>
                                            <td><?php echo $documento['nome']; ?></td>
                                            <td><?php echo ucfirst($documento['tipo']); ?></td>
                                            <td><?php echo formatDate($documento['data_upload']); ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL . '/' . $documento['caminho']; ?>" class="btn btn-sm btn-primary" target="_blank" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Orientações -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Orientações</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orientacoes)): ?>
                        <p class="text-muted">Nenhuma orientação registrada.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Descrição</th>
                                        <th>Status</th>
                                        <th>Observações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orientacoes as $orientacao): ?>
                                        <tr>
                                            <td><?php echo formatDate($orientacao['data_orientacao'], 'd/m/Y H:i'); ?></td>
                                            <td><?php echo $orientacao['descricao']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $orientacao['status'] === 'agendada' ? 'warning' : 
                                                        ($orientacao['status'] === 'realizada' ? 'success' : 'danger');
                                                ?>">
                                                    <?php echo ucfirst($orientacao['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $orientacao['observacoes']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <h4 class="alert-heading">Bem-vindo ao Sistema!</h4>
                <p>Você ainda não tem um projeto cadastrado. Entre em contato com a coordenação para mais informações.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Modal de Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload de Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario(this);">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="documento" class="form-label">Arquivo</label>
                            <input type="file" class="form-control" id="documento" name="documento" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="proposta">Proposta</option>
                                <option value="monografia">Monografia</option>
                                <option value="apresentacao">Apresentação</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar</button>
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
</body>
</html>
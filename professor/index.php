<?php
require_once '../../../config/config.php';
require_once '../../../config/database.php';

if (!isLoggedIn() || !isProfessor()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar projetos sob orientação
$stmt = $conn->prepare("
    SELECT p.*, u.nome as aluno_nome 
    FROM projetos p 
    INNER JOIN usuarios u ON p.aluno_id = u.id 
    WHERE p.orientador_id = ? 
    ORDER BY p.data_cadastro DESC
");
$stmt->execute([$_SESSION['user_id']]);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar próximas orientações
$stmt = $conn->prepare("
    SELECT o.*, p.titulo as projeto_titulo, u.nome as aluno_nome 
    FROM orientacoes o 
    INNER JOIN projetos p ON o.projeto_id = p.id 
    INNER JOIN usuarios u ON p.aluno_id = u.id 
    WHERE p.orientador_id = ? AND o.status = 'agendada' 
    ORDER BY o.data_orientacao ASC LIMIT 5
");
$stmt->execute([$_SESSION['user_id']]);
$proximas_orientacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Professor - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../../assets/css/style.css" rel="stylesheet">
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
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/dashboard/professor/">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/orientacoes.php">
                            <i class="fas fa-chalkboard-teacher me-1"></i>Orientações
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
        <h2 class="mb-4">Dashboard do Professor</h2>
        
        <!-- Próximas Orientações -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Próximas Orientações</h5>
            </div>
            <div class="card-body">
                <?php if (empty($proximas_orientacoes)): ?>
                    <p class="text-muted mb-0">Não há orientações agendadas.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Aluno</th>
                                    <th>Projeto</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($proximas_orientacoes as $orientacao): ?>
                                    <tr>
                                        <td><?php echo formatDate($orientacao['data_orientacao'], 'd/m/Y H:i'); ?></td>
                                        <td><?php echo $orientacao['aluno_nome']; ?></td>
                                        <td><?php echo $orientacao['projeto_titulo']; ?></td>
                                        <td>
                                            <a href="<?php echo BASE_URL; ?>/orientacoes.php?id=<?php echo $orientacao['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
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

        <!-- Projetos em Orientação -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Projetos em Orientação</h5>
            </div>
            <div class="card-body">
                <?php if (empty($projetos)): ?>
                    <p class="text-muted mb-0">Não há projetos sob sua orientação.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($projetos as $projeto): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $projeto['titulo']; ?></h5>
                                        <p class="text-muted mb-2">Aluno: <?php echo $projeto['aluno_nome']; ?></p>
                                        <p class="mb-2">Status: 
                                            <span class="badge bg-<?php 
                                                echo $projeto['status'] === 'em_andamento' ? 'warning' : 
                                                    ($projeto['status'] === 'concluido' ? 'success' : 'danger');
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $projeto['status'])); ?>
                                            </span>
                                        </p>
                                        <p class="mb-3"><?php echo substr($projeto['descricao'], 0, 150); ?>...</p>
                                        <a href="<?php echo BASE_URL; ?>/projeto.php?id=<?php echo $projeto['id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>
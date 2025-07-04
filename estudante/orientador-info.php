<?php
require_once '../../../config/config.php';
require_once '../../../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar informações do projeto e orientadores
$stmt = $conn->prepare("SELECT p.*, 
    u1.nome as orientador_nome, u1.email as orientador_email,
    u2.nome as coorientador_nome, u2.email as coorientador_email
FROM projetos p 
LEFT JOIN usuarios u1 ON p.orientador_id = u1.id 
LEFT JOIN usuarios u2 ON p.coorientador_id = u2.id 
WHERE p.estudante_id = ? ORDER BY p.data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar orientações agendadas
$orientacoes = [];
if ($projeto) {
    $stmt = $conn->prepare("SELECT * FROM orientacoes WHERE projeto_id = ? ORDER BY data_orientacao DESC");
    $stmt->execute([$projeto['id']]);
    $orientacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orientador/Coorientador - SISTEMA DE GESTÃO DE PAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .orientador-card {
            transition: transform 0.2s;
        }
        .orientador-card:hover {
            transform: translateY(-5px);
        }
        .timeline {
            position: relative;
            padding: 20px 0;
        }
        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 2px;
            background-color: #3498db;
        }
        .timeline-item::after {
            content: '';
            position: absolute;
            left: -4px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #3498db;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Orientador/Coorientador</h1>
                </div>

                <?php if ($projeto): ?>
                <!-- Informações dos Orientadores -->
                <div class="row mb-4">
                    <?php if ($projeto['orientador_nome']): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card orientador-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-user-tie me-2"></i>Orientador</h5>
                                <hr>
                                <p><strong>Nome:</strong> <?php echo htmlspecialchars($projeto['orientador_nome']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($projeto['orientador_email']); ?></p>
                                <a href="mailto:<?php echo htmlspecialchars($projeto['orientador_email']); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-2"></i>Enviar Email
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($projeto['coorientador_nome']): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card orientador-card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-user-graduate me-2"></i>Coorientador</h5>
                                <hr>
                                <p><strong>Nome:</strong> <?php echo htmlspecialchars($projeto['coorientador_nome']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($projeto['coorientador_email']); ?></p>
                                <a href="mailto:<?php echo htmlspecialchars($projeto['coorientador_email']); ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope me-2"></i>Enviar Email
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Histórico de Orientações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histórico de Orientações</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <?php foreach ($orientacoes as $orientacao): ?>
                            <div class="timeline-item">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <?php echo date('d/m/Y H:i', strtotime($orientacao['data_orientacao'])); ?>
                                        </h6>
                                        <p class="card-text"><?php echo htmlspecialchars($orientacao['observacoes']); ?></p>
                                        <span class="badge bg-<?php echo $orientacao['status'] == 'realizada' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($orientacao['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Agendar Nova Orientação -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Agendar Nova Orientação</h5>
                    </div>
                    <div class="card-body">
                        <form action="agendar-orientacao.php" method="POST">
                            <div class="mb-3">
                                <label for="data" class="form-label">Data e Hora</label>
                                <input type="datetime-local" class="form-control" id="data" name="data" required>
                            </div>
                            <div class="mb-3">
                                <label for="assunto" class="form-label">Assunto</label>
                                <input type="text" class="form-control" id="assunto" name="assunto" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-check me-2"></i>Solicitar Orientação
                            </button>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Você ainda não tem um projeto cadastrado.
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
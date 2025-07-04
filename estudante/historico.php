<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar histórico de documentos
$stmt = $conn->prepare("SELECT d.*, p.titulo as projeto_titulo 
    FROM documentos d 
    INNER JOIN projetos p ON d.projeto_id = p.id 
    WHERE p.estudante_id = ? 
    ORDER BY d.data_upload DESC");
$stmt->execute([$_SESSION['user_id']]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar histórico de mensagens
$stmt = $conn->prepare("SELECT m.*, u.nome as remetente_nome 
    FROM mensagens m 
    INNER JOIN usuarios u ON m.remetente_id = u.id 
    WHERE (m.destinatario_id = ? OR m.remetente_id = ?) 
    ORDER BY m.data_envio DESC");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar histórico de avaliações
$stmt = $conn->prepare("SELECT a.*, u.nome as avaliador_nome 
    FROM avaliacoes_projeto a 
    INNER JOIN usuarios u ON a.orientador_id = u.id 
    INNER JOIN projetos p ON a.projeto_id = p.id 
    WHERE p.estudante_id = ? 
    ORDER BY a.data_avaliacao DESC");
$stmt->execute([$_SESSION['user_id']]);
$avaliacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - SISTEMATFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
        }
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #2c3e50;
            padding: 20px;
            color: white;
            overflow-y: auto;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        .timeline-item {
            border-left: 2px solid #3498db;
            padding-left: 20px;
            margin-bottom: 20px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            width: 12px;
            height: 12px;
            background-color: #3498db;
            border-radius: 50%;
            position: absolute;
            left: -7px;
            top: 0;
        }
        .timeline-date {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="mb-4">SISTEMA DE GESTÃO DE PAP</h4>
        <div class="nav flex-column">
            <a href="./index.php" class="nav-link">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="./temas-disponiveis.php" class="nav-link">
                <i class="fas fa-list me-2"></i> Temas Disponíveis
            </a>
            <a href="./meu-orientador.php" class="nav-link">
                <i class="fas fa-user-tie me-2"></i> Meu Orientador
            </a>
            <a href="./entregas-progressivas.php" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Entregas Progressivas
            </a>
            <a href="./documentos.php" class="nav-link">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
            <a href="./mensagem.php" class="nav-link">
                <i class="fas fa-envelope me-2"></i> Mensagens
            </a>
            <a href="./historico.php" class="nav-link active">
                <i class="fas fa-history me-2"></i> Histórico
            </a>
            <a href="#perfil" class="nav-link" data-bs-toggle="pill">
                <i class="fas fa-user me-2"></i> Perfil
            </a>
            <a href="./logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Sair
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Histórico de Atividades</h2>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="historyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="documentos-tab" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">Documentos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="mensagens-tab" data-bs-toggle="tab" data-bs-target="#mensagens" type="button" role="tab">Mensagens</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="avaliacoes-tab" data-bs-toggle="tab" data-bs-target="#avaliacoes" type="button" role="tab">Avaliações</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="historyTabsContent">
            <!-- Documentos Tab -->
            <div class="tab-pane fade show active" id="documentos" role="tabpanel">
                <div class="timeline">
                    <?php foreach($documentos as $doc): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <?php echo date('d/m/Y H:i', strtotime($doc['data_upload'])); ?>
                        </div>
                        <h5 class="mt-2"><?php echo htmlspecialchars($doc['nome']); ?></h5>
                        <p class="mb-1">
                            <strong>Projeto:</strong> <?php echo htmlspecialchars($doc['projeto_titulo']); ?><br>
                            <strong>Tipo:</strong> <?php echo ucfirst($doc['tipo']); ?>
                        </p>
                        <div class="mt-2">
                            <a href="../uploads/<?php echo $doc['caminho_arquivo']; ?>" class="btn btn-sm btn-primary d-inline-flex align-items-center" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mensagens Tab -->
            <div class="tab-pane fade" id="mensagens" role="tabpanel">
                <div class="timeline">
                    <?php foreach($mensagens as $msg): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <i class="fas fa-clock me-2"></i>
                            <?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?>
                        </div>
                        <h5 class="mt-2"><?php echo htmlspecialchars($msg['remetente_nome']); ?></h5>
                        <p class="mb-1"><?php echo htmlspecialchars($msg['conteudo']); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Avaliações Tab -->
            <div class="tab-pane fade" id="avaliacoes" role="tabpanel">
                <div class="timeline">
                    <?php foreach($avaliacoes as $aval): ?>
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <i class="fas fa-star me-2"></i>
                            <?php echo date('d/m/Y', strtotime($aval['data_avaliacao'])); ?>
                        </div>
                        <h5 class="mt-2">Avaliação de <?php echo htmlspecialchars($aval['avaliador_nome']); ?></h5>
                        <p class="mb-1">
                            <strong>Nota:</strong> <?php echo $aval['nota']; ?>/10<br>
                            <strong>Comentário:</strong> <?php echo htmlspecialchars($aval['comentario']); ?>
                        </p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
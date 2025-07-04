<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar mensagens recebidas pelo estudante
$stmt = $conn->prepare("SELECT m.*, u.nome as remetente_nome 
FROM mensagens m 
LEFT JOIN usuarios u ON m.remetente_id = u.id 
WHERE m.destinatario_id = ? 
ORDER BY m.data_envio DESC");
$stmt->execute([$_SESSION['user_id']]);
$mensagens_recebidas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar mensagens enviadas pelo estudante
$stmt = $conn->prepare("SELECT m.*, u.nome as destinatario_nome 
FROM mensagens m 
LEFT JOIN usuarios u ON m.destinatario_id = u.id 
WHERE m.remetente_id = ? 
ORDER BY m.data_envio DESC");
$stmt->execute([$_SESSION['user_id']]);
$mensagens_enviadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar orientador e coorientador do estudante para envio de mensagem
$stmt = $conn->prepare("SELECT u.id, u.nome, 'orientador' as tipo 
FROM usuarios u 
INNER JOIN projetos p ON u.id = p.orientador_id 
WHERE p.estudante_id = ? 
UNION 
SELECT u.id, u.nome, 'coorientador' as tipo 
FROM usuarios u 
INNER JOIN projeto_coorientadores pc ON u.id = pc.coorientador_id 
INNER JOIN projetos p ON pc.projeto_id = p.id 
WHERE p.estudante_id = ? 
ORDER BY tipo");
$stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - SISTEMA DE GESTÃO DE PAP</title>
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
        .message-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .message-card:hover {
            transform: translateY(-2px);
        }
        .unread {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="mb-4">SISTEMA DE GESTÃO DE PAP</h4>
        <div class="nav flex-column">
            <a href="index.php" class="nav-link">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="temas-disponiveis.php" class="nav-link">
                <i class="fas fa-list me-2"></i> Temas Disponíveis
            </a>
            <a href="meu-orientador.php" class="nav-link">
                <i class="fas fa-user-tie me-2"></i> Meu Orientador
            </a>
            <a href="entregas-progressivas.php" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Entregas Progressivas
            </a>
            <a href="defesas.php" class="nav-link"><!--falta "defesa.php" arquivo-->
                <i class="fas fa-presentation me-2"></i> Defesas
            </a>
            <a href="documentos.php" class="nav-link">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
            <a href="mensagem.php" class="nav-link active">
                <i class="fas fa-envelope me-2"></i> Mensagens
            </a>
            <a href="historico.php" class="nav-link">
                <i class="fas fa-history me-2"></i> Histórico
            </a>
            <a href="/logout.php" class="nav-link text-danger mt-3">
                <i class="fas fa-sign-out-alt me-2"></i> Sair
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <h2 class="mb-4">Mensagens</h2>

            <!-- Nova Mensagem -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Nova Mensagem</h5>
                </div>
                <div class="card-body">
                    <form action="processar-mensagem.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Destinatário</label>
                            <select class="form-select" name="destinatario_id" required>
                                <?php foreach ($professores as $professor): ?>
                                <option value="<?php echo $professor['id']; ?>"><?php echo htmlspecialchars($professor['nome']); ?> (<?php echo ucfirst($professor['tipo']); ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assunto</label>
                            <input type="text" class="form-control" name="assunto" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensagem</label>
                            <textarea class="form-control" name="mensagem" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mensagens Recebidas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Mensagens Recebidas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($mensagens_recebidas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Você não tem mensagens recebidas.
                    </div>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($mensagens_recebidas as $mensagem): ?>
                        <div class="list-group-item message-card <?php echo !$mensagem['lida'] ? 'unread' : ''; ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($mensagem['assunto']); ?></h6>
                                <small><?php echo date('d/m/Y H:i', strtotime($mensagem['data_envio'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($mensagem['mensagem']); ?></p>
                            <small>De: <?php echo htmlspecialchars($mensagem['remetente_nome']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mensagens Enviadas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mensagens Enviadas</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($mensagens_enviadas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Você não tem mensagens enviadas.
                    </div>
                    <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($mensagens_enviadas as $mensagem): ?>
                        <div class="list-group-item message-card">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($mensagem['assunto']); ?></h6>
                                <small><?php echo date('d/m/Y H:i', strtotime($mensagem['data_envio'])); ?></small>
                            </div>
                            <p class="mb-1"><?php echo htmlspecialchars($mensagem['mensagem']); ?></p>
                            <small>Para: <?php echo htmlspecialchars($mensagem['destinatario_nome']); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
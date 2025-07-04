<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$database = new Database();
$pdo = $database->getConnection();

$user_id = $_SESSION['user_id'];
$mensagem = '';
$tipo_mensagem = '';

// Verificar se o estudante já tem um projeto com tema associado
$stmt = $pdo->prepare("SELECT p.id FROM projetos p 
INNER JOIN inscricoes_tema i ON p.estudante_id = i.estudante_id 
WHERE p.estudante_id = ? AND p.status NOT IN ('concluido', 'reprovado')");
$stmt->execute([$user_id]);
$projeto_com_tema = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar temas disponíveis
try {
    $stmt = $pdo->prepare("SELECT t.id, t.titulo, t.descricao, u.nome as orientador_nome, t.status 
        FROM temas_tfc t 
        LEFT JOIN usuarios u ON t.docente_proponente_id = u.id 
        WHERE t.status IN ('disponivel', 'aprovado', 'publicado') 
        AND (t.data_limite_escolha >= CURDATE() OR t.data_limite_escolha IS NULL)");
    $stmt->execute();
    $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($projeto_com_tema) {
        $mensagem = 'Você já possui um projeto com tema associado.';
        $tipo_mensagem = 'warning';
    }
} catch (PDOException $e) {
    $temas = [];
    $mensagem = 'Erro ao buscar temas: ' . htmlspecialchars($e->getMessage());
    $tipo_mensagem = 'danger';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temas Disponíveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/temas-disponiveis.css" rel="stylesheet">
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
            z-index: 1000;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        .nav-link.active {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
<div class="sidebar">
        <h4 class="mb-4">SISTEMA DE GESTÃO DE PAP</h4>
        <div class="nav flex-column">
            <a href="index.php" class="nav-link active" data-bs-toggle="pill">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="../estudante/temas-disponiveis.php" class="nav-link">
                <i class="fas fa-list me-2"></i> Temas Disponíveis
            </a>
            <a href="../estudante/meu-orientador.php" class="nav-link">
                <i class="fas fa-user-tie me-2"></i> Meu Orientador
            </a>
            <a href="../estudante/entregas-progressivas.php" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Entregas Progressivas
            </a>
            <a href="./documentos.php" class="nav-link" data-bs-toggle="pill">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
             <a class="nav-link" href="./mensagem.php">
                    <i class="fas fa-envelope me-2"></i>Mensagens
            </a>
            <a class="nav-link" href="historico.php">
                    <i class="fas fa-history me-2"></i>Histórico
            </a>
            <a href="./perfil.php" class="nav-link" data-bs-toggle="pill">
                <i class="fas fa-user me-2"></i> Perfil
            </a>
            <a class="nav-link text-danger" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Sair
             </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Temas Disponíveis</h2>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="submeter-proposta.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Submeter Proposta Personalizada
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <?php 
                $flash = getFlashMessage();
                if ($flash): ?>
                    <div class="alert alert-<?php echo $flash['type']; ?>"> <?php echo $flash['message']; ?> </div>
                <?php endif; ?>
                <?php if (!empty($temas)): ?>
                    <div class="temas-grid">
                        <?php foreach ($temas as $tema): ?>
                            <div class="tema-card">
                                <div class="tema-card-header">
                                    <h3 class="tema-titulo"><?php echo htmlspecialchars($tema['titulo']); ?></h3>
                                </div>
                                <div class="tema-card-body">
                                    <div class="tema-info">
                                        <i class="fas fa-user-tie"></i>
                                        <span class="tema-orientador"><?php echo htmlspecialchars($tema['orientador_nome']); ?></span>
                                    </div>
                                    <p class="tema-descricao"><?php echo htmlspecialchars($tema['descricao']); ?></p>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($tema['status']) {
                                        case 'disponivel':
                                            $statusClass = 'disponivel';
                                            $statusText = 'Disponível';
                                            break;
                                        case 'em_andamento':
                                            $statusClass = 'em-andamento';
                                            $statusText = 'Em Andamento';
                                            break;
                                        case 'concluido':
                                            $statusClass = 'concluido';
                                            $statusText = 'Concluído';
                                            break;
                                        default:
                                            $statusClass = 'outro';
                                            $statusText = ucfirst($tema['status']);
                                    }
                                    ?>
                                    <div class="tema-info">
                                        <i class="fas fa-info-circle"></i>
                                        <span class="tema-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </div>
                                </div>
                                <div class="tema-card-footer">
                                    <?php if (($tema['status'] === 'disponivel' || $tema['status'] === 'publicado') && !$projeto_com_tema): ?>
                                        <form action="selecionar-tema.php" method="POST">
                                            <input type="hidden" name="tema_id" value="<?php echo $tema['id']; ?>">
                                            <button type="submit" class="btn-escolher-tema">
                                                <i class="fas fa-check me-2"></i>Escolher Este Tema
                                            </button>
                                        </form>
                                    <?php elseif($projeto_com_tema): ?>
                                        <button class="btn-escolher-tema" disabled>
                                            <i class="fas fa-info-circle me-2"></i>Você já possui um tema selecionado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhum tema disponível no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('#selecionarTemaForm');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (confirm('Tem certeza que deseja escolher este tema?')) {
                    this.submit();
                }
            });
        });
    });
    </script>
</body>
</html>
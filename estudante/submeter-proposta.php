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

// Processar submissão da proposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submeter_proposta'])) {
    $titulo = $_POST['titulo'];
    $area_pesquisa = $_POST['area_pesquisa'];
    $resumo = $_POST['resumo'];
    $file = $_FILES['proposta_file'];
    
    if (empty($titulo) || empty($area_pesquisa) || empty($resumo)) {
        $mensagem = 'Todos os campos são obrigatórios.';
        $tipo_mensagem = 'danger';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $mensagem = 'Erro no upload do arquivo.';
        $tipo_mensagem = 'danger';
    } elseif ($file['type'] !== 'application/pdf') {
        $mensagem = 'Por favor, envie apenas arquivos PDF.';
        $tipo_mensagem = 'danger';
    } else {
        $upload_dir = "../uploads/estudantes/{$user_id}/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = 'proposta_inicial_' . time() . '.pdf';
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            try {
                // Inserir proposta no banco de dados
                $stmt = $pdo->prepare("INSERT INTO propostas (estudante_id, titulo, area_pesquisa, resumo, arquivo_caminho, status, data_submissao) VALUES (?, ?, ?, ?, ?, 'pendente', NOW())");
                $stmt->execute([$user_id, $titulo, $area_pesquisa, $resumo, $filepath]);
                
                $mensagem = 'Proposta submetida com sucesso! Aguarde a avaliação do orientador.';
                $tipo_mensagem = 'success';
            } catch (PDOException $e) {
                $mensagem = 'Erro ao registrar a proposta.';
                $tipo_mensagem = 'danger';
            }
        } else {
            $mensagem = 'Erro ao fazer upload do arquivo.';
            $tipo_mensagem = 'danger';
        }
    }
}

// Buscar propostas anteriores
$stmt = $pdo->prepare("SELECT * FROM propostas WHERE estudante_id = ? ORDER BY data_submissao DESC");
$stmt->execute([$user_id]);
$propostas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submeter Proposta - SISTEMATFC</title>
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
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .table-responsive {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
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
            <a href="submeter-proposta.php" class="nav-link active">
                <i class="fas fa-file-upload me-2"></i> Submissão Inicial
            </a>
            <a href="entregas-progressivas.php" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Entregas Progressivas
            </a>
            <a href="#defesas" class="nav-link">
                <i class="fas fa-presentation me-2"></i> Defesas
            </a>
            <a href="#documentos" class="nav-link">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
            <a href="#perfil" class="nav-link">
                <i class="fas fa-user me-2"></i> Perfil
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($mensagem): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?>" role="alert">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <h2>Submeter Proposta Inicial</h2>
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título da Proposta</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="area_pesquisa" class="form-label">Área de Pesquisa</label>
                        <select class="form-select" id="area_pesquisa" name="area_pesquisa" required>
                            <option value="">Selecione uma área</option>
                            <option value="Engenharia de Software">Engenharia de Software</option>
                            <option value="Inteligência Artificial">Inteligência Artificial</option>
                            <option value="Redes de Computadores">Redes de Computadores</option>
                            <option value="Segurança da Informação">Segurança da Informação</option>
                            <option value="Banco de Dados">Banco de Dados</option>
                            <option value="Computação Gráfica">Computação Gráfica</option>
                            <option value="Sistemas Distribuídos">Sistemas Distribuídos</option>
                            <option value="Computação Móvel">Computação Móvel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="resumo" class="form-label">Resumo</label>
                        <textarea class="form-control" id="resumo" name="resumo" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="proposta_file" class="form-label">Arquivo da Proposta (PDF)</label>
                        <input type="file" class="form-control" id="proposta_file" name="proposta_file" accept=".pdf" required>
                    </div>
                    <button type="submit" name="submeter_proposta" class="btn btn-primary">Submeter Proposta</button>
                </form>
            </div>
        </div>

        <?php if (!empty($propostas)): ?>
            <h3>Propostas Anteriores</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Área de Pesquisa</th>
                            <th>Status</th>
                            <th>Data de Submissão</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($propostas as $proposta): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($proposta['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($proposta['area_pesquisa']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $proposta['status'] === 'aprovada' ? 'success' : 
                                            ($proposta['status'] === 'pendente' ? 'warning' : 'danger');
                                    ?>">
                                        <?php echo ucfirst($proposta['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($proposta['data_submissao'])); ?></td>
                                <td>
                                    <a href="<?php echo str_replace('../', '', $proposta['arquivo_caminho']); ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-eye me-1"></i> Visualizar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info" role="alert">
                Você ainda não submeteu nenhuma proposta.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
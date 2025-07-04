<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    setMessage('error', 'Acesso não autorizado.');
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar o projeto atual do estudante
$stmt = $conn->prepare("SELECT id FROM projetos WHERE estudante_id = ? ORDER BY data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar documentos enviados
$documentos = [];
if ($projeto) {
    $stmt = $conn->prepare("SELECT * FROM documentos WHERE projeto_id = ? ORDER BY data_upload DESC");
    $stmt->execute([$projeto['id']]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar status dos capítulos
$capitulos = [];
if ($projeto) {
    $stmt = $conn->prepare("SELECT * FROM capitulos WHERE projeto_id = ? ORDER BY numero_capitulo");
    $stmt->execute([$projeto['id']]);
    $capitulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entregas Progressivas - SISTEMA DE GESTÃO DE PAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/SISTEMATFC/assets/css/style.css">
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
        .status-card {
            transition: transform 0.2s;
        }
        .status-card:hover {
            transform: translateY(-5px);
        }
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .upload-area:hover {
            border-color: #3498db;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="mb-4">SISTEMA DE GESTÃO DE PAP</h4>
        <div class="nav flex-column">
            <a href="#dashboard" class="nav-link active" data-bs-toggle="pill">
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
            <a href="#perfil" class="nav-link" data-bs-toggle="pill">
                <i class="fas fa-user me-2"></i> Perfil
            </a>
            <a class="nav-link text-danger" href="./logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Sair
             </a>
        </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
        <h1>Entregas Progressivas</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?>">
                <?php 
                echo $_SESSION['message']['text']; 
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (!$projeto): ?>
            <div class="alert alert-warning">
                Você precisa ter um projeto cadastrado para fazer entregas progressivas.
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Enviar Novo Documento</h6>
                </div>
                <div class="card-body">
                    <form action="processar-upload.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo do Documento:</label>
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Selecione o tipo</option>
                                <option value="capitulo1">Capítulo 1</option>
                                <option value="capitulo2">Capítulo 2</option>
                                <option value="capitulo3">Capítulo 3</option>
                                <option value="versao_parcial">Versão Parcial</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="documento" class="form-label">Arquivo (PDF, DOC ou DOCX):</label>
                            <input type="file" name="documento" id="documento" class="form-control" required accept=".pdf,.doc,.docx">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Enviar Documento
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Documentos Enviados</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($documentos)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Nenhum documento enviado ainda.
                        </div>
                    <?php else: ?>
                        <?php foreach ($documentos as $doc): ?>
                            <div class="document-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><?php echo ucfirst(str_replace('_', ' ', $doc['tipo'])); ?></h5>
                                    <?php
                                    if (strpos($doc['tipo'], 'capitulo') === 0) {
                                        $cap_num = substr($doc['tipo'], -1);
                                        $cap_status = 'pendente';
                                        foreach ($capitulos as $cap) {
                                            if ($cap['numero_capitulo'] == $cap_num) {
                                                $cap_status = $cap['status'];
                                                break;
                                            }
                                        }
                                        echo '<span class="status-badge status-' . $cap_status . '">' . 
                                             ucfirst($cap_status) . '</span>';
                                    }
                                    ?>
                                </div>
                                <div class="mt-3">
                                    <p class="mb-1">
                                        <i class="fas fa-file me-2"></i>
                                        <?php echo htmlspecialchars($doc['nome']); ?>
                                    </p>
                                    <p class="text-muted mb-3">
                                        <i class="fas fa-calendar me-2"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($doc['data_upload'])); ?>
                                    </p>
                                    <a href="/SISTEMATFC/uploads/estudantes/<?php echo $_SESSION['user_id'] . '/' . $doc['caminho']; ?>" 
                                       target="_blank" class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye me-2"></i>Visualizar
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="/SISTEMATFC/assets/js/main.js"></script>
</body>
</html>
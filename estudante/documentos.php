<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar documentos do estudante
$stmt = $conn->prepare("SELECT d.*, p.titulo as projeto_titulo 
FROM documentos d 
INNER JOIN projetos p ON d.projeto_id = p.id 
WHERE p.estudante_id = ? 
ORDER BY d.data_upload DESC");
$stmt->execute([$_SESSION['user_id']]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentos - SISTEMA DE GESTÃO DE PAP</title>
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
        .document-card {
            transition: transform 0.2s;
        }
        .document-card:hover {
            transform: translateY(-5px);
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
            <a href="documentos.php" class="nav-link active">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
            <a href="mensagem.php" class="nav-link">
                <i class="fas fa-envelope me-2"></i> Mensagens
            </a>
            <a href="historico.php" class="nav-link">
                <i class="fas fa-history me-2"></i> Histórico
            </a>
            <a href="#perfil" class="nav-link" data-bs-toggle="pill"><!--falta o arquivo "perfil"-->
                <i class="fas fa-user me-2"></i> Perfil
            </a>
            <a href="logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i> Sair
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="mb-4">Documentos</h2>

        <!-- Upload Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Upload de Documento</h5>
            </div>
            <div class="card-body">
                <form action="processar-upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Documento</label>
                        <select class="form-select" name="tipo_documento" required>
                            <option value="">Selecione o tipo...</option>
                            <option value="capitulo">Capítulo</option>
                            <option value="relatorio">Relatório</option>
                            <option value="apresentacao">Apresentação</option>
                            <option value="codigo">Código Fonte</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="3" placeholder="Descreva brevemente o conteúdo do documento"></textarea>
                    </div>
                    <div class="upload-area mb-3">
                        <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                        <p>Clique para fazer upload ou arraste o arquivo aqui</p>
                        <input type="file" name="documento" class="d-none" required>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Enviar Documento
                    </button>
                </form>
            </div>
        </div>

        <!-- Documents List -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Meus Documentos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($documentos)): ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Nenhum documento encontrado.
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($documentos as $doc): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card document-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($doc['nome']); ?></h6>
                                                <small class="text-muted"><?php echo ucfirst($doc['tipo']); ?></small>
                                            </div>
                                        </div>
                                        <p class="card-text small">
                                            <?php echo htmlspecialchars($doc['descricao'] ?? 'Sem descrição'); ?>
                                        </p>
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($doc['data_upload'])); ?>
                                            </small>
                                        </div>
                                        <div class="mt-3">
                                            <a href="visualizar-documento.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-primary me-2">
                                                <i class="fas fa-eye me-1"></i>Visualizar
                                            </a>
                                            <a href="processar-download.php?id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Drag and drop functionality
        const uploadArea = document.querySelector('.upload-area');
        const fileInput = uploadArea.querySelector('input[type="file"]');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#3498db';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#ddd';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#ddd';
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                const fileName = e.dataTransfer.files[0].name;
                uploadArea.querySelector('p').textContent = `Arquivo selecionado: ${fileName}`;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                const fileName = fileInput.files[0].name;
                uploadArea.querySelector('p').textContent = `Arquivo selecionado: ${fileName}`;
            }
        });
    </script>
</body>
</html>
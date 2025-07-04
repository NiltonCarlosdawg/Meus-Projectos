<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar temas disponíveis
$stmt = $conn->prepare("SELECT * FROM temas WHERE status = 'disponivel' ORDER BY data_cadastro DESC");
$stmt->execute();
$temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar projeto atual do aluno
$stmt = $conn->prepare("SELECT * FROM projetos WHERE estudante_id = ? ORDER BY data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escolha do Tema - SISTEMA DE  GESTÃO DE PAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .tema-card {
            transition: transform 0.2s;
            cursor: pointer;
        }
        .tema-card:hover {
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
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Escolha do Tema</h1>
                </div>

                <?php if ($projeto): ?>
                    <div class="alert alert-info">
                        <h5>Tema Atual</h5>
                        <p><strong>Título:</strong> <?php echo htmlspecialchars($projeto['titulo']); ?></p>
                        <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($projeto['status'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Formulário de Proposta de Tema -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Propor Novo Tema</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-tema.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título do Tema</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição Detalhada</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Perfil de Licenciatura</label>
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Arraste seu arquivo PDF aqui ou clique para selecionar</p>
                                    <input type="file" name="perfil" id="fileInput" class="d-none" accept=".pdf" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submeter Proposta</button>
                        </form>
                    </div>
                </div>

                <!-- Temas Disponíveis -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Temas Disponíveis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($temas as $tema): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card tema-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($tema['titulo']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($tema['descricao']); ?></p>
                                        <form action="selecionar-tema.php" method="POST">
                                            <input type="hidden" name="tema_id" value="<?php echo $tema['id']; ?>">
                                            <button type="submit" class="btn btn-outline-primary">Selecionar Tema</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');

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
            fileInput.files = e.dataTransfer.files;
        });

        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const fileName = file.name;
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                document.querySelector('.upload-area p').textContent = `Arquivo selecionado: ${fileName} (${fileSize}MB)`;
            }
        });
    </script>
</body>
</html>
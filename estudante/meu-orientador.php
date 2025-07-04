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

// Buscar informações do estudante e seus orientadores
$stmt = $pdo->prepare("SELECT e.*, 
                             o.nome as orientador_nome,
                             o.email as orientador_email,
                             o.departamento as orientador_departamento,
                             c.nome as coorientador_nome,
                             c.email as coorientador_email,
                             c.departamento as coorientador_departamento
                      FROM estudantes e
                      LEFT JOIN usuarios o ON e.orientador_id = o.id
                      LEFT JOIN usuarios c ON e.coorientador_id = c.id
                      WHERE e.usuario_id = ?");
$stmt->execute([$user_id]);
$estudante = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$estudante) {
    $estudante = array(
        'orientador_nome' => null,
        'orientador_email' => null,
        'orientador_departamento' => null,
        'coorientador_nome' => null,
        'coorientador_email' => null,
        'coorientador_departamento' => null
    );
}

// Processar upload do Perfil de Licenciatura
if (isset($_POST['upload_perfil']) && isset($_FILES['perfil_file'])) {
    $file = $_FILES['perfil_file'];
    $projeto_id = $_POST['projeto_id'];
    
    // Verificar se é um PDF
    if ($file['type'] === 'application/pdf') {
        $upload_dir = "../uploads/estudantes/{$user_id}/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $filename = 'perfil_licenciatura_' . time() . '.pdf';
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            try {
                // Registrar o documento no banco de dados
                $stmt = $pdo->prepare("INSERT INTO documentos (projeto_id, nome, tipo, caminho) VALUES (?, ?, 'perfil_licenciatura', ?)");
                $stmt->execute([$projeto_id, 'Perfil de Licenciatura', $filepath]);
                $mensagem = 'Perfil de Licenciatura enviado com sucesso!';
                $tipo_mensagem = 'success';
            } catch (PDOException $e) {
                $mensagem = 'Erro ao registrar o documento.';
                $tipo_mensagem = 'danger';
            }
        } else {
            $mensagem = 'Erro ao fazer upload do arquivo.';
            $tipo_mensagem = 'danger';
        }
    } else {
        $mensagem = 'Por favor, envie apenas arquivos PDF.';
        $tipo_mensagem = 'danger';
    }
}

// Buscar projeto do estudante
$stmt = $pdo->prepare("SELECT * FROM projetos WHERE estudante_id = ?");
$stmt->execute([$user_id]);
$projeto = $stmt->fetch();

// Buscar documentos do projeto
if ($projeto) {
    $stmt = $pdo->prepare("SELECT * FROM documentos WHERE projeto_id = ? AND tipo = 'perfil_licenciatura' ORDER BY data_upload DESC");
    $stmt->execute([$projeto['id']]);
    $documentos = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Orientador - SISTEMATFC</title>
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
            <a href="../estudante/index.php" class="nav-link ">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
            <a href="../estudante/temas-disponiveis.php" class="nav-link ">
                <i class="fas fa-list me-2"></i> Temas Disponíveis
            </a>
            <a href="../estudante/meu-orientador.php" class="nav-link active">
                <i class="fas fa-user-tie me-2"></i> Meu Orientador
            </a>
            <a href="../estudante/entregas-progressivas.php" class="nav-link">
                <i class="fas fa-tasks me-2"></i> Entregas Progressivas
            </a>
            <a href="../estudantes/documentos.php" class="nav-link" data-bs-toggle="pill">
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
            <a class="nav-link text-danger" href="/logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Sair
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

        <h2>Meus Orientadores</h2>
        <div class="card mb-4">
            <div class="card-body">
                <?php if ($estudante['orientador_nome']): ?>
                    <h3>Orientador</h3>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($estudante['orientador_nome']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($estudante['orientador_email']); ?></p>
                    <p><strong>Departamento:</strong> <?php echo htmlspecialchars($estudante['orientador_departamento']); ?></p>
                <?php else: ?>
                    <p class="text-muted">Nenhum orientador atribuído ainda.</p>
                <?php endif; ?>

                <?php if ($estudante['coorientador_nome']): ?>
                    <h3 class="mt-4">Coorientador</h3>
                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($estudante['coorientador_nome']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($estudante['coorientador_email']); ?></p>
                    <p><strong>Departamento:</strong> <?php echo htmlspecialchars($estudante['coorientador_departamento']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($projeto): ?>
            <h2>Perfil de Licenciatura</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="mb-4">
                        <input type="hidden" name="projeto_id" value="<?php echo $projeto['id']; ?>">
                        <div class="mb-3">
                            <label for="perfil_file" class="form-label">Upload do Perfil de Licenciatura (PDF)</label>
                            <input type="file" class="form-control" id="perfil_file" name="perfil_file" accept=".pdf" required>
                        </div>
                        <button type="submit" name="upload_perfil" class="btn btn-primary">Enviar Perfil</button>
                    </form>

                    <?php if (!empty($documentos)): ?>
                        <h4>Documentos Enviados</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Data de Upload</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($documentos as $doc): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($doc['nome']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($doc['data_upload'])); ?></td>
                                            <td>
                                                <a href="<?php echo str_replace('../', '', $doc['caminho']); ?>" class="btn btn-sm btn-info" target="_blank">Visualizar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Nenhum documento enviado ainda.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                Você precisa ter um projeto registrado para fazer upload do Perfil de Licenciatura.
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
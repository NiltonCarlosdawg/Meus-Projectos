<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar informações do projeto atual do aluno e tema associado
$stmt = $conn->prepare("SELECT p.*, u.nome as orientador_nome, u.email as orientador_email, o.area_orientacao, o.titulacao, o.departamento,
    t.titulo as tema_titulo, t.descricao as tema_descricao, t.area_pesquisa as tema_area
FROM projetos p 
LEFT JOIN usuarios u ON p.orientador_id = u.id 
LEFT JOIN orientadores o ON u.id = o.usuario_id 
LEFT JOIN inscricoes_tema i ON p.estudante_id = i.estudante_id
LEFT JOIN temas_tfc t ON i.tema_id = t.id
WHERE p.estudante_id = ? ORDER BY p.data_cadastro DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar temas disponíveis
$stmt = $conn->prepare("SELECT t.*, u.nome as docente_nome 
FROM temas_tfc t 
LEFT JOIN usuarios u ON t.docente_proponente_id = u.id 
WHERE t.status = 'publicado' AND (t.data_limite_escolha >= CURDATE() OR t.data_limite_escolha IS NULL)");
$stmt->execute();
$temas_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar orientadores disponíveis
$stmt = $conn->prepare("SELECT u.*, o.area_orientacao, o.titulacao, o.departamento 
FROM usuarios u 
INNER JOIN orientadores o ON u.id = o.usuario_id 
WHERE u.tipo_usuario = 'orientador' AND u.status = TRUE AND o.disponivel_novos_orientandos = TRUE");
$stmt->execute();
$orientadores_disponiveis = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar documentos do projeto
$documentos = [];
if ($projeto) {
    $stmt = $conn->prepare("SELECT d.* FROM documentos d WHERE d.projeto_id = ? ORDER BY d.data_upload DESC");
    $stmt->execute([$projeto['id']]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Estudante - SISTEMATFC</title>
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
            <a href="./documentos.php" class="nav-link"  class="nav-link">
                <i class="fas fa-folder me-2"></i> Documentos
            </a>
            <a class="nav-link" href="./mensagem.php">
                <i class="fas fa-envelope me-2"></i>Mensagens
            </a>
            <a class="nav-link" href="historico.php">
                <i class="fas fa-history me-2"></i>Histórico
            </a>
            <a href="./pre-defesa.php" class="nav-link">
                <i class="fas fa-file-alt me-2"></i>Pré-defesa
            </a>
            <a href="./defesa-final.php" class="nav-link">
                <i class="fas fa-graduation-cap me-2"></i>Defesa Final
            </a>
            <a href="./pos-defesa.php" class="nav-link">
                <i class="fas fa-check-circle me-2"></i>Pós-defesa
            </a>
            <a href="#" class="nav-link" data-bs-toggle="pill" data-section="perfil"><!--falta o arquivo "perfil"-->
                <i class="fas fa-user me-2"></i> Perfil
            </a>
             <a href="../logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Sair
             </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="tab-content">
            <!-- Dashboard Tab -->
            <div class="tab-pane fade show active" id="dashboard">
                <h2 class="mb-4">Dashboard</h2>
                
                <!-- Status Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100 status-card">
                            <div class="card-body">
                                <h5 class="card-title">Status do Projeto</h5>
                                <p class="card-text">
                                    <?php if ($projeto): ?>
                                        <strong>Status atual:</strong> <?php echo ucfirst(str_replace('_', ' ', $projeto['status'])); ?><br>
                                        <strong>Data de início:</strong> <?php echo date('d/m/Y', strtotime($projeto['data_cadastro'])); ?><br>
                                        <?php if ($projeto['tema_titulo']): ?>
                                            <strong>Tema do TCC:</strong> <?php echo $projeto['tema_titulo']; ?><br>
                                            <strong>Área de Pesquisa:</strong> <?php echo $projeto['tema_area']; ?><br>
                                            <div class="mt-3">
                                                <a href="capitulo1.php" class="btn btn-primary btn-sm me-2">Submeter Capítulo 1</a>
                                                <a href="capitulo2.php" class="btn btn-primary btn-sm me-2">Submeter Capítulo 2</a>
                                                <a href="capitulo3.php" class="btn btn-primary btn-sm">Submeter Capítulo 3</a>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning mt-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Você ainda não selecionou um tema.
                                                <a href="temas-disponiveis.php" class="btn btn-warning btn-sm ms-2">Escolher Tema</a>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Nenhum projeto iniciado.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card status-card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Orientador</h5>
                                <p class="card-text h4"><?php echo $projeto ? $projeto['orientador_nome'] : 'Não Atribuído'; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card status-card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Tema do Projeto</h5>
                                <p class="card-text"><?php echo $projeto && $projeto['tema_titulo'] ? $projeto['tema_titulo'] : 'Nenhum tema selecionado'; ?></p>
                                <?php if ($projeto && $projeto['tema_area']): ?>
                                <small>Área: <?php echo $projeto['tema_area']; ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Atividades Recentes</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach($documentos as $doc): ?>
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo $doc['nome']; ?></h6>
                                    <small><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></small>
                                </div>
                                <p class="mb-1">Tipo: <?php echo ucfirst($doc['tipo']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submissão Inicial Tab -->
            <div class="tab-pane fade" id="submissao">
                <h2 class="mb-4">Submissão Inicial</h2>
                
                <!-- Tema Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Escolha do Tema</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-proposta.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Temas Disponíveis</label>
                                <div class="list-group">
                                    <?php foreach($temas_disponiveis as $tema): ?>
                                    <div class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($tema['titulo']); ?></h6>
                                            <small>Prazo: <?php echo $tema['data_limite_escolha'] ? date('d/m/Y', strtotime($tema['data_limite_escolha'])) : 'Sem prazo'; ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo htmlspecialchars($tema['descricao']); ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>Proponente: <?php echo htmlspecialchars($tema['docente_nome']); ?>
                                            <i class="fas fa-graduation-cap ms-2 me-1"></i>Curso: <?php echo htmlspecialchars($tema['curso']); ?>
                                            <i class="fas fa-users ms-2 me-1"></i>Vagas: <?php echo $tema['max_estudantes']; ?>
                                        </small>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="selecionarTema(<?php echo $tema['id']; ?>)">Escolher este tema</button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="mb-3">Ou proponha um novo tema</h6>
                                <input type="text" class="form-control mb-2" name="titulo" placeholder="Título do seu tema">
                                <textarea class="form-control mb-2" name="descricao" rows="3" placeholder="Descrição detalhada do tema"></textarea>
                                <select class="form-select mb-2" name="area_pesquisa">
                                    <option value="">Selecione a área de pesquisa...</option>
                                    <option value="Engenharia de Software">Engenharia de Software</option>
                                    <option value="Inteligência Artificial">Inteligência Artificial</option>
                                    <option value="Redes de Computadores">Redes de Computadores</option>
                                    <option value="Segurança da Informação">Segurança da Informação</option>
                                    <option value="Banco de Dados">Banco de Dados</option>
                                </select>
                                <div class="upload-area">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p>Faça upload do seu Perfil de Licenciatura (PDF)</p>
                                    <input type="file" name="arquivo" class="d-none" accept=".pdf">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Submeter Proposta</button>
                        </form>
                    </div>
                </div>
                
                <!-- Orientadores Disponíveis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Orientadores Disponíveis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach($orientadores_disponiveis as $orientador): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($orientador['nome']); ?></h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="fas fa-graduation-cap me-1"></i><?php echo htmlspecialchars($orientador['titulacao']); ?><br>
                                                <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($orientador['departamento']); ?><br>
                                                <i class="fas fa-book me-1"></i><?php echo htmlspecialchars($orientador['area_orientacao']); ?>
                                            </small>
                                        </p>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="solicitarOrientacao(<?php echo $orientador['id']; ?>)">
                                            Solicitar Orientação
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entregas Progressivas Tab -->
            <div class="tab-pane fade" id="entregas">
                <h2 class="mb-4">Entregas Progressivas</h2>
                
                <!-- Capítulos Progress -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Capítulo 1 - Introdução</h5>
                            </div>
                            <div class="card-body">
                                <div class="upload-area mb-3">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <p>Upload do Capítulo 1</p>
                                    <input type="file" class="d-none">
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Aguardando revisão do orientador
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Capítulo 2 - Revisão</h5>
                            </div>
                            <div class="card-body">
                                <div class="upload-area mb-3">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <p>Upload do Capítulo 2</p>
                                    <input type="file" class="d-none">
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    Pendente
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">Capítulo 3 - Metodologia</h5>
                            </div>
                            <div class="card-body">
                                <div class="upload-area mb-3">
                                    <i class="fas fa-file-upload fa-2x mb-2"></i>
                                    <p>Upload do Capítulo 3</p>
                                    <input type="file" class="d-none">
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    Pendente
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Feedback do Orientador</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <!-- Feedback items will be loaded dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Defesas Tab -->
            <div class="tab-pane fade" id="defesas">
                <h2 class="mb-4">Defesas</h2>
                
                <!-- Pré-defesa Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pré-defesa</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-calendar me-2"></i>Data: 15/12/2023</h6>
                            <h6><i class="fas fa-map-marker-alt me-2"></i>Local: Sala 101</h6>
                        </div>
                        <div class="upload-area mt-3">
                            <i class="fas fa-file-upload fa-2x mb-2"></i>
                            <p>Upload da versão corrigida pós pré-defesa</p>
                            <input type="file" class="d-none">
                        </div>
                    </div>
                </div>

                <!-- Defesa Final Section -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Defesa Final</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-calendar me-2"></i>Data: A definir</h6>
                            <h6><i class="fas fa-users me-2"></i>Banca: A definir</h6>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="upload-area">
                                    <i class="fas fa-file-word fa-2x mb-2"></i>
                                    <p>Relatório Final (Word)</p>
                                    <input type="file" class="d-none">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="upload-area">
                                    <i class="fas fa-file-pdf fa-2x mb-2"></i>
                                    <p>Relatório Final (PDF)</p>
                                    <input type="file" class="d-none">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="upload-area">
                                    <i class="fas fa-file-powerpoint fa-2x mb-2"></i>
                                    <p>Apresentação</p>
                                    <input type="file" class="d-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentos Tab -->
            <div class="tab-pane fade" id="documentos">
                <h2 class="mb-4">Documentos</h2>
                
                <div class="row">
                    <!-- Documentos Úteis -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Documentos Úteis</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt me-2"></i>Modelo de TFC
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt me-2"></i>Normas ABNT
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-file-alt me-2"></i>Guia de Formatação
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <i class="fas fa-calendar-alt me-2"></i>Cronograma TFC
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meus Documentos -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Meus Documentos</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <?php foreach($documentos as $doc): ?>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo $doc['nome']; ?></h6>
                                            <small><?php echo date('d/m/Y', strtotime($doc['data_upload'])); ?></small>
                                        </div>
                                        <small class="text-muted">Status: <?php echo ucfirst($doc['status']); ?></small>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Perfil Tab -->
        <section id="perfil" class="content-section">
            <div class="tab-pane fade" >
                <h2 class="mb-4">Meu Perfil</h2>
                
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome Completo</label>
                                    <input type="text" class="form-control" value="<?php echo $_SESSION['nome']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" value="<?php echo $_SESSION['email']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Curso</label>
                                    <input type="text" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Turno</label>
                                    <select class="form-select">
                                        <option>Manhã</option>
                                        <option>Tarde</option>
                                        <option>Noite</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="tel" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Foto de Perfil</label>
                                    <input type="file" class="form-control">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Atualizar Perfil</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ativar upload ao clicar na área de upload
        document.querySelectorAll('.upload-area').forEach(area => {
            area.addEventListener('click', () => {
                area.querySelector('input[type="file"]').click();
            });
        });

        // Exibir nome do arquivo selecionado
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', (e) => {
                const fileName = e.target.files[0].name;
                const uploadArea = input.closest('.upload-area');
                uploadArea.querySelector('p').textContent = fileName;
            });
        });
    </script>
</body>
</html>

<!-- Mensagens Tab -->
<div class="tab-pane fade" id="mensagens">
    <h2 class="mb-4">Mensagens</h2>
    
    <!-- Formulário de Envio de Mensagem -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Nova Mensagem</h5>
            <form action="enviar_mensagem.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="assunto" class="form-label">Assunto</label>
                    <input type="text" class="form-control" id="assunto" name="assunto" required>
                </div>
                <div class="mb-3">
                    <label for="mensagem" class="form-label">Mensagem</label>
                    <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="anexo" class="form-label">Anexo (opcional)</label>
                    <input type="file" class="form-control" id="anexo" name="anexo">
                </div>
                <input type="hidden" name="destinatario_id" value="<?php echo $projeto['orientador_id']; ?>">
                <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
            </form>
        </div>
    </div>

    <!-- Lista de Mensagens -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Histórico de Mensagens</h5>
            <div class="list-group">
                <?php
                // Buscar mensagens do estudante
                $stmt = $conn->prepare("SELECT m.*, u.nome as remetente_nome 
                    FROM mensagens m 
                    JOIN usuarios u ON m.remetente_id = u.id 
                    WHERE (m.remetente_id = ? OR m.destinatario_id = ?) 
                    ORDER BY m.data_envio DESC");
                $stmt->execute([$_SESSION['user_id'], $_SESSION['user_id']]);
                $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($mensagens as $mensagem): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1"><?php echo htmlspecialchars($mensagem['assunto']); ?></h6>
                            <small><?php echo date('d/m/Y H:i', strtotime($mensagem['data_envio'])); ?></small>
                        </div>
                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?></p>
                        <small>De: <?php echo htmlspecialchars($mensagem['remetente_nome']); ?></small>
                        <?php if ($mensagem['arquivo_anexo']): ?>
                            <div class="mt-2">
                                <a href="<?php echo htmlspecialchars($mensagem['arquivo_anexo']); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-paperclip"></i> Anexo
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
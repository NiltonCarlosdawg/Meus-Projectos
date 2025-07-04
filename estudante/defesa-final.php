<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar informações da defesa final do aluno
$stmt = $conn->prepare("SELECT p.*, d.data_defesa, d.hora_defesa, d.sala, d.status as defesa_status,
    u.nome as orientador_nome, u.email as orientador_email
FROM projetos p 
LEFT JOIN defesas d ON p.id = d.projeto_id
LEFT JOIN usuarios u ON p.orientador_id = u.id 
WHERE p.estudante_id = ? AND p.status = 'defesa_final'");
$stmt->execute([$_SESSION['user_id']]);
$defesa = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar membros da banca
$membros_banca = [];
if ($defesa) {
    $stmt = $conn->prepare("SELECT u.nome, u.titulacao, u.email, u.departamento 
    FROM membros_banca mb 
    JOIN usuarios u ON mb.professor_id = u.id 
    WHERE mb.defesa_id = ?");
    $stmt->execute([$defesa['id']]);
    $membros_banca = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar documentos enviados
$documentos = [];
if ($defesa) {
    $stmt = $conn->prepare("SELECT * FROM documentos WHERE projeto_id = ? AND tipo IN ('relatorio_final_word', 'relatorio_final_pdf', 'apresentacao', 'codigo_fonte') ORDER BY data_upload DESC");
    $stmt->execute([$defesa['id']]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Defesa Final - SISTEMATFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container py-4">
            <h2 class="mb-4">Defesa Final</h2>

            <?php if ($defesa): ?>
                <!-- Informações da Defesa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Detalhes da Defesa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar me-2"></i>Data: <?php echo date('d/m/Y', strtotime($defesa['data_defesa'])); ?></h6>
                                <h6><i class="fas fa-clock me-2"></i>Hora: <?php echo date('H:i', strtotime($defesa['hora_defesa'])); ?></h6>
                                <h6><i class="fas fa-map-marker-alt me-2"></i>Local: <?php echo htmlspecialchars($defesa['sala']); ?></h6>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-user-tie me-2"></i>Orientador: <?php echo htmlspecialchars($defesa['orientador_nome']); ?></h6>
                                <h6><i class="fas fa-envelope me-2"></i>Email: <?php echo htmlspecialchars($defesa['orientador_email']); ?></h6>
                                <h6><i class="fas fa-info-circle me-2"></i>Status: <?php echo ucfirst($defesa['defesa_status']); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membros da Banca -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Banca Examinadora</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($membros_banca as $membro): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($membro['nome']); ?></h6>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-graduation-cap me-1"></i><?php echo htmlspecialchars($membro['titulacao']); ?><br>
                                                    <i class="fas fa-building me-1"></i><?php echo htmlspecialchars($membro['departamento']); ?><br>
                                                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($membro['email']); ?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Upload de Documentos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Documentos para Defesa</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-upload.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Relatório Final (Word)</label>
                                    <input type="file" class="form-control" name="relatorio_word" accept=".doc,.docx">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Relatório Final (PDF)</label>
                                    <input type="file" class="form-control" name="relatorio_pdf" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Apresentação (PowerPoint/PDF)</label>
                                    <input type="file" class="form-control" name="apresentacao" accept=".ppt,.pptx,.pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Código-fonte (ZIP)</label>
                                    <input type="file" class="form-control" name="codigo_fonte" accept=".zip">
                                </div>
                            </div>
                            <input type="hidden" name="tipo" value="defesa_final">
                            <button type="submit" class="btn btn-primary">Enviar Documentos</button>
                        </form>

                        <!-- Lista de Documentos Enviados -->
                        <div class="mt-4">
                            <h6>Documentos Enviados:</h6>
                            <div class="list-group">
                                <?php foreach ($documentos as $doc): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($doc['nome']); ?></h6>
                                            <small><?php echo date('d/m/Y H:i', strtotime($doc['data_upload'])); ?></small>
                                        </div>
                                        <p class="mb-1">Status: <?php echo ucfirst($doc['status']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Confirmação de Presença -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Confirmação de Presença</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-confirmacao.php" method="POST">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirma_presenca" name="confirma_presenca" required>
                                    <label class="form-check-label" for="confirma_presenca">
                                        Confirmo minha presença na defesa final na data e horário especificados
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="aceite_regras" name="aceite_regras" required>
                                    <label class="form-check-label" for="aceite_regras">
                                        Li e aceito as regras e procedimentos da defesa final
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Confirmar Participação</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Sua defesa final ainda não foi agendada. Continue trabalhando no seu projeto e aguarde o contato do seu orientador.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
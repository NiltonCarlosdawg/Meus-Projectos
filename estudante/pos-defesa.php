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
    af.nota_final, af.observacoes,
    u.nome as orientador_nome, u.email as orientador_email
FROM projetos p 
LEFT JOIN defesas d ON p.id = d.projeto_id
LEFT JOIN avaliacoes_finais af ON p.id = af.projeto_id
LEFT JOIN usuarios u ON p.orientador_id = u.id 
WHERE p.estudante_id = ? AND p.status = 'concluido'");
$stmt->execute([$_SESSION['user_id']]);
$defesa = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar documentos enviados
$documentos = [];
if ($defesa) {
    $stmt = $conn->prepare("SELECT * FROM documentos WHERE projeto_id = ? AND tipo IN ('versao_definitiva', 'submissao_repositorio') ORDER BY data_upload DESC");
    $stmt->execute([$defesa['id']]);
    $documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pós-defesa - SISTEMA DE GESTÃO DE PAP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container py-4">
            <h2 class="mb-4">Pós-defesa</h2>

            <?php if ($defesa && $defesa['defesa_status'] === 'concluido'): ?>
                <!-- Nota Final e Observações -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Resultado da Defesa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-star me-2"></i>Nota Final: <?php echo number_format($defesa['nota_final'], 1); ?></h6>
                                <h6><i class="fas fa-calendar me-2"></i>Data da Defesa: <?php echo date('d/m/Y', strtotime($defesa['data_defesa'])); ?></h6>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-comments me-2"></i>Observações:</h6>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($defesa['observacoes'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download da Ata -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ata de Defesa</h5>
                    </div>
                    <div class="card-body">
                        <p>Faça o download da sua ata de defesa:</p>
                        <a href="download-ata.php?id=<?php echo $defesa['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download da Ata
                        </a>
                    </div>
                </div>

                <!-- Upload de Documentos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Documentos Finais</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-upload.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Versão Definitiva (PDF)</label>
                                    <input type="file" class="form-control" name="versao_definitiva" accept=".pdf">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Submissão ao Repositório</label>
                                    <input type="file" class="form-control" name="submissao_repositorio" accept=".pdf,.zip">
                                </div>
                            </div>
                            <input type="hidden" name="tipo" value="pos_defesa">
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

            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Sua defesa ainda não foi concluída ou você ainda não realizou a defesa final.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
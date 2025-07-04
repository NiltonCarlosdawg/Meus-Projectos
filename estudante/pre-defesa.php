<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAluno()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar informações da pré-defesa do aluno
$stmt = $conn->prepare("SELECT p.*, d.data_defesa, d.hora_defesa, d.sala, d.status as defesa_status,
    u.nome as orientador_nome, u.email as orientador_email
FROM projetos p 
LEFT JOIN defesas d ON p.id = d.projeto_id
LEFT JOIN usuarios u ON p.orientador_id = u.id 
WHERE p.estudante_id = ? AND p.status = 'pre_defesa'");
$stmt->execute([$_SESSION['user_id']]);
$pre_defesa = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar membros da banca
$membros_banca = [];
if ($pre_defesa) {
    $stmt = $conn->prepare("SELECT u.nome, u.titulacao, u.email 
    FROM membros_banca mb 
    JOIN usuarios u ON mb.professor_id = u.id 
    WHERE mb.defesa_id = ?");
    $stmt->execute([$pre_defesa['id']]);
    $membros_banca = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar observações da banca
$observacoes = [];
if ($pre_defesa) {
    $stmt = $conn->prepare("SELECT * FROM observacoes_banca WHERE defesa_id = ? ORDER BY data_registro DESC");
    $stmt->execute([$pre_defesa['id']]);
    $observacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pré-defesa - SISTEMATFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container py-4">
            <h2 class="mb-4">Pré-defesa</h2>

            <?php if ($pre_defesa): ?>
                <!-- Informações da Pré-defesa -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Detalhes da Pré-defesa</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-calendar me-2"></i>Data: <?php echo date('d/m/Y', strtotime($pre_defesa['data_defesa'])); ?></h6>
                                <h6><i class="fas fa-clock me-2"></i>Hora: <?php echo date('H:i', strtotime($pre_defesa['hora_defesa'])); ?></h6>
                                <h6><i class="fas fa-map-marker-alt me-2"></i>Local: <?php echo htmlspecialchars($pre_defesa['sala']); ?></h6>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-user-tie me-2"></i>Orientador: <?php echo htmlspecialchars($pre_defesa['orientador_nome']); ?></h6>
                                <h6><i class="fas fa-envelope me-2"></i>Email: <?php echo htmlspecialchars($pre_defesa['orientador_email']); ?></h6>
                                <h6><i class="fas fa-info-circle me-2"></i>Status: <?php echo ucfirst($pre_defesa['defesa_status']); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Membros da Banca -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Membros da Banca</h5>
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

                <!-- Upload da Versão Corrigida -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Upload da Versão Corrigida</h5>
                    </div>
                    <div class="card-body">
                        <form action="processar-upload.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="arquivo" class="form-label">Selecione o arquivo corrigido (PDF)</label>
                                <input type="file" class="form-control" id="arquivo" name="arquivo" accept=".pdf" required>
                            </div>
                            <div class="mb-3">
                                <label for="comentarios" class="form-label">Comentários sobre as correções</label>
                                <textarea class="form-control" id="comentarios" name="comentarios" rows="3"></textarea>
                            </div>
                            <input type="hidden" name="tipo" value="pre_defesa_corrigida">
                            <button type="submit" class="btn btn-primary">Enviar Versão Corrigida</button>
                        </form>
                    </div>
                </div>

                <!-- Observações e Recomendações da Banca -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Observações e Recomendações da Banca</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($observacoes)): ?>
                            <p class="text-muted">Nenhuma observação registrada ainda.</p>
                        <?php else: ?>
                            <div class="list-group">
                                <?php foreach ($observacoes as $obs): ?>
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($obs['titulo']); ?></h6>
                                            <small><?php echo date('d/m/Y', strtotime($obs['data_registro'])); ?></small>
                                        </div>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars($obs['descricao'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Sua pré-defesa ainda não foi agendada. Aguarde o contato do seu orientador.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
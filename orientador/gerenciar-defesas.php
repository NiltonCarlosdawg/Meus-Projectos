<?php
require_once '../config/config.php';
require_once '../includes/auth_check.php';
require_once '../config/database.php';

// Verificar se é um orientador
if ($_SESSION['tipo_usuario'] !== 'orientador') {
    header('Location: ' . BASE_URL);
    exit();
}

$orientador_id = $_SESSION['user_id'];

// Buscar defesas dos orientandos
$stmt = $pdo->prepare("
    SELECT 
        d.id as defesa_id,
        d.data_defesa,
        d.hora_defesa,
        d.sala,
        d.status as defesa_status,
        p.id as projeto_id,
        p.titulo as projeto_titulo,
        u.nome as estudante_nome,
        GROUP_CONCAT(DISTINCT mb_u.nome) as membros_banca
    FROM defesas d
    INNER JOIN projetos p ON d.projeto_id = p.id
    INNER JOIN usuarios u ON p.estudante_id = u.id
    LEFT JOIN membros_banca mb ON d.id = mb.defesa_id
    LEFT JOIN usuarios mb_u ON mb.professor_id = mb_u.id
    WHERE p.orientador_id = ?
    GROUP BY d.id
    ORDER BY d.data_defesa DESC, d.hora_defesa ASC
");

$stmt->execute([$orientador_id]);
$defesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Defesas - <?php echo APP_NAME; ?></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/defesas.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1>Gerenciar Defesas</h1>
                </div>

                <!-- Lista de Defesas -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Defesas Agendadas</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Estudante</th>
                                        <th>Projeto</th>
                                        <th>Data</th>
                                        <th>Hora</th>
                                        <th>Local</th>
                                        <th>Banca</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($defesas as $defesa): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($defesa['estudante_nome']); ?></td>
                                            <td><?php echo htmlspecialchars($defesa['projeto_titulo']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($defesa['data_defesa'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($defesa['hora_defesa'])); ?></td>
                                            <td><?php echo htmlspecialchars($defesa['sala']); ?></td>
                                            <td><?php echo htmlspecialchars($defesa['membros_banca']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $defesa['defesa_status'] == 'aprovado' ? 'success' : ($defesa['defesa_status'] == 'reprovado' ? 'danger' : 'warning'); ?>">
                                                    <?php echo ucfirst($defesa['defesa_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            onclick="avaliarDefesa(<?php echo $defesa['projeto_id']; ?>)">
                                                        <i class="fas fa-star me-1"></i>Avaliar
                                                    </button>
                                                    <a href="../admin/gerar-ata.php?defesa_id=<?php echo $defesa['defesa_id']; ?>" 
                                                       class="btn btn-sm btn-secondary">
                                                        <i class="fas fa-file-alt me-1"></i>Ata
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal de Avaliação -->
                <div class="modal fade" id="avaliacaoModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Avaliar Defesa</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formAvaliacao">
                                    <input type="hidden" name="projeto_id" id="projeto_id">
                                    <div class="mb-3">
                                        <label for="nota" class="form-label">Nota Final</label>
                                        <input type="number" class="form-control" id="nota" name="nota" 
                                               min="0" max="10" step="0.1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="parecer" class="form-label">Parecer</label>
                                        <textarea class="form-control" id="parecer" name="parecer" 
                                                  rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status da Defesa</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="aprovado">Aprovado</option>
                                            <option value="reprovado">Reprovado</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                <button type="button" class="btn btn-primary" onclick="salvarAvaliacao()">Salvar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script>
        function avaliarDefesa(projetoId) {
            $('#projeto_id').val(projetoId);
            $('#avaliacaoModal').modal('show');
        }

        function salvarAvaliacao() {
            const formData = new FormData(document.getElementById('formAvaliacao'));
            
            fetch('processar_avaliacao_final.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Avaliação registrada com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao registrar avaliação: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao processar a requisição');
            });

            $('#avaliacaoModal').modal('hide');
        }
    </script>
</body>
</html>
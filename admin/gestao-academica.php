<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

// Verifica se o usuário está logado e é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';
$db = new Database();
$conn = $db->getConnection();

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'atribuir_orientador':
                $estudante_id = $_POST['estudante_id'];
                $orientador_id = $_POST['orientador_id'];
                
                $sql = "UPDATE estudantes SET orientador_id = ? WHERE usuario_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$orientador_id, $estudante_id])) {
                    $success = 'Orientador atribuído com sucesso!';
                } else {
                    $error = 'Erro ao atribuir orientador.';
                }
                break;

            case 'atribuir_coorientador':
                $estudante_id = $_POST['estudante_id'];
                $coorientador_id = $_POST['coorientador_id'];
                
                $sql = "UPDATE estudantes SET coorientador_id = ? WHERE usuario_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$coorientador_id, $estudante_id])) {
                    $success = 'Coorientador atribuído com sucesso!';
                } else {
                    $error = 'Erro ao atribuir coorientador.';
                }
                break;

            case 'atualizar_status':
                $projeto_id = $_POST['projeto_id'];
                $status = $_POST['status'];
                $observacoes = $_POST['observacoes'];
                
                $sql = "UPDATE projetos SET status = ?, observacoes = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$status, $observacoes, $projeto_id])) {
                    $success = 'Status do projeto atualizado com sucesso!';
                } else {
                    $error = 'Erro ao atualizar status do projeto.';
                }
                break;

            case 'aprovar_proposta':
                $projeto_id = $_POST['projeto_id'];
                $sql = "UPDATE projetos SET status = 'aprovado' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$projeto_id])) {
                    $success = 'Proposta aprovada com sucesso!';
                } else {
                    $error = 'Erro ao aprovar proposta.';
                }
                break;
        }
    }
}

// Buscar estudantes e seus projetos
$sql = "SELECT e.*, u.nome as estudante_nome, u2.nome as orientador_nome, u3.nome as coorientador_nome,
        p.titulo as projeto_titulo, p.status as projeto_status, p.id as projeto_id
        FROM estudantes e
        LEFT JOIN usuarios u ON e.usuario_id = u.id
        LEFT JOIN usuarios u2 ON e.orientador_id = u2.id
        LEFT JOIN usuarios u3 ON e.coorientador_id = u3.id
        LEFT JOIN projetos p ON e.usuario_id = p.estudante_id
        WHERE u.tipo_usuario = 'estudante'
        ORDER BY u.nome";
$stmt = $conn->prepare($sql);
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar orientadores disponíveis
$sql = "SELECT u.id, u.nome
        FROM usuarios u
        INNER JOIN orientadores o ON u.id = o.usuario_id
        WHERE u.tipo_usuario = 'orientador'
        AND o.disponivel_novos_orientandos = TRUE
        ORDER BY u.nome";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar coorientadores disponíveis
$sql = "SELECT u.id, u.nome
        FROM usuarios u
        INNER JOIN coorientadores c ON u.id = c.usuario_id
        WHERE u.tipo_usuario = 'coorientador'
        AND c.disponivel_novos_orientandos = TRUE
        ORDER BY u.nome";
$stmt = $conn->prepare($sql);
$stmt->execute();
$coorientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Acadêmica - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
            <div class="sidebar-header">
                <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i><?php echo APP_NAME; ?></h4>
            </div>
            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/dashboard/admin/">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/usuarios.php">
                        <i class="fas fa-users"></i>Usuários
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/temas-tfc.php">
                        <i class="fas fa-book"></i>Temas TFC
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/gestao-academica.php" class="active">
                        <i class="fas fa-user-graduate"></i>Gestão Acadêmica
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/relatorios.php">
                        <i class="fas fa-chart-bar"></i>Relatórios
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/configuracoes.php">
                        <i class="fas fa-cog"></i>Configurações
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/logout.php">
                        <i class="fas fa-sign-out-alt"></i>Sair
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="dashboard-header">
                <h2><i class="fas fa-user-graduate me-2"></i>Gestão Acadêmica</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Lista de Estudantes e Projetos -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Estudantes e Projetos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabelaGestaoAcademica" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Estudante</th>
                                    <th>Orientador</th>
                                    <th>Coorientador</th>
                                    <th>Projeto</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudantes as $estudante): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estudante['estudante_nome']); ?></td>
                                        <td><?php echo htmlspecialchars($estudante['orientador_nome'] ?? 'Não atribuído'); ?></td>
                                        <td><?php echo htmlspecialchars($estudante['coorientador_nome'] ?? 'Não atribuído'); ?></td>
                                        <td><?php echo htmlspecialchars($estudante['projeto_titulo'] ?? 'Sem projeto'); ?></td>
                                        <td>
                                            <?php if ($estudante['projeto_status']): ?>
                                                <span class="badge bg-<?php echo $estudante['projeto_status'] === 'aprovado' ? 'success' : 'warning'; ?>">
                                                    <?php echo ucfirst($estudante['projeto_status']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sem status</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="atribuirOrientador(<?php echo $estudante['usuario_id']; ?>)">
                                                <i class="fas fa-user-plus"></i> Orientador
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="atribuirCoorientador(<?php echo $estudante['usuario_id']; ?>)">
                                                <i class="fas fa-user-plus"></i> Coorientador
                                            </button>
                                            <?php if ($estudante['projeto_id']): ?>
                                                <button class="btn btn-sm btn-warning" onclick="atualizarStatus(<?php echo $estudante['projeto_id']; ?>)">
                                                    <i class="fas fa-edit"></i> Status
                                                </button>
                                                <?php if ($estudante['projeto_status'] === 'pendente'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="aprovar_proposta">
                                                        <input type="hidden" name="projeto_id" value="<?php echo $estudante['projeto_id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Aprovar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Atribuir Orientador -->
    <div class="modal fade" id="modalOrientador" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atribuir Orientador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="atribuir_orientador">
                        <input type="hidden" name="estudante_id" id="orientador_estudante_id">
                        <div class="mb-3">
                            <label for="orientador_id" class="form-label">Selecione o Orientador</label>
                            <select class="form-select" name="orientador_id" id="orientador_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($orientadores as $orientador): ?>
                                    <option value="<?php echo $orientador['id']; ?>">
                                        <?php echo htmlspecialchars($orientador['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atribuir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Atribuir Coorientador -->
    <div class="modal fade" id="modalCoorientador" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atribuir Coorientador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="atribuir_coorientador">
                        <input type="hidden" name="estudante_id" id="coorientador_estudante_id">
                        <div class="mb-3">
                            <label for="coorientador_id" class="form-label">Selecione o Coorientador</label>
                            <select class="form-select" name="coorientador_id" id="coorientador_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($coorientadores as $coorientador): ?>
                                    <option value="<?php echo $coorientador['id']; ?>">
                                        <?php echo htmlspecialchars($coorientador['nome']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atribuir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Atualizar Status -->
    <div class="modal fade" id="modalStatus" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atualizar Status do Projeto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="atualizar_status">
                        <input type="hidden" name="projeto_id" id="status_projeto_id">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status do Projeto</label>
                            <select class="form-select" name="status" id="status" required>
                                <option value="proposta">Proposta</option>
                                <option value="pre_relatorio">Pré-Relatório</option>
                                <option value="relatorio_final">Relatório Final</option>
                                <option value="defesa">Defesa</option>
                                <option value="concluido">Concluído</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" name="observacoes" id="observacoes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tabelaGestaoAcademica').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                },
                responsive: true
            });
        });

        function atribuirOrientador(estudanteId) {
            document.getElementById('orientador_estudante_id').value = estudanteId;
            new bootstrap.Modal(document.getElementById('modalOrientador')).show();
        }

        function atribuirCoorientador(estudanteId) {
            document.getElementById('coorientador_estudante_id').value = estudanteId;
            new bootstrap.Modal(document.getElementById('modalCoorientador')).show();
        }

        function atualizarStatus(projetoId) {
            document.getElementById('status_projeto_id').value = projetoId;
            new bootstrap.Modal(document.getElementById('modalStatus')).show();
        }
    </script>
</body>
</html>
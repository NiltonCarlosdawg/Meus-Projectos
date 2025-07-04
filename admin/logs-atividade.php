<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Filtros
$usuario_id = isset($_GET['usuario_id']) ? (int)$_GET['usuario_id'] : null;
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$tipo_acao = isset($_GET['tipo_acao']) ? $_GET['tipo_acao'] : '';

// Construir query base
$sql = "SELECT l.*, u.nome as usuario_nome, u.email as usuario_email, u.tipo_usuario 
        FROM user_logs l 
        LEFT JOIN usuarios u ON l.usuario_id = u.id 
        WHERE 1=1";
$params = [];

// Aplicar filtros
if ($usuario_id) {
    $sql .= " AND l.usuario_id = ?";
    $params[] = $usuario_id;
}
if ($data_inicio) {
    $sql .= " AND DATE(l.data_registro) >= ?";
    $params[] = $data_inicio;
}
if ($data_fim) {
    $sql .= " AND DATE(l.data_registro) <= ?";
    $params[] = $data_fim;
}
if ($tipo_acao) {
    $sql .= " AND l.acao = ?";
    $params[] = $tipo_acao;
}

$sql .= " ORDER BY l.data_registro DESC";

// Executar query
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar usuários para o filtro
$stmt = $conn->query("SELECT id, nome, email FROM usuarios WHERE status = TRUE ORDER BY nome");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar tipos de ações distintas
$stmt = $conn->query("SELECT DISTINCT acao FROM user_logs ORDER BY acao");
$acoes = $stmt->fetchAll(PDO::FETCH_COLUMN);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs de Atividade - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
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
                    <a href="<?php echo BASE_URL; ?>/admin/estudantes.php">
                        <i class="fas fa-user-graduate"></i>Estudantes
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/orientadores.php">
                        <i class="fas fa-chalkboard-teacher"></i>Orientadores
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/professores.php">
                        <i class="fas fa-user-tie"></i>Professores
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/coorientadores.php">
                        <i class="fas fa-user-friends"></i>Coorientadores
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/logs-atividade.php" class="active">
                        <i class="fas fa-history"></i>Logs de Atividade
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
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Logs de Atividade</h2>
                </div>

                <!-- Filtros -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="usuario_id" class="form-label">Usuário</label>
                                <select class="form-select" id="usuario_id" name="usuario_id">
                                    <option value="">Todos</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>" <?php echo $usuario_id == $usuario['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($usuario['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="data_inicio" class="form-label">Data Início</label>
                                <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo $data_inicio; ?>">
                            </div>
                            <div class="col-md-2">
                                <label for="data_fim" class="form-label">Data Fim</label>
                                <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo $data_fim; ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="tipo_acao" class="form-label">Tipo de Ação</label>
                                <select class="form-select" id="tipo_acao" name="tipo_acao">
                                    <option value="">Todas</option>
                                    <?php foreach ($acoes as $acao): ?>
                                        <option value="<?php echo $acao; ?>" <?php echo $tipo_acao == $acao ? 'selected' : ''; ?>>
                                            <?php echo ucfirst(str_replace('_', ' ', $acao)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-2"></i>Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabela de Logs -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="logsTable">
                                <thead>
                                    <tr>
                                        <th>Data/Hora</th>
                                        <th>Usuário</th>
                                        <th>Tipo</th>
                                        <th>Ação</th>
                                        <th>Descrição</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y H:i:s', strtotime($log['data_registro'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($log['usuario_nome']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo $log['usuario_email']; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $log['tipo_usuario'] === 'admin' ? 'danger' : 
                                                        ($log['tipo_usuario'] === 'professor' ? 'primary' : 'success');
                                                ?>">
                                                    <?php echo ucfirst($log['tipo_usuario']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $log['acao'])); ?></td>
                                            <td><?php echo htmlspecialchars($log['descricao']); ?></td>
                                            <td><?php echo $log['ip_address']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#logsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                }
            });
        });
    </script>
</body>
</html>
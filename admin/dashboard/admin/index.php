<?php
require_once '../../../config/config.php';
require_once '../../../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar estatísticas gerais
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM usuarios");
$stmt->execute();
$total_usuarios = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM projetos");
$stmt->execute();
$total_projetos = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orientacoes");
$stmt->execute();
$total_orientacoes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT tipo_usuario, COUNT(*) as total FROM usuarios GROUP BY tipo_usuario");
$stmt->execute();
$estatisticas_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimos logs de atividades
$stmt = $conn->prepare("
    SELECT l.*, u.nome as usuario_nome 
    FROM user_logs l 
    LEFT JOIN usuarios u ON l.usuario_id = u.id 
    ORDER BY l.data_registro DESC 
    LIMIT 10
");
$stmt->execute();
$ultimos_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimas importações
$stmt = $conn->prepare("
    SELECT 
        l.*,
        u.nome as admin_nome,
        l.descricao as arquivo_nome,
        l.data_registro as data_importacao,
        'pendente' as status,
        0 as registros_processados,
        0 as total_registros,
        0 as registros_com_erro
    FROM user_logs l
    LEFT JOIN usuarios u ON l.usuario_id = u.id
    WHERE l.acao LIKE '%importação%'
    ORDER BY l.data_registro DESC
    LIMIT 5
");
$stmt->execute();
$ultimas_importacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../../assets/css/style.css" rel="stylesheet">
    <link href="../../../assets/css/dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .sidebar{
        width: 320px !important;

    }
</style>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
            <div class="sidebar-header">
                <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>SISTEMA DE GESTÃO DE PAP</h4>
            </div>
            <div class="sidebar-content">
                <ul class="sidebar-menu">
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/dashboard/admin/" class="active">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                    </li>
                  
                    <li>
                        <a href="#estudantes-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <i class="fas fa-user-graduate"></i>Estudantes
                            <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <ul class="collapse submenu" id="estudantes-submenu">
                            <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-estudante.php"><i class="fas fa-plus-circle"></i>Cadastrar Estudante</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/ativar-desativar-estudante.php"><i class="fas fa-toggle-on"></i>Ativar/Desativar Conta</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/atribuir-orientador.php"><i class="fas fa-user-plus"></i>Atribuir Orientador</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/consultar-projetos-estudante.php"><i class="fas fa-search"></i>Consultar Projetos</a></li>
                        </ul>
                    </li>
                    <li>
                <a href="<?php echo BASE_URL; ?>/admin/lista-tfcs.php">
                    <i class="fas fa-file-alt"></i>Lista de TFCs
                </a>
            </li>
                    <li>
                        <a href="#orientadores-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <i class="fas fa-chalkboard-teacher"></i>Docentes/Orientadores
                            <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <ul class="collapse submenu" id="orientadores-submenu">
                            <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-orientador.php"><i class="fas fa-user-plus"></i>Cadastrar Orientador</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/atualizar-dados-orientador.php"><i class="fas fa-user-edit"></i>Atualizar Dados</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/consultar-carga-orientacao.php"><i class="fas fa-tasks"></i>Consultar Carga</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#banca-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                            <i class="fas fa-users-cog"></i>Júri/Banca
                            <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                        </a>
                        <ul class="collapse submenu" id="banca-submenu">
                            <li><a href="<?php echo BASE_URL; ?>/admin/cadastrar-membro-banca.php"><i class="fas fa-user-plus"></i>Cadastrar Membro</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/associar-banca-projeto.php"><i class="fas fa-link"></i>Associar a Projeto</a></li>
                            <li><a href="<?php echo BASE_URL; ?>/admin/emitir-pauta.php"><i class="fas fa-file-alt"></i>Emitir Pauta</a></li>
                        </ul>
                    </li>
                    <!-- Outras Seções -->
            
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/gerenciar-defesas.php">
                    <i class="fas fa-tasks"></i>Gerenciar Defesas
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/estudantes-aptos.php">
                    <i class="fas fa-user-check"></i>Estudantes Aptos
                </a>
            </li>
            <li>
                <a href="<?php echo BASE_URL; ?>/admin/agendamentos.php">
                    <i class="fas fa-calendar-alt"></i>Agendamentos
                </a>
            </li>
            <li>
                <a href="#documentosSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                    <i class="fas fa-file-pdf"></i>Documentos
                    <span class="submenu-indicator"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="collapse submenu" id="documentosSubmenu">
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-ata.php"><i class="fas fa-file-alt"></i>Gerar Ata</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-pauta.php"><i class="fas fa-clipboard-list"></i>Gerar Pauta</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/gerar-convite.php"><i class="fas fa-envelope"></i>Gerar Convite</a></li>
                </ul>
            </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/coorientadores.php">
                            <i class="fas fa-users"></i>Coorientadores
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>/admin/temas-tfc.php">
                            <i class="fas fa-book"></i>Temas TFC
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
        </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Dashboard Administrativo</h2>
            <div class="d-flex align-items-center">
                <span class="me-3"><?php echo date('d/m/Y'); ?></span>
                <div class="btn-group">
                    <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-1"></i>Admin
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Total de Usuários</h6>
                            <h3 class="mb-0"><?php echo $total_usuarios; ?></h3>
                        </div>
                        <div class="icon-circle bg-white-50">
                            <i class="fas fa-users fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Total de Projetos</h6>
                            <h3 class="mb-0"><?php echo $total_projetos; ?></h3>
                        </div>
                        <div class="icon-circle bg-white-50">
                            <i class="fas fa-project-diagram fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card info">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Orientações</h6>
                            <h3 class="mb-0"><?php echo $total_orientacoes; ?></h3>
                        </div>
                        <div class="icon-circle bg-white-50">
                            <i class="fas fa-chalkboard-teacher fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">Projetos Ativos</h6>
                            <h3 class="mb-0">75%</h3>
                        </div>
                        <div class="icon-circle bg-white-50">
                            <i class="fas fa-chart-pie fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-xl-8 col-lg-7">
                <div class="chart-container">
                    <h5 class="card-title mb-3">Distribuição de Usuários</h5>
                    <canvas id="userDistributionChart"></canvas>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5">
                <div class="chart-container">
                    <h5 class="card-title mb-3">Tipos de Usuários</h5>
                    <canvas id="userTypePieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Últimas Atividades e Importações -->
        <div class="row">
            <!-- Últimos Logs -->
            <div class="col-md-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Últimas Atividades</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Usuário</th>
                                        <th>Ação</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($ultimos_logs as $log): ?>
                                        <tr>
                                            <td><?php echo $log['usuario_nome']; ?></td>
                                            <td><?php echo $log['acao']; ?></td>
                                            <td><?php echo formatDate($log['data_registro']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimas Importações -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Últimas Importações</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($ultimas_importacoes as $importacao): ?>
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1"><?php echo $importacao['arquivo_nome']; ?></h6>
                                        <small class="text-muted">
                                            Por: <?php echo $importacao['admin_nome']; ?><br>
                                            <?php echo formatDate($importacao['data_importacao']); ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?php 
                                        echo $importacao['status'] === 'concluido' ? 'success' : 
                                            ($importacao['status'] === 'erro' ? 'danger' : 'warning');
                                    ?>">
                                        <?php echo ucfirst($importacao['status']); ?>
                                    </span>
                                </div>
                                <div class="mt-2">
                                    <form method="post" action="../../../admin/ativar-usuarios-importados.php" style="display:inline;">
                                        <input type="hidden" name="importacao_id" value="<?php echo $importacao['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Deseja ativar todos os usuários importados deste arquivo?');">
                                            <i class="fas fa-user-check"></i> Ativar Usuários
                                        </button>
                                    </form>
                                </div>
                                <?php if ($importacao['status'] !== 'pendente'): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Processados: <?php echo $importacao['registros_processados']; ?>/<?php echo $importacao['total_registros']; ?>
                                            <?php if ($importacao['registros_com_erro'] > 0): ?>
                                                (<?php echo $importacao['registros_com_erro']; ?> erros)
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Ações Rápidas -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Criar Usuários</h5>
                        <p class="card-text">Adicione novos estudantes, docentes e avaliadores ao sistema.</p>
                        <a href="<?php echo BASE_URL; ?>/admin/usuarios.php?action=create" class="btn btn-primary w-100">Criar Usuário</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-users-cog fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Gerenciar Usuários</h5>
                        <p class="card-text">Edite informações, gerencie permissões e status dos usuários.</p>
                        <a href="<?php echo BASE_URL; ?>/admin/usuarios.php" class="btn btn-info w-100 text-white">Gerenciar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-file-import fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Importar Usuários</h5>
                        <p class="card-text">Importe múltiplos usuários através de arquivo CSV/Excel.</p>
                        <a href="<?php echo BASE_URL; ?>/admin/gerenciar-usuarios.php" class="btn btn-success w-100">Importar</a>
                    </div>
                </div>
            </div>
        </div>

       

    </main>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../assets/js/main.js"></script>
    <script>
        // Gráfico de Distribuição de Usuários por Tipo
        const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(userDistributionCtx, {
            type: 'bar',
            data: {
                labels: ['Estudantes', 'Professores', 'Orientadores', 'Administradores'],
                datasets: [{
                    label: 'Total por Tipo',
                    data: [<?php echo implode(',', array_map(function($stat) { return $stat['total']; }, $estatisticas_usuarios)); ?>],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    borderRadius: 5
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Pizza - Tipos de Usuários
        const userTypeCtx = document.getElementById('userTypePieChart').getContext('2d');
        new Chart(userTypeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Estudantes', 'Professores', 'Orientadores', 'Administradores'],
                datasets: [{
                    data: [45, 25, 20, 10],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html>
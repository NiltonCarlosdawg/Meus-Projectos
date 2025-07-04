<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once 'includes/user_functions.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/login.php');
}

$database = new Database();
$pdo = $database->getConnection();

// Buscar defesas agendadas
try {
    $stmt = $pdo->prepare("SELECT d.id, d.data_defesa, d.hora_defesa, e.nome AS estudante, o.nome AS orientador, d.local, d.status,
        (SELECT GROUP_CONCAT(u.nome SEPARATOR ', ') FROM membros_banca mb JOIN usuarios u ON mb.usuario_id = u.id WHERE mb.defesa_id = d.id) AS banca
        FROM defesas d
        LEFT JOIN projetos p ON d.projeto_id = p.id
        LEFT JOIN usuarios e ON p.estudante_id = e.id
        LEFT JOIN usuarios o ON p.orientador_id = o.id
        ORDER BY d.data_defesa DESC, d.hora_defesa DESC");
    $stmt->execute();
    $defesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $defesas = [];
    $mensagem = 'Erro ao buscar defesas: ' . htmlspecialchars($e->getMessage());
    $tipo_mensagem = 'danger';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Defesas - SISTEMATFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="../assets/css/defesas.css" rel="stylesheet">
    <style>
        .table-actions button, .table-actions a {
            margin-right: 5px;
        }
        .dashboard-container {
            min-height: 100vh;
            display: flex;
            flex-direction: row;
        }
        .main-content {
            flex: 1;
            padding: 30px 40px;
            background: #f8f9fa;
        }
        @media (max-width: 991px) {
            .main-content {
                padding: 15px 5px;
            }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
            <div class="sidebar-header">
                <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Sistema TFC</h4>
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
    <div class="main-content">
        <div class="dashboard-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-tasks me-2"></i>Gerenciar Defesas</h2>
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
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem ?? 'info'; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <a href="agendar-defesa.php" class="btn btn-success"><i class="fas fa-plus me-2"></i>Agendar Nova Defesa</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Defesas Agendadas</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($defesas)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Estudante</th>
                                    <th>Orientador</th>
                                    <th>Banca</th>
                                    <th>Local</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($defesas as $defesa): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($defesa['id']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($defesa['data_defesa']))); ?></td>
                                    <td><?php echo htmlspecialchars(substr($defesa['hora_defesa'],0,5)); ?></td>
                                    <td><?php echo htmlspecialchars($defesa['estudante']); ?></td>
                                    <td><?php echo htmlspecialchars($defesa['orientador']); ?></td>
                                    <td><?php echo htmlspecialchars($defesa['banca']); ?></td>
                                    <td><?php echo htmlspecialchars($defesa['local']); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($defesa['status'])); ?></td>
                                    <td class="table-actions">
                                        <a href="visualizar-defesa.php?id=<?php echo $defesa['id']; ?>" class="btn btn-info btn-sm" title="Visualizar"><i class="fas fa-eye"></i></a>
                                        <a href="editar-data-defesa.php?id=<?php echo $defesa['id']; ?>" class="btn btn-primary btn-sm" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="remover-defesa.php?id=<?php echo $defesa['id']; ?>" class="btn btn-danger btn-sm" title="Remover" onclick="return confirm('Tem certeza que deseja remover esta defesa?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nenhuma defesa agendada encontrada.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<footer class="bg-dark text-light py-4 mt-auto">
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> SISTEMATFC. Todos os direitos reservados.</p>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Inicializar variáveis de mensagem
$alert_type = '';
$alert_message = '';

// Processar mensagens via GET
if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'removido':
            $alert_type = 'success';
            $alert_message = 'Estudante removido com sucesso.';
            break;
        case 'status':
            $alert_type = 'success';
            $alert_message = 'Status do estudante atualizado com sucesso.';
            break;
            case 'erro':
    $alert_type = 'danger';
    $alert_message = 'Ocorreu um erro ao processar a operação. Por favor, tente novamente.';
    break;
    }
}

// Buscar todos os estudantes
$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.nome,
        u.email,
        u.status,
        u.data_cadastro,
        o.nome as orientador_nome,
        p.titulo as projeto_titulo,
        p.status as projeto_status
    FROM usuarios u
    LEFT JOIN projetos p ON p.estudante_id = u.id
    LEFT JOIN usuarios o ON p.orientador_id = o.id
    WHERE u.tipo_usuario = 'estudante'
    ORDER BY u.nome ASC
");
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Estudantes - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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

        <main class="main-content">
            <div class="container py-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gerenciar Estudantes</h2>
                    <a href="cadastrar-estudante.php" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Adicionar Estudante
                    </a>
                </div>

               <?php if ($alert_type && $alert_message): ?>
    <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show" role="alert">
        <?php echo $alert_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Orientador</th>
                                        <th>Projeto</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estudantes as $estudante): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($estudante['nome']); ?></td>
                                        <td><?php echo htmlspecialchars($estudante['email']); ?></td>
                                        <td><?php echo htmlspecialchars($estudante['orientador_nome'] ?? 'Não atribuído'); ?></td>
                                        <td>
                                            <?php if ($estudante['projeto_titulo']): ?>
                                                <span class="badge bg-<?php echo $estudante['projeto_status'] == 'ativo' ? 'success' : 'warning'; ?>">
                                                    <?php echo htmlspecialchars($estudante['projeto_titulo']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Sem projeto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $estudante['status'] ? 'success' : 'danger'; ?>">
                                                <?php echo $estudante['status'] ? 'Ativo' : 'Inativo'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="atribuir-orientador.php?id=<?php echo $estudante['id']; ?>" class="btn btn-sm btn-primary" title="Atribuir Orientador">
                                                    <i class="fas fa-user-plus"></i>
                                                </a>
                                                <a href="ativar-desativar-estudante.php?id=<?php echo $estudante['id']; ?>" class="btn btn-sm btn-warning" title="Ativar/Desativar">
                                                    <i class="fas fa-toggle-on"></i>
                                                </a>
                                                <a href="remover_estudante.php?id=<?php echo $estudante['id']; ?>" class="btn btn-sm btn-danger" title="Remover" onclick="return confirm('Tem certeza que deseja remover este estudante?');">
    <i class="fas fa-trash"></i>
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
            </div>
        </main>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'cadastrado'): ?>
<script>
    Swal.fire({
        title: 'Sucesso!',
        text: 'Estudante cadastrado com sucesso!',
        icon: 'success',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
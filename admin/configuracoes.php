<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

$mensagem = '';
$tipo_mensagem = '';

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Atualizar configurações
        $stmt = $conn->prepare("UPDATE configuracoes SET 
            nome_instituicao = :nome_instituicao,
            periodo_letivo = :periodo_letivo,
            data_limite_defesa = :data_limite_defesa,
            email_sistema = :email_sistema,
            notificacoes_ativas = :notificacoes_ativas,
            max_orientandos = :max_orientandos
            WHERE id = 1");

        $stmt->execute([
            'nome_instituicao' => $_POST['nome_instituicao'],
            'periodo_letivo' => $_POST['periodo_letivo'],
            'data_limite_defesa' => $_POST['data_limite_defesa'],
            'email_sistema' => $_POST['email_sistema'],
            'notificacoes_ativas' => isset($_POST['notificacoes_ativas']) ? 1 : 0,
            'max_orientandos' => $_POST['max_orientandos']
        ]);

        $mensagem = 'Configurações atualizadas com sucesso!';
        $tipo_mensagem = 'success';
    } catch (PDOException $e) {
        $mensagem = 'Erro ao atualizar as configurações: ' . $e->getMessage();
        $tipo_mensagem = 'danger';
    }
}

// Buscar configurações atuais
$stmt = $conn->query("SELECT * FROM configuracoes WHERE id = 1");
$config = $stmt->fetch(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%) !important;">
            <div class="sidebar-header" style="background-color: #91530c !Important">
                <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i><?php echo APP_NAME; ?></h4>
            </div>
            <ul class="sidebar-menu" >
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
                    <a href="<?php echo BASE_URL; ?>/admin/relatorios.php">
                        <i class="fas fa-chart-bar"></i>Relatórios
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/configuracoes.php" class="active">
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
+        <!-- Sidebar -->
        <div class="sidebar" style="background-color: #91530c !Important">
            <div class="sidebar-header" style="background-color: #91530c !Important">
                <h4 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>SISTEMA DE GESTÃO DE PAP</h4>
            </div>
            <div class="sidebar-content" style="background-color: #91530c !Important">
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
            <div class="container-fluid">
                <h2 class="mb-4">Configurações do Sistema</h2>

                <?php if ($mensagem): ?>
                <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensagem; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="nome_instituicao" class="form-label">Nome da Instituição</label>
                                <input type="text" class="form-control" id="nome_instituicao" name="nome_instituicao" 
                                       value="<?php echo htmlspecialchars($config['nome_instituicao'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="periodo_letivo" class="form-label">Período Letivo Atual</label>
                                <input type="text" class="form-control" id="periodo_letivo" name="periodo_letivo" 
                                       value="<?php echo htmlspecialchars($config['periodo_letivo'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="data_limite_defesa" class="form-label">Data Limite para Defesas</label>
                                <input type="date" class="form-control" id="data_limite_defesa" name="data_limite_defesa" 
                                       value="<?php echo $config['data_limite_defesa'] ?? ''; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email_sistema" class="form-label">E-mail do Sistema</label>
                                <input type="email" class="form-control" id="email_sistema" name="email_sistema" 
                                       value="<?php echo htmlspecialchars($config['email_sistema'] ?? ''); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="max_orientandos" class="form-label">Número Máximo de Orientandos por Orientador</label>
                                <input type="number" class="form-control" id="max_orientandos" name="max_orientandos" 
                                       value="<?php echo htmlspecialchars($config['max_orientandos'] ?? '5'); ?>" required min="1">
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="notificacoes_ativas" name="notificacoes_ativas" 
                                       <?php echo ($config['notificacoes_ativas'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="notificacoes_ativas">Ativar Notificações por E-mail</label>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Configurações
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
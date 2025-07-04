<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar estudantes aptos para defesa (com projeto em andamento)
$stmt = $conn->prepare("
    SELECT e.id, e.nome, e.matricula, o.nome as orientador, p.id as projeto_id, p.titulo
    FROM usuarios e
    INNER JOIN projetos p ON e.id = p.estudante_id
    INNER JOIN usuarios o ON p.orientador_id = o.id
    WHERE e.tipo_usuario = 'estudante'
    AND p.status = 'em_andamento'
    AND NOT EXISTS (SELECT 1 FROM defesas d WHERE d.projeto_id = p.id)
    ORDER BY e.nome");
$stmt->execute();
$estudantes_aptos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar professores disponíveis para banca
$stmt = $conn->prepare("
    SELECT id, nome, departamento
    FROM usuarios
    WHERE (tipo_usuario = 'professor' OR tipo_usuario = 'orientador')
    AND status = TRUE
    ORDER BY nome");
$stmt->execute();
$professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Defesa - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
-      <!-- Sidebar -->
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
            <div class="container-fluid px-4">
                <h1 class="mt-4">Agendar Defesa</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="gerenciar-defesas.php">Gerenciar Defesas</a></li>
                    <li class="breadcrumb-item active">Agendar Defesa</li>
                </ol>

                <?php if (isset($_GET['msg'])): ?>
                    <?php if ($_GET['msg'] == 'sucesso'): ?>
                        <div class="alert alert-success" role="alert">
                            Defesa agendada com sucesso!
                        </div>
                    <?php elseif ($_GET['msg'] == 'erro'): ?>
                        <div class="alert alert-danger" role="alert">
                            Erro ao agendar defesa. Por favor, tente novamente.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-calendar-plus me-1"></i>
                        Agendar Nova Defesa
                    </div>
                    <div class="card-body">
                        <form action="processar-agendamento-defesa.php" method="POST" id="formAgendarDefesa">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="estudante" class="form-label">Estudante</label>
                                    <select class="form-select" name="projeto_id" required>
                                        <option value="">Selecione um estudante</option>
                                        <?php foreach ($estudantes_aptos as $estudante): ?>
                                            <option value="<?php echo $estudante['projeto_id']; ?>">
                                                <?php echo htmlspecialchars($estudante['nome']); ?> - 
                                                <?php echo htmlspecialchars($estudante['matricula']); ?> - 
                                                <?php echo htmlspecialchars($estudante['titulo']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="data_defesa" class="form-label">Data da Defesa</label>
                                    <input type="date" class="form-control" name="data_defesa" required
                                           min="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="hora_defesa" class="form-label">Hora da Defesa</label>
                                    <input type="time" class="form-control" name="hora_defesa" required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="sala" class="form-label">Local/Sala</label>
                                    <input type="text" class="form-control" name="sala" required
                                           placeholder="Ex: Sala 101, Auditório, etc">
                                </div>
                                <div class="col-md-8">
                                    <label for="membros_banca" class="form-label">Membros da Banca</label>
                                    <select class="form-select" name="membros_banca[]" multiple required>
                                        <?php foreach ($professores as $professor): ?>
                                            <option value="<?php echo $professor['id']; ?>">
                                                <?php echo htmlspecialchars($professor['nome']); ?> - 
                                                <?php echo htmlspecialchars($professor['departamento']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Pressione Ctrl para selecionar múltiplos membros</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Agendar Defesa
                                    </button>
                                    <a href="gerenciar-defesas.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Validação do formulário
            $('#formAgendarDefesa').on('submit', function(e) {
                const membros = $('select[name="membros_banca[]"]').val();
                if (!membros || membros.length < 2) {
                    e.preventDefault();
                    alert('Selecione pelo menos 2 membros para a banca');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
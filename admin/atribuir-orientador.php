<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar estudantes sem orientador
$stmt = $conn->prepare("SELECT u.id, u.nome FROM usuarios u WHERE u.tipo_usuario = 'estudante' AND u.id NOT IN (SELECT p.estudante_id FROM projetos p WHERE p.orientador_id IS NOT NULL)");
$stmt->execute();
$estudantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar estudantes com orientador (para atribuir coorientador)
$stmt = $conn->prepare("SELECT u.id, u.nome FROM usuarios u INNER JOIN projetos p ON u.id = p.estudante_id WHERE u.tipo_usuario = 'estudante' AND p.orientador_id IS NOT NULL");
$stmt->execute();
$estudantes_com_orientador = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar orientadores disponíveis
$stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'orientador' AND status = 1");
$stmt->execute();
$orientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar coorientadores disponíveis
$stmt = $conn->prepare("SELECT id, nome FROM usuarios WHERE tipo_usuario = 'coorientador' AND status = 1");
$stmt->execute();
$coorientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar atribuições de orientadores existentes
$stmt = $conn->prepare("
    SELECT 
        p.id,
        e.nome as estudante_nome,
        o.nome as orientador_nome,
        p.data_cadastro as data_atribuicao
    FROM projetos p
    INNER JOIN usuarios e ON p.estudante_id = e.id
    LEFT JOIN usuarios o ON p.orientador_id = o.id
    WHERE p.orientador_id IS NOT NULL
    ORDER BY p.data_cadastro DESC
");
$stmt->execute();
$atribuicoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar atribuições de coorientadores existentes
$stmt = $conn->prepare("
    SELECT 
        e.id,
        u.nome as estudante_nome,
        c.nome as coorientador_nome,
        e.data_cadastro as data_atribuicao
    FROM estudantes e
    INNER JOIN usuarios u ON e.usuario_id = u.id
    LEFT JOIN usuarios c ON e.coorientador_id = c.id
    WHERE e.coorientador_id IS NOT NULL
    ORDER BY e.data_cadastro DESC
");
$stmt->execute();
$atribuicoes_coorientador = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar o formulário de atribuição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar qual formulário foi enviado
    $form_type = $_POST['form_type'] ?? '';
    
    if ($form_type === 'orientador') {
        $estudante_id = $_POST['estudante_id'] ?? null;
        $orientador_id = $_POST['orientador_id'] ?? null;

        if ($estudante_id && $orientador_id) {
    try {
        // Verificar se o estudante já tem orientador
        $stmt = $conn->prepare("SELECT id FROM projetos WHERE estudante_id = ? AND orientador_id IS NOT NULL");
        $stmt->execute([$estudante_id]);
        if ($stmt->rowCount() > 0) {
            $error = "Este estudante já possui um orientador atribuído.";
        } else {
            // Iniciar transação para garantir consistência dos dados
            $conn->beginTransaction();
            
            // Verificar se já existe um projeto para o estudante
            $stmt = $conn->prepare("SELECT id FROM projetos WHERE estudante_id = ?");
            $stmt->execute([$estudante_id]);
            if ($stmt->rowCount() > 0) {
                // Atualizar o projeto existente com o orientador
                $stmt = $conn->prepare("UPDATE projetos SET orientador_id = ? WHERE estudante_id = ?");
                $stmt->execute([$orientador_id, $estudante_id]);
            } else {
                // Criar um novo projeto para o estudante
                $stmt = $conn->prepare("INSERT INTO projetos (estudante_id, orientador_id, data_cadastro) VALUES (?, ?, NOW())");
                $stmt->execute([$estudante_id, $orientador_id]);
            }
            
            // Atualizar também a tabela estudantes
            $stmt = $conn->prepare("UPDATE estudantes SET orientador_id = ? WHERE usuario_id = ?");
            $stmt->execute([$orientador_id, $estudante_id]);
            
            // Confirmar transação
            $conn->commit();
            
            $success = "Orientador atribuído com sucesso!";
            // Recarregar a página para atualizar as listas
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }
    } catch (PDOException $e) {
        // Em caso de erro, reverter transação
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error = "Erro ao atribuir orientador: " . $e->getMessage();
    } catch (PDOException $e) {
        $error = "Erro ao processar a solicitação: " . $e->getMessage();
    }
} else {
    $error = "Por favor, selecione um estudante e um orientador.";
}
    } elseif ($form_type === 'coorientador') {
        $estudante_id = $_POST['estudante_coorientador_id'] ?? null;
        $coorientador_id = $_POST['coorientador_id'] ?? null;

        if ($estudante_id && $coorientador_id) {
            try {
                // Verificar se o estudante já tem coorientador
                $stmt = $conn->prepare("SELECT id FROM estudantes WHERE usuario_id = ? AND coorientador_id IS NOT NULL");
                $stmt->execute([$estudante_id]);
                if ($stmt->rowCount() > 0) {
                    $error_coorientador = "Este estudante já possui um coorientador atribuído.";
                } else {
                    // Iniciar transação para garantir consistência dos dados
                    $conn->beginTransaction();
                    
                    // Atualizar a tabela estudantes com o novo coorientador
                    $stmt = $conn->prepare("UPDATE estudantes SET coorientador_id = ?, data_cadastro = NOW() WHERE usuario_id = ?");
                    $stmt->execute([$coorientador_id, $estudante_id]);
                    
                    // Confirmar transação
                    $conn->commit();
                    
                    $success_coorientador = "Coorientador atribuído com sucesso!";
                    // Recarregar a página para atualizar as listas
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                }
            } catch (PDOException $e) {
                // Em caso de erro, reverter transação
                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }
                $error_coorientador = "Erro ao atribuir coorientador: " . $e->getMessage();
            } catch (Exception $e) {
                $error_coorientador = "Erro ao processar a solicitação: " . $e->getMessage();
            }
        } else {
            $error_coorientador = "Por favor, selecione um estudante e um coorientador.";
        }
    }
}

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuir Orientador - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
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

    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Atribuir Orientador/Coorientador</h2>
            <div class="d-flex align-items-center">
                <div class="user-info text-end me-3">
                    <p class="mb-0"><?php echo $_SESSION['nome']; ?></p>
                    <small class="text-muted">Administrador</small>
                </div>
                <img src="../assets/img/user-avatar.png" alt="Avatar" class="rounded-circle" width="40">
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Formulário de Atribuição -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Nova Atribuição de Orientador</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($success)): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $success; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="form_type" value="orientador">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="estudante_id" class="form-label">Estudante</label>
                                        <select class="form-select" id="estudante_id" name="estudante_id" required>
                                            <option value="">Selecione um estudante</option>
                                            <?php foreach ($estudantes as $estudante): ?>
                                                <option value="<?php echo $estudante['id']; ?>">
                                                    <?php echo htmlspecialchars($estudante['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="orientador_id" class="form-label">Orientador</label>
                                        <select class="form-select" id="orientador_id" name="orientador_id" required>
                                            <option value="">Selecione um orientador</option>
                                            <?php foreach ($orientadores as $orientador): ?>
                                                <option value="<?php echo $orientador['id']; ?>">
                                                    <?php echo htmlspecialchars($orientador['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Atribuir Orientador</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário de Atribuição de Coorientador -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Nova Atribuição de Coorientador</h5>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error_coorientador)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_coorientador; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($success_coorientador)): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $success_coorientador; ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <input type="hidden" name="form_type" value="coorientador">
                                <div class="row">
                                  
                                    <div class="col-md-6 mb-3">
                                        <label for="coorientador_id" class="form-label">Coorientador</label>
                                        <select class="form-select" id="coorientador_id" name="coorientador_id" required>
                                            <option value="">Selecione um coorientador</option>
                                            <?php foreach ($coorientadores as $coorientador): ?>
                                                <option value="<?php echo $coorientador['id']; ?>">
                                                    <?php echo htmlspecialchars($coorientador['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                     <div class="col-md-6 mb-3">
                                        <label for="estudante_coorientador_id" class="form-label">Estudante (com orientador)</label>
                                        <select class="form-select" id="estudante_coorientador_id" name="estudante_coorientador_id" required>
                                            <option value="">Selecione um estudante</option>
                                            <?php foreach ($estudantes_com_orientador as $estudante): ?>
                                                <option value="<?php echo $estudante['id']; ?>">
                                                    <?php echo htmlspecialchars($estudante['nome']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
 </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Atribuir Coorientador</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Atribuições Existentes -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Atribuições de Orientadores</h5>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="atribuicoesTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Orientador</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estudante</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data de Atribuição</th>
                                            <th class="text-secondary opacity-7">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($atribuicoes)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">Nenhuma atribuição encontrada.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($atribuicoes as $atribuicao): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($atribuicao['orientador_nome']); ?></h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($atribuicao['estudante_nome']); ?></p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            <?php echo date('d/m/Y', strtotime($atribuicao['data_atribuicao'])); ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="remover-orientador.php?id=<?php echo $atribuicao['id']; ?>" 
                                                           class="btn btn-link text-danger px-2 mb-0" 
                                                           onclick="return confirm('Tem certeza que deseja remover esta atribuição?')">
                                                            <i class="fas fa-times"></i> Remover
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Atribuições de Coorientadores Existentes -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Atribuições de Coorientadores</h5>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table id="atribuicoesCoorientadorTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estudante</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Coorientador</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data de Atribuição</th>
                                            <th class="text-secondary opacity-7">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($atribuicoes_coorientador)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center py-4">Nenhuma atribuição de coorientador encontrada.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($atribuicoes_coorientador as $atribuicao): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($atribuicao['estudante_nome']); ?></h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($atribuicao['coorientador_nome']); ?></p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <span class="text-secondary text-xs font-weight-bold">
                                                            <?php echo date('d/m/Y', strtotime($atribuicao['data_atribuicao'])); ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle">
                                                        <a href="remover-coorientador.php?id=<?php echo $atribuicao['id']; ?>" 
                                                           class="btn btn-link text-danger px-2 mb-0" 
                                                           onclick="return confirm('Tem certeza que deseja remover esta atribuição de coorientador?')">
                                                            <i class="fas fa-times"></i> Remover
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/sidebar.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#atribuicoesTable')) {
                $('#atribuicoesTable').DataTable().destroy();
            }
            
            $('#atribuicoesTable').DataTable({
                language: {
                    url: '../assets/js/dataTables.pt-BR.json'
                },
                order: [[2, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false
                }],
                initComplete: function() {
                    $(this).show();
                }
            });
            
            // Inicializar DataTable para atribuições de coorientadores
            if ($.fn.DataTable.isDataTable('#atribuicoesCoorientadorTable')) {
                $('#atribuicoesCoorientadorTable').DataTable().destroy();
            }
            
            $('#atribuicoesCoorientadorTable').DataTable({
                language: {
                    url: '../assets/js/dataTables.pt-BR.json'
                },
                order: [[2, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false
                }],
                initComplete: function() {
                    $(this).show();
                }
            });
        });
    </script>
</body>
</html>
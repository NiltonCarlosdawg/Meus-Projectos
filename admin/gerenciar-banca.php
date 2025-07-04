<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$success = '';
$error = '';

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'adicionar_membro':
                if (!empty($_POST['projeto_id']) && !empty($_POST['professor_id'])) {
                    try {
                        // Verificar se o professor já está na banca
                        $stmt = $conn->prepare('SELECT COUNT(*) FROM banca_avaliadora WHERE projeto_id = ? AND professor_id = ?');
                        $stmt->execute([$_POST['projeto_id'], $_POST['professor_id']]);
                        if ($stmt->fetchColumn() > 0) {
                            $error = 'Professor já é membro desta banca.';
                        } else {
                            // Verificar quantidade de membros
                            $stmt = $conn->prepare('SELECT COUNT(*) FROM banca_avaliadora WHERE projeto_id = ?');
                            $stmt->execute([$_POST['projeto_id']]);
                            if ($stmt->fetchColumn() >= 3) {
                                $error = 'A banca já possui o número máximo de membros (3).';
                            } else {
                                $stmt = $conn->prepare('INSERT INTO banca_avaliadora (projeto_id, professor_id) VALUES (?, ?)');
                                $stmt->execute([$_POST['projeto_id'], $_POST['professor_id']]);
                                $success = 'Membro adicionado à banca com sucesso!';
                            }
                        }
                    } catch (PDOException $e) {
                        $error = 'Erro ao adicionar membro à banca.';
                    }
                }
                break;

            case 'remover_membro':
                if (!empty($_POST['banca_id'])) {
                    try {
                        $stmt = $conn->prepare('DELETE FROM banca_avaliadora WHERE id = ?');
                        $stmt->execute([$_POST['banca_id']]);
                        $success = 'Membro removido da banca com sucesso!';
                    } catch (PDOException $e) {
                        $error = 'Erro ao remover membro da banca.';
                    }
                }
                break;
        }
    }
}

// Buscar projetos com suas bancas
$stmt = $conn->prepare(
    "SELECT p.id, p.titulo, d.data_defesa,
            e.numero_processo,
            u_est.nome as estudante_nome,
            u_ori.nome as orientador_nome
     FROM projetos p
     INNER JOIN estudantes e ON p.estudante_id = e.id
     INNER JOIN usuarios u_est ON e.usuario_id = u_est.id
     LEFT JOIN usuarios u_ori ON e.orientador_id = u_ori.id
     LEFT JOIN defesas d ON p.id = d.projeto_id
     WHERE p.status = 'em_andamento'
     ORDER BY d.data_defesa DESC"
);
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar todos os professores para o select de adicionar membro
$stmt = $conn->prepare(
    "SELECT u.id, u.nome, u.titulacao, u.departamento
     FROM usuarios u
     INNER JOIN professores prof ON u.id = prof.usuario_id"
);
try {
    $stmt->execute();
    $professores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Erro ao buscar professores: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $professores = [];
}

$db->closeConnection();

// Captura de mensagens via parâmetro GET
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
$alerta = '';
if ($msg === 'erro') {
    $alerta = '<div class="alert alert-danger">Ocorreu um erro ao processar a solicitação da banca.</div>';
} elseif ($msg === 'sucesso') {
    $alerta = '<div class="alert alert-success">Operação realizada com sucesso!</div>';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Bancas - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
<body>
    <?php if (!empty($alerta)) echo $alerta; ?>
    <div class="dashboard-container">
         <!-- Sidebar -->
         <div class="sidebar"style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
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

        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <h2 class="mb-4">Gestão de Bancas Avaliadoras</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Lista de Projetos e suas Bancas -->
                <?php foreach ($projetos as $projeto): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><?php echo htmlspecialchars($projeto['titulo']); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Estudante:</strong> <?php echo htmlspecialchars($projeto['estudante_nome']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Nº Processo:</strong> <?php echo htmlspecialchars($projeto['numero_processo']); ?>
                                </div>
                                <div class="col-md-4">
                                    <strong>Data Defesa:</strong> <?php echo date('d/m/Y', strtotime($projeto['data_defesa'])); ?>
                                </div>
                            </div>

                            <!-- Membros da Banca -->
                            <h6>Membros da Banca:</h6>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Titulação</th>
                                            <th>Departamento</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $conn->prepare(
                                            "SELECT b.id, u.nome, u.titulacao, u.departamento
                                             FROM banca_avaliadora b
                                             INNER JOIN usuarios u ON b.professor_id = u.id
                                             INNER JOIN professores prof ON u.id = prof.usuario_id
                                             WHERE b.projeto_id = ?"
                                        );
                                        $stmt->execute([$projeto['id']]);
                                        $membros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        foreach ($membros as $membro):
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($membro['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($membro['titulacao']); ?></td>
                                            <td><?php echo htmlspecialchars($membro['departamento']); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="remover_membro">
                                                    <input type="hidden" name="banca_id" value="<?php echo $membro['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este membro da banca?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Adicionar Membro à Banca -->
                            <?php if (count($membros) < 3): ?>
                            <form method="POST" class="row g-3 align-items-end">
                                <input type="hidden" name="action" value="adicionar_membro">
                                <input type="hidden" name="projeto_id" value="<?php echo $projeto['id']; ?>">
                                <div class="col-md-8">
                                    <label for="professor_id_<?php echo $projeto['id']; ?>" class="form-label">Adicionar Membro</label>
                                    <select class="form-select" name="professor_id" id="professor_id_<?php echo $projeto['id']; ?>" required>
                                        <option value="">Selecione um professor</option>
                                        <?php foreach ($professores as $professor): ?>
                                            <option value="<?php echo $professor['id']; ?>">
                                                <?php echo htmlspecialchars($professor['nome']); ?> - 
                                                <?php echo htmlspecialchars($professor['titulacao']); ?> - 
                                                <?php echo htmlspecialchars($professor['departamento']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Adicionar à Banca
                                    </button>
                                </div>
                            </form>
                            <?php endif; ?>

                            <?php if (count($membros) === 3): ?>
                            <div class="mt-3">
                                <a href="emitir-pauta.php?projeto_id=<?php echo $projeto['id']; ?>" class="btn btn-success">
                                    <i class="fas fa-file-pdf me-2"></i>Emitir Pauta
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
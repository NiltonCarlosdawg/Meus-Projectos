<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar todos os projetos dos estudantes
$stmt = $conn->prepare("
    SELECT d.id, d.data_defesa, d.hora_defesa, d.sala,
           e.nome as estudante_nome, e.matricula ,
           est.curso as curso,
           p.titulo as projeto_titulo,
           t.titulo as tema_titulo,
           o.nome as orientador_nome,
           d.projeto_id,
           est.numero_processo,
           GROUP_CONCAT(DISTINCT mb_u.nome SEPARATOR ', ') as membros_banca
    FROM defesas d
    LEFT JOIN projetos p ON d.projeto_id = p.id
    LEFT JOIN usuarios e ON p.estudante_id = e.id
    LEFT JOIN estudantes est ON est.usuario_id = e.id
    LEFT JOIN usuarios o ON p.orientador_id = o.id
    LEFT JOIN membros_banca mb ON d.id = mb.defesa_id
    LEFT JOIN usuarios mb_u ON mb.professor_id = mb_u.id
    LEFT JOIN inscricoes_tema i ON p.estudante_id = i.estudante_id
    LEFT JOIN temas_tfc t ON i.tema_id = t.id
    WHERE d.status != 'cancelada'
    GROUP BY d.id, d.data_defesa, d.hora_defesa, d.sala, e.nome, e.matricula, est.curso, p.titulo, t.titulo, o.nome
    ORDER BY d.data_defesa DESC, d.hora_defesa ASC");
$stmt->execute();
$defesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Projetos - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
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
        <!-- Main Content -->
        <div class="main-content">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Consulta de Projetos dos Estudantes</h2>
                    <a href="exportar-projetos-pdf.php" class="btn btn-primary">
                        <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                    </a>
                </div>

                <!-- Filtros -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form id="filtroForm" class="row g-3">
                            <div class="col-md-4">
                                <label for="filtroCurso" class="form-label">Curso</label>
                                <select class="form-select" id="filtroCurso">
                                    <option value="">Todos os Cursos</option>
                                    <option value="Engenharia Informática">Engenharia Informática</option>
                                    <option value="Engenharia de Telecomunicações">Engenharia de Telecomunicações</option>
                                    <option value="Informática de Gestão">Informática de Gestão</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filtroDataInicio" class="form-label">Data de Defesa (Início)</label>
                                <input type="date" class="form-control" id="filtroDataInicio">
                            </div>
                            <div class="col-md-4">
                                <label for="filtroDataFim" class="form-label">Data de Defesa (Fim)</label>
                                <input type="date" class="form-control" id="filtroDataFim">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tabela de Projetos -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="tabelaProjetos" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Estudante</th>
                                        <th>Curso</th>
                                        <th>Nº Processo</th>
                                        <th>Tema da Defesa</th>
                                        <th>Orientador</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($defesas as $projeto): ?>
                                    <tr>
                                        <td><?php echo isset($projeto['estudante_nome']) ? htmlspecialchars($projeto['estudante_nome']) : '-'; ?></td>
                                        <td><?php echo isset($projeto['curso']) ? htmlspecialchars($projeto['curso']) : '-'; ?></td>
                                        <td><?php echo isset($projeto['numero_processo']) ? htmlspecialchars($projeto['numero_processo']) : '-'; ?></td>
                                        <td><?php echo isset($projeto['tema_titulo']) ? htmlspecialchars($projeto['tema_titulo']) : '-'; ?></td>
                                        <td><?php echo isset($projeto['orientador_nome']) ? htmlspecialchars($projeto['orientador_nome']) : '-'; ?></td>
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
    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            var table = $('#tabelaProjetos').DataTable({
                language: {
                    url: '../assets/js/dataTables.pt-BR.json'
                },
                order: [[0, 'asc']]
            });

            // Aplicar filtros
            $('#filtroCurso, #filtroDataInicio, #filtroDataFim').on('change', function() {
                table.draw();
            });

            // Personalizar a filtragem
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var cursoSelecionado = $('#filtroCurso').val();
                    var dataInicio = $('#filtroDataInicio').val();
                    var dataFim = $('#filtroDataFim').val();
                    var curso = data[1]; // índice da coluna do curso
                    var dataDefesa = data[4].split('/').reverse().join('-'); // converter data para formato yyyy-mm-dd

                    // Filtro de curso
                    if (cursoSelecionado && curso !== cursoSelecionado) {
                        return false;
                    }

                    // Filtro de data
                    if (dataInicio && dataDefesa < dataInicio) {
                        return false;
                    }
                    if (dataFim && dataDefesa > dataFim) {
                        return false;
                    }

                    return true;
                }
            );
        });
    </script>
</body>
</html>
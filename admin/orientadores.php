<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect(path: '/');
}

$db = new Database();
$conn = $db->getConnection();

// Listar todos os orientadores ativos
$stmt = $conn->query("
    SELECT u.id, u.nome, u.email,
           (SELECT COUNT(*) FROM estudantes e WHERE e.orientador_id = u.id) as total_orientandos
    FROM usuarios u
    WHERE u.tipo_usuario = 'orientador' AND u.status = TRUE
    ORDER BY u.nome
");
$orientadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Orientadores - <?php echo APP_NAME; ?></title>
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
                    <a href="<?php echo BASE_URL; ?>/admin/estudantes.php">
                        <i class="fas fa-user-graduate"></i>Estudantes
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?>/admin/orientadores.php" class="active">
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
                    <h2>Gestão de Orientadores</h2>
                    <div class="d-flex gap-2">
                        <a href="cadastrar-orientador.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Cadastrar Orientador
                        </a>
                        <div class="btn-group">
                            <a href="exportar-orientadores.php?tipo=pdf" class="btn btn-secondary">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </a>
                            <a href="exportar-orientadores.php?tipo=excel" class="btn btn-secondary">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </a>
                            <a href="exportar-orientadores.php?tipo=csv" class="btn btn-secondary">
                                <i class="fas fa-file-csv me-2"></i>CSV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Orientadores -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="orientadoresTable">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Total de Orientandos</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orientadores as $orientador): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($orientador['nome']); ?></td>
                                            <td><?php echo htmlspecialchars($orientador['email']); ?></td>
                                            <td><?php echo $orientador['total_orientandos']; ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="cadastrar-orientador.php?id=<?php echo $orientador['id']; ?>" class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-info" title="Detalhes" onclick="verDetalhes(<?php echo $orientador['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" title="Remover" onclick="confirmarRemocao(<?php echo $orientador['id']; ?>, '<?php echo htmlspecialchars($orientador['nome'], ENT_QUOTES); ?>')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#orientadoresTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                }
            });
        });

        function verDetalhes(id) {
            // Implementar visualização de detalhes via AJAX
            $.get('get_orientador_detalhes.php?id=' + id, function(data) {
                Swal.fire({
                    title: 'Detalhes do Orientador',
                    html: data,
                    icon: 'info'
                });
            });
        }

        function confirmarRemocao(id, nome) {
            Swal.fire({
                title: 'Confirmar Remoção',
                text: `Deseja realmente remover o orientador ${nome}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `remover_orientador.php?id=${id}`;
                }
            });
        }
    </script>
</body>
</html>
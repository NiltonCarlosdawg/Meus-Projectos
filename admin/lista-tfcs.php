<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar todos os TFCs
$stmt = $conn->prepare("
    SELECT 
        p.id,
        p.titulo,
        p.descricao,
        p.status,
        p.data_cadastro,
        p.codigo_fonte_path,
        o.nome as orientador_nome,
        e.nome as estudante_nome,
        t.titulo as tema_titulo
    FROM projetos p
    LEFT JOIN usuarios e ON p.estudante_id = e.id
    LEFT JOIN usuarios o ON p.orientador_id = o.id
    LEFT JOIN inscricoes_tema i ON p.estudante_id = i.estudante_id
    LEFT JOIN temas_tfc t ON i.tema_id = t.id
    ORDER BY p.data_cadastro DESC
");
$stmt->execute();
$tfcs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Imprimir a consulta SQL
echo "<!-- Query: " . str_replace('\n', ' ', $stmt->queryString) . " -->\n";

// Debug: Imprimir os resultados
echo "<!-- Results: " . print_r($tfcs, true) . " -->\n";

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de TFCs - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .dataTables_wrapper .dataTables_length select {
            width: 60px;
        }
        .dataTables_wrapper {
            padding: 20px;
        }
        .dataTables_filter input {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 12px;
        }
        .dataTables_length select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px;
        }
        .dataTables_paginate .paginate_button {
            padding: 6px 12px;
            margin-left: 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .dataTables_paginate .paginate_button.current {
            background: #007bff;
            color: white !important;
            border-color: #007bff;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .project-title {
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            color: #344767 !important;
            padding: 8px 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include_once 'includes/sidebar.php'; ?>

    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Lista de Trabalhos de Fim de Curso</h2>
            <div class="d-flex align-items-center">
                <div class="user-info text-end me-3">
                    <p class="mb-0"><?php echo $_SESSION['nome']; ?></p>
                    <small class="text-muted">Administrador</small>
                </div>
                <img src="../assets/img/user-avatar.png" alt="Avatar" class="rounded-circle" width="40">
            </div>
        </div>

        <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Trabalhos de Fim de Curso</h5>

                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table id="tfcsTable" class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Título</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estudante</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Orientador</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Documentos</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data Submissão</th>
                                        <th class="text-secondary opacity-7">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tfcs)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Nenhum TFC encontrado.</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($tfcs as $tfc): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 project-title"><?php echo !empty($tfc['tema_titulo']) ? htmlspecialchars($tfc['tema_titulo']) : 'Tema não definido'; ?></h6>
                                                        <p class="text-sm text-primary mb-0"><?php echo htmlspecialchars($tfc['titulo']); ?></p>
                                                        <p class="text-xs text-secondary mb-0 mt-1"><?php echo substr(htmlspecialchars($tfc['descricao']), 0, 100) . '...'; ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                           
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($tfc['estudante_nome']); ?></p>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($tfc['orientador_nome']); ?></p>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="visualizar-documento.php?id=<?php echo $tfc['id']; ?>&tipo=pdf" class="btn btn-sm btn-outline-primary" title="Visualizar PDF">
                                                        <i class="fas fa-file-pdf"></i>
                                                    </a>
                                                    <a href="visualizar-documento.php?id=<?php echo $tfc['id']; ?>&tipo=doc" class="btn btn-sm btn-outline-info" title="Visualizar DOC">
                                                        <i class="fas fa-file-word"></i>
                                                    </a>
                                                    <a href="visualizar-documento.php?id=<?php echo $tfc['id']; ?>&tipo=ppt" class="btn btn-sm btn-outline-danger" title="Visualizar PPT">
                                                        <i class="fas fa-file-powerpoint"></i>
                                                    </a>
                                                    <?php if (!empty($tfc['codigo_fonte_path'])): ?>
                                                        <a href="visualizar-codigo.php?id=<?php echo $tfc['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Download Código Fonte">
                                                            <i class="fas fa-code"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <form action="processar-upload-codigo.php" method="post" enctype="multipart/form-data" class="d-inline">
                                                        <input type="hidden" name="projeto_id" value="<?php echo $tfc['id']; ?>">
                                                        <input type="file" name="codigo_fonte" class="d-none" id="codigo_fonte_<?php echo $tfc['id']; ?>" onchange="this.form.submit()">
                                                        <button type="button" class="btn btn-sm btn-outline-success" title="Upload Código Fonte" onclick="document.getElementById('codigo_fonte_<?php echo $tfc['id']; ?>').click()">
                                                            <i class="fas fa-upload"></i>
                                                        </button>
                                                    </form>
                                                    <a href="visualizar-documento.php?id=<?php echo $tfc['id']; ?>&tipo=src" class="btn btn-sm btn-outline-secondary" title="Código Fonte">
                                                        <i class="fas fa-code"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span class="badge badge-sm bg-<?php 
                                                    echo $tfc['status'] === 'aprovado' ? 'success' : 
                                                        ($tfc['status'] === 'reprovado' ? 'danger' : 
                                                        ($tfc['status'] === 'em_revisao' ? 'warning' : 'info'));
                                                ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $tfc['status'])); ?>
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-xs font-weight-bold"><?php echo date('d/m/Y', strtotime($tfc['data_cadastro'])); ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="btn-group">
                                                    <a href="visualizar-tfc.php?id=<?php echo $tfc['id']; ?>" class="btn btn-link text-dark px-2 mb-0" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="editar-tfc.php?id=<?php echo $tfc['id']; ?>" class="btn btn-link text-dark px-2 mb-0" title="Editar">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <a href="editar-data-defesa.php?id=<?php echo $tfc['id']; ?>" class="btn btn-link text-primary px-2 mb-0" title="Editar Data da Defesa">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-link text-danger px-2 mb-0" title="Excluir" 
                                                            onclick="if(confirm('Tem certeza que deseja excluir este TFC?')) window.location.href='excluir-tfc.php?id=<?php echo $tfc['id']; ?>'">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/sidebar.js"></script>
    <script>
        $(document).ready(function() {
            $('#tfcsTable').DataTable({
                language: {
                    url: '../assets/js/dataTables.pt-BR.json'
                },
                order: [[5, 'desc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
            });
        });
    </script>
</body>
</html>
<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar todos os temas TFC
$stmt = $conn->prepare("SELECT t.*, u.nome as docente_nome 
FROM temas_tfc t 
LEFT JOIN usuarios u ON t.docente_proponente_id = u.id 
ORDER BY t.data_cadastro DESC");
$stmt->execute();
$temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db->closeConnection();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Temas TFC - SISTEMATFC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }
        .status-publicado { background-color: #28a745; color: white; }
        .status-rascunho { background-color: #6c757d; color: white; }
        .status-expirado { background-color: #dc3545; color: white; }
    </style>
</head>
<body>
    <?php include_once '../includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row" style="background: linear-gradient(180deg,  #91530c 10%,rgb(133, 74, 8)  100%);">
            <?php include_once 'includes/sidebar.php'; ?>
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <h2 class="mb-4">Temas TFC Cadastrados</h2>
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="temasTfcTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Área de Pesquisa</th>
                                        <th>Docente Proponente</th>
                                        <th>Vagas</th>
                                        <th>Data Limite</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($temas as $tema): 
                                        $status_class = '';
                                        $status_text = $tema['status'];
                                        
                                        if ($tema['status'] == 'publicado') {
                                            if ($tema['data_limite_escolha'] && strtotime($tema['data_limite_escolha']) < time()) {
                                                $status_class = 'status-expirado';
                                                $status_text = 'Expirado';
                                            } else {
                                                $status_class = 'status-publicado';
                                                $status_text = 'Publicado';
                                            }
                                        } else {
                                            $status_class = 'status-rascunho';
                                            $status_text = 'Rascunho';
                                        }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($tema['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($tema['area_pesquisa']); ?></td>
                                        <td><?php echo htmlspecialchars($tema['docente_nome']); ?></td>
                                        <td><?php echo $tema['max_estudantes']; ?></td>
                                        <td>
                                            <?php 
                                            if ($tema['data_limite_escolha']) {
                                                echo date('d/m/Y', strtotime($tema['data_limite_escolha']));
                                            } else {
                                                echo 'Sem limite';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="status-badge <?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="visualizarTema(<?php echo $tema['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editarTema(<?php echo $tema['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="excluirTema(<?php echo $tema['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#temasTfcTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json'
                }
            });
        });

        function visualizarTema(id) {
            // Implementar visualização detalhada do tema
            alert('Visualizar tema ' + id);
        }

        function editarTema(id) {
            // Implementar edição do tema
            window.location.href = 'editar-tema-tfc.php?id=' + id;
        }

        function excluirTema(id) {
            if (confirm('Tem certeza que deseja excluir este tema?')) {
                // Implementar exclusão do tema
                window.location.href = 'excluir-tema-tfc.php?id=' + id;
            }
        }
    </script>
</body>
</html>
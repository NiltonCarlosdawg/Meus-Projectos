<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projeto_id = $_POST['projeto_id'];
    $nova_data_defesa = $_POST['data_defesa'];
    $hora_defesa = $_POST['hora_defesa'];
    $local_defesa = $_POST['local_defesa'];

    try {
        $stmt = $conn->prepare('UPDATE projetos SET data_defesa = ?, hora_defesa = ?, local_defesa = ? WHERE id = ?');
        $stmt->execute([$nova_data_defesa, $hora_defesa, $local_defesa, $projeto_id]);

        // Registrar log da alteração
        $usuario_id = $_SESSION['user_id'];
        $stmt = $conn->prepare('INSERT INTO logs (usuario_id, acao, detalhes) VALUES (?, ?, ?)');
        $detalhes = "Alteração da data de defesa do projeto ID: $projeto_id para $nova_data_defesa";
        $stmt->execute([$usuario_id, 'editar_data_defesa', $detalhes]);

        $_SESSION['success'] = 'Data da defesa atualizada com sucesso!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erro ao atualizar a data da defesa.';
    }

    redirect('lista-tfcs.php');
}

$projeto_id = $_GET['id'] ?? null;
if (!$projeto_id) {
    redirect('lista-tfcs.php');
}

// Buscar informações do projeto
$stmt = $conn->prepare(
    "SELECT p.*, e.nome as estudante_nome 
    FROM projetos p 
    LEFT JOIN usuarios e ON p.estudante_id = e.id 
    WHERE p.id = ?"
);
$stmt->execute([$projeto_id]);
$projeto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$projeto) {
    redirect('lista-tfcs.php');
}

$db->closeConnection();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Data da Defesa - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
</head>
  <body>
      <?php include '../includes/sidebar.php'; ?>

      <div class="main-content">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Editar Data da Defesa</h2>
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
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Projeto: <?php echo htmlspecialchars($projeto['titulo']); ?></h5>
                            <p class="text-muted">Estudante: <?php echo htmlspecialchars($projeto['estudante_nome']); ?></p>

                            <form action="editar-data-defesa.php" method="POST" class="mt-4">
                                <input type="hidden" name="projeto_id" value="<?php echo $projeto_id; ?>">
                                
                                <div class="mb-3">
                                    <label for="data_defesa" class="form-label">Data da Defesa</label>
                                    <input type="date" class="form-control" id="data_defesa" name="data_defesa" 
                                           value="<?php echo $projeto['data_defesa'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="hora_defesa" class="form-label">Hora da Defesa</label>
                                    <input type="time" class="form-control" id="hora_defesa" name="hora_defesa" 
                                           value="<?php echo $projeto['hora_defesa'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="local_defesa" class="form-label">Local da Defesa</label>
                                    <input type="text" class="form-control" id="local_defesa" name="local_defesa" 
                                           value="<?php echo htmlspecialchars($projeto['local_defesa'] ?? ''); ?>" required>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="lista-tfcs.php" class="btn btn-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/sidebar.js"></script>
    <script>
        // Validação da data de defesa
        document.getElementById('data_defesa').addEventListener('change', function() {
            const dataDefesa = new Date(this.value);
            const hoje = new Date();
            hoje.setHours(0, 0, 0, 0);

            if (dataDefesa < hoje) {
                alert('A data de defesa não pode ser anterior à data atual!');
                this.value = '';
            }
        });
    </script>
</body>
</html>
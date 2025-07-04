<?php
require_once '../config/config.php';
require_once '../config/database.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('/');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $departamento = trim($_POST['departamento'] ?? '');
    $area_especializacao = trim($_POST['area_especializacao'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (empty($nome) || empty($email) || empty($departamento) || empty($area_especializacao) || empty($senha)) {
        $error = 'Todos os campos são obrigatórios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email inválido.';
    } else {
        $db = new Database();
        $conn = $db->getConnection();

        // Verificar se o email já está em uso
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ? AND status = TRUE");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Este email já está em uso.';
        } else {
            // Inserir novo coorientador
            try {
                $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario, departamento, area_especializacao, status) VALUES (?, ?, ?, 'coorientador', ?, ?, TRUE)");
                $stmt->execute([$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $departamento, $area_especializacao]);
                $success = 'Coorientador cadastrado com sucesso!';
                // Limpar os campos após o sucesso
                $nome = $email = $departamento = $area_especializacao = '';
            } catch (PDOException $e) {
                $error = 'Erro ao cadastrar coorientador. Por favor, tente novamente.';
            }
        }
        $db->closeConnection();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Coorientador - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/dashboard.css" rel="stylesheet">
    <style>
        .form-control {
            background-color: rgba(13, 110, 253, 0.05);
            border-color: rgba(13, 110, 253, 0.2);
        }
        .form-control:focus {
            background-color: rgba(13, 110, 253, 0.1);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
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
                    <h2>Cadastrar Coorientador</h2>
                    <a href="coorientadores.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Voltar
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($nome ?? ''); ?>" required>
                                <div class="invalid-feedback">Por favor, informe o nome completo.</div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                <div class="invalid-feedback">Por favor, informe um email válido.</div>
                            </div>

                            <div class="mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" class="form-control" id="departamento" name="departamento" value="<?php echo htmlspecialchars($departamento ?? ''); ?>" required>
                                <div class="invalid-feedback">Por favor, informe o departamento.</div>
                            </div>

                            <div class="mb-3">
                                <label for="area_especializacao" class="form-label">Área de Especialização</label>
                                <input type="text" class="form-control" id="area_especializacao" name="area_especializacao" value="<?php echo htmlspecialchars($area_especializacao ?? ''); ?>" required>
                                <div class="invalid-feedback">Por favor, informe a área de especialização.</div>
                            </div>

                            <div class="mb-3">
                                <label for="senha" class="form-label">Senha</label>
                                <input type="password" class="form-control" id="senha" name="senha" required>
                                <div class="invalid-feedback">Por favor, informe uma senha.</div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Cadastrar Coorientador
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validação do formulário
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>
<?php
require_once 'config/config.php';
require_once 'config/database.php';

if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_usuario = $_POST['tipo_usuario'];

    if ($tipo_usuario === 'estudante') {
        $email = sanitizeInput($_POST['email']);
        $senha = $_POST['senha'];

        if (empty($email) || empty($senha)) {
            $error = 'Por favor, preencha todos os campos.';
        } else {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT u.id, u.nome, u.senha, u.tipo_usuario FROM usuarios u INNER JOIN estudantes e ON e.usuario_id = u.id WHERE u.email = ? AND u.tipo_usuario = 'estudante' AND u.status = TRUE");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

             
                redirect('/estudante/index.php');
            } else {
                $error = 'Email ou senha inválidos.';
            }

            $db->closeConnection();
        }
    } else {
        $email = sanitizeInput($_POST['email']);
        $senha = $_POST['senha'];

        if (empty($email) || empty($senha)) {
            $error = 'Por favor, preencha todos os campos.';
        } else {
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->prepare("SELECT id, nome, senha, tipo_usuario FROM usuarios WHERE email = ? AND tipo_usuario = ? AND status = TRUE");
            $stmt->execute([$email, $tipo_usuario]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($senha, $usuario['senha'])) {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['nome'] = $usuario['nome'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];


                
                switch($usuario['tipo_usuario']) {
                    case 'orientador':
                        redirect('/dashboard/orientador/index.php');
                        break;
                    case 'professor':
                        redirect('/admin/dashboard/professor');
                        break;
                    case 'coorientador':
                        redirect('/coorientador/index.php');
                        break;
                    case 'admin':
                        redirect('/admin/dashboard/admin');
                        break;
                    default:
                        redirect('/index.php');
                }
            } else {
                $error = 'Email ou senha inválidos.';
            }

            $db->closeConnection();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, #e3e6f0 0%, #f8fafc 100%); min-height: 100vh;">
    <div class="floating-icons">
        <i class="fas fa-book floating-icon" style="top: 10%; left: 10%;"></i>
        <i class="fas fa-graduation-cap floating-icon" style="top: 20%; left: 80%;"></i>
        <i class="fas fa-atom floating-icon" style="top: 60%; left: 15%;"></i>
        <i class="fas fa-calculator floating-icon" style="top: 70%; left: 75%;"></i>
        <i class="fas fa-microscope floating-icon" style="top: 40%; left: 90%;"></i>
        <i class="fas fa-dna floating-icon" style="top: 80%; left: 40%;"></i>
    </div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-md-8 col-lg-5">
                <div class="card shadow-lg border-0 rounded-4 login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/img/logo.jpeg" alt="Logo" style="width: 120px;">
                            <h2 class="mt-3 mb-2 fw-bold text-primary" style="color:#91530c!important;">Bem-vindo!</h2>
                            <p class="text-muted mb-4">Acesse sua conta para continuar</p>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center rounded-3 py-2 px-3 mb-4 shadow-sm"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <div class="row mb-4 g-2 justify-content-center">
                            <div class="col-6 col-md-4">
                                <div class="card h-100 user-type-card text-center py-3 px-2" data-type="estudante">
                                    <i class="fas fa-user-graduate fa-2x mb-2 text-primary"></i>
                                    <h6 class="card-title mb-0">Estudante</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card h-100 user-type-card text-center py-3 px-2" data-type="orientador">
                                    <i class="fas fa-chalkboard-teacher fa-2x mb-2 text-success"></i>
                                    <h6 class="card-title mb-0">Orientador</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card h-100 user-type-card text-center py-3 px-2" data-type="coorientador">
                                    <i class="fas fa-users-cog fa-2x mb-2 text-info"></i>
                                    <h6 class="card-title mb-0">Coorientador</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card h-100 user-type-card text-center py-3 px-2" data-type="professor">
                                    <i class="fas fa-book-reader fa-2x mb-2 text-warning"></i>
                                    <h6 class="card-title mb-0">Professor</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="card h-100 user-type-card text-center py-3 px-2" data-type="admin">
                                    <i class="fas fa-user-shield fa-2x mb-2 text-danger"></i>
                                    <h6 class="card-title mb-0">Admin</h6>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="" autocomplete="off">
                            <input type="hidden" name="tipo_usuario" id="tipo_usuario" value="">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-lg shadow-sm" id="email" name="email" placeholder="Digite seu email" required>
                            </div>
                            <div class="mb-3">
                                <label for="senha" class="form-label senha-label">Senha</label>
                                <input type="password" class="form-control form-control-lg shadow-sm" id="senha" name="senha" placeholder="Digite sua senha" required>
                            </div>
                            <button type="submit" id="btnEntrar" class="btn btn-primary w-100 py-2 fw-bold shadow" style="background:#91530c;">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.user-type-card').forEach(function(card) {
            card.addEventListener('click', function() {
                document.getElementById('tipo_usuario').value = card.getAttribute('data-type');
                document.querySelectorAll('.user-type-card').forEach(function(c) {
                    c.classList.remove('selected');
                });
                card.classList.add('selected');
            });
        });
    </script>
</body>
</html>
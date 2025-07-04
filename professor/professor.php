<?php
require_once '../../../config/config.php';
require_once '../../../config/database.php';

if (!isLoggedIn() || !isProfessor()) {
    redirect('/login.php');
}

$db = new Database();
$conn = $db->getConnection();

// Buscar dados do professor
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) AS projetos_supervisionados FROM projetos WHERE professor_id = ?");
$stmt->execute([$user_id]);
$projetos = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Professor - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include '../../../includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include '../../../includes/sidebar_professor.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <h2 class="mt-3">Bem-vindo, Professor <?php echo $_SESSION['nome']; ?></h2>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-secondary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Projetos Supervisionados</h5>
                                <p class="card-text display-4"><?php echo $projetos['projetos_supervisionados']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
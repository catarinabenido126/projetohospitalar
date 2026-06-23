<?php
session_start();

$validation_errors = [];

if (!empty($_SESSION['validation_errors'])) {
    $validation_errors = $_SESSION['validation_errors'];
    unset($_SESSION['validation_errors']);
}

$server_error = '';

if (!empty($_SESSION['server_error'])) {
    $server_error = $_SESSION['server_error'];
    unset($_SESSION['server_error']);
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediSync - Login</title>
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/1241126.css">
    <link rel="shortcut icon" href="../assets/img/logo.png" type="image/png">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="login-body">
    <header class="login-header">
        <div class="login-logo">
            <img src="../assets/img/logo.png" alt="Logo MediSync">
        </div>
        <a href="../public/index.php" class="login-home">
            <i class="fa-solid fa-house me-2"></i>
            Página Inicial
        </a>
    </header>
    <main class="login-main">
        <div class="login-card">
            <div class="text-center">
                <h2>Medi<span>Sync</span></h2>
                <p>Sistema de Gestão de Equipamentos Médicos</p>
            </div>
            <hr>
            <?php if (!empty($validation_errors)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <?php foreach ($validation_errors as $error) : ?>
                        <div><?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($server_error)) : ?>
                <div class="alert alert-danger p-2 text-center">
                    <div><?php echo htmlspecialchars($server_error); ?></div>
                </div>
            <?php endif; ?>
            <form name="formulario" action="../private/processa_login.php" method="post">
                <div class="mb-3">
                    <label for="utilizador" class="form-label">Utilizador</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" id="utilizador" name="text_username" class="form-control" placeholder="Insira o seu utilizador" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Palavra-passe</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" id="password" name="text_password" class="form-control" placeholder="Insira a sua palavra-passe" required>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="lembrar">
                        <label class="form-check-label" for="lembrar">Lembrar-me</label>
                    </div>
                    <a href="#" class="login-link" data-bs-toggle="modal" data-bs-target="#modalRecuperarPassword">
                        Esqueceu a palavra-passe?
                    </a>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>
                    Iniciar Sessão
                </button>
            </form>
            <div class="alert alert-info mt-4 mb-0">
                <i class="fa-solid fa-circle-info me-2"></i>
                <strong>Acesso restrito a utilizadores autorizados.</strong><br>
                Por favor, inicie sessão para continuar.
            </div>
        </div>
    </main>
    <footer class="login-footer">
        2026 MediSync. Todos os direitos reservados.
    </footer>
    <div class="modal fade" id="modalRecuperarPassword" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Recuperação de Palavra-passe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fa-solid fa-key fa-3x text-primary"></i>
                    </div>
                    <p>A recuperação automática de palavra-passe não se encontra disponível.</p>
                    <p class="mb-0">Caso tenha perdido as suas credenciais de acesso, contacte o administrador do sistema MediSync.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Compreendi</button>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
require_once 'includes/funcoes.php';

start_session();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../login/login.php');
    exit();
}

$username = isset($_POST['text_username']) ? $_POST['text_username'] : '';
$password = isset($_POST['text_password']) ? $_POST['text_password'] : '';

$validation_errors = [];

if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $validation_errors[] = 'O username tem que ser um email válido.';
}

if (strlen($username) < 5 || strlen($username) > 50) {
    $validation_errors[] = 'O username deve ter entre 5 e 50 caracteres.';
}

if (strlen($password) < 6 || strlen($password) > 12) {
    $validation_errors[] = 'A password deve ter entre 6 e 12 caracteres.';
}

if (!empty($validation_errors)) {
    $_SESSION['validation_errors'] = $validation_errors;
    header('Location: ../login/login.php');
    exit();
}

$result['status'] = 1;

if (!$result['status']) {
    $_SESSION['server_error'] = 'Login inválido';
    header('Location: ../login/login.php');
    exit();
}

$_SESSION['utilizador'] = $username;

header('Location: index.php');
exit();
<?php

require_once 'includes/funcoes.php';
require_once 'includes/database.php';

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

try {
    $comando = $database->prepare("
        SELECT *, AES_DECRYPT(email, :chave) AS email_decifrado
        FROM utilizadores
        WHERE AES_DECRYPT(email, :chave) = :u AND ativo = 1
    ");
    $comando->execute([
        ':chave' => MYSQL_AES_KEY,
        ':u'     => $username
    ]);
    $utilizador = $comando->fetch(PDO::FETCH_OBJ);

    if (!$utilizador || !password_verify($password, $utilizador->password_hash)) {
        $_SESSION['server_error'] = 'Credenciais inválidas. Por favor verifique o email e a palavra-passe.';
        header('Location: ../login/login.php');
        exit();
    }

    $ultimoAcessoAnterior = $utilizador->ultimo_acesso;
    $stmtUltimoAcesso = $database->prepare(
        "UPDATE utilizadores SET ultimo_acesso = NOW() WHERE id_utilizador = :id"
    );
    $stmtUltimoAcesso->execute([':id' => $utilizador->id_utilizador]);

    // Definir sessão
    $_SESSION['utilizador']    = $utilizador->email_decifrado;
    $_SESSION['id_utilizador'] = $utilizador->id_utilizador;   // necessário para registar_historico()
    $_SESSION['perfil']        = $utilizador->perfil;
    $_SESSION['ultimo_acesso'] = $ultimoAcessoAnterior;         // para mostrar na página inicial

    registar_historico(
        $database,
        'Autenticação',
        'Login',
        $utilizador->email_decifrado,
        'Login efetuado com sucesso. Perfil: ' . $utilizador->perfil
    );

} catch (PDOException $e) {
    $_SESSION['server_error'] = 'Erro ao ligar à base de dados.';
    header('Location: ../login/login.php');
    exit();
}

header('Location: index.php');
exit();
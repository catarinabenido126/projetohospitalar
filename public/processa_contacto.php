<?php
require_once __DIR__ . '/../private/includes/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$assunto = trim($_POST['assunto'] ?? '');
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($nome) || empty($assunto) || empty($mensagem) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?erro_contacto=1#contacto');
    exit();
}

try {
    $sql = "
        INSERT INTO mensagens_contacto
        (nome, email, assunto, mensagem, ativo, data_envio, created_at, updated_at)
        VALUES
        (:nome, :email, :assunto, :mensagem, 1, NOW(), NOW(), NOW())
    ";

    $query = $database->prepare($sql);
    $query->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':assunto' => $assunto,
        ':mensagem' => $mensagem
    ]);

    header('Location: index.php?contacto_enviado=1#contacto');
    exit();

} catch (PDOException $e) {
    header('Location: index.php?erro_contacto=1#contacto');
    exit();
}
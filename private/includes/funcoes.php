<?php

require_once __DIR__ . '/../../config/config.php';

function start_session()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

function check_session()
{
    return isset($_SESSION['utilizador']);
}

function redirect_if_not_logged($redirect_to = '/login/login.php')
{
    start_session();

    if (!check_session()) {
        header("Location: " . BASE_URL . $redirect_to);
        exit();
    }
}

function logout_and_redirect($redirect_to = '/login/login.php')
{
    start_session();
    session_unset();
    session_destroy();

    header("Location: " . BASE_URL . $redirect_to);
    exit();
}

function aes_encrypt($valor)
{
    $iv = substr(hash('sha256', AES_ENCRYPTION_KEY), 0, 16);
    $encriptado = openssl_encrypt($valor, AES_ENCRYPTION_METHOD, AES_ENCRYPTION_KEY, 0, $iv);
    return urlencode($encriptado);
}

function aes_decrypt($valor)
{
    $iv = substr(hash('sha256', AES_ENCRYPTION_KEY), 0, 16);
    return openssl_decrypt($valor, AES_ENCRYPTION_METHOD, AES_ENCRYPTION_KEY, 0, $iv);
}
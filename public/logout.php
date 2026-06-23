<?php
require_once '../private/includes/funcoes.php';
require_once '../private/includes/database.php';

start_session();


if (!empty($_SESSION['id_utilizador'])) {
    try {
        registar_historico(
            $database,
            'Autenticação',
            'Logout',
            $_SESSION['utilizador'] ?? null,
            'Sessão terminada.'
        );
    } catch (Exception $e) {
    }
}

logout_and_redirect('../login/login.php');
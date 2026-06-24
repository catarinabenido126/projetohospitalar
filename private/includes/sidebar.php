<?php
require_once __DIR__ . '/../../config/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$perfil_atual = $_SESSION['perfil'] ?? '';
$is_admin     = ($perfil_atual === 'Administrador');
?>
<aside class="col-md-3 col-lg-2 menu-lateral text-white p-3">
    <h4 class="titulo-menu">Menu</h4>
    <nav class="nav flex-column menu-nav">

        <a href="<?= BASE_URL ?>/private/index.php" class="nav-link">
            <i class="fa-solid fa-house"></i>
            Página Inicial
        </a>
        <a href="<?= BASE_URL ?>/private/views/dashboard/dashboard.php" class="nav-link">
            <i class="fa-solid fa-chart-line"></i>
            Dashboard
        </a>
        <a href="<?= BASE_URL ?>/private/views/equipamentos/lista.php" class="nav-link">
            <i class="fa-solid fa-stethoscope"></i>
            Equipamentos
        </a>
        <a href="<?= BASE_URL ?>/private/views/fornecedores/lista.php" class="nav-link">
            <i class="fa-solid fa-truck"></i>
            Fornecedores
        </a>
        <a href="<?= BASE_URL ?>/private/views/localizacao/lista.php" class="nav-link">
            <i class="fa-solid fa-location-dot"></i>
            Localizações
        </a>
        <?php if ($is_admin): ?>
        <a href="<?= BASE_URL ?>/private/views/gestao/gestao.php" class="nav-link">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            Gestão
        </a>
        <?php endif; ?>

    </nav>
</aside>
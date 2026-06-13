<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$nome = $_SESSION['utilizador'] ?? 'Utilizador';
?>
<header class="container-fluid barra-superior text-white">
    <div class="row align-items-center">
        <div class="col-6 d-flex align-items-center p-2">
        <a href="/private/index.php">
            <img src="/assets/img/logo.png"
                 alt="Logo da MediSync"
                 class="me-3">
        </a>
        </div>
        <div class="col-6 text-end d-flex justify-content-end align-items-center">
            <a href="/private/views/historico/historico.php"
           class="icone-historico"
           title="Histórico de alterações">
            <i class="fa-solid fa-clock-rotate-left"></i>
            </a>
            <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <?php echo htmlspecialchars($nome); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item"
                       href="#"
                       data-bs-toggle="modal"
                       data-bs-target="#modalPassword">
                        Alterar password
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item" href="/public/logout.php">Sair</a>
                </li>
            </ul>
            </div>
        </div>
    </div>
</header>
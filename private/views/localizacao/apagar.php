<?php

require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Apagar Localização</h2>
            <hr>
            <p>
                Tem a certeza que pretende apagar esta localização?
            </p>
            <div class="mb-3">
                <strong>Edifício:</strong>
                Edifício A
            </div>
            <div class="mb-3">
                <strong>Piso:</strong>
                Piso 1
            </div>
            <div class="mb-3">
                <strong>Sala:</strong>
                Sala 101
            </div>
            <div class="mb-3">
                <strong>Serviço:</strong>
                UCI
            </div>
            <div class="mb-3">
                <strong>Responsável:</strong>
                Enf. Marta Silva
            </div>
            <div class="mb-3">
                <strong>Contacto:</strong>
                912 345 678
            </div>
            <form action="lista.php" method="post">
                <button type="submit" class="btn btn-danger">
                    Confirmar eliminação
                </button>
                <a href="lista.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
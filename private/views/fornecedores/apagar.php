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
                <h2>Apagar Fornecedor</h2>
                <hr>
                <p>
                    Tem a certeza que pretende apagar este fornecedor?
                </p>
                <p><strong>Nome da empresa:</strong> Philips Healthcare</p>
                <p><strong>NIF:</strong> 501234567</p>
                <p><strong>Tipo de fornecedor:</strong> Fabricante</p>
                <p><strong>Email:</strong> geral@philips-healthcare.pt</p>
                <form action="../fornecedores/lista.html" method="post">
                    <button type="submit" class="btn btn-danger">
                        Confirmar eliminação
                    </button>
                    <a href="../fornecedores/lista.html" class="btn btn-secondary">
                        Cancelar
                    </a>
                </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
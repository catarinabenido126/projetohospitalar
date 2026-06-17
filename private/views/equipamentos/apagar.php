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
                <h2>Apagar Equipamento</h2>
                <hr>
                <p>Tem a certeza que pretende apagar este equipamento?</p>
                <p><strong>Código interno:</strong> EQ-0001</p>
                <p><strong>Designação:</strong> Monitor Multiparamétrico</p>
                <p><strong>Categoria:</strong> Monitorização</p>
                <p><strong>Estado:</strong> Ativo</p>
                <form action="../equipamentos/lista.html" method="post">
                    <button type="submit" class="btn btn-danger">
                        Confirmar eliminação
                    </button>
                    <a href="../equipamentos/lista.html" class="btn btn-secondary">
                        Cancelar
                    </a>
                </form>
            </main>
        </div>
    </div>
    <script src="../../../assets/bootstrap/bootstrap.bundle.min.js"></script>
    <div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alterar password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Password atual</label>
                    <input type="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nova password</label>
                    <input type="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirmar nova password</label>
                    <input type="password" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="alert('Password alterada com sucesso.')">
                    Guardar alteração
                </button>
            </div>
        </div>
    </div>
<?php include '../../includes/footer.php'; ?>
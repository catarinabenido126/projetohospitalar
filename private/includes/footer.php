<script src="/assets/bootstrap/bootstrap.bundle.min.js"></script>

<!-- Toast container — canto superior direito, partilhado por todas as páginas -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="toastSucesso" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-circle-check me-2"></i>
                <span id="toastSucessoMensagem">Operação realizada com sucesso.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
    <div id="toastErro" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body">
                <i class="fa-solid fa-circle-xmark me-2"></i>
                <span id="toastErroMensagem">Ocorreu um erro.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
    <div id="toastAviso" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body text-dark">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <span id="toastAvisoMensagem">Atenção.</span>
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
        </div>
    </div>
</div>

<script>
// Inicialização global de tooltips Bootstrap
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });
});

// Funções globais para mostrar toasts
function mostrarToastSucesso(mensagem) {
    document.getElementById('toastSucessoMensagem').textContent = mensagem || 'Operação realizada com sucesso.';
    bootstrap.Toast.getOrCreateInstance(document.getElementById('toastSucesso')).show();
}
function mostrarToastErro(mensagem) {
    document.getElementById('toastErroMensagem').textContent = mensagem || 'Ocorreu um erro.';
    bootstrap.Toast.getOrCreateInstance(document.getElementById('toastErro')).show();
}
function mostrarToastAviso(mensagem) {
    document.getElementById('toastAvisoMensagem').textContent = mensagem || 'Atenção.';
    bootstrap.Toast.getOrCreateInstance(document.getElementById('toastAviso')).show();
}
</script>
<div class="modal fade" id="modalPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-triangle-exclamation text-warning me-2"></i>
                    Alterar password
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center py-4">
                <i class="fa-solid fa-lock fa-3x text-secondary mb-3"></i>

                <p class="mb-0">
                    De momento não é possível realizar o seu pedido.
                </p>

                <small class="text-muted">
                    Tente novamente mais tarde ou contacte o administrador do sistema.
                </small>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
            </div>

        </div>
    </div>
</div>
</body>
</html>
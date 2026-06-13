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
            <h2>Nova Localização</h2>
            <hr>
            <form action="lista.php" method="post">
                <h4>Estrutura física</h4>
                <div class="mb-3">
                    <label for="edificio" class="form-label">
                        Edifício
                    </label>
                    <input type="text" class="form-control" id="edificio" name="edificio" required>
                </div>
                <div class="mb-3">
                    <label for="piso" class="form-label">
                        Piso
                    </label>
                    <input type="text" class="form-control" id="piso" name="piso" required>
                </div>
                <div class="mb-3">
                    <label for="sala" class="form-label">
                        Sala
                    </label>
                    <input type="text" class="form-control" id="sala" name="sala" required>
                </div>
                <div class="mb-3">
                    <label for="servico" class="form-label">
                        Tipo de localização
                    </label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="">
                            Selecione um tipo de serviço
                        </option>
                        <option>Urgência</option>
                        <option>UCI</option>
                        <option>Bloco Operatório</option>
                        <option>Consultas</option>
                        <option>Laboratório</option>
                        <option>Radiologia</option>
                        <option>Reabilitação</option>
                        <option>Armazém</option>
                    </select>
                </div>
                <h4>Responsável</h4>
                <div class="mb-3">
                    <label for="responsavel" class="form-label">
                        Responsável pela área
                    </label>
                    <input type="text" class="form-control" id="responsavel" name="responsavel">
                </div>
                <div class="mb-3">
                    <label for="contacto-loc" class="form-label">
                        Contacto
                    </label>
                    <input type="text" class="form-control" id="contacto-loc" name="contacto-loc">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <button type="submit" class="btn btn-success">
                    Guardar
                </button>
                <a href="lista.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
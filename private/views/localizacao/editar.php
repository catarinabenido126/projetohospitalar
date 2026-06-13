<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Editar Localização</h2>
            <hr>
            <form action="lista.php" method="post">
                <h4>Informação da localização</h4>
                <div class="mb-3">
                    <label for="edificio" class="form-label">Edifício</label>
                    <select class="form-select" id="edificio" name="edificio" required>
                        <option value="">Selecione o edifício</option>
                        <option selected>Edifício A</option>
                        <option>Edifício B</option>
                        <option>Edifício C</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="piso" class="form-label">Piso</label>
                    <select class="form-select" id="piso" name="piso" required>
                        <option value="">Selecione o piso</option>
                        <option>Piso 0</option>
                        <option selected>Piso 1</option>
                        <option>Piso 2</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sala" class="form-label">Sala</label>
                    <input type="text" class="form-control" id="sala" name="sala" value="Sala 101" required>
                </div>
                <div class="mb-3">
                    <label for="servico" class="form-label">Serviço</label>
                    <select class="form-select" id="servico" name="servico" required>
                        <option value="">Selecione o serviço</option>
                        <option>Urgência</option>
                        <option selected>UCI</option>
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
                    <label for="responsavel" class="form-label">Responsável</label>
                    <input type="text" class="form-control" id="responsavel" name="responsavel" value="Enf. Marta Silva">
                </div>
                <div class="mb-3">
                    <label for="contacto-loc" class="form-label">Contacto</label>
                    <input type="tel" class="form-control" id="contacto-loc" name="contacto" value="912 345 678">
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
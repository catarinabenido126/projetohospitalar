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
            <h2>Editar Fornecedor</h2>
            <hr>
            <form action="lista.php" method="post">
                <h4>Informação principal</h4>
                <div class="mb-3">
                    <label for="nomeEmpresa" class="form-label">Nome da empresa</label>
                    <input type="text" class="form-control" id="nomeEmpresa" name="nomeEmpresa" value="Philips Healthcare" required>
                </div>
                <div class="mb-3">
                    <label for="nif" class="form-label">NIF</label>
                    <input type="text" class="form-control" id="nif" name="nif" value="501234567" required>
                </div>
                <div class="mb-3">
                    <label for="tipoFornecedor" class="form-label">Tipo de fornecedor</label>
                    <select class="form-select" id="tipoFornecedor" name="tipoFornecedor" required>
                        <option value="">Selecione o tipo de fornecedor</option>
                        <option selected>Fabricante</option>
                        <option>Distribuidor</option>
                        <option>Assistência técnica</option>
                        <option>Consumíveis ou acessórios</option>
                    </select>
                </div>
                <h4>Contactos</h4>
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="telefone" name="telefone" value="222 000 100">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="geral@philips-healthcare.pt">
                </div>
                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" class="form-control" id="website" name="website" value="https://www.philips.pt">
                </div>
                <h4>Pessoa de contacto</h4>
                <div class="mb-3">
                    <label for="pessoaContacto" class="form-label">Nome da pessoa de contacto</label>
                    <input type="text" class="form-control" id="pessoaContacto" name="pessoaContacto" value="Ana Martins">
                </div>
                <div class="mb-3">
                    <label for="telefoneContacto" class="form-label">Telefone da pessoa de contacto</label>
                    <input type="tel" class="form-control" id="telefoneContacto" name="telefoneContacto" value="912 345 678">
                </div>
                <div class="mb-3">
                    <label for="emailContacto" class="form-label">Email da pessoa de contacto</label>
                    <input type="email" class="form-control" id="emailContacto" name="emailContacto" value="ana.martins@philips-healthcare.pt">
                </div>
                <h4>Morada</h4>
                <div class="mb-3">
                    <label for="morada" class="form-label">Morada</label>
                    <input type="text" class="form-control" id="morada" name="morada" value="Rua da Saúde, 120">
                </div>
                <div class="mb-3">
                    <label for="codigoPostal" class="form-label">Código postal</label>
                    <input type="text" class="form-control" id="codigoPostal" name="codigoPostal" value="4200-300">
                </div>
                <div class="mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="Porto">
                </div>
                <div class="mb-3">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" class="form-control" id="pais" name="pais" value="Portugal">
                </div>
                <h4>Observações</h4>
                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4">Não estão de serviço à quinta-feita de tarde.</textarea>
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
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Fornecedores</h2>
                <div>
                    <button type="button" class="btn btn-outline-danger me-2">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                        PDF
                    </button>
                    <button type="button" class="btn btn-outline-success me-2">
                        <i class="fa-solid fa-file-excel me-1"></i>
                        Excel
                    </button>
                    <a href="novo.php" class="btn btn-success">
                        <i class="fa-solid fa-plus me-1"></i>
                        Novo fornecedor
                    </a>
                </div>
            </div>
            <hr>
            <p class="text-muted">
                Lista resumida dos fornecedores registados. A ficha completa pode ser consultada nos detalhes.
            </p>
            <div class="mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar fornecedor</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="tipoFornecedor" class="form-label">Tipo de fornecedor</label>
                            <select class="form-select" id="tipoFornecedor" name="tipoFornecedor">
                                <option value="">Todos</option>
                                <option>Fabricante</option>
                                <option>Distribuidor</option>
                                <option>Assistência técnica</option>
                                <option>Consumíveis ou acessórios</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter me-1"></i>
                            Filtrar
                        </button>
                        <a href="lista.php" class="btn btn-secondary">
                            <i class="fa-solid fa-broom me-1"></i>
                            Limpar
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ordenacao" class="form-label">Critério de ordenação</label>
                            <select class="form-select" id="ordenacao" name="ordenacao">
                                <option value="">Selecione uma opção</option>
                                <option>Nome da empresa (A-Z)</option>
                                <option>Nome da empresa (Z-A)</option>
                                <option>NIF (ascendente)</option>
                                <option>NIF (descendente)</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-arrow-down-a-z me-1"></i>
                            Ordenar
                        </button>
                        <a href="lista.php" class="btn btn-secondary">
                            <i class="fa-solid fa-broom me-1"></i>
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>NIF</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Website</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Philips Healthcare</td>
                            <td>501234567</td>
                            <td>Fabricante</td>
                            <td> <i class="fa-solid fa-phone me-1"></i>
                                222 000 100</td>
                            <td> <i class="fa-solid fa-envelope me-1"></i>
                                geral@philips-healthcare.pt</td>
                            <td>
                                <a href="https://www.philips.pt" target="_blank">
                                    www.philips.pt
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="detalhes.php" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="apagar.php" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>Dräger Portugal</td>
                            <td>509876543</td>
                            <td>Assistência técnica</td>
                            <td> <i class="fa-solid fa-phone me-1"></i>
                                213 000 200</td>
                            <td> <i class="fa-solid fa-envelope me-1"></i>
                                assistencia@draeger.pt</td>
                            <td>
                                <a href="https://www.draeger.com" target="_blank">
                                    www.draeger.com
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="detalhes.php" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="apagar.php" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="mb-0 text-muted">
                        A mostrar 1 a 2 de 2 fornecedores
                    </p>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Anterior</a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">Seguinte</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
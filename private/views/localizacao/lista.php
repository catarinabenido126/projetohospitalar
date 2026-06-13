<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">
                    Listagem de Localizações
                </h2>
                <a href="novo.php" class="btn btn-success">
                    <i class="fa-solid fa-plus me-1"></i>
                    Nova localização
                </a>
            </div>
            <hr>
            <p class="text-muted">
                Lista resumida das áreas e localizações hospitalares registadas.
            </p>
            <div class="mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="edificio" class="form-label">
                                Edifício
                            </label>
                            <select class="form-select" id="edificio" name="edificio">
                                <option value="">Todos</option>
                                <option>Edifício A</option>
                                <option>Edifício B</option>
                                <option>Edifício C</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="piso" class="form-label">
                                Piso
                            </label>
                            <select class="form-select" id="piso" name="piso">
                                <option value="">Todos</option>
                                <option>Piso 0</option>
                                <option>Piso 1</option>
                                <option>Piso 2</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="servico" class="form-label">
                                Serviço
                            </label>
                            <select class="form-select" id="servico" name="servico">
                                <option value="">Todos</option>
                                <option>UCI</option>
                                <option>Urgência</option>
                                <option>Bloco Operatório</option>
                                <option>Consultas</option>
                                <option>Laboratório</option>
                                <option>Radiologia</option>
                                <option>Reabilitação</option>
                                <option>Armazém</option>
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
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Edifício</th>
                            <th>Piso</th>
                            <th>Sala</th>
                            <th>Serviço</th>
                            <th>Responsável</th>
                            <th>Contacto</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Edifício A</td>
                            <td>Piso 1</td>
                            <td>Sala 101</td>
                            <td>UCI</td>
                            <td>Enf. Marta Silva</td>
                            <td>
                                <i class="fa-solid fa-phone me-1"></i>
                                912 345 678
                            </td>
                            <td class="text-center">
                                <a href="equipamentos.php" class="btn btn-sm btn-outline-primary me-1">
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
                            <td>Edifício B</td>
                            <td>Piso 0</td>
                            <td>Sala 2</td>
                            <td>Bloco operatório</td>
                            <td>Dr. João Costa</td>
                            <td>
                                <i class="fa-solid fa-phone me-1"></i>
                                913 222 444
                            </td>
                            <td class="text-center">
                                <a href="equipamentos.php" class="btn btn-sm btn-outline-primary me-1">
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
                        A mostrar 1 a 2 de 2 localizações
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
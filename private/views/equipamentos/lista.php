<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Equipamentos</h2>
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
                        Novo equipamento
                    </a>
                </div>
            </div>
            <hr>
            <div id="mensagemSucesso" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Equipamento guardado com sucesso.
            </div>
            <div id="mensagemCriado" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Equipamento criado com sucesso.
            </div>
            <p class="text-muted">
                Lista resumida dos equipamentos médicos registados. A ficha completa pode ser consultada nos detalhes.
            </p>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar equipamento</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Ex: EQ-0001">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas</option>
                                <option>Monitorização</option>
                                <option>Suporte de Vida</option>
                                <option>Diagnóstico</option>
                                <option>Imagiologia</option>
                                <option>Laboratório</option>
                                <option>Terapia</option>
                                <option>Cirurgia</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <option>Ativo</option>
                                <option>Em manutenção</option>
                                <option>Em calibração</option>
                                <option>Inativo</option>
                                <option>Abatido</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="criticidade" class="form-label">Criticidade</label>
                            <select class="form-select" id="criticidade" name="criticidade">
                                <option value="">Todas</option>
                                <option>Baixa</option>
                                <option>Média</option>
                                <option>Alta</option>
                                <option>Suporte de vida</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="localizacao" class="form-label">Localização</label>
                            <select class="form-select" id="localizacao" name="localizacao">
                                <option value="">Todas</option>
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
                                <option>Código interno (ascendente)</option>
                                <option>Código interno (descendente)</option>
                                <option>Designação (A-Z)</option>
                                <option>Designação (Z-A)</option>
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
            <div class="caixa-tabela table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Designação</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Criticidade</th>
                            <th>Localização</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>EQ-0001</td>
                            <td>Monitor Multiparamétrico
                                <br><small class="text-muted"> 3 componentes associados</small>
                            </td>
                            <td>Monitorização</td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td><span class="badge bg-danger">Alta</span></td>
                            <td>UCI - Sala 101</td>
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
                            <td>EQ-0002</td>
                            <td>Ventilador Pulmonar
                                <br> <small class="text-muted">2 componentes associados </small>
                            </td>
                            <td>Suporte de Vida</td>
                            <td><span class="badge bg-warning text-dark">Em manutenção</span></td>
                            <td><span class="badge bg-danger">Suporte de vida</span></td>
                            <td>UCI - Sala 102</td>
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
                        A mostrar 1 a 2 de 2 equipamentos
                    </p>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    Anterior
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">
                                    1
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    Seguinte
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <script>
                const params = new URLSearchParams(window.location.search);
                if (params.get("criado") === "1") {
                    document.getElementById("mensagemCriado").classList.remove("d-none");
                    setTimeout(() => document.getElementById("mensagemCriado").classList.add("d-none"), 5000);
                }
                if (params.get("guardado") === "1") {
                    document.getElementById("mensagemSucesso").classList.remove("d-none");
                    setTimeout(() => document.getElementById("mensagemSucesso").classList.add("d-none"), 5000);
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
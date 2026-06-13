<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Histórico de Alterações</h2>
                    <p class="text-muted mb-0">Registo das últimas ações realizadas no sistema.</p>
                </div>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalApagarHistorico">
                    <i class="fa-solid fa-trash me-1"></i>
                    Apagar histórico
                </button>
            </div>
            <hr>
            <div class="caixa-filtros mb-4">
                <form action="historico.php" method="get">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="modulo" class="form-label">Módulo</label>
                            <select class="form-select" id="modulo" name="modulo">
                                <option value="">Todos</option>
                                <option>Equipamentos</option>
                                <option>Fornecedores</option>
                                <option>Localizações</option>
                                <option>Documentação</option>
                                <option>Garantias</option>
                                <option>Contratos</option>
                                <option>Gestão</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="acao" class="form-label">Ação</label>
                            <select class="form-select" id="acao" name="acao">
                                <option value="">Todas</option>
                                <option>Criação</option>
                                <option>Edição</option>
                                <option>Remoção</option>
                                <option>Consulta</option>
                                <option>Upload de documento</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Ex: EQ-0001">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter me-1"></i>
                            Filtrar
                        </button>
                        <a href="historico.php" class="btn btn-secondary">
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
                            <th>Data/Hora</th>
                            <th>Utilizador</th>
                            <th>Módulo</th>
                            <th>Ação</th>
                            <th>Registo</th>
                            <th class="text-center">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>10/06/2026 15:42</td>
                            <td>Utilizador</td>
                            <td>Equipamentos</td>
                            <td><span class="badge bg-success">Criação</span></td>
                            <td>EQ-0126</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>10/06/2026 14:18</td>
                            <td>Utilizador</td>
                            <td>Equipamentos</td>
                            <td><span class="badge bg-warning text-dark">Edição</span></td>
                            <td>EQ-0001</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>09/06/2026 17:05</td>
                            <td>Utilizador</td>
                            <td>Documentação</td>
                            <td><span class="badge bg-warning text-dark">Edição</span></td>
                            <td>Manual MP5</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>09/06/2026 11:30</td>
                            <td>Utilizador</td>
                            <td>Fornecedores</td>
                            <td><span class="badge bg-success">Criação</span></td>
                            <td>Philips Healthcare</td>
                            <td class="text-center">
                                <a href="../fornecedores/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>08/06/2026 16:10</td>
                            <td>Utilizador</td>
                            <td>Localizações</td>
                            <td><span class="badge bg-warning text-dark">Edição</span></td>
                            <td>UCI - Sala 101</td>
                            <td class="text-center">
                                <a href="../localizacao/equipamentos.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>07/06/2026 10:22</td>
                            <td>Utilizador</td>
                            <td>Garantias</td>
                            <td><span class="badge bg-warning text-dark">Edição</span></td>
                            <td>Garantia EQ-0001</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>06/06/2026 09:40</td>
                            <td>Utilizador</td>
                            <td>Contratos</td>
                            <td><span class="badge bg-success">Criação</span></td>
                            <td>Contrato manutenção MP5</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>05/06/2026 18:12</td>
                            <td>Utilizador</td>
                            <td>Equipamentos</td>
                            <td><span class="badge bg-danger">Remoção</span></td>
                            <td>EQ-0045</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="fa-solid fa-eye-slash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="mb-0 text-muted">A mostrar 1 a 8 de 8 alterações</p>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item disabled"><a class="page-link" href="#">Seguinte</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="modal fade" id="modalApagarHistorico" tabindex="-1" aria-labelledby="modalApagarHistoricoLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="modalApagarHistoricoLabel">Apagar histórico</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <p>Tem a certeza que pretende apagar o histórico de alterações?</p>
                            <p class="text-muted mb-0">Esta ação remove os registos apresentados nesta página. Num sistema real, esta operação deverá estar limitada a utilizadores autorizados.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="apagarHistorico()">
                                Confirmar eliminação
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function apagarHistorico() {
                    document.querySelector(".caixa-tabela tbody").innerHTML = "";
                    document.querySelector(".caixa-tabela").insertAdjacentHTML("afterbegin", '<div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Histórico apagado com sucesso.</div>');
                }
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
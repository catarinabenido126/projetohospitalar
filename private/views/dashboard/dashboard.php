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
            <h2>Dashboard</h2>
            <hr>
            <p class="text-muted">Visão geral do estado global do parque tecnológico hospitalar.</p>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-primary">
                        <i class="fa-solid fa-laptop-medical text-primary"></i>
                        <h5>Total de equipamentos</h5>
                        <p class="text-primary">126</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-success">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <h5>Equipamentos ativos</h5>
                        <p class="text-success">98</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-warning">
                        <i class="fa-solid fa-screwdriver-wrench text-warning"></i>
                        <h5>Em manutenção</h5>
                        <p class="text-warning">11</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger">
                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                        <h5>Equipamentos inativos</h5>
                        <p class="text-danger">8</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger">
                        <i class="fa-solid fa-shield-halved text-danger"></i>
                        <h5>Garantias expiradas</h5>
                        <p class="text-danger">14</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-secondary">
                        <i class="fa-solid fa-file-circle-xmark text-secondary"></i>
                        <h5>Sem documentação</h5>
                        <p class="text-secondary">9</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-info">
                        <i class="fa-solid fa-calendar-days text-info"></i>
                        <h5>Garantias a expirar</h5>
                        <p class="text-info">7</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-dark">
                        <i class="fa-solid fa-sliders text-dark"></i>
                        <h5>Em calibração</h5>
                        <p class="text-dark">5</p>
                    </div>
                </div>
            </div>
            <hr>
            <h4>Distribuição do inventário</h4>
            <p class="text-muted">Análise resumida dos equipamentos por categoria, estado e serviço.</p>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por categoria</h5>
                        <div class="area-grafico">
                            <canvas id="graficoCategoria"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por estado</h5>
                        <div class="area-grafico">
                            <canvas id="graficoEstado"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h4>Resumo por serviço</h4>
            <p class="text-muted">Distribuição dos equipamentos pelas principais áreas hospitalares.</p>
            <div class="row mb-4">
                <div class="col-md-12 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por serviço</h5>
                        <div class="area-grafico">
                            <canvas id="graficoServico"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h4>Situações a acompanhar</h4>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Garantias a expirar nos próximos 30 dias</h5>
                        <p class="text-muted">Equipamentos com garantias próximas do fim que podem necessitar de renovação ou acompanhamento.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Fornecedor</th>
                                        <th>Fim da garantia</th>
                                        <th class="text-center">Ver detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EQ-0001</td>
                                        <td>Philips Healthcare</td>
                                        <td>16/06/2026</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0023</td>
                                        <td>Dräger Portugal</td>
                                        <td>25/06/2026</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0045</td>
                                        <td>MedTech Solutions</td>
                                        <td>02/07/2026</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos críticos não ativos</h5>
                        <p class="text-muted">Equipamentos de criticidade alta ou suporte de vida que não se encontram atualmente em estado ativo.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Criticidade</th>
                                        <th>Estado</th>
                                        <th class="text-center">Ver detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EQ-0001</td>
                                        <td><span class="badge bg-danger">Suporte de vida</span></td>
                                        <td><span class="badge bg-warning text-dark">Em manutenção</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0023</td>
                                        <td><span class="badge bg-danger">Alta</span></td>
                                        <td><span class="badge bg-info text-dark">Em calibração</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0045</td>
                                        <td><span class="badge bg-danger">Alta</span></td>
                                        <td><span class="badge bg-secondary">Inativo</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos sem documentos registados</h5>
                        <p class="text-muted">Equipamentos sem documentos associados na respetiva ficha de inventário.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Categoria</th>
                                        <th>Serviço</th>
                                        <th class="text-center">Ver detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EQ-0010</td>
                                        <td>Diagnóstico</td>
                                        <td>Consultas</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0027</td>
                                        <td>Laboratório</td>
                                        <td>Laboratório</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0045</td>
                                        <td>Reabilitação</td>
                                        <td>Reabilitação</td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos em calibração</h5>
                        <p class="text-muted">Equipamentos que se encontram atualmente em processo de calibração ou verificação técnica.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Serviço</th>
                                        <th>Criticidade</th>
                                        <th class="text-center">Ver detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EQ-0010</td>
                                        <td>Urgência</td>
                                        <td><span class="badge bg-warning text-dark">Média</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0027</td>
                                        <td>Radiologia</td>
                                        <td><span class="badge bg-danger">Alta</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0045</td>
                                        <td>UCI</td>
                                        <td><span class="badge bg-danger">Alta</span></td>
                                        <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <h4>Equipamentos recentemente adicionados</h4>
            <p class="text-muted">Últimos equipamentos registados no inventário hospitalar.</p>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Equipamento</th>
                            <th>Categoria</th>
                            <th>Serviço</th>
                            <th>Data de aquisição</th>
                            <th>Estado</th>
                            <th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>EQ-0126</td>
                            <td>Desfibrilhador Zoll R Series</td>
                            <td>Suporte de Vida</td>
                            <td>Urgência</td>
                            <td>08/06/2026</td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                        <tr>
                            <td>EQ-0125</td>
                            <td>Bomba de Infusão B. Braun Infusomat Space</td>
                            <td>Terapia</td>
                            <td>Armazém</td>
                            <td>05/06/2026</td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                        <tr>
                            <td>EQ-0124</td>
                            <td>Ecógrafo Portátil</td>
                            <td>Diagnóstico</td>
                            <td>Consultas</td>
                            <td>02/06/2026</td>
                            <td><span class="badge bg-info text-dark">Em calibração</span></td>
                            <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                        <tr>
                            <td>EQ-0123</td>
                            <td>Monitor Multiparamétrico Philips IntelliVue MP5</td>
                            <td>Monitorização</td>
                            <td>UCI</td>
                            <td>28/05/2026</td>
                            <td><span class="badge bg-success">Ativo</span></td>
                            <td class="text-center"><a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            <h4>Resumo Financeiro</h4>
            <p class="text-muted">Indicadores financeiros associados ao parque tecnológico hospitalar.</p>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-success">
                        <i class="fa-solid fa-euro-sign text-success"></i>
                        <h5>Investimento Total</h5>
                        <p class="text-success">2 450 000 €</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-primary">
                        <i class="fa-solid fa-cart-shopping text-primary"></i>
                        <h5>Aquisições em 2026</h5>
                        <p class="text-primary">340 000 €</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger">
                        <i class="fa-solid fa-crown text-danger"></i>
                        <h5>Equipamento de Maior Valor</h5>
                        <p class="text-danger">120 000 €</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-warning">
                        <i class="fa-solid fa-calculator text-warning"></i>
                        <h5>Custo Médio</h5>
                        <p class="text-warning">19 445 €</p>
                    </div>
                </div>
            </div>
            <script src="/assets/js/chart.umd.min.js"></script>
            <script>
                new Chart(document.getElementById('graficoCategoria'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Monitorização', 'Suporte de Vida', 'Diagnóstico', 'Imagiologia', 'Laboratório', 'Terapia', 'Cirurgia', 'Reabilitação'],
                        datasets: [{
                            data: [26, 18, 15, 12, 19, 14, 11, 11],
                            backgroundColor: ['#a8d8ff', '#b8eacb', '#ffd6a5', '#d6c5f0', '#ffe5ec', '#d6d6d6', '#f9dcc4', '#cde7be']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
                new Chart(document.getElementById('graficoEstado'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Ativo', 'Em manutenção', 'Em calibração', 'Inativo', 'Abatido'],
                        datasets: [{
                            data: [98, 11, 5, 8, 4],
                            backgroundColor: ['#b8eacb', '#ffe5a3', '#a8d8ff', '#d6d6d6', '#f5b7b1']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
                new Chart(document.getElementById('graficoServico'), {
                    type: 'bar',
                    data: {
                        labels: ['Urgência', 'UCI', 'Bloco Operatório', 'Consultas', 'Laboratório', 'Radiologia', 'Reabilitação', 'Armazém'],
                        datasets: [{
                            label: 'N.º de equipamentos',
                            data: [21, 32, 24, 10, 18, 9, 6, 6],
                            backgroundColor: '#a8d8ff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
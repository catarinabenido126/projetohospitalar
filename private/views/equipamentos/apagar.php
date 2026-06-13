<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Detalhes do Equipamento</h2>
                    <p class="text-muted mb-0">Monitor Multiparamétrico Philips IntelliVue MP5 • EQ-0001</p>
                </div>
                <div>
                    <a href="lista.php" class="btn btn-secondary">Voltar</a>
                    <a href="editar.php" class="btn btn-warning">Editar</a>
                </div>
            </div>
                <hr>
                <ul class="nav nav-tabs mb-4" id="tabsEquipamento" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#equipamento"
                            type="button">
                            <i class="fa-solid fa-stethoscope me-1"></i> Equipamento
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#componentes" type="button">
                            <i class="fa-solid fa-microchip me-1"></i> Componentes
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#aquisicao" type="button">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Aquisição
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button">
                            <i class="fa-solid fa-truck me-1"></i> Fornecedor
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" type="button">
                            <i class="fa-solid fa-location-dot me-1"></i> Localização
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantias" type="button">
                            <i class="fa-solid fa-shield-halved me-1"></i> Garantias
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contratos" type="button">
                            <i class="fa-solid fa-file-contract me-1"></i> Contratos
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentos" type="button">
                            <i class="fa-solid fa-folder-open me-1"></i> Documentos
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="equipamento">
                        <h4><i class="fa-solid fa-stethoscope me-2"></i>Informação do equipamento</h4>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Código interno:</strong> EQ-0001</p>
                                <p><strong>Designação:</strong> Monitor Multiparamétrico</p>
                                <p><strong>Categoria:</strong> Monitorização</p>
                                <p><strong>Marca:</strong> Philips</p>
                                <p><strong>Modelo:</strong> IntelliVue MP5</p>
                            </div>

                            <div class="col-md-6">
                                <p><strong>Número de série:</strong> MP5-2022-45873</p>
                                <p><strong>Ano de fabrico:</strong> 2022</p>
                                <p><strong>Estado:</strong> Ativo</p>
                                <p><strong>Criticidade:</strong> Alta</p>
                                <p><strong>Tipo de entrada:</strong> Compra</p>
                            </div>
                        </div>
                        <hr>
                        <h5>Observações</h5>
                        <p>Equipamento reservado para dia 12/06/2026 - UCI.</p>
                        <hr>
                        <h5>Documentos do equipamento</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome do documento</th>
                                        <th>Ficheiro</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Manual de Operação Philips IntelliVue MP5</td>
                                        <td>manual_operacao_philips_intellivue_mp5.pdf</td>
                                        <td>Manual</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/manual_operacao_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/manual_operacao_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ficha Técnica Philips IntelliVue MP5</td>
                                        <td>ficha_tecnica_philips_intellivue_mp5.pdf</td>
                                        <td>Ficha técnica</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/ficha_tecnica_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/ficha_tecnica_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Certificado CE do Equipamento IntelliVue MP5</td>
                                        <td>certificado_ce_intellivue_mp5.pdf</td>
                                        <td>Certificação</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/certificado_ce_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/certificado_ce_intellivue_mp5.pdf" download
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="componentes">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Não existem componentes associados a este equipamento.
                        </div>
                        <h4><i class="fa-solid fa-microchip me-2"></i>Componentes associados</h4>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Componente</th>
                                        <th>Estado</th>
                                        <th>Notificação</th>
                                        <th>Documento associado</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>EQ-0001.01</td>
                                        <td>Sensor SpO₂ Philips M1191BL</td>
                                        <td><span class="badge bg-success">Funcional</span></td>
                                        <td>Sem notificações</td>
                                        <td>manual_sensor_spo2_m1191bl.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/manual_sensor_spo2_m1191bl.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/manual_sensor_spo2_m1191bl.pdf" download
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0001.02</td>
                                        <td>Cabo ECG Philips IntelliVue</td>
                                        <td><span class="badge bg-warning text-dark">Atenção</span></td>
                                        <td>Cabo com desgaste visível</td>
                                        <td>ficha_tecnica_cabo_ecg_intellivue.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/ficha_tecnica_cabo_ecg_intellivue.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/ficha_tecnica_cabo_ecg_intellivue.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>EQ-0001.03</td>
                                        <td>Bateria Interna Philips MP5</td>
                                        <td><span class="badge bg-danger">Substituir</span></td>
                                        <td>Autonomia reduzida</td>
                                        <td>relatorio_teste_bateria_mp5_2026.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/relatorio_teste_bateria_mp5_2026.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/relatorio_teste_bateria_mp5_2026.pdf" download
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Não existem contratos associados a este equipamento.
                            </div>
                            <h4><i class="fa-solid fa-boxes-stacked me-2"></i>Consumíveis Necessários</h4>
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Consumível</th>
                                            <th>Quantidade em Stock</th>
                                            <th>Stock Mínimo</th>
                                            <th>Última Atualização</th>
                                            <th>Observações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Soro Fisiológico 500 ml</td>
                                            <td>24</td>
                                            <td>10</td>
                                            <td>09/06/2026</td>
                                            <td>Necessário para administração intravenosa.</td>
                                        </tr>
                                        <tr>
                                            <td>Sistema de Perfusão</td>
                                            <td>35</td>
                                            <td>15</td>
                                            <td>08/06/2026</td>
                                            <td>Compatível com bomba de infusão.</td>
                                        </tr>
                                        <tr>
                                            <td>Seringa 10 ml</td>
                                            <td>120</td>
                                            <td>50</td>
                                            <td>09/06/2026</td>
                                            <td>Material descartável.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="aquisicao">
                        <h4><i class="fa-solid fa-cart-shopping me-2"></i>Dados de Aquisição / Entrada</h4>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong>Tipo de entrada:</strong> Compra</p>
                                <p><strong>Data de entrada:</strong> 15/03/2023</p>
                                <p><strong>Entidade associada:</strong> Philips Healthcare</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Custo de aquisição:</strong> 3 500 €</p>
                                <p><strong>Número da fatura:</strong> FT-2023/4587</p>
                                <p><strong>Método de pagamento:</strong> Transferência bancária</p>
                            </div>
                        </div>
                        <hr>
                        <h5>Documentos da entrada</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome do documento</th>
                                        <th>Ficheiro</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Fatura de Aquisição do Monitor Philips MP5</td>
                                        <td>fatura_aquisicao_monitor_mp5_ft20234587.pdf</td>
                                        <td>Fatura</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/fatura_aquisicao_monitor_mp5_ft20234587.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/fatura_aquisicao_monitor_mp5_ft20234587.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Comprovativo de Pagamento da Aquisição do Monitor MP5</td>
                                        <td>comprovativo_pagamento_monitor_mp5.pdf</td>
                                        <td>Comprovativo de pagamento</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/comprovativo_pagamento_monitor_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/comprovativo_pagamento_monitor_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="fornecedor">
                        <h4><i class="fa-solid fa-truck me-2"></i>Fornecedor</h4>
                        <h5 class="mt-3">Informação principal</h5>
                        <p><strong>Nome da empresa:</strong> Philips Healthcare</p>
                        <p><strong>NIF:</strong> 501234567</p>
                        <p><strong>Tipo de fornecedor:</strong> Fabricante</p>
                        <hr>
                        <h5>Contactos</h5>
                        <p><strong>Telefone:</strong> 222 000 100</p>
                        <p><strong>Email:</strong> geral@philips-healthcare.pt</p>
                        <p><strong>Website:</strong> https://www.philips.pt</p>
                        <hr>
                        <h5>Pessoa de contacto</h5>
                        <p><strong>Nome:</strong> Ana Martins</p>
                        <p><strong>Telefone:</strong> 912 345 678</p>
                        <p><strong>Email:</strong> ana.martins@philips-healthcare.pt</p>
                        <hr>
                        <h5>Morada</h5>
                        <p><strong>Morada:</strong> Rua da Saúde, 120</p>
                        <p><strong>Código postal:</strong> 4200-300</p>
                        <p><strong>Cidade:</strong> Porto</p>
                        <p><strong>País:</strong> Portugal</p>
                        <hr>
                        <h5>Documentação do fornecedor</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome do documento</th>
                                        <th>Ficheiro</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Certificado ISO 13485 Philips Healthcare</td>
                                        <td>certificado_iso13485_philips_healthcare.pdf</td>
                                        <td>Certificação</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/certificado_iso13485_philips_healthcare.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/certificado_iso13485_philips_healthcare.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Declaração de Distribuidor Autorizado Philips</td>
                                        <td>declaracao_distribuidor_autorizado_philips.pdf</td>
                                        <td>Declaração</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/declaracao_distribuidor_autorizado_philips.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/declaracao_distribuidor_autorizado_philips.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="text-end">
                                <a href="../fornecedores/editar.php" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-truck me-2"></i>
                                    Editar ficha do fornecedor
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="localizacao">
                        <h4><i class="fa-solid fa-location-dot me-2"></i>Localização</h4>
                        <p class="mt-3"><strong>Edifício:</strong> Edifício A</p>
                        <p><strong>Piso:</strong> Piso 1</p>
                        <p><strong>Sala:</strong> Sala 101</p>
                        <p><strong>Serviço:</strong> UCI</p>
                        <p><strong>Responsável:</strong> Enf. Ana Costa</p>
                        <p><strong>Contacto do/a responsável:</strong> 912345678</p>
                        <hr>
                        <h5>Documentação da localização</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome do documento</th>
                                        <th>Ficheiro</th>
                                        <th>Tipo</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Planta da Unidade de Cuidados Intensivos</td>
                                        <td>planta_unidade_cuidados_intensivos.pdf</td>
                                        <td>Planta</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/planta_unidade_cuidados_intensivos.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/planta_unidade_cuidados_intensivos.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Relatório de Instalação do Monitor MP5 na UCI</td>
                                        <td>relatorio_instalacao_monitor_mp5_uci.pdf</td>
                                        <td>Instalação</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/relatorio_instalacao_monitor_mp5_uci.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/relatorio_instalacao_monitor_mp5_uci.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="text-end">
                                <a href="../localizacao/editar.php" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-location-dot me-2"></i>
                                    Editar ficha da localização
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="garantias">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Não existem garantias associadas a este equipamento.
                        </div>
                        <h4><i class="fa-solid fa-shield-halved me-2"></i>Garantias</h4>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Garantia</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>Estado</th>
                                        <th>Documento</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Garantia Comercial Philips IntelliVue MP5</td>
                                        <td>15/03/2023</td>
                                        <td>15/03/2026</td>
                                        <td><span class="badge bg-danger">Expirada</span></td>
                                        <td>garantia_comercial_philips_mp5.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/garantia_comercial_philips_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/garantia_comercial_philips_mp5.pdf" download
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Extensão de Garantia Premium Philips MP5 2026-2028</td>
                                        <td>16/03/2026</td>
                                        <td>16/03/2028</td>
                                        <td><span class="badge bg-success">Ativa</span></td>
                                        <td>extensao_garantia_premium_mp5_2026_2028.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/extensao_garantia_premium_mp5_2026_2028.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/extensao_garantia_premium_mp5_2026_2028.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="contratos">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Não existem contratos associados a este equipamento.
                        </div>
                        <h4><i class="fa-solid fa-file-contract me-2"></i>Contratos</h4>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Contrato</th>
                                        <th>Fornecedor</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>Valor</th>
                                        <th>Documento</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Contrato de Manutenção Preventiva do Monitor MP5</td>
                                        <td>MedTech Solutions</td>
                                        <td>01/01/2025</td>
                                        <td>31/12/2027</td>
                                        <td>850 €/ano</td>
                                        <td>contrato_manutencao_preventiva_monitor_mp5.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/contrato_manutencao_preventiva_monitor_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/contrato_manutencao_preventiva_monitor_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contrato de Calibração Anual do Philips IntelliVue MP5</td>
                                        <td>Philips Healthcare</td>
                                        <td>01/01/2025</td>
                                        <td>31/12/2026</td>
                                        <td>300 €/ano</td>
                                        <td>contrato_calibracao_anual_philips_intellivue_mp5.pdf</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/contrato_calibracao_anual_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="../../../assets/docs/contrato_calibracao_anual_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="documentos">
                        <h4><i class="fa-solid fa-folder-open me-2"></i>Todos os documentos associados ao equipamento
                        </h4>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Nome do documento</th>
                                        <th>Ficheiro</th>
                                        <th>Tipo</th>
                                        <th>Associado a</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Manual de Operação Philips IntelliVue MP5</td>
                                        <td>manual_operacao_philips_intellivue_mp5.pdf</td>
                                        <td>Manual</td>
                                        <td>Equipamento</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/manual_operacao_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/manual_operacao_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ficha Técnica Philips IntelliVue MP5</td>
                                        <td>ficha_tecnica_philips_intellivue_mp5.pdf</td>
                                        <td>Ficha técnica</td>
                                        <td>Equipamento</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/ficha_tecnica_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/ficha_tecnica_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Certificado CE do Equipamento IntelliVue MP5</td>
                                        <td>certificado_ce_intellivue_mp5.pdf</td>
                                        <td>Certificação</td>
                                        <td>Equipamento</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/certificado_ce_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/certificado_ce_intellivue_mp5.pdf" download
                                                class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Manual do Sensor SpO₂ Philips M1191BL</td>
                                        <td>manual_sensor_spo2_m1191bl.pdf</td>
                                        <td>Manual</td>
                                        <td>Componente</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/manual_sensor_spo2_m1191bl.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/manual_sensor_spo2_m1191bl.pdf" download
                                                class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Ficha Técnica do Cabo ECG Philips IntelliVue</td>
                                        <td>ficha_tecnica_cabo_ecg_intellivue.pdf</td>
                                        <td>Ficha técnica</td>
                                        <td>Componente</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/ficha_tecnica_cabo_ecg_intellivue.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/ficha_tecnica_cabo_ecg_intellivue.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Relatório de Teste da Bateria Interna Philips MP5 2026</td>
                                        <td>relatorio_teste_bateria_mp5_2026.pdf</td>
                                        <td>Relatório</td>
                                        <td>Componente</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/relatorio_teste_bateria_mp5_2026.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/relatorio_teste_bateria_mp5_2026.pdf" download
                                                class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Fatura de Aquisição do Monitor Philips MP5</td>
                                        <td>fatura_aquisicao_monitor_mp5_ft20234587.pdf</td>
                                        <td>Fatura</td>
                                        <td>Compra</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/fatura_aquisicao_monitor_mp5_ft20234587.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/fatura_aquisicao_monitor_mp5_ft20234587.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Comprovativo de Pagamento da Aquisição do Monitor MP5</td>
                                        <td>comprovativo_pagamento_monitor_mp5.pdf</td>
                                        <td>Pagamento</td>
                                        <td>Compra</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/comprovativo_pagamento_monitor_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/comprovativo_pagamento_monitor_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Certificado ISO 13485 Philips Healthcare</td>
                                        <td>certificado_iso13485_philips_healthcare.pdf</td>
                                        <td>Certificação</td>
                                        <td>Fornecedor</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/certificado_iso13485_philips_healthcare.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/certificado_iso13485_philips_healthcare.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Declaração de Distribuidor Autorizado Philips</td>
                                        <td>declaracao_distribuidor_autorizado_philips.pdf</td>
                                        <td>Declaração</td>
                                        <td>Fornecedor</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/declaracao_distribuidor_autorizado_philips.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/declaracao_distribuidor_autorizado_philips.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Planta da Unidade de Cuidados Intensivos</td>
                                        <td>planta_unidade_cuidados_intensivos.pdf</td>
                                        <td>Planta</td>
                                        <td>Localização</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/planta_unidade_cuidados_intensivos.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/planta_unidade_cuidados_intensivos.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Relatório de Instalação do Monitor MP5 na UCI</td>
                                        <td>relatorio_instalacao_monitor_mp5_uci.pdf</td>
                                        <td>Instalação</td>
                                        <td>Localização</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/relatorio_instalacao_monitor_mp5_uci.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/relatorio_instalacao_monitor_mp5_uci.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Garantia Comercial Philips IntelliVue MP5</td>
                                        <td>garantia_comercial_philips_mp5.pdf</td>
                                        <td>Garantia</td>
                                        <td>Garantias</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/garantia_comercial_philips_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/garantia_comercial_philips_mp5.pdf" download
                                                class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Extensão de Garantia Premium Philips MP5 2026-2028</td>
                                        <td>extensao_garantia_premium_mp5_2026_2028.pdf</td>
                                        <td>Garantia</td>
                                        <td>Garantias</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/extensao_garantia_premium_mp5_2026_2028.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/extensao_garantia_premium_mp5_2026_2028.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contrato de Manutenção Preventiva do Monitor MP5</td>
                                        <td>contrato_manutencao_preventiva_monitor_mp5.pdf</td>
                                        <td>Contrato</td>
                                        <td>Contratos</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/contrato_manutencao_preventiva_monitor_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/contrato_manutencao_preventiva_monitor_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Contrato de Calibração Anual do Philips IntelliVue MP5</td>
                                        <td>contrato_calibracao_anual_philips_intellivue_mp5.pdf</td>
                                        <td>Contrato</td>
                                        <td>Contratos</td>
                                        <td class="text-center">
                                            <a href="../../../assets/docs/contrato_calibracao_anual_philips_intellivue_mp5.pdf"
                                                target="_blank" class="btn btn-sm btn-outline-primary"><i
                                                    class="fa-solid fa-eye"></i></a>
                                            <a href="../../../assets/docs/contrato_calibracao_anual_philips_intellivue_mp5.pdf"
                                                download class="btn btn-sm btn-outline-success"><i
                                                    class="fa-solid fa-download"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
        </main>
     </div>
 </div>
<?php include '../../includes/footer.php'; ?>
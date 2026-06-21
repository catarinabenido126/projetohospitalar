<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? '';
$idEquipamento = aes_decrypt($idEncriptado);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit();
}

try {
    $sql = "
        SELECT
            e.id_equipamento,
            e.codigo_interno,
            e.designacao,
            e.marca,
            e.modelo,
            e.fabricante,
            e.numero_serie,
            e.ano_fabrico,
            e.observacoes,
            c.nome_categoria,
            ee.nome_estado,
            cr.nivel,
            l.edificio,
            l.piso,
            l.sala,
            l.responsavel,
            l.contacto,
            s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN criticidades cr ON e.id_criticidade = cr.id_criticidade
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.id_equipamento = :id AND e.ativo = 1
    ";
    $query = $database->prepare($sql);
    $query->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
    $query->execute();
    $equipamento = $query->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
           <main class="col-md-9 col-lg-10 p-4 area-conteudo">
             <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Detalhes do Equipamento</h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($equipamento['designacao']) ?> • <?= htmlspecialchars($equipamento['codigo_interno']) ?></p>
                 </div>
            <div>
                        <a href="../equipamentos/lista.php" class="btn btn-secondary">Voltar</a>
                        <a href="../equipamentos/editar.php?id=<?= aes_encrypt($equipamento['id_equipamento']) ?>" class="btn btn-warning">Editar</a>
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
                                <p><strong>Código interno:</strong> <?= htmlspecialchars($equipamento['codigo_interno']) ?></p>
                                <p><strong>Designação:</strong> <?= htmlspecialchars($equipamento['designacao']) ?></p>
                                <p><strong>Categoria:</strong> <?= htmlspecialchars($equipamento['nome_categoria']) ?></p>
                                <p><strong>Marca:</strong> <?= htmlspecialchars($equipamento['marca']) ?></p>
                                <p><strong>Modelo:</strong> <?= htmlspecialchars($equipamento['modelo']) ?></p>
                            </div>

                            <div class="col-md-6">
                                <p><strong>Número de série:</strong> <?= htmlspecialchars($equipamento['numero_serie']) ?></p>
                                <p><strong>Ano de fabrico:</strong> <?= $equipamento['ano_fabrico'] ? htmlspecialchars($equipamento['ano_fabrico']) : '—' ?></p>
                                <p><strong>Estado:</strong> <?= htmlspecialchars($equipamento['nome_estado']) ?></p>
                                <p><strong>Criticidade:</strong> <?= htmlspecialchars($equipamento['nivel']) ?></p>
                                <p><strong>Fabricante:</strong> <?= htmlspecialchars($equipamento['fabricante']) ?></p>
                            </div>
                        </div>
                        <hr>
                        <h5>Observações</h5>
                        <p><?= !empty($equipamento['observacoes']) ? nl2br(htmlspecialchars($equipamento['observacoes'])) : 'Sem observações registadas.' ?></p>
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
                                        <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
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
                                        <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
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
                                        <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="text-end">
                                <a href="../fornecedores/editar.html" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-truck me-2"></i>
                                    Editar ficha do fornecedor
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="localizacao">
                        <h4><i class="fa-solid fa-location-dot me-2"></i>Localização</h4>
                        <p class="mt-3"><strong>Edifício:</strong> Edifício <?= htmlspecialchars($equipamento['edificio']) ?></p>
                        <p><strong>Piso:</strong> Piso <?= htmlspecialchars($equipamento['piso']) ?></p>
                        <p><strong>Sala:</strong> Sala <?= htmlspecialchars($equipamento['sala']) ?></p>
                        <p><strong>Serviço:</strong> <?= htmlspecialchars($equipamento['nome_servico']) ?></p>
                        <p><strong>Responsável:</strong> <?= !empty($equipamento['responsavel']) ? htmlspecialchars($equipamento['responsavel']) : '—' ?></p>
                        <p><strong>Contacto do/a responsável:</strong> <?= !empty($equipamento['contacto']) ? htmlspecialchars($equipamento['contacto']) : '—' ?></p>
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
                                        <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="text-end">
                                <a href="../localizacao/editar.html" class="btn btn-outline-primary">
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
                                        <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
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
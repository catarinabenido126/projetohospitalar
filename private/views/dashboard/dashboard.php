<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();

// ─── Contagens principais ────────────────────────────────────────────────────
$totalEquipamentos = 0;
$ativos = $manutencao = $calibracao = $inativos = $abatidos = 0;
$estadosRaw = [];
$porCategoria = $porServico = $recentes = [];
$listaCriticosNaoAtivos = [];
$criticosNaoAtivos = 0;

try {
    $totalEquipamentos = $database->query(
        "SELECT COUNT(*) FROM equipamentos WHERE ativo = 1"
    )->fetchColumn();

    $estadosRaw = $database->query("
        SELECT ee.nome_estado, COUNT(*) AS total
        FROM equipamentos e
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        WHERE e.ativo = 1
        GROUP BY ee.nome_estado
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    $ativos     = $estadosRaw['Ativo']          ?? 0;
    $manutencao = $estadosRaw['Em manutenção']   ?? 0;
    $calibracao = $estadosRaw['Em calibração']   ?? 0;
    $inativos   = $estadosRaw['Inativo']         ?? 0;
    $abatidos   = $estadosRaw['Abatido']         ?? 0;

    $criticosNaoAtivos = $database->query("
        SELECT COUNT(*)
        FROM equipamentos e
        INNER JOIN criticidades cr ON e.id_criticidade = cr.id_criticidade
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        WHERE e.ativo = 1
          AND cr.nivel IN ('Alta', 'Suporte de vida')
          AND ee.nome_estado != 'Ativo'
    ")->fetchColumn();

    $porCategoria = $database->query("
        SELECT c.nome_categoria, COUNT(*) AS total
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        WHERE e.ativo = 1
        GROUP BY c.nome_categoria
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $porServico = $database->query("
        SELECT s.nome_servico, COUNT(*) AS total
        FROM equipamentos e
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1
        GROUP BY s.nome_servico
        ORDER BY total DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $recentes = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico, ee.nome_estado,
               e.created_at
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1
        ORDER BY e.created_at DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $listaCriticosNaoAtivos = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, cr.nivel, ee.nome_estado
        FROM equipamentos e
        INNER JOIN criticidades cr ON e.id_criticidade = cr.id_criticidade
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        WHERE e.ativo = 1
          AND cr.nivel IN ('Alta', 'Suporte de vida')
          AND ee.nome_estado != 'Ativo'
        ORDER BY e.codigo_interno ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {}

// ─── Equipamentos sem documentação ──────────────────────────────────────────
$semDocumentacao = 0;
$listaSemDoc = [];

try {
    $qSemDoc = "
        WHERE e.ativo = 1 AND NOT EXISTS (
            SELECT 1 FROM documentos d WHERE d.ativo = 1 AND (
                d.id_equipamento = e.id_equipamento
                OR d.id_garantia   IN (SELECT g.id_garantia  FROM garantias g             WHERE g.id_equipamento  = e.id_equipamento)
                OR d.id_contrato   IN (SELECT c.id_contrato  FROM contratos c             WHERE c.id_equipamento  = e.id_equipamento)
                OR d.id_entrada    IN (SELECT en.id_entrada  FROM entradas_equipamento en WHERE en.id_equipamento = e.id_equipamento)
                OR d.id_componente IN (SELECT co.id_componente FROM componentes co        WHERE co.id_equipamento = e.id_equipamento)
            )
        )
    ";
    $semDocumentacao = $database->query("SELECT COUNT(*) FROM equipamentos e $qSemDoc")->fetchColumn();
    $listaSemDoc = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c    ON e.id_categoria = c.id_categoria
        INNER JOIN localizacoes l  ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s      ON l.id_servico = s.id_servico
        $qSemDoc
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// ─── Garantias ───────────────────────────────────────────────────────────────
$garantiasExpiradas   = 0;
$garantiasExpirando30 = 0;
$listaGarantiasExpirando = [];
$listaGarantiasExpiradas = [];

try {
    $garantiasExpiradas = $database->query(
        "SELECT COUNT(*) FROM garantias WHERE data_fim < CURDATE()"
    )->fetchColumn();

    $garantiasExpirando30 = $database->query("
        SELECT COUNT(*) FROM garantias
        WHERE data_fim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ")->fetchColumn();

    $listaGarantiasExpirando = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, g.data_fim,
               IFNULL(f.nome_empresa, '—') AS fornecedor
        FROM garantias g
        INNER JOIN equipamentos e ON g.id_equipamento = e.id_equipamento
        LEFT JOIN equipamento_fornecedor ef
               ON ef.id_equipamento = e.id_equipamento AND ef.ativo = 1
        LEFT JOIN fornecedores f ON ef.id_fornecedor = f.id_fornecedor
        WHERE g.data_fim BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
          AND e.ativo = 1
        ORDER BY g.data_fim ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $listaGarantiasExpiradas = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, g.data_fim,
               IFNULL(f.nome_empresa, '—') AS fornecedor
        FROM garantias g
        INNER JOIN equipamentos e ON g.id_equipamento = e.id_equipamento
        LEFT JOIN equipamento_fornecedor ef
               ON ef.id_equipamento = e.id_equipamento AND ef.ativo = 1
        LEFT JOIN fornecedores f ON ef.id_fornecedor = f.id_fornecedor
        WHERE g.data_fim < CURDATE()
          AND e.ativo = 1
        ORDER BY g.data_fim DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {}

// ─── Listas para modais dos cards ────────────────────────────────────────────
$listaAtivos = $listaManutencao = $listaCalibracao = $listaInativos = [];

try {
    $listaAtivos = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1 AND ee.nome_estado = 'Ativo'
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_ASSOC);

    $listaManutencao = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1 AND ee.nome_estado = 'Em manutenção'
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_ASSOC);

    $listaCalibracao = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1 AND ee.nome_estado = 'Em calibração'
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_ASSOC);

    $listaInativos = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao,
               c.nome_categoria, s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE e.ativo = 1 AND ee.nome_estado = 'Inativo'
        ORDER BY e.codigo_interno
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {}

// ─── Consumíveis abaixo do stock mínimo ──────────────────────────────────────
// Tabela: consumiveis (id_consumivel, id_equipamento, nome, stock_atual, stock_minimo, ativo)
$consumiveisCriticos = [];
$totalConsumiveisCriticos = 0;

try {
    $consumiveisCriticos = $database->query("
        SELECT c.id_consumivel, c.nome_consumivel AS nome, c.stock_atual, c.stock_minimo,
               e.id_equipamento, e.codigo_interno, e.designacao,
               s.nome_servico
        FROM consumiveis c
        INNER JOIN equipamentos e ON c.id_equipamento = e.id_equipamento
        INNER JOIN localizacoes l ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE c.ativo = 1
          AND e.ativo = 1
          AND c.stock_atual < c.stock_minimo
        ORDER BY (c.stock_minimo - c.stock_atual) DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    $totalConsumiveisCriticos = count($consumiveisCriticos);
} catch (PDOException $e) {
    // tabela pode não existir ainda — não faz nada
}

// ─── Financeiro via entradas_equipamento ─────────────────────────────────────
$investimentoTotal  = 0;
$aquisicoesAnoAtual = 0;
$maiorValor  = 0;
$custoMedio  = 0;

try {
    $rowFin = $database->query("
        SELECT IFNULL(SUM(custo_aquisicao), 0)  AS total,
               COUNT(custo_aquisicao)            AS num,
               IFNULL(MAX(custo_aquisicao), 0)   AS maximo
        FROM entradas_equipamento
        WHERE custo_aquisicao IS NOT NULL
    ")->fetch(PDO::FETCH_ASSOC);

    $investimentoTotal = (float)$rowFin['total'];
    $maiorValor        = (float)$rowFin['maximo'];
    $custoMedio        = $rowFin['num'] > 0 ? $investimentoTotal / $rowFin['num'] : 0;

    $aquisicoesAnoAtual = (float)$database->query("
        SELECT IFNULL(SUM(custo_aquisicao), 0)
        FROM entradas_equipamento
        WHERE YEAR(data_entrada) = YEAR(CURDATE())
          AND custo_aquisicao IS NOT NULL
    ")->fetchColumn();
} catch (PDOException $e) {}

// ─── Helpers ─────────────────────────────────────────────────────────────────
function classeEstado(string $estado): string {
    return match($estado) {
        'Ativo'           => 'bg-success',
        'Em manutenção'   => 'bg-warning text-dark',
        'Em calibração'   => 'bg-info text-dark',
        'Em quarentena'   => 'bg-warning text-dark',
        'Inativo'         => 'bg-danger',
        'Abatido'         => 'bg-dark',
        default           => 'bg-secondary'
    };
}
function classeCriticidade(string $nivel): string {
    return match($nivel) {
        'Baixa'           => 'bg-success',
        'Média'           => 'bg-warning text-dark',
        'Alta',
        'Suporte de vida' => 'bg-danger',
        default           => 'bg-secondary'
    };
}
function formatEuro(float $valor): string {
    return number_format($valor, 0, ',', ' ') . ' €';
}

// Tabela genérica para modais de cards (código, designação, categoria, serviço, ver)
function tabelaModalEquipamentos(array $lista, string $colSpan = '5'): void {
    if (empty($lista)) {
        echo '<div class="p-4 text-center text-muted">Sem registos.</div>';
        return;
    }
    echo '<table class="table table-sm table-bordered mb-0">
        <thead class="table-secondary">
            <tr><th>Código</th><th>Designação</th><th>Categoria</th><th>Serviço</th><th class="text-center">Ver</th></tr>
        </thead><tbody>';
    foreach ($lista as $eq) {
        echo '<tr>
            <td>' . htmlspecialchars($eq['codigo_interno']) . '</td>
            <td>' . htmlspecialchars($eq['designacao']) . '</td>
            <td>' . htmlspecialchars($eq['nome_categoria']) . '</td>
            <td>' . htmlspecialchars($eq['nome_servico']) . '</td>
            <td class="text-center">
                <a href="../equipamentos/detalhes.php?id=' . $eq['id_equipamento'] . '" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
            </td>
        </tr>';
    }
    echo '</tbody></table>';
}

$labelsCategoria = array_column($porCategoria, 'nome_categoria');
$dadosCategoria  = array_column($porCategoria, 'total');
$labelsEstado = ['Ativo', 'Em manutenção', 'Em calibração', 'Em quarentena', 'Inativo', 'Abatido'];
$dadosEstado  = [
    $ativos, $manutencao, $calibracao,
    $estadosRaw['Em quarentena'] ?? 0,
    $inativos, $abatidos
];
$labelsServico = array_column($porServico, 'nome_servico');
$dadosServico  = array_column($porServico, 'total');
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

            <!-- ── Cards de topo ── -->
            <div class="row mb-4">
                <!-- Total -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-primary" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalTotalEquipamentos" title="Clique para ver a lista">
                        <i class="fa-solid fa-laptop-medical text-primary"></i>
                        <h5>Total de equipamentos</h5>
                        <p class="text-primary"><?= $totalEquipamentos ?></p>
                    </div>
                </div>
                <!-- Ativos -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-success" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalAtivos" title="Clique para ver a lista">
                        <i class="fa-solid fa-circle-check text-success"></i>
                        <h5>Equipamentos ativos</h5>
                        <p class="text-success"><?= $ativos ?></p>
                    </div>
                </div>
                <!-- Manutenção -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-warning" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalManutencao" title="Clique para ver a lista">
                        <i class="fa-solid fa-screwdriver-wrench text-warning"></i>
                        <h5>Em manutenção</h5>
                        <p class="text-warning"><?= $manutencao ?></p>
                    </div>
                </div>
                <!-- Inativos -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalInativos" title="Clique para ver a lista">
                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                        <h5>Equipamentos inativos</h5>
                        <p class="text-danger"><?= $inativos ?></p>
                    </div>
                </div>
                <!-- Garantias expiradas -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalGarantiasExpiradas" title="Clique para ver a lista">
                        <i class="fa-solid fa-shield-halved text-danger"></i>
                        <h5>Garantias expiradas</h5>
                        <p class="text-danger"><?= $garantiasExpiradas ?></p>
                    </div>
                </div>
                <!-- Consumíveis em falta -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard <?= $totalConsumiveisCriticos > 0 ? 'border-info' : 'border-success' ?>" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalConsumiveisCriticos" title="Clique para ver a lista">
                        <i class="fa-solid fa-boxes-stacked <?= $totalConsumiveisCriticos > 0 ? 'text-info' : 'text-success' ?>"></i>
                        <h5>Consumíveis em falta</h5>
                        <p class="<?= $totalConsumiveisCriticos > 0 ? 'text-info' : 'text-success' ?>"><?= $totalConsumiveisCriticos ?></p>
                    </div>
                </div>
                <!-- Em calibração -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-dark" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalCalibracao" title="Clique para ver a lista">
                        <i class="fa-solid fa-sliders text-dark"></i>
                        <h5>Em calibração</h5>
                        <p class="text-dark"><?= $calibracao ?></p>
                    </div>
                </div>
                <!-- Sem documentação -->
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-secondary" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#modalSemDoc" title="Clique para ver a lista">
                        <i class="fa-solid fa-file-circle-xmark text-secondary"></i>
                        <h5>Sem documentação</h5>
                        <p class="text-secondary"><?= $semDocumentacao ?></p>
                    </div>
                </div>
            </div>

            <hr>

            <!-- ── Gráficos ── -->
            <h4>Distribuição do inventário</h4>
            <p class="text-muted">Análise resumida dos equipamentos por categoria, estado e serviço.</p>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por categoria</h5>
                        <div class="area-grafico"><canvas id="graficoCategoria"></canvas></div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por estado</h5>
                        <div class="area-grafico"><canvas id="graficoEstado"></canvas></div>
                    </div>
                </div>
            </div>
            <hr>
            <h4>Resumo por serviço</h4>
            <div class="row mb-4">
                <div class="col-md-12 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos por serviço</h5>
                        <div class="area-grafico"><canvas id="graficoServico"></canvas></div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- ── Situações a acompanhar ── -->
            <h4>Situações a acompanhar</h4>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Garantias a expirar nos próximos 30 dias</h5>
                        <p class="text-muted">Equipamentos com garantias próximas do fim.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr><th>Código</th><th>Fornecedor</th><th>Fim da garantia</th><th class="text-center">Ver</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($listaGarantiasExpirando)) : ?>
                                        <tr><td colspan="4" class="text-center text-muted">Sem garantias a expirar nos próximos 30 dias.</td></tr>
                                    <?php else : ?>
                                        <?php foreach ($listaGarantiasExpirando as $g) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($g['codigo_interno']) ?></td>
                                            <td><?= htmlspecialchars($g['fornecedor']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($g['data_fim'])) ?></td>
                                            <td class="text-center">
                                                <a href="../equipamentos/detalhes.php?id=<?= $g['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="caixa-dashboard">
                        <h5>Equipamentos críticos não ativos</h5>
                        <p class="text-muted">Equipamentos de criticidade Alta ou Suporte de Vida fora de serviço.</p>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr><th>Código</th><th>Criticidade</th><th>Estado</th><th class="text-center">Ver</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($listaCriticosNaoAtivos)) : ?>
                                        <tr><td colspan="4" class="text-center text-muted">Sem equipamentos críticos fora de serviço.</td></tr>
                                    <?php else : ?>
                                        <?php foreach ($listaCriticosNaoAtivos as $eq) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                            <td><span class="badge <?= classeCriticidade($eq['nivel']) ?>"><?= htmlspecialchars($eq['nivel']) ?></span></td>
                                            <td><span class="badge <?= classeEstado($eq['nome_estado']) ?>"><?= htmlspecialchars($eq['nome_estado']) ?></span></td>
                                            <td class="text-center">
                                                <a href="../equipamentos/detalhes.php?id=<?= $eq['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Recentes ── -->
            <h4>Equipamentos recentemente adicionados</h4>
            <p class="text-muted">Últimos equipamentos registados no inventário hospitalar.</p>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr><th>Código</th><th>Equipamento</th><th>Categoria</th><th>Serviço</th><th>Registado em</th><th>Estado</th><th class="text-center">Ver</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentes)) : ?>
                            <tr><td colspan="7" class="text-center text-muted">Sem equipamentos registados.</td></tr>
                        <?php else : ?>
                            <?php foreach ($recentes as $eq) : ?>
                            <tr>
                                <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                <td><?= htmlspecialchars($eq['nome_categoria']) ?></td>
                                <td><?= htmlspecialchars($eq['nome_servico']) ?></td>
                                <td><?= date('d/m/Y', strtotime($eq['created_at'])) ?></td>
                                <td><span class="badge <?= classeEstado($eq['nome_estado']) ?>"><?= htmlspecialchars($eq['nome_estado']) ?></span></td>
                                <td class="text-center">
                                    <a href="../equipamentos/detalhes.php?id=<?= $eq['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <hr>

            <!-- ── Financeiro ── -->
            <h4>Resumo Financeiro</h4>
            <p class="text-muted">Indicadores financeiros baseados nos dados reais de aquisição.</p>
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-success">
                        <i class="fa-solid fa-euro-sign text-success"></i>
                        <h5>Investimento Total</h5>
                        <p class="text-success"><?= formatEuro($investimentoTotal) ?></p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-primary">
                        <i class="fa-solid fa-cart-shopping text-primary"></i>
                        <h5>Aquisições este ano</h5>
                        <p class="text-primary"><?= formatEuro($aquisicoesAnoAtual) ?></p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-danger">
                        <i class="fa-solid fa-crown text-danger"></i>
                        <h5>Equipamento de Maior Valor</h5>
                        <p class="text-danger"><?= formatEuro($maiorValor) ?></p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card-dashboard border-warning">
                        <i class="fa-solid fa-calculator text-warning"></i>
                        <h5>Custo Médio</h5>
                        <p class="text-warning"><?= formatEuro($custoMedio) ?></p>
                    </div>
                </div>
            </div>

            <!-- ── Gráficos JS ── -->
            <script src="/assets/js/chart.umd.min.js"></script>
            <script>
                const labelsCategoria = <?= json_encode($labelsCategoria) ?>;
                const dadosCategoria  = <?= json_encode($dadosCategoria) ?>;
                const labelsEstado    = <?= json_encode($labelsEstado) ?>;
                const dadosEstado     = <?= json_encode($dadosEstado) ?>;
                const labelsServico   = <?= json_encode($labelsServico) ?>;
                const dadosServico    = <?= json_encode($dadosServico) ?>;
                const coresDoughnut = ['#a8d8ff','#b8eacb','#ffd6a5','#d6c5f0','#ffe5ec','#d6d6d6','#f9dcc4','#cde7be','#fce4ec','#e8f5e9'];

                new Chart(document.getElementById('graficoCategoria'), {
                    type: 'doughnut',
                    data: { labels: labelsCategoria, datasets: [{ data: dadosCategoria, backgroundColor: coresDoughnut }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
                new Chart(document.getElementById('graficoEstado'), {
                    type: 'doughnut',
                    data: { labels: labelsEstado, datasets: [{ data: dadosEstado, backgroundColor: ['#b8eacb','#ffe5a3','#a8d8ff','#f0c040','#d6d6d6','#f5b7b1'] }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
                new Chart(document.getElementById('graficoServico'), {
                    type: 'bar',
                    data: { labels: labelsServico, datasets: [{ label: 'N.º de equipamentos', data: dadosServico, backgroundColor: '#a8d8ff' }] },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            </script>
        </main>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     MODAIS DOS CARDS DE TOPO
     ════════════════════════════════════════════════════════════ -->

<!-- Modal: Total de equipamentos -->
<div class="modal fade" id="modalTotalEquipamentos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-laptop-medical text-primary me-2"></i>Todos os equipamentos (<?= $totalEquipamentos ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php tabelaModalEquipamentos(array_merge($listaAtivos, $listaManutencao, $listaCalibracao, $listaInativos)); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver lista completa</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Ativos -->
<div class="modal fade" id="modalAtivos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-circle-check text-success me-2"></i>Equipamentos ativos (<?= $ativos ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php tabelaModalEquipamentos($listaAtivos); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver lista completa</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Em manutenção -->
<div class="modal fade" id="modalManutencao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-screwdriver-wrench text-warning me-2"></i>Em manutenção (<?= $manutencao ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php tabelaModalEquipamentos($listaManutencao); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver lista completa</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Inativos -->
<div class="modal fade" id="modalInativos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-circle-xmark text-danger me-2"></i>Equipamentos inativos (<?= $inativos ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php tabelaModalEquipamentos($listaInativos); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver lista completa</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Garantias expiradas -->
<div class="modal fade" id="modalGarantiasExpiradas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-shield-halved text-danger me-2"></i>Garantias expiradas (<?= $garantiasExpiradas ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($listaGarantiasExpiradas)) : ?>
                    <div class="p-4 text-center text-success"><i class="fa-solid fa-circle-check me-2"></i>Sem garantias expiradas.</div>
                <?php else : ?>
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-secondary">
                            <tr><th>Código</th><th>Fornecedor</th><th>Expirou em</th><th class="text-center">Ver</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listaGarantiasExpiradas as $g) : ?>
                            <tr>
                                <td><?= htmlspecialchars($g['codigo_interno']) ?></td>
                                <td><?= htmlspecialchars($g['fornecedor']) ?></td>
                                <td class="text-danger"><?= date('d/m/Y', strtotime($g['data_fim'])) ?></td>
                                <td class="text-center">
                                    <a href="../equipamentos/detalhes.php?id=<?= $g['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Garantias a expirar (30d) -->
<div class="modal fade" id="modalGarantiasExpirando" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-calendar-days text-info me-2"></i>Garantias a expirar nos próximos 30 dias (<?= $garantiasExpirando30 ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($listaGarantiasExpirando)) : ?>
                    <div class="p-4 text-center text-success"><i class="fa-solid fa-circle-check me-2"></i>Sem garantias a expirar nos próximos 30 dias.</div>
                <?php else : ?>
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-secondary">
                            <tr><th>Código</th><th>Fornecedor</th><th>Fim da garantia</th><th class="text-center">Ver</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listaGarantiasExpirando as $g) : ?>
                            <tr>
                                <td><?= htmlspecialchars($g['codigo_interno']) ?></td>
                                <td><?= htmlspecialchars($g['fornecedor']) ?></td>
                                <td><?= date('d/m/Y', strtotime($g['data_fim'])) ?></td>
                                <td class="text-center">
                                    <a href="../equipamentos/detalhes.php?id=<?= $g['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Em calibração -->
<div class="modal fade" id="modalCalibracao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-sliders text-dark me-2"></i>Em calibração (<?= $calibracao ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php tabelaModalEquipamentos($listaCalibracao); ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver lista completa</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Sem documentação -->
<div class="modal fade" id="modalSemDoc" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-file-circle-xmark text-secondary me-2"></i>Sem documentação (<?= $semDocumentacao ?>)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($listaSemDoc)) : ?>
                    <div class="p-4 text-center text-success"><i class="fa-solid fa-circle-check me-2"></i>Todos os equipamentos têm documentação associada.</div>
                <?php else : ?>
                    <?php tabelaModalEquipamentos($listaSemDoc); ?>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="../equipamentos/lista.php" class="btn btn-primary">Ver equipamentos</a>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Consumíveis em falta -->
<div class="modal fade" id="modalConsumiveisCriticos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa-solid fa-boxes-stacked <?= $totalConsumiveisCriticos > 0 ? 'text-warning' : 'text-success' ?> me-2"></i>
                    Consumíveis abaixo do stock mínimo (<?= $totalConsumiveisCriticos ?>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <?php if (empty($consumiveisCriticos)) : ?>
                    <div class="p-4 text-center text-success">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        Todos os consumíveis têm stock acima do mínimo definido.
                    </div>
                <?php else : ?>
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>Consumível</th>
                                <th>Equipamento</th>
                                <th>Serviço</th>
                                <th class="text-center">Stock atual</th>
                                <th class="text-center">Stock mínimo</th>
                                <th class="text-center">Diferença</th>
                                <th class="text-center">Nível</th>
                                <th class="text-center">Ver</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consumiveisCriticos as $cons) :
                                $diferenca = $cons['stock_minimo'] - $cons['stock_atual'];
                                $ratio = $cons['stock_minimo'] > 0
                                    ? $cons['stock_atual'] / $cons['stock_minimo']
                                    : 0;
                                if ($ratio <= 0) {
                                    $nivel = '<span class="badge bg-dark">Sem stock</span>';
                                } elseif ($ratio < 0.5) {
                                    $nivel = '<span class="badge bg-danger">Crítico</span>';
                                } else {
                                    $nivel = '<span class="badge bg-warning text-dark">Baixo</span>';
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($cons['nome']) ?></td>
                                <td>
                                    <?= htmlspecialchars($cons['codigo_interno']) ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($cons['designacao']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($cons['nome_servico']) ?></td>
                                <td class="text-center fw-bold text-danger"><?= $cons['stock_atual'] ?></td>
                                <td class="text-center"><?= $cons['stock_minimo'] ?></td>
                                <td class="text-center text-danger">−<?= $diferenca ?></td>
                                <td class="text-center"><?= $nivel ?></td>
                                <td class="text-center">
                                    <a href="../equipamentos/detalhes.php?id=<?= $cons['id_equipamento'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
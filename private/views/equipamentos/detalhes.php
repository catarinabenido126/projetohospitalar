<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$perfilAtual = $_SESSION['perfil'] ?? '';

// ── Desencriptar e validar ID ────────────────────────────────────────────────
$idEncrypted = $_GET['id'] ?? null;
$id = $idEncrypted ? aes_decrypt($idEncrypted) : null;
if (!$id || !is_numeric($id)) {
    header('Location: lista.php');
    exit();
}
$id = (int)$id;

// ── Equipamento principal ────────────────────────────────────────────────────
try {
    $stmt = $database->prepare("
        SELECT e.*, c.nome_categoria, ee.nome_estado, cr.nivel,
               l.edificio, l.piso, l.sala, l.responsavel, l.contacto AS loc_contacto,
               s.nome_servico
        FROM equipamentos e
        INNER JOIN categorias c           ON e.id_categoria   = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado      = ee.id_estado
        INNER JOIN criticidades cr        ON e.id_criticidade = cr.id_criticidade
        INNER JOIN localizacoes l         ON e.id_localizacao = l.id_localizacao
        INNER JOIN servicos s             ON l.id_servico     = s.id_servico
        WHERE e.id_equipamento = :id AND e.ativo = 1
    ");
    $stmt->execute([':id' => $id]);
    $eq = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $eq = null; }

if (!$eq) {
    header('Location: lista.php');
    exit();
}

// ── Entrada / Aquisição ──────────────────────────────────────────────────────
try {
    $stmtEnt = $database->prepare("
        SELECT en.*, te.tipo_entrada
        FROM entradas_equipamento en
        INNER JOIN tipos_entrada te ON en.id_tipo_entrada = te.id_tipo_entrada
        WHERE en.id_equipamento = :id AND en.ativo = 1
        LIMIT 1
    ");
    $stmtEnt->execute([':id' => $id]);
    $entrada = $stmtEnt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $entrada = null; }

// ── Fornecedor associado ─────────────────────────────────────────────────────
try {
    $stmtForn = $database->prepare("
        SELECT f.*, tf.tipo AS tipo_fornecedor
        FROM equipamento_fornecedor ef
        INNER JOIN fornecedores f       ON ef.id_fornecedor       = f.id_fornecedor
        LEFT  JOIN tipos_fornecedor tf  ON f.id_tipo_fornecedor   = tf.id_tipo_fornecedor
        WHERE ef.id_equipamento = :id AND ef.ativo = 1
        LIMIT 1
    ");
    $stmtForn->execute([':id' => $id]);
    $fornecedor = $stmtForn->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $fornecedor = null; }

// ── Garantias ────────────────────────────────────────────────────────────────
try {
    $stmtGar = $database->prepare("
        SELECT g.*, eg.nome_estado AS estado_garantia
        FROM garantias g
        INNER JOIN estados_garantia eg ON g.id_estado_garantia = eg.id_estado_garantia
        WHERE g.id_equipamento = :id AND g.ativo = 1
        ORDER BY g.data_fim DESC
    ");
    $stmtGar->execute([':id' => $id]);
    $garantias = $stmtGar->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $garantias = []; }

// ── Contratos ────────────────────────────────────────────────────────────────
try {
    $stmtCon = $database->prepare("
        SELECT c.*, f.nome_empresa
        FROM contratos c
        INNER JOIN fornecedores f ON c.id_fornecedor = f.id_fornecedor
        WHERE c.id_equipamento = :id AND c.ativo = 1
        ORDER BY c.data_fim DESC
    ");
    $stmtCon->execute([':id' => $id]);
    $contratos = $stmtCon->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $contratos = []; }

// ── Componentes ──────────────────────────────────────────────────────────────
try {
    $stmtComp = $database->prepare("
        SELECT co.*, ec.estado AS nome_estado_componente
        FROM componentes co
        INNER JOIN estados_componentes ec ON co.id_estado_componente = ec.id_estado_componente
        WHERE co.id_equipamento = :id AND co.ativo = 1
        ORDER BY co.codigo_componente
    ");
    $stmtComp->execute([':id' => $id]);
    $componentes = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $componentes = []; }

// ── Consumíveis ──────────────────────────────────────────────────────────────
try {
    $stmtCons = $database->prepare("
        SELECT *
        FROM consumiveis
        WHERE id_equipamento = :id AND ativo = 1
        ORDER BY nome_consumivel
    ");
    $stmtCons->execute([':id' => $id]);
    $consumiveis = $stmtCons->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $consumiveis = []; }

// ── Documentos agrupados ─────────────────────────────────────────────────────
try {
    $stmtDocs = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento,
               CASE
                   WHEN d.id_equipamento IS NOT NULL AND d.id_garantia IS NULL AND d.id_contrato IS NULL
                        AND d.id_entrada IS NULL AND d.id_componente IS NULL AND d.id_fornecedor IS NULL
                        THEN 'Equipamento'
                   WHEN d.id_garantia   IS NOT NULL THEN 'Garantia'
                   WHEN d.id_contrato   IS NOT NULL THEN 'Contrato'
                   WHEN d.id_entrada    IS NOT NULL THEN 'Aquisição'
                   WHEN d.id_componente IS NOT NULL THEN 'Componente'
                   WHEN d.id_fornecedor IS NOT NULL THEN 'Fornecedor'
                   ELSE 'Outro'
               END AS associado_a
        FROM documentos d
        INNER JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        WHERE d.ativo = 1 AND (
            d.id_equipamento = :id
            OR d.id_garantia   IN (SELECT id_garantia  FROM garantias             WHERE id_equipamento = :id)
            OR d.id_contrato   IN (SELECT id_contrato  FROM contratos             WHERE id_equipamento = :id)
            OR d.id_entrada    IN (SELECT id_entrada   FROM entradas_equipamento  WHERE id_equipamento = :id)
            OR d.id_componente IN (SELECT id_componente FROM componentes          WHERE id_equipamento = :id)
        )
        ORDER BY associado_a, d.nome_documento
    ");
    $stmtDocs->execute([':id' => $id]);
    $documentos = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
    // Docs só do equipamento (tab principal)
    $docsEquipamento = array_filter($documentos, fn($d) => $d['associado_a'] === 'Equipamento');
} catch (PDOException $e) { $documentos = []; $docsEquipamento = []; }

// ── Helpers ──────────────────────────────────────────────────────────────────
function badgeEstado(string $estado): string {
    $classe = match($estado) {
        'Ativo'           => 'bg-success',
        'Em manutenção'   => 'bg-warning text-dark',
        'Em calibração'   => 'bg-info text-dark',
        'Em quarentena'   => 'bg-warning text-dark',
        'Inativo'         => 'bg-danger',
        'Abatido'         => 'bg-dark',
        default           => 'bg-secondary'
    };
    return '<span class="badge ' . $classe . '">' . htmlspecialchars($estado) . '</span>';
}
function badgeCriticidade(string $nivel): string {
    $classe = match($nivel) {
        'Baixa'           => 'bg-success',
        'Média'           => 'bg-warning text-dark',
        'Alta',
        'Suporte de vida' => 'bg-danger',
        default           => 'bg-secondary'
    };
    return '<span class="badge ' . $classe . '">' . htmlspecialchars($nivel) . '</span>';
}
function badgeEstadoComp(string $estado): string {
    $classe = match($estado) {
        'Funcional'       => 'bg-success',
        'Em manutenção'   => 'bg-warning text-dark',
        'Avariado'        => 'bg-danger',
        'Substituído'     => 'bg-dark',
        default           => 'bg-secondary'
    };
    return '<span class="badge ' . $classe . '">' . htmlspecialchars($estado) . '</span>';
}
function badgeGarantia(string $estado): string {
    $classe = match($estado) {
        'Ativa'    => 'bg-success',
        'Expirada' => 'bg-danger',
        default    => 'bg-secondary'
    };
    return '<span class="badge ' . $classe . '">' . htmlspecialchars($estado) . '</span>';
}
function btnDoc(string $caminho, string $nome): string {
    $c = htmlspecialchars($caminho);
    return '<a href="' . $c . '" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Ver"><i class="fa-solid fa-eye"></i></a>'
         . '<a href="' . $c . '" download class="btn btn-sm btn-outline-success" title="Descarregar"><i class="fa-solid fa-download"></i></a>';
}
function stockBadge(int $atual, int $minimo): string {
    if ($atual === 0)           return '<span class="badge bg-dark">Sem stock</span>';
    if ($atual < $minimo * 0.5) return '<span class="badge bg-danger">Crítico</span>';
    if ($atual < $minimo)       return '<span class="badge bg-warning text-dark">Baixo</span>';
    return '<span class="badge bg-success">OK</span>';
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
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($eq['designacao']) ?> •
                        <?= htmlspecialchars($eq['codigo_interno']) ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="lista.php" class="btn btn-secondary">Voltar</a>
                    <?php if (in_array($perfilAtual, ['Administrador', 'Tecnico'])): ?>
                        <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-warning">
                            <i class="fa-solid fa-pen me-1"></i> Editar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <hr>

            <!-- ── TABS ── -->
            <ul class="nav nav-tabs mb-4" id="tabsEquipamento" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#equipamento" type="button" role="tab">
                        <i class="fa-solid fa-stethoscope me-1"></i> Equipamento
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#componentes" type="button" role="tab">
                        <i class="fa-solid fa-microchip me-1"></i> Componentes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#aquisicao" type="button" role="tab">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Aquisição
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#fornecedor" type="button" role="tab">
                        <i class="fa-solid fa-truck me-1"></i> Fornecedor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#localizacao" type="button" role="tab">
                        <i class="fa-solid fa-location-dot me-1"></i> Localização
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#garantias" type="button" role="tab">
                        <i class="fa-solid fa-shield-halved me-1"></i> Garantias
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#contratos" type="button" role="tab">
                        <i class="fa-solid fa-file-contract me-1"></i> Contratos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#documentos" type="button" role="tab">
                        <i class="fa-solid fa-folder-open me-1"></i> Documentos
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- ══ TAB: EQUIPAMENTO ══ -->
                <div class="tab-pane fade show active" id="equipamento">
                    <h4><i class="fa-solid fa-stethoscope me-2"></i>Informação do equipamento</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Código interno:</strong> <?= htmlspecialchars($eq['codigo_interno']) ?></p>
                            <p><strong>Designação:</strong> <?= htmlspecialchars($eq['designacao']) ?></p>
                            <p><strong>Categoria:</strong> <?= htmlspecialchars($eq['nome_categoria']) ?></p>
                            <p><strong>Marca:</strong> <?= htmlspecialchars($eq['marca']) ?></p>
                            <p><strong>Modelo:</strong> <?= htmlspecialchars($eq['modelo']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Número de série:</strong> <?= htmlspecialchars($eq['numero_serie']) ?></p>
                            <p><strong>Ano de fabrico:</strong> <?= htmlspecialchars($eq['ano_fabrico'] ?? '—') ?></p>
                            <p><strong>Estado:</strong> <?= badgeEstado($eq['nome_estado']) ?></p>
                            <p><strong>Criticidade:</strong> <?= badgeCriticidade($eq['nivel']) ?></p>
                        </div>
                    </div>
                    <?php if (!empty($eq['observacoes'])): ?>
                    <hr>
                    <h5>Observações</h5>
                    <p><?= nl2br(htmlspecialchars($eq['observacoes'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($docsEquipamento)): ?>
                    <hr>
                    <h5>Documentos do equipamento</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead><tr><th>Nome</th><th>Ficheiro</th><th>Tipo</th><th class="text-center">Ações</th></tr></thead>
                            <tbody>
                                <?php foreach ($docsEquipamento as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['nome_documento']) ?></td>
                                    <td><?= htmlspecialchars($d['nome_ficheiro']) ?></td>
                                    <td><?= htmlspecialchars($d['tipo_documento']) ?></td>
                                    <td class="text-center"><?= btnDoc($d['caminho_ficheiro'], $d['nome_documento']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: COMPONENTES ══ -->
                <div class="tab-pane fade" id="componentes">
                    <h4><i class="fa-solid fa-microchip me-2"></i>Componentes associados</h4>
                    <?php if (empty($componentes)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Este equipamento não tem componentes associados.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr><th>Código</th><th>Componente</th><th>Estado</th><th>Notificação</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($componentes as $c): ?>
                                <tr>
                                    <td><?= htmlspecialchars($c['codigo_componente']) ?></td>
                                    <td><?= htmlspecialchars($c['nome_componente']) ?></td>
                                    <td><?= badgeEstadoComp($c['nome_estado_componente']) ?></td>
                                    <td><?= $c['notificacao'] ? htmlspecialchars($c['notificacao']) : '<span class="text-muted">—</span>' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>

                    <hr>
                    <h4><i class="fa-solid fa-boxes-stacked me-2"></i>Consumíveis necessários</h4>
                    <?php if (empty($consumiveis)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Este equipamento não tem consumíveis associados.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Consumível</th>
                                    <th class="text-center">Stock atual</th>
                                    <th class="text-center">Stock mínimo</th>
                                    <th class="text-center">Nível</th>
                                    <th>Última atualização</th>
                                    <th>Observações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consumiveis as $cons): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cons['nome_consumivel']) ?></td>
                                    <td class="text-center <?= $cons['stock_atual'] < $cons['stock_minimo'] ? 'text-danger fw-bold' : '' ?>">
                                        <?= $cons['stock_atual'] ?>
                                    </td>
                                    <td class="text-center"><?= $cons['stock_minimo'] ?></td>
                                    <td class="text-center"><?= stockBadge((int)$cons['stock_atual'], (int)$cons['stock_minimo']) ?></td>
                                    <td><?= $cons['ultima_atualizacao'] ? date('d/m/Y', strtotime($cons['ultima_atualizacao'])) : '—' ?></td>
                                    <td><?= $cons['observacoes'] ? htmlspecialchars($cons['observacoes']) : '<span class="text-muted">—</span>' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: AQUISIÇÃO ══ -->
                <div class="tab-pane fade" id="aquisicao">
                    <h4><i class="fa-solid fa-cart-shopping me-2"></i>Dados de Aquisição / Entrada</h4>
                    <?php if (!$entrada): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem dados de aquisição registados.
                        </div>
                    <?php else: ?>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Tipo de entrada:</strong> <?= htmlspecialchars($entrada['tipo_entrada']) ?></p>
                            <p><strong>Data de entrada:</strong> <?= $entrada['data_entrada'] ? date('d/m/Y', strtotime($entrada['data_entrada'])) : '—' ?></p>
                            <p><strong>Entidade associada:</strong> <?= htmlspecialchars($entrada['entidade_associada'] ?? '—') ?></p>
                        </div>
                        <div class="col-md-6">
                            <?php if (!empty($entrada['custo_aquisicao'])): ?>
                                <p><strong>Custo de aquisição:</strong> <?= number_format((float)$entrada['custo_aquisicao'], 2, ',', ' ') ?> €</p>
                            <?php endif; ?>
                            <?php if (!empty($entrada['numero_fatura'])): ?>
                                <p><strong>Número da fatura:</strong> <?= htmlspecialchars($entrada['numero_fatura']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($entrada['metodo_pagamento'])): ?>
                                <p><strong>Método de pagamento:</strong> <?= htmlspecialchars($entrada['metodo_pagamento']) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($entrada['valor_mensal'])): ?>
                                <p><strong>Valor mensal:</strong> <?= number_format((float)$entrada['valor_mensal'], 2, ',', ' ') ?> €</p>
                            <?php endif; ?>
                            <?php if (!empty($entrada['data_fim_aluguer'])): ?>
                                <p><strong>Fim do aluguer:</strong> <?= date('d/m/Y', strtotime($entrada['data_fim_aluguer'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php
                    $docsAquisicao = array_filter($documentos, fn($d) => $d['associado_a'] === 'Aquisição');
                    if (!empty($docsAquisicao)):
                    ?>
                    <hr>
                    <h5>Documentos da entrada</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead><tr><th>Nome</th><th>Ficheiro</th><th>Tipo</th><th class="text-center">Ações</th></tr></thead>
                            <tbody>
                                <?php foreach ($docsAquisicao as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['nome_documento']) ?></td>
                                    <td><?= htmlspecialchars($d['nome_ficheiro']) ?></td>
                                    <td><?= htmlspecialchars($d['tipo_documento']) ?></td>
                                    <td class="text-center"><?= btnDoc($d['caminho_ficheiro'], $d['nome_documento']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: FORNECEDOR ══ -->
                <div class="tab-pane fade" id="fornecedor">
                    <h4><i class="fa-solid fa-truck me-2"></i>Fornecedor</h4>
                    <?php if (!$fornecedor): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem fornecedor associado.
                        </div>
                    <?php else: ?>
                    <h5 class="mt-3">Informação principal</h5>
                    <p><strong>Nome da empresa:</strong> <?= htmlspecialchars($fornecedor['nome_empresa']) ?></p>
                    <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor['nif']) ?></p>
                    <p><strong>Tipo de fornecedor:</strong> <?= htmlspecialchars($fornecedor['tipo_fornecedor'] ?? '—') ?></p>
                    <hr>
                    <h5>Contactos</h5>
                    <p><strong>Telefone:</strong> <?= htmlspecialchars($fornecedor['telefone'] ?? '—') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($fornecedor['email'] ?? '—') ?></p>
                    <?php if (!empty($fornecedor['website'])): ?>
                        <p><strong>Website:</strong> <a href="<?= htmlspecialchars($fornecedor['website']) ?>" target="_blank"><?= htmlspecialchars($fornecedor['website']) ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($fornecedor['pessoa_contacto'])): ?>
                    <hr>
                    <h5>Pessoa de contacto</h5>
                    <p><strong>Nome:</strong> <?= htmlspecialchars($fornecedor['pessoa_contacto']) ?></p>
                    <?php if (!empty($fornecedor['telefone_contacto'])): ?>
                        <p><strong>Telefone:</strong> <?= htmlspecialchars($fornecedor['telefone_contacto']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($fornecedor['email_contacto'])): ?>
                        <p><strong>Email:</strong> <?= htmlspecialchars($fornecedor['email_contacto']) ?></p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php if (!empty($fornecedor['morada'])): ?>
                    <hr>
                    <h5>Morada</h5>
                    <p><strong>Morada:</strong> <?= htmlspecialchars($fornecedor['morada']) ?></p>
                    <?php if (!empty($fornecedor['codigo_postal'])): ?>
                        <p><strong>Código postal:</strong> <?= htmlspecialchars($fornecedor['codigo_postal']) ?></p>
                    <?php endif; ?>
                    <p><strong>Cidade:</strong> <?= htmlspecialchars($fornecedor['cidade'] ?? '—') ?></p>
                    <?php endif; ?>
                    <div class="text-end mt-3">
                        <a href="../fornecedores/detalhes.php?id=<?= aes_encrypt($fornecedor['id_fornecedor']) ?>" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye me-1"></i> Ver ficha do fornecedor
                        </a>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: LOCALIZAÇÃO ══ -->
                <div class="tab-pane fade" id="localizacao">
                    <h4><i class="fa-solid fa-location-dot me-2"></i>Localização</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <p><strong>Edifício:</strong> <?= htmlspecialchars($eq['edificio']) ?></p>
                            <p><strong>Piso:</strong> <?= htmlspecialchars($eq['piso']) ?></p>
                            <p><strong>Sala:</strong> <?= htmlspecialchars($eq['sala']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Serviço:</strong> <?= htmlspecialchars($eq['nome_servico']) ?></p>
                            <p><strong>Responsável:</strong> <?= htmlspecialchars($eq['responsavel'] ?? '—') ?></p>
                            <p><strong>Contacto:</strong> <?= htmlspecialchars($eq['loc_contacto'] ?? '—') ?></p>
                        </div>
                    </div>
                </div>

                <!-- ══ TAB: GARANTIAS ══ -->
                <div class="tab-pane fade" id="garantias">
                    <h4><i class="fa-solid fa-shield-halved me-2"></i>Garantias</h4>
                    <?php if (empty($garantias)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem garantias registadas.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr><th>Garantia</th><th>Início</th><th>Fim</th><th>Estado</th><th class="text-center">Docs</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($garantias as $g):
                                    $docsGar = array_filter($documentos, fn($d) => $d['id_garantia'] == $g['id_garantia']);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($g['nome_garantia']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($g['data_inicio'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($g['data_fim'])) ?></td>
                                    <td><?= badgeGarantia($g['estado_garantia']) ?></td>
                                    <td class="text-center">
                                        <?php foreach ($docsGar as $d): ?>
                                            <?= btnDoc($d['caminho_ficheiro'], $d['nome_documento']) ?>
                                        <?php endforeach; ?>
                                        <?php if (empty($docsGar)): ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: CONTRATOS ══ -->
                <div class="tab-pane fade" id="contratos">
                    <h4><i class="fa-solid fa-file-contract me-2"></i>Contratos</h4>
                    <?php if (empty($contratos)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem contratos registados.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr><th>Contrato</th><th>Fornecedor</th><th>Início</th><th>Fim</th><th>Valor anual</th><th class="text-center">Docs</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contratos as $ct):
                                    $docsCt = array_filter($documentos, fn($d) => $d['id_contrato'] == $ct['id_contrato']);
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($ct['nome_contrato']) ?></td>
                                    <td><?= htmlspecialchars($ct['nome_empresa']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($ct['data_inicio'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($ct['data_fim'])) ?></td>
                                    <td><?= $ct['valor_anual'] ? number_format((float)$ct['valor_anual'], 2, ',', ' ') . ' €/ano' : '—' ?></td>
                                    <td class="text-center">
                                        <?php foreach ($docsCt as $d): ?>
                                            <?= btnDoc($d['caminho_ficheiro'], $d['nome_documento']) ?>
                                        <?php endforeach; ?>
                                        <?php if (empty($docsCt)): ?><span class="text-muted">—</span><?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ══ TAB: DOCUMENTOS ══ -->
                <div class="tab-pane fade" id="documentos">
                    <h4><i class="fa-solid fa-folder-open me-2"></i>Todos os documentos associados</h4>
                    <?php if (empty($documentos)): ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem documentos associados a este equipamento.
                        </div>
                    <?php else: ?>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr><th>Nome</th><th>Ficheiro</th><th>Tipo</th><th>Associado a</th><th class="text-center">Ações</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentos as $d): ?>
                                <tr>
                                    <td><?= htmlspecialchars($d['nome_documento']) ?></td>
                                    <td><?= htmlspecialchars($d['nome_ficheiro']) ?></td>
                                    <td><?= htmlspecialchars($d['tipo_documento']) ?></td>
                                    <td><?= htmlspecialchars($d['associado_a']) ?></td>
                                    <td class="text-center"><?= btnDoc($d['caminho_ficheiro'], $d['nome_documento']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

            </div><!-- /tab-content -->
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
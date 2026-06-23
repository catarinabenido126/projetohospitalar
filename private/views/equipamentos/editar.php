<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
restringir_perfil(['Administrador', 'Tecnico']);

// ── Desencriptar e validar ID ────────────────────────────────────────────────
$idEncrypted = $_GET['id'] ?? null;
$id = $idEncrypted ? aes_decrypt($idEncrypted) : null;
if (!$id || !is_numeric($id)) {
    header('Location: lista.php');
    exit();
}
$id = (int)$id;

// ── Carregar dados do equipamento ────────────────────────────────────────────
try {
    $stmt = $database->prepare("
        SELECT e.*, c.nome_categoria, ee.nome_estado, cr.nivel,
               l.edificio, l.piso, l.sala, s.nome_servico
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

// ── Dropdowns ────────────────────────────────────────────────────────────────
$categorias   = $database->query("SELECT id_categoria, nome_categoria FROM categorias WHERE ativo=1 ORDER BY nome_categoria")->fetchAll(PDO::FETCH_ASSOC);
$estados      = $database->query("SELECT id_estado, nome_estado FROM estados_equipamento WHERE ativo=1 ORDER BY nome_estado")->fetchAll(PDO::FETCH_ASSOC);
$criticidades = $database->query("SELECT id_criticidade, nivel FROM criticidades WHERE ativo=1 ORDER BY id_criticidade")->fetchAll(PDO::FETCH_ASSOC);
$localizacoes = $database->query("SELECT l.id_localizacao, l.edificio, l.piso, l.sala, s.nome_servico FROM localizacoes l INNER JOIN servicos s ON l.id_servico=s.id_servico WHERE l.ativo=1 ORDER BY l.edificio, l.piso, l.sala")->fetchAll(PDO::FETCH_ASSOC);
$fornecedores = $database->query("SELECT id_fornecedor, nome_empresa FROM fornecedores WHERE ativo=1 ORDER BY nome_empresa")->fetchAll(PDO::FETCH_ASSOC);
$tiposDocs    = $database->query("SELECT id_tipo_documento, tipo FROM tipos_documento WHERE ativo=1 ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);
$estadosComp  = $database->query("SELECT id_estado_componente, estado FROM estados_componentes WHERE ativo=1 ORDER BY id_estado_componente")->fetchAll(PDO::FETCH_ASSOC);
$estadosGar   = $database->query("SELECT id_estado_garantia, estado AS nome_estado FROM estados_garantia WHERE ativo=1 ORDER BY id_estado_garantia")->fetchAll(PDO::FETCH_ASSOC);

// ── Fornecedor atual ─────────────────────────────────────────────────────────
try {
    $stmtF = $database->prepare("SELECT id_fornecedor FROM equipamento_fornecedor WHERE id_equipamento=:id AND ativo=1 LIMIT 1");
    $stmtF->execute([':id' => $id]);
    $fornecedorAtual = $stmtF->fetchColumn();
} catch (PDOException $e) { $fornecedorAtual = null; }

// ── Entrada / Aquisição ──────────────────────────────────────────────────────
try {
    $stmtEnt = $database->prepare("
        SELECT en.*, te.tipo_entrada FROM entradas_equipamento en
        INNER JOIN tipos_entrada te ON en.id_tipo_entrada = te.id_tipo_entrada
        WHERE en.id_equipamento = :id AND en.ativo = 1 LIMIT 1
    ");
    $stmtEnt->execute([':id' => $id]);
    $entrada = $stmtEnt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $entrada = null; }

// ── Garantias ────────────────────────────────────────────────────────────────
try {
    $stmtGar = $database->prepare("
        SELECT g.*, eg.nome_estado AS estado_garantia
        FROM garantias g
        INNER JOIN estados_garantia eg ON g.id_estado_garantia = eg.id_estado_garantia
        WHERE g.id_equipamento = :id AND g.ativo = 1 ORDER BY g.data_fim DESC
    ");
    $stmtGar->execute([':id' => $id]);
    $garantias = $stmtGar->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $garantias = []; }

// ── Contratos ────────────────────────────────────────────────────────────────
try {
    $stmtCon = $database->prepare("
        SELECT c.*, f.nome_empresa FROM contratos c
        INNER JOIN fornecedores f ON c.id_fornecedor = f.id_fornecedor
        WHERE c.id_equipamento = :id AND c.ativo = 1 ORDER BY c.data_fim DESC
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
        WHERE co.id_equipamento = :id AND co.ativo = 1 ORDER BY co.codigo_componente
    ");
    $stmtComp->execute([':id' => $id]);
    $componentes = $stmtComp->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $componentes = []; }

// ── Consumíveis ──────────────────────────────────────────────────────────────
try {
    $stmtCons = $database->prepare("
        SELECT * FROM consumiveis WHERE id_equipamento = :id AND ativo = 1 ORDER BY nome_consumivel
    ");
    $stmtCons->execute([':id' => $id]);
    $consumiveis = $stmtCons->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $consumiveis = []; }

// ── Documentos do equipamento ────────────────────────────────────────────────
try {
    $stmtDocs = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento FROM documentos d
        INNER JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        WHERE d.ativo = 1 AND d.id_equipamento = :id
          AND d.id_garantia IS NULL AND d.id_contrato IS NULL
          AND d.id_entrada IS NULL AND d.id_componente IS NULL
        ORDER BY d.nome_documento
    ");
    $stmtDocs->execute([':id' => $id]);
    $docsEquipamento = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $docsEquipamento = []; }
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Editar Equipamento</h2>
                    <p class="text-muted mb-0">
                        <?= htmlspecialchars($eq['designacao']) ?> • <?= htmlspecialchars($eq['codigo_interno']) ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="detalhes.php?id=<?= htmlspecialchars($idEncrypted) ?>" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" form="formEditarEquipamento" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Alterações
                    </button>
                </div>
            </div>
            <hr>

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
            </ul>

            <form id="formEditarEquipamento" action="lista.php?guardado=1" method="post">
                <input type="hidden" name="id_equipamento" value="<?= $id ?>">
                <div class="tab-content">

                    <!-- ══ TAB: EQUIPAMENTO ══ -->
                    <div class="tab-pane fade show active" id="equipamento">
                        <h4><i class="fa-solid fa-stethoscope me-2"></i>Informação do Equipamento</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Código Interno</label>
                                <input type="text" name="codigo_interno" class="form-control mb-3" value="<?= htmlspecialchars($eq['codigo_interno']) ?>">
                                <label class="form-label">Designação</label>
                                <input type="text" name="designacao" class="form-control mb-3" value="<?= htmlspecialchars($eq['designacao']) ?>">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control mb-3" value="<?= htmlspecialchars($eq['marca']) ?>">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control mb-3" value="<?= htmlspecialchars($eq['modelo']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Série</label>
                                <input type="text" name="numero_serie" class="form-control mb-3" value="<?= htmlspecialchars($eq['numero_serie']) ?>">
                                <label class="form-label">Ano de Fabrico</label>
                                <input type="number" name="ano_fabrico" class="form-control mb-3" value="<?= htmlspecialchars($eq['ano_fabrico'] ?? '') ?>">
                                <label class="form-label">Categoria</label>
                                <select name="id_categoria" class="form-select mb-3">
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>" <?= $eq['id_categoria'] == $cat['id_categoria'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nome_categoria']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Estado</label>
                                <select name="id_estado" class="form-select mb-3">
                                    <?php foreach ($estados as $est): ?>
                                        <option value="<?= $est['id_estado'] ?>" <?= $eq['id_estado'] == $est['id_estado'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($est['nome_estado']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Criticidade</label>
                                <select name="id_criticidade" class="form-select mb-3">
                                    <?php foreach ($criticidades as $crit): ?>
                                        <option value="<?= $crit['id_criticidade'] ?>" <?= $eq['id_criticidade'] == $crit['id_criticidade'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($crit['nivel']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control mb-4" rows="4"><?= htmlspecialchars($eq['observacoes'] ?? '') ?></textarea>

                        <?php if (!empty($docsEquipamento)): ?>
                        <hr>
                        <h5><i class="fa-solid fa-file-lines me-2"></i>Documentos do Equipamento</h5>
                        <?php foreach ($docsEquipamento as $d): ?>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    <?= htmlspecialchars($d['nome_documento']) ?>
                                    <span class="badge bg-light text-primary border ms-2"><?= htmlspecialchars($d['tipo_documento']) ?></span>
                                </h6>
                            </div>
                            <p class="mb-0 text-muted small">
                                <i class="fa-regular fa-file-lines me-1"></i>
                                <?= htmlspecialchars($d['nome_ficheiro']) ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary mt-2">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar Documento
                        </button>
                    </div>

                    <!-- ══ TAB: COMPONENTES ══ -->
                    <div class="tab-pane fade" id="componentes">
                        <h4><i class="fa-solid fa-microchip me-2"></i>Componentes Associados</h4>
                        <?php if (empty($componentes)): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Este equipamento não tem componentes associados.
                            </div>
                        <?php else: ?>
                            <?php foreach ($componentes as $i => $c): ?>
                            <div class="border rounded p-3 mb-3 bg-white">
                                <h5>Componente <?= $i + 1 ?></h5>
                                <label class="form-label">Código do componente</label>
                                <input type="text" name="componentes[<?= $c['id_componente'] ?>][codigo]" class="form-control mb-2" value="<?= htmlspecialchars($c['codigo_componente']) ?>">
                                <small class="text-muted d-block mb-3">Formato: <?= htmlspecialchars($eq['codigo_interno']) ?>.01</small>
                                <label class="form-label">Nome do componente</label>
                                <input type="text" name="componentes[<?= $c['id_componente'] ?>][nome]" class="form-control mb-3" value="<?= htmlspecialchars($c['nome_componente']) ?>">
                                <label class="form-label">Estado</label>
                                <select name="componentes[<?= $c['id_componente'] ?>][id_estado]" class="form-select mb-3">
                                    <?php foreach ($estadosComp as $ec): ?>
                                        <option value="<?= $ec['id_estado_componente'] ?>" <?= $c['id_estado_componente'] == $ec['id_estado_componente'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ec['estado']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Notificação</label>
                                <textarea name="componentes[<?= $c['id_componente'] ?>][notificacao]" class="form-control mb-3" rows="2"><?= htmlspecialchars($c['notificacao'] ?? '') ?></textarea>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary mb-4">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar Componente
                        </button>

                        <hr>
                        <h4><i class="fa-solid fa-box-open me-2"></i>Consumíveis</h4>
                        <?php if (empty($consumiveis)): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Este equipamento não tem consumíveis associados.
                            </div>
                        <?php else: ?>
                            <?php foreach ($consumiveis as $j => $cons): ?>
                            <div class="border rounded p-3 mb-3 bg-white">
                                <h5>Consumível <?= $j + 1 ?></h5>
                                <label class="form-label">Nome do consumível</label>
                                <input type="text" name="consumiveis[<?= $cons['id_consumivel'] ?>][nome]" class="form-control mb-3" value="<?= htmlspecialchars($cons['nome_consumivel']) ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Stock atual</label>
                                        <input type="number" name="consumiveis[<?= $cons['id_consumivel'] ?>][stock_atual]" class="form-control mb-3" value="<?= $cons['stock_atual'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Stock mínimo</label>
                                        <input type="number" name="consumiveis[<?= $cons['id_consumivel'] ?>][stock_minimo]" class="form-control mb-3" value="<?= $cons['stock_minimo'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Última atualização</label>
                                        <input type="date" name="consumiveis[<?= $cons['id_consumivel'] ?>][ultima_atualizacao]" class="form-control mb-3" value="<?= $cons['ultima_atualizacao'] ?? '' ?>">
                                    </div>
                                </div>
                                <label class="form-label">Observações</label>
                                <textarea name="consumiveis[<?= $cons['id_consumivel'] ?>][observacoes]" class="form-control mb-3" rows="2"><?= htmlspecialchars($cons['observacoes'] ?? '') ?></textarea>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar Consumível
                        </button>
                    </div>

                    <!-- ══ TAB: AQUISIÇÃO ══ -->
                    <div class="tab-pane fade" id="aquisicao">
                        <h4><i class="fa-solid fa-cart-shopping me-2"></i>Dados de Aquisição / Entrada</h4>
                        <?php if ($entrada): ?>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de entrada</label>
                                <input type="text" class="form-control mb-3" value="<?= htmlspecialchars($entrada['tipo_entrada']) ?>" readonly>
                                <label class="form-label">Data de entrada</label>
                                <input type="date" name="data_entrada" class="form-control mb-3" value="<?= $entrada['data_entrada'] ?? '' ?>">
                                <label class="form-label">Entidade associada</label>
                                <input type="text" name="entidade_associada" class="form-control mb-3" value="<?= htmlspecialchars($entrada['entidade_associada'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($entrada['custo_aquisicao'])): ?>
                                <label class="form-label">Custo de aquisição (€)</label>
                                <input type="number" step="0.01" name="custo_aquisicao" class="form-control mb-3" value="<?= $entrada['custo_aquisicao'] ?>">
                                <?php endif; ?>
                                <?php if (!empty($entrada['numero_fatura'])): ?>
                                <label class="form-label">Número da fatura</label>
                                <input type="text" name="numero_fatura" class="form-control mb-3" value="<?= htmlspecialchars($entrada['numero_fatura']) ?>">
                                <?php endif; ?>
                                <?php if (!empty($entrada['metodo_pagamento'])): ?>
                                <label class="form-label">Método de pagamento</label>
                                <input type="text" name="metodo_pagamento" class="form-control mb-3" value="<?= htmlspecialchars($entrada['metodo_pagamento']) ?>">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info mt-3">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Sem dados de aquisição registados.
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- ══ TAB: FORNECEDOR ══ -->
                    <div class="tab-pane fade" id="fornecedor">
                        <h4><i class="fa-solid fa-truck me-2"></i>Fornecedor Associado</h4>
                        <label class="form-label">Selecionar fornecedor</label>
                        <select name="id_fornecedor" class="form-select mb-4">
                            <option value="">— Sem fornecedor —</option>
                            <?php foreach ($fornecedores as $f): ?>
                                <option value="<?= $f['id_fornecedor'] ?>" <?= $fornecedorAtual == $f['id_fornecedor'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['nome_empresa']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="text-end">
                            <a href="../fornecedores/lista.php" class="btn btn-outline-primary">
                                <i class="fa-solid fa-truck me-1"></i> Ver todos os fornecedores
                            </a>
                        </div>
                    </div>

                    <!-- ══ TAB: LOCALIZAÇÃO ══ -->
                    <div class="tab-pane fade" id="localizacao">
                        <h4><i class="fa-solid fa-location-dot me-2"></i>Localização Associada</h4>
                        <label class="form-label">Selecionar localização</label>
                        <select name="id_localizacao" class="form-select mb-4">
                            <?php foreach ($localizacoes as $loc): ?>
                                <option value="<?= $loc['id_localizacao'] ?>" <?= $eq['id_localizacao'] == $loc['id_localizacao'] ? 'selected' : '' ?>>
                                    Edifício <?= htmlspecialchars($loc['edificio']) ?> • Piso <?= htmlspecialchars($loc['piso']) ?> • Sala <?= htmlspecialchars($loc['sala']) ?> • <?= htmlspecialchars($loc['nome_servico']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Edifício:</strong> <?= htmlspecialchars($eq['edificio']) ?></p>
                                <p><strong>Piso:</strong> <?= htmlspecialchars($eq['piso']) ?></p>
                                <p><strong>Sala:</strong> <?= htmlspecialchars($eq['sala']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Serviço:</strong> <?= htmlspecialchars($eq['nome_servico']) ?></p>
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
                            <?php foreach ($garantias as $k => $g): ?>
                            <div class="border rounded p-3 mb-3 bg-white">
                                <h5>Garantia <?= $k + 1 ?></h5>
                                <label class="form-label">Nome da garantia</label>
                                <input type="text" name="garantias[<?= $g['id_garantia'] ?>][nome]" class="form-control mb-3" value="<?= htmlspecialchars($g['nome_garantia']) ?>">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Data de início</label>
                                        <input type="date" name="garantias[<?= $g['id_garantia'] ?>][data_inicio]" class="form-control mb-3" value="<?= $g['data_inicio'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Data de fim</label>
                                        <input type="date" name="garantias[<?= $g['id_garantia'] ?>][data_fim]" class="form-control mb-3" value="<?= $g['data_fim'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Estado</label>
                                        <select name="garantias[<?= $g['id_garantia'] ?>][id_estado]" class="form-select mb-3">
                                            <?php foreach ($estadosGar as $eg): ?>
                                                <option value="<?= $eg['id_estado_garantia'] ?>" <?= $g['id_estado_garantia'] == $eg['id_estado_garantia'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($eg['nome_estado']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar Garantia
                        </button>
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
                            <?php foreach ($contratos as $l => $ct): ?>
                            <div class="border rounded p-3 mb-3 bg-white">
                                <h5>Contrato <?= $l + 1 ?></h5>
                                <label class="form-label">Nome do contrato</label>
                                <input type="text" name="contratos[<?= $ct['id_contrato'] ?>][nome]" class="form-control mb-3" value="<?= htmlspecialchars($ct['nome_contrato']) ?>">
                                <label class="form-label">Fornecedor</label>
                                <select name="contratos[<?= $ct['id_contrato'] ?>][id_fornecedor]" class="form-select mb-3">
                                    <?php foreach ($fornecedores as $f): ?>
                                        <option value="<?= $f['id_fornecedor'] ?>" <?= $ct['id_fornecedor'] == $f['id_fornecedor'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($f['nome_empresa']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Data de início</label>
                                        <input type="date" name="contratos[<?= $ct['id_contrato'] ?>][data_inicio]" class="form-control mb-3" value="<?= $ct['data_inicio'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Data de fim</label>
                                        <input type="date" name="contratos[<?= $ct['id_contrato'] ?>][data_fim]" class="form-control mb-3" value="<?= $ct['data_fim'] ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Valor anual (€)</label>
                                        <input type="number" step="0.01" name="contratos[<?= $ct['id_contrato'] ?>][valor_anual]" class="form-control mb-3" value="<?= $ct['valor_anual'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar Contrato
                        </button>
                    </div>

                </div>
            </form>

            <script>
                function mostrarCamposEntrada() {
                    const tipo = document.getElementById("tipoEntradaEquipamento")?.value;
                    if (!tipo) return;
                    document.querySelectorAll(".campos-entrada").forEach(b => b.classList.add("d-none"));
                    const mapa = { compra: "camposCompra", aluguer: "camposAluguer", doacao: "camposDoacao", emprestimo: "camposEmprestimo" };
                    if (mapa[tipo]) document.getElementById(mapa[tipo])?.classList.remove("d-none");
                }
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
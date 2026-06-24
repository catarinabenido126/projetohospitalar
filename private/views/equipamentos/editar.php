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

// ── Remover documento (soft delete via GET) ───────────────────────────────────
if (isset($_GET['remover_doc']) && ctype_digit($_GET['remover_doc'])) {
    try {
        $database->prepare("UPDATE documentos SET ativo = 0, updated_at = NOW() WHERE id_documento = :id")
                 ->execute([':id' => (int)$_GET['remover_doc']]);
        registar_historico($database, 'Equipamentos', 'Remoção de documento', null, 'Documento removido (id: ' . (int)$_GET['remover_doc'] . ').');
    } catch (PDOException $e) {}
    header('Location: editar.php?id=' . urlencode($idEncrypted));
    exit();
}

// ── Processar POST (guardar alterações) ──────────────────────────────────────
$erros = [];
$erro_sistema = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_interno = trim($_POST['codigo_interno'] ?? '');
    $designacao     = trim($_POST['designacao']     ?? '');
    $marca          = trim($_POST['marca']          ?? '');
    $modelo         = trim($_POST['modelo']         ?? '');
    $numero_serie   = trim($_POST['numero_serie']   ?? '');
    $ano_fabrico    = trim($_POST['ano_fabrico']    ?? '');
    $id_categoria   = $_POST['id_categoria']   ?? '';
    $id_estado      = $_POST['id_estado']      ?? '';
    $id_criticidade = $_POST['id_criticidade'] ?? '';
    $id_localizacao = $_POST['id_localizacao'] ?? '';
    $observacoes    = trim($_POST['observacoes'] ?? '');

    if (empty($codigo_interno)) $erros[] = "O código interno é obrigatório.";
    if (empty($designacao))     $erros[] = "A designação é obrigatória.";
    if (empty($marca))          $erros[] = "A marca é obrigatória.";
    if (empty($modelo))         $erros[] = "O modelo é obrigatório.";
    if (empty($numero_serie))   $erros[] = "O número de série é obrigatório.";
    if (!ctype_digit($id_categoria))   $erros[] = "Categoria inválida.";
    if (!ctype_digit($id_estado))      $erros[] = "Estado inválido.";
    if (!ctype_digit($id_criticidade)) $erros[] = "Criticidade inválida.";
    if (!ctype_digit($id_localizacao)) $erros[] = "Localização inválida.";
    if ($ano_fabrico !== '' && (!ctype_digit($ano_fabrico) || (int)$ano_fabrico < 1900 || (int)$ano_fabrico > (int)date('Y'))) {
        $erros[] = "Ano de fabrico inválido.";
    }

    if (empty($erros)) {
        try {
            // UPDATE equipamento principal
            $database->prepare("
                UPDATE equipamentos SET
                    codigo_interno = :codigo, designacao = :designacao,
                    marca = :marca, modelo = :modelo, numero_serie = :serie,
                    ano_fabrico = :ano, id_categoria = :categoria,
                    id_estado = :estado, id_criticidade = :criticidade,
                    id_localizacao = :localizacao, observacoes = :observacoes,
                    updated_at = NOW()
                WHERE id_equipamento = :id AND ativo = 1
            ")->execute([
                ':codigo'      => $codigo_interno,
                ':designacao'  => $designacao,
                ':marca'       => $marca,
                ':modelo'      => $modelo,
                ':serie'       => $numero_serie,
                ':ano'         => $ano_fabrico !== '' ? (int)$ano_fabrico : null,
                ':categoria'   => (int)$id_categoria,
                ':estado'      => (int)$id_estado,
                ':criticidade' => (int)$id_criticidade,
                ':localizacao' => (int)$id_localizacao,
                ':observacoes' => $observacoes !== '' ? $observacoes : null,
                ':id'          => $id,
            ]);

            // UPDATE componentes
            if (!empty($_POST['componentes']) && is_array($_POST['componentes'])) {
                $stmtComp = $database->prepare("
                    UPDATE componentes SET codigo_componente = :codigo, nome_componente = :nome,
                        id_estado_componente = :estado, notificacao = :notif, updated_at = NOW()
                    WHERE id_componente = :id AND id_equipamento = :eq
                ");
                foreach ($_POST['componentes'] as $idComp => $c) {
                    if (!ctype_digit((string)$idComp)) continue;
                    $stmtComp->execute([
                        ':codigo' => trim($c['codigo'] ?? ''),
                        ':nome'   => trim($c['nome']   ?? ''),
                        ':estado' => (int)($c['id_estado'] ?? 1),
                        ':notif'  => trim($c['notificacao'] ?? '') ?: null,
                        ':id'     => (int)$idComp,
                        ':eq'     => $id,
                    ]);
                }
            }

            // UPDATE consumíveis
            if (!empty($_POST['consumiveis']) && is_array($_POST['consumiveis'])) {
                $stmtCons = $database->prepare("
                    UPDATE consumiveis SET nome_consumivel = :nome, stock_atual = :stock_atual,
                        stock_minimo = :stock_minimo, ultima_atualizacao = :ultima,
                        observacoes = :obs, updated_at = NOW()
                    WHERE id_consumivel = :id AND id_equipamento = :eq
                ");
                foreach ($_POST['consumiveis'] as $idCons => $c) {
                    if (!ctype_digit((string)$idCons)) continue;
                    $stmtCons->execute([
                        ':nome'         => trim($c['nome'] ?? ''),
                        ':stock_atual'  => (int)($c['stock_atual']  ?? 0),
                        ':stock_minimo' => (int)($c['stock_minimo'] ?? 0),
                        ':ultima'       => $c['ultima_atualizacao'] ?: null,
                        ':obs'          => trim($c['observacoes'] ?? '') ?: null,
                        ':id'           => (int)$idCons,
                        ':eq'           => $id,
                    ]);
                }
            }

            // UPDATE garantias
            if (!empty($_POST['garantias']) && is_array($_POST['garantias'])) {
                $stmtGar = $database->prepare("
                    UPDATE garantias SET nome_garantia = :nome, data_inicio = :inicio,
                        data_fim = :fim, id_estado_garantia = :estado, updated_at = NOW()
                    WHERE id_garantia = :id AND id_equipamento = :eq
                ");
                foreach ($_POST['garantias'] as $idGar => $g) {
                    if (!ctype_digit((string)$idGar)) continue;
                    $stmtGar->execute([
                        ':nome'   => trim($g['nome']   ?? ''),
                        ':inicio' => $g['data_inicio'] ?: null,
                        ':fim'    => $g['data_fim']    ?: null,
                        ':estado' => (int)($g['id_estado'] ?? 1),
                        ':id'     => (int)$idGar,
                        ':eq'     => $id,
                    ]);
                }
            }

            // UPDATE contratos
            if (!empty($_POST['contratos']) && is_array($_POST['contratos'])) {
                $stmtCon = $database->prepare("
                    UPDATE contratos SET nome_contrato = :nome, id_fornecedor = :fornecedor,
                        data_inicio = :inicio, data_fim = :fim, valor_anual = :valor,
                        updated_at = NOW()
                    WHERE id_contrato = :id AND id_equipamento = :eq
                ");
                foreach ($_POST['contratos'] as $idCon => $c) {
                    if (!ctype_digit((string)$idCon)) continue;
                    $stmtCon->execute([
                        ':nome'       => trim($c['nome'] ?? ''),
                        ':fornecedor' => (int)($c['id_fornecedor'] ?? 0),
                        ':inicio'     => $c['data_inicio'] ?: null,
                        ':fim'        => $c['data_fim']    ?: null,
                        ':valor'      => $c['valor_anual'] !== '' ? (float)$c['valor_anual'] : null,
                        ':id'         => (int)$idCon,
                        ':eq'         => $id,
                    ]);
                }
            }

            registar_historico($database, 'Equipamentos', 'Edição', $codigo_interno, 'Equipamento editado com sucesso.');
            header('Location: lista.php?guardado=1');
            exit();

        } catch (PDOException $e) {
            $erro_sistema = "Erro ao guardar: " . $e->getMessage();
        }
    }
}

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
        SELECT en.*, te.tipo AS tipo_entrada FROM entradas_equipamento en
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

// ── Documentos das garantias (indexados por id_garantia) ─────────────────────
$docsGarantias = [];
try {
    $stmtDG = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento FROM documentos d
        INNER JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        INNER JOIN garantias g ON d.id_garantia = g.id_garantia
        WHERE d.ativo = 1 AND g.id_equipamento = :id
        ORDER BY d.nome_documento
    ");
    $stmtDG->execute([':id' => $id]);
    foreach ($stmtDG->fetchAll(PDO::FETCH_ASSOC) as $doc) {
        $docsGarantias[$doc['id_garantia']][] = $doc;
    }
} catch (PDOException $e) {}

// ── Documentos dos contratos (indexados por id_contrato) ─────────────────────
$docsContratos = [];
try {
    $stmtDC = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento FROM documentos d
        INNER JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        INNER JOIN contratos c ON d.id_contrato = c.id_contrato
        WHERE d.ativo = 1 AND c.id_equipamento = :id
        ORDER BY d.nome_documento
    ");
    $stmtDC->execute([':id' => $id]);
    foreach ($stmtDC->fetchAll(PDO::FETCH_ASSOC) as $doc) {
        $docsContratos[$doc['id_contrato']][] = $doc;
    }
} catch (PDOException $e) {}

// ── Documentos da entrada/aquisição ──────────────────────────────────────────
$docsEntrada = [];
try {
    $stmtDE = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento FROM documentos d
        INNER JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        INNER JOIN entradas_equipamento en ON d.id_entrada = en.id_entrada
        WHERE d.ativo = 1 AND en.id_equipamento = :id
        ORDER BY d.nome_documento
    ");
    $stmtDE->execute([':id' => $id]);
    $docsEntrada = $stmtDE->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
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
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <strong>Foram encontrados erros:</strong>
                    <ul class="mb-0"><?php foreach ($erros as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro_sistema) ?></div>
            <?php endif; ?>

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

            <form id="formEditarEquipamento" action="editar.php?id=<?= urlencode($idEncrypted) ?>" method="post">
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
                                <label class="form-label">Criticidade</label>
                                <select name="id_criticidade" class="form-select mb-3">
                                    <?php foreach ($criticidades as $crit): ?>
                                        <option value="<?= $crit['id_criticidade'] ?>" <?= $eq['id_criticidade'] == $crit['id_criticidade'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($crit['nivel']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
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
                            </div>
                        </div>
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control mb-4" rows="4"><?= htmlspecialchars($eq['observacoes'] ?? '') ?></textarea>

                        <?php if (!empty($docsEquipamento)): ?>
                        <hr>
                        <h5><i class="fa-solid fa-file-lines me-2"></i>Documentos do Equipamento</h5>
                        <?php foreach ($docsEquipamento as $d): ?>
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    <strong><?= htmlspecialchars($d['nome_documento']) ?></strong>
                                    <span class="badge bg-light text-primary border ms-2"><?= htmlspecialchars($d['tipo_documento']) ?></span>
                                </span>
                                <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>&remover_doc=<?= $d['id_documento'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Remover este documento?')">
                                    <i class="fa-solid fa-trash me-1"></i> Remover
                                </a>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fa-regular fa-file-lines me-1"></i>
                                <?= htmlspecialchars($d['nome_ficheiro']) ?>
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Substituir ficheiro:</label>
                                <input type="file" name="substituir_doc[<?= $d['id_documento'] ?>]" class="form-control form-control-sm" style="max-width:300px">
                            </div>
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
                        <div class="mb-3">
                            <label class="form-label">Tipo de entrada</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($entrada['tipo_entrada']) ?>" readonly>
                        </div>

                        <?php if ($entrada['tipo_entrada'] === 'Compra'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Data de entrada</label>
                                <input type="date" name="data_entrada" class="form-control mb-3" value="<?= $entrada['data_entrada'] ?? '' ?>">
                                <label class="form-label">Número da fatura</label>
                                <input type="text" name="numero_fatura" class="form-control mb-3" value="<?= htmlspecialchars($entrada['numero_fatura'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Custo de aquisição (€)</label>
                                <input type="number" step="0.01" name="custo_aquisicao" class="form-control mb-3" value="<?= $entrada['custo_aquisicao'] ?? '' ?>">
                                <label class="form-label">Método de pagamento</label>
                                <input type="text" name="metodo_pagamento" class="form-control mb-3" value="<?= htmlspecialchars($entrada['metodo_pagamento'] ?? '') ?>">
                            </div>
                        </div>

                        <?php elseif ($entrada['tipo_entrada'] === 'Aluguer'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Data de início do aluguer</label>
                                <input type="date" name="data_entrada" class="form-control mb-3" value="<?= $entrada['data_entrada'] ?? '' ?>">
                                <label class="form-label">Valor mensal (€)</label>
                                <input type="number" step="0.01" name="valor_mensal" class="form-control mb-3" value="<?= $entrada['valor_mensal'] ?? '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de fim do aluguer</label>
                                <input type="date" name="data_fim_aluguer" class="form-control mb-3" value="<?= $entrada['data_fim_aluguer'] ?? '' ?>">
                            </div>
                        </div>
                        <label class="form-label">Condições do aluguer</label>
                        <textarea name="condicoes_aluguer" class="form-control mb-3" rows="3"><?= htmlspecialchars($entrada['condicoes_aluguer'] ?? '') ?></textarea>

                        <?php elseif ($entrada['tipo_entrada'] === 'Doação'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Data de entrada</label>
                                <input type="date" name="data_entrada" class="form-control mb-3" value="<?= $entrada['data_entrada'] ?? '' ?>">
                            </div>
                        </div>
                        <label class="form-label">Condições da doação</label>
                        <textarea name="condicoes_doacao" class="form-control mb-3" rows="3"><?= htmlspecialchars($entrada['condicoes_doacao'] ?? '') ?></textarea>

                        <?php elseif ($entrada['tipo_entrada'] === 'Empréstimo'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Entidade proprietária</label>
                                <input type="text" name="entidade_proprietaria" class="form-control mb-3" value="<?= htmlspecialchars($entrada['entidade_proprietaria'] ?? '') ?>">
                                <label class="form-label">Data de início do empréstimo</label>
                                <input type="date" name="data_inicio_emprestimo" class="form-control mb-3" value="<?= $entrada['data_inicio_emprestimo'] ?? '' ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data prevista de devolução</label>
                                <input type="date" name="data_prevista_devolucao" class="form-control mb-3" value="<?= $entrada['data_prevista_devolucao'] ?? '' ?>">
                            </div>
                        </div>
                        <label class="form-label">Condições do empréstimo</label>
                        <textarea name="condicoes_emprestimo" class="form-control mb-3" rows="3"><?= htmlspecialchars($entrada['condicoes_emprestimo'] ?? '') ?></textarea>
                        <?php endif; ?>

                        <?php if (!empty($docsEntrada)): ?>
                        <hr>
                        <h5><i class="fa-solid fa-file-lines me-2"></i>Documentos da aquisição</h5>
                        <?php foreach ($docsEntrada as $d): ?>
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    <strong><?= htmlspecialchars($d['nome_documento']) ?></strong>
                                    <span class="badge bg-light text-primary border ms-2"><?= htmlspecialchars($d['tipo_documento']) ?></span>
                                </span>
                                <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>&remover_doc=<?= $d['id_documento'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Remover este documento?')">
                                    <i class="fa-solid fa-trash me-1"></i> Remover
                                </a>
                            </div>
                            <p class="text-muted small mb-2">
                                <i class="fa-regular fa-file-lines me-1"></i><?= htmlspecialchars($d['nome_ficheiro']) ?>
                            </p>
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label mb-0 text-muted small">Substituir ficheiro:</label>
                                <input type="file" name="substituir_doc[<?= $d['id_documento'] ?>]" class="form-control form-control-sm" style="max-width:300px">
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="mt-3">
                            <h6><i class="fa-solid fa-plus me-2"></i>Adicionar documento à aquisição</h6>
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Nome do documento</label>
                                    <input type="text" name="novo_doc_entrada[nome]" class="form-control" placeholder="Ex: Fatura de compra">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo</label>
                                    <select name="novo_doc_entrada[id_tipo]" class="form-select">
                                        <option value="">Selecionar tipo</option>
                                        <?php foreach ($tiposDocs as $td): ?>
                                            <option value="<?= $td['id_tipo_documento'] ?>"><?= htmlspecialchars($td['tipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ficheiro</label>
                                    <input type="file" name="novo_doc_entrada[ficheiro]" class="form-control">
                                </div>
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
                        <h4><i class="fa-solid fa-truck me-2"></i>Fornecedores Associados</h4>

                        <?php
                        // Carregar todas as relações actuais com tipo
                        $relacoesAtuais = [];
                        try {
                            $stmtRel = $database->prepare("
                                SELECT ef.id_fornecedor, ef.id_tipo_relacao, f.nome_empresa, tr.tipo
                                FROM equipamento_fornecedor ef
                                INNER JOIN fornecedores f ON ef.id_fornecedor = f.id_fornecedor
                                INNER JOIN tipos_relacao_fornecedor tr ON ef.id_tipo_relacao = tr.id_tipo_relacao
                                WHERE ef.id_equipamento = :id AND ef.ativo = 1
                                ORDER BY tr.id_tipo_relacao
                            ");
                            $stmtRel->execute([':id' => $id]);
                            $relacoesAtuais = $stmtRel->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {}

                        // Remover relação via GET
                        if (isset($_GET['remover_forn']) && isset($_GET['remover_tipo']) &&
                            ctype_digit($_GET['remover_forn']) && ctype_digit($_GET['remover_tipo'])) {
                            try {
                                $database->prepare("
                                    UPDATE equipamento_fornecedor SET ativo = 0, updated_at = NOW()
                                    WHERE id_equipamento = :eq AND id_fornecedor = :forn AND id_tipo_relacao = :tipo
                                ")->execute([':eq' => $id, ':forn' => (int)$_GET['remover_forn'], ':tipo' => (int)$_GET['remover_tipo']]);
                                registar_historico($database, 'Equipamentos', 'Edição', null, 'Fornecedor removido do equipamento.');
                            } catch (PDOException $e) {}
                            header('Location: editar.php?id=' . urlencode($idEncrypted) . '#fornecedor');
                            exit();
                        }

                        // Adicionar nova relação via POST
                        if (isset($_POST['adicionar_fornecedor'])) {
                            $nf = (int)($_POST['novo_id_fornecedor'] ?? 0);
                            $nt = (int)($_POST['novo_id_tipo_relacao'] ?? 0);
                            if ($nf > 0 && $nt > 0) {
                                try {
                                    $database->prepare("
                                        INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor, id_tipo_relacao, ativo, created_at, updated_at)
                                        VALUES (:eq, :forn, :tipo, 1, NOW(), NOW())
                                        ON DUPLICATE KEY UPDATE ativo = 1, updated_at = NOW()
                                    ")->execute([':eq' => $id, ':forn' => $nf, ':tipo' => $nt]);
                                    registar_historico($database, 'Equipamentos', 'Edição', null, 'Fornecedor adicionado ao equipamento.');
                                } catch (PDOException $e) {}
                                header('Location: editar.php?id=' . urlencode($idEncrypted) . '#fornecedor');
                                exit();
                            }
                        }
                        ?>

                        <?php if (empty($relacoesAtuais)): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fa-solid fa-circle-info me-2"></i>
                                Este equipamento não tem fornecedores associados.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive mt-3 mb-4">
                                <table class="table table-bordered align-middle">
                                    <thead>
                                        <tr><th>Fornecedor</th><th>Tipo de relação</th><th class="text-center">Remover</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($relacoesAtuais as $rel): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rel['nome_empresa']) ?></td>
                                            <td><span class="badge bg-secondary"><?= htmlspecialchars($rel['tipo']) ?></span></td>
                                            <td class="text-center">
                                                <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>&remover_forn=<?= $rel['id_fornecedor'] ?>&remover_tipo=<?= $rel['id_tipo_relacao'] ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Remover esta associação?')">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <hr>
                        <h5><i class="fa-solid fa-plus me-2"></i>Adicionar fornecedor</h5>
                        <form method="post" action="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Fornecedor</label>
                                    <select name="novo_id_fornecedor" class="form-select">
                                        <option value="">— Selecionar —</option>
                                        <?php foreach ($fornecedores as $f): ?>
                                            <option value="<?= $f['id_fornecedor'] ?>"><?= htmlspecialchars($f['nome_empresa']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de relação</label>
                                    <select name="novo_id_tipo_relacao" class="form-select">
                                        <option value="">— Selecionar —</option>
                                        <?php
                                        $tiposRelacao = $database->query("SELECT id_tipo_relacao, tipo FROM tipos_relacao_fornecedor WHERE ativo=1 ORDER BY id_tipo_relacao")->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($tiposRelacao as $tr): ?>
                                            <option value="<?= $tr['id_tipo_relacao'] ?>"><?= htmlspecialchars($tr['tipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" name="adicionar_fornecedor" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-plus me-1"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                        </form>
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
                                <?php if (!empty($docsGarantias[$g['id_garantia']])): ?>
                                <hr>
                                <h6><i class="fa-solid fa-file-lines me-2"></i>Documentos da garantia</h6>
                                <?php foreach ($docsGarantias[$g['id_garantia']] as $d): ?>
                                <div class="border rounded p-3 mb-2 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>
                                            <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                            <strong><?= htmlspecialchars($d['nome_documento']) ?></strong>
                                            <span class="badge bg-light text-primary border ms-2"><?= htmlspecialchars($d['tipo_documento']) ?></span>
                                        </span>
                                        <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>&remover_doc=<?= $d['id_documento'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Remover este documento?')">
                                            <i class="fa-solid fa-trash me-1"></i> Remover
                                        </a>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <i class="fa-regular fa-file-lines me-1"></i><?= htmlspecialchars($d['nome_ficheiro']) ?>
                                    </p>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="form-label mb-0 text-muted small">Substituir ficheiro:</label>
                                        <input type="file" name="substituir_doc[<?= $d['id_documento'] ?>]" class="form-control form-control-sm" style="max-width:300px">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <h6><i class="fa-solid fa-plus me-2"></i>Adicionar documento à garantia</h6>
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Nome do documento</label>
                                            <input type="text" name="novo_doc_garantia[<?= $g['id_garantia'] ?>][nome]" class="form-control" placeholder="Ex: Certificado de garantia">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tipo</label>
                                            <select name="novo_doc_garantia[<?= $g['id_garantia'] ?>][id_tipo]" class="form-select">
                                                <option value="">Selecionar tipo</option>
                                                <?php foreach ($tiposDocs as $td): ?>
                                                    <option value="<?= $td['id_tipo_documento'] ?>"><?= htmlspecialchars($td['tipo']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ficheiro</label>
                                            <input type="file" name="novo_doc_garantia[<?= $g['id_garantia'] ?>][ficheiro]" class="form-control">
                                        </div>
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
                                <?php if (!empty($docsContratos[$ct['id_contrato']])): ?>
                                <hr>
                                <h6><i class="fa-solid fa-file-lines me-2"></i>Documentos do contrato</h6>
                                <?php foreach ($docsContratos[$ct['id_contrato']] as $d): ?>
                                <div class="border rounded p-3 mb-2 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>
                                            <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                            <strong><?= htmlspecialchars($d['nome_documento']) ?></strong>
                                            <span class="badge bg-light text-primary border ms-2"><?= htmlspecialchars($d['tipo_documento']) ?></span>
                                        </span>
                                        <a href="editar.php?id=<?= htmlspecialchars($idEncrypted) ?>&remover_doc=<?= $d['id_documento'] ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Remover este documento?')">
                                            <i class="fa-solid fa-trash me-1"></i> Remover
                                        </a>
                                    </div>
                                    <p class="text-muted small mb-2">
                                        <i class="fa-regular fa-file-lines me-1"></i><?= htmlspecialchars($d['nome_ficheiro']) ?>
                                    </p>
                                    <div class="d-flex align-items-center gap-2">
                                        <label class="form-label mb-0 text-muted small">Substituir ficheiro:</label>
                                        <input type="file" name="substituir_doc[<?= $d['id_documento'] ?>]" class="form-control form-control-sm" style="max-width:300px">
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <h6><i class="fa-solid fa-plus me-2"></i>Adicionar documento ao contrato</h6>
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Nome do documento</label>
                                            <input type="text" name="novo_doc_contrato[<?= $ct['id_contrato'] ?>][nome]" class="form-control" placeholder="Ex: Contrato assinado">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Tipo</label>
                                            <select name="novo_doc_contrato[<?= $ct['id_contrato'] ?>][id_tipo]" class="form-select">
                                                <option value="">Selecionar tipo</option>
                                                <?php foreach ($tiposDocs as $td): ?>
                                                    <option value="<?= $td['id_tipo_documento'] ?>"><?= htmlspecialchars($td['tipo']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ficheiro</label>
                                            <input type="file" name="novo_doc_contrato[<?= $ct['id_contrato'] ?>][ficheiro]" class="form-control">
                                        </div>
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
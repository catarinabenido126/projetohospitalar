<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = "";
$categorias   = $database->query("SELECT id_categoria, nome_categoria FROM categorias WHERE ativo = 1 ORDER BY nome_categoria")->fetchAll(PDO::FETCH_ASSOC);
$estados      = $database->query("SELECT id_estado, nome_estado FROM estados_equipamento WHERE ativo = 1 ORDER BY nome_estado")->fetchAll(PDO::FETCH_ASSOC);
$criticidades = $database->query("SELECT id_criticidade, nivel FROM criticidades WHERE ativo = 1 ORDER BY id_criticidade")->fetchAll(PDO::FETCH_ASSOC);
$localizacoes = $database->query("SELECT l.id_localizacao, l.edificio, l.piso, l.sala, s.nome_servico FROM localizacoes l INNER JOIN servicos s ON l.id_servico = s.id_servico WHERE l.ativo = 1 ORDER BY l.edificio, l.piso, l.sala")->fetchAll(PDO::FETCH_ASSOC);
$fornecedores  = $database->query("SELECT id_fornecedor, nome_empresa FROM fornecedores WHERE ativo = 1 ORDER BY nome_empresa")->fetchAll(PDO::FETCH_ASSOC);
$tiposRelacao  = $database->query("SELECT id_tipo_relacao, tipo FROM tipos_relacao_fornecedor WHERE ativo = 1 ORDER BY id_tipo_relacao")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo       = trim($_POST["codigo"]       ?? "");
    $designacao   = trim($_POST["designacao"]   ?? "");
    $categoria    = trim($_POST["categoria"]    ?? "");
    $marca        = trim($_POST["marca"]        ?? "");
    $modelo       = trim($_POST["modelo"]       ?? "");
    $numero_serie = trim($_POST["numero_serie"] ?? "");
    $ano_fabrico  = trim($_POST["ano_fabrico"]  ?? "");
    $estado       = trim($_POST["estado"]       ?? "");
    $criticidade  = trim($_POST["criticidade"]  ?? "");
    $localizacao  = trim($_POST["localizacao"]  ?? "");
    $observacoes  = trim($_POST["observacoes"]  ?? "");

    if (empty($codigo)) {
        $erros[] = "O campo Código Interno é obrigatório.";
    } elseif (!preg_match('/^[A-Za-z]{1,5}-\d{1,6}$/', $codigo)) {
        $erros[] = "Código Interno inválido (ex: EQ-0001).";
    }
    if (empty($designacao)) {
        $erros[] = "O campo Designação é obrigatório.";
    } elseif (preg_match('/^\d+$/', $designacao)) {
        $erros[] = "A Designação não pode conter apenas números.";
    }
    if (empty($categoria) || !ctype_digit($categoria)) {
        $erros[] = "Selecione uma categoria válida.";
    }
    if (empty($marca)) {
        $erros[] = "O campo Marca é obrigatório.";
    }
    if (empty($modelo)) {
        $erros[] = "O campo Modelo é obrigatório.";
    }
    if (empty($numero_serie)) {
        $erros[] = "O campo Número de Série é obrigatório.";
    } elseif (strlen($numero_serie) < 3) {
        $erros[] = "O Número de Série deve ter pelo menos 3 caracteres.";
    }
    if (!empty($ano_fabrico) && (!ctype_digit($ano_fabrico) || $ano_fabrico < 1900 || $ano_fabrico > date("Y"))) {
        $erros[] = "O Ano de Fabrico deve ser um ano válido.";
    }
    if (empty($estado) || !ctype_digit($estado)) {
        $erros[] = "Selecione um estado válido.";
    }
    if (empty($criticidade) || !ctype_digit($criticidade)) {
        $erros[] = "Selecione uma criticidade válida.";
    }
    if (empty($localizacao) || !ctype_digit($localizacao)) {
        $erros[] = "Selecione uma localização válida.";
    }
    if (empty($erros)) {
        $codigo       = strtoupper($codigo);
        $designacao   = ucwords(strtolower($designacao));
        $numero_serie = strtoupper($numero_serie);
    }
    if (empty($erros)) {
        try {
            $sql = "INSERT INTO equipamentos (codigo_interno, designacao, id_categoria, marca, modelo, numero_serie, ano_fabrico, id_estado, id_criticidade, id_localizacao, observacoes, ativo, created_at, updated_at)
                    VALUES (:codigo, :designacao, :categoria, :marca, :modelo, :numero_serie, :ano_fabrico, :estado, :criticidade, :localizacao, :observacoes, 1, NOW(), NOW())";
            $query = $database->prepare($sql);
            $query->execute([
                ":codigo"       => $codigo,
                ":designacao"   => $designacao,
                ":categoria"    => $categoria,
                ":marca"        => $marca,
                ":modelo"       => $modelo,
                ":numero_serie" => $numero_serie,
                ":ano_fabrico"  => $ano_fabrico !== "" ? $ano_fabrico : null,
                ":estado"       => $estado,
                ":criticidade"  => $criticidade,
                ":localizacao"  => $localizacao,
                ":observacoes"  => $observacoes
            ]);
            $novoId = $database->lastInsertId();

            // Guardar fornecedores associados
            if (!empty($_POST['fornecedores']) && is_array($_POST['fornecedores'])) {
                $stmtForn = $database->prepare("
                    INSERT INTO equipamento_fornecedor (id_equipamento, id_fornecedor, id_tipo_relacao, ativo, created_at, updated_at)
                    VALUES (:eq, :forn, :tipo, 1, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE ativo = 1, updated_at = NOW()
                ");
                foreach ($_POST['fornecedores'] as $fEntry) {
                    $idF = (int)($fEntry['id_fornecedor']  ?? 0);
                    $idT = (int)($fEntry['id_tipo_relacao'] ?? 0);
                    if ($idF > 0 && $idT > 0) {
                        $stmtForn->execute([':eq' => $novoId, ':forn' => $idF, ':tipo' => $idT]);
                    }
                }
            }

            registar_historico($database, 'Equipamentos', 'Criação', $codigo, 'Equipamento criado com sucesso.');
            header("Location: lista.php?criado=1");
            exit();
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
    }
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
                    <h2>Novo Equipamento</h2>
                    <p class="text-muted mb-0">Preencha os dados para registar um novo equipamento.</p>
                </div>
                <div>
                    <a href="lista.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" form="formNovoEquipamento" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar Equipamento
                    </button>
                </div>
            </div>
            <hr>

            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <strong>Foram encontrados os seguintes erros:</strong>
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger">
                    <strong>Erro:</strong>
                    <p class="mb-0"><?= htmlspecialchars($erro_sistema) ?></p>
                </div>
            <?php endif; ?>

            <ul class="nav nav-tabs mb-4" id="tabsEquipamento">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#equipamento" type="button">
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
            </ul>

            <form id="formNovoEquipamento" action="#" method="post" novalidate>
                <div class="tab-content">

                    <!-- ── TAB: EQUIPAMENTO ─────────────────────────────── -->
                    <div class="tab-pane fade show active" id="equipamento">
                        <h4><i class="fa-solid fa-stethoscope me-2"></i>Informação do Equipamento</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Código Interno</label>
                                <input type="text" name="codigo" class="form-control mb-3" placeholder="Ex: EQ-0001"
                                    value="<?= htmlspecialchars($_POST['codigo'] ?? '') ?>" required>
                                <label class="form-label">Designação</label>
                                <input type="text" name="designacao" class="form-control mb-3" placeholder="Ex: Monitor Multiparamétrico"
                                    value="<?= htmlspecialchars($_POST['designacao'] ?? '') ?>" required>
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-select mb-3" required>
                                    <option value="" disabled <?= empty($_POST['categoria'] ?? '') ? 'selected' : '' ?>>Selecionar categoria</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>" <?= (($_POST['categoria'] ?? '') == $cat['id_categoria']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['nome_categoria']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control mb-3" placeholder="Ex: Philips"
                                    value="<?= htmlspecialchars($_POST['marca'] ?? '') ?>" required>
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control mb-3" placeholder="Ex: IntelliVue MP5"
                                    value="<?= htmlspecialchars($_POST['modelo'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Série</label>
                                <input type="text" name="numero_serie" class="form-control mb-3"
                                    value="<?= htmlspecialchars($_POST['numero_serie'] ?? '') ?>" required>
                                <label class="form-label">Ano de Fabrico</label>
                                <input type="number" name="ano_fabrico" class="form-control mb-3" placeholder="Ex: 2022"
                                    value="<?= htmlspecialchars($_POST['ano_fabrico'] ?? '') ?>">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select mb-3" required>
                                    <option value="" disabled <?= empty($_POST['estado'] ?? '') ? 'selected' : '' ?>>Selecionar estado</option>
                                    <?php foreach ($estados as $est): ?>
                                        <option value="<?= $est['id_estado'] ?>" <?= (($_POST['estado'] ?? '') == $est['id_estado']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($est['nome_estado']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Criticidade</label>
                                <select name="criticidade" class="form-select mb-3" required>
                                    <option value="" disabled <?= empty($_POST['criticidade'] ?? '') ? 'selected' : '' ?>>Selecionar criticidade</option>
                                    <?php foreach ($criticidades as $crit): ?>
                                        <option value="<?= $crit['id_criticidade'] ?>" <?= (($_POST['criticidade'] ?? '') == $crit['id_criticidade']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($crit['nivel']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control mb-4" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                    </div>

                    <!-- ── TAB: COMPONENTES ────────────────────────────── -->
                    <div class="tab-pane fade" id="componentes">

                        <!-- Secção: Componentes -->
                        <h4><i class="fa-solid fa-microchip me-2"></i>Componentes Associados</h4>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="semComponentes" onchange="toggleComponentes()">
                            <label class="form-check-label" for="semComponentes">Este equipamento não possui componentes associados</label>
                        </div>
                        <div id="areaComponentes">
                            <div id="listaComponentes"></div>
                            <button type="button" class="btn btn-outline-primary mb-4" id="btnAdicionarComponente" onclick="adicionarComponente()">
                                <i class="fa-solid fa-plus me-1"></i> Adicionar Componente
                            </button>
                        </div>

                        <hr>

                        <!-- Secção: Consumíveis -->
                        <h4><i class="fa-solid fa-box-open me-2"></i>Consumíveis</h4>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="semConsumiveis" onchange="toggleConsumiveis()">
                            <label class="form-check-label" for="semConsumiveis">Este equipamento não necessita de consumíveis</label>
                        </div>
                        <div id="areaConsumiveis">
                            <div id="listaConsumiveis"></div>
                            <button type="button" class="btn btn-outline-primary" id="btnAdicionarConsumivel" onclick="adicionarConsumivel()">
                                <i class="fa-solid fa-plus me-1"></i> Adicionar Consumível
                            </button>
                        </div>
                    </div>

                    <!-- ── TAB: AQUISIÇÃO ──────────────────────────────── -->
                    <div class="tab-pane fade" id="aquisicao">
                        <h4><i class="fa-solid fa-cart-shopping me-2"></i>Dados de Aquisição / Entrada</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de entrada</label>
                                <select class="form-select mb-3" id="tipoEntradaEquipamento" name="id_tipo_entrada" onchange="mostrarCamposEntrada()">
                                    <option value="" selected disabled>Selecionar tipo de entrada</option>
                                    <option value="1">Compra</option>
                                    <option value="2">Aluguer</option>
                                    <option value="3">Doação</option>
                                    <option value="4">Empréstimo</option>
                                </select>

                                <!-- Compra: data entrada -->
                                <div class="campos-entrada-esq" id="camposCompraEsq">
                                    <label class="form-label">Data de entrada</label>
                                    <input type="date" name="data_entrada" class="form-control mb-3">
                                </div>
                                <!-- Aluguer: data início + valor mensal -->
                                <div class="campos-entrada-esq d-none" id="camposAluguerEsq">
                                    <label class="form-label">Data de início do aluguer</label>
                                    <input type="date" name="data_entrada" class="form-control mb-3">
                                    <label class="form-label">Valor mensal (€)</label>
                                    <input type="number" step="0.01" name="valor_mensal" class="form-control mb-3">
                                </div>
                                <!-- Doação: data entrada + condições -->
                                <div class="campos-entrada-esq d-none" id="campsDoacaoEsq">
                                    <label class="form-label">Data de entrada</label>
                                    <input type="date" name="data_entrada" class="form-control mb-3">
                                    <label class="form-label">Condições da doação</label>
                                    <textarea name="condicoes_doacao" class="form-control mb-3" rows="3"></textarea>
                                </div>
                                <!-- Empréstimo: entidade proprietária + data início -->
                                <div class="campos-entrada-esq d-none" id="camposEmprestimoEsq">
                                    <label class="form-label">Entidade proprietária</label>
                                    <input type="text" name="entidade_proprietaria" class="form-control mb-3" placeholder="Ex: Hospital de Santa Maria">
                                    <label class="form-label">Data de início do empréstimo</label>
                                    <input type="date" name="data_inicio_emprestimo" class="form-control mb-3">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Compra: custo + fatura + método -->
                                <div class="campos-entrada" id="camposCompra">
                                    <label class="form-label">Custo de aquisição (€)</label>
                                    <input type="number" step="0.01" name="custo_aquisicao" class="form-control mb-3">
                                    <label class="form-label">Número da fatura</label>
                                    <input type="text" name="numero_fatura" class="form-control mb-3">
                                    <label class="form-label">Método de pagamento</label>
                                    <input type="text" name="metodo_pagamento" class="form-control mb-3" placeholder="Ex: Transferência bancária">
                                </div>
                                <!-- Aluguer: data fim + condições -->
                                <div class="campos-entrada d-none" id="camposAluguer">
                                    <label class="form-label">Data de fim do aluguer</label>
                                    <input type="date" name="data_fim_aluguer" class="form-control mb-3">
                                    <label class="form-label">Condições do aluguer</label>
                                    <textarea name="condicoes_aluguer" class="form-control mb-3" rows="3"></textarea>
                                </div>
                                <!-- Doação: vazio (condições já na esquerda) -->
                                <div class="campos-entrada d-none" id="camposDoacao"></div>
                                <!-- Empréstimo: data devolução + condições -->
                                <div class="campos-entrada d-none" id="camposEmprestimo">
                                    <label class="form-label">Data prevista de devolução</label>
                                    <input type="date" name="data_prevista_devolucao" class="form-control mb-3">
                                    <label class="form-label">Condições do empréstimo</label>
                                    <textarea name="condicoes_emprestimo" class="form-control mb-3" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ── TAB: FORNECEDOR ─────────────────────────────── -->
                    <div class="tab-pane fade" id="fornecedor">
                        <h4><i class="fa-solid fa-truck me-2"></i>Fornecedores Associados</h4>
                        <p class="text-muted">Podes associar um ou mais fornecedores a este equipamento, cada um com o seu tipo de relação.</p>

                        <div id="areaFornecedores">
                            <!-- Linha 1 (sempre visível) -->
                            <div class="border rounded p-3 mb-3 bg-white linha-fornecedor">
                                <div class="row align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Fornecedor</label>
                                        <select name="fornecedores[0][id_fornecedor]" class="form-select">
                                            <option value="">— Sem fornecedor —</option>
                                            <?php foreach ($fornecedores as $f): ?>
                                                <option value="<?= $f['id_fornecedor'] ?>"><?= htmlspecialchars($f['nome_empresa']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Tipo de relação</label>
                                        <select name="fornecedores[0][id_tipo_relacao]" class="form-select">
                                            <option value="">— Selecionar tipo —</option>
                                            <?php foreach ($tiposRelacao as $tr): ?>
                                                <option value="<?= $tr['id_tipo_relacao'] ?>"><?= htmlspecialchars($tr['tipo']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-outline-danger w-100 btn-remover-fornecedor" onclick="removerFornecedor(this)" style="display:none">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary" onclick="adicionarFornecedor()">
                            <i class="fa-solid fa-plus me-1"></i> Adicionar outro fornecedor
                        </button>

                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>
                            Podes associar mais fornecedores depois de criar o equipamento, na página de edição.
                        </div>
                    </div>

                    <!-- ── TAB: LOCALIZAÇÃO ────────────────────────────── -->
                    <div class="tab-pane fade" id="localizacao">
                        <h4><i class="fa-solid fa-location-dot me-2"></i>Localização Associada</h4>
                        <label class="form-label">Selecionar localização existente</label>
                        <select name="localizacao" class="form-select mb-4" required>
                            <option value="" disabled <?= empty($_POST['localizacao'] ?? '') ? 'selected' : '' ?>>Selecionar localização</option>
                            <?php foreach ($localizacoes as $loc): ?>
                                <option value="<?= $loc['id_localizacao'] ?>" <?= (($_POST['localizacao'] ?? '') == $loc['id_localizacao']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loc['edificio'] . ' • ' . $loc['piso'] . ' • Sala ' . $loc['sala'] . ' • ' . $loc['nome_servico']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="alert alert-info mb-0">
                            <i class="fa-solid fa-circle-info me-2"></i>A localização é criada e gerida no módulo de localizações.
                        </div>
                    </div>

                    <!-- ── TAB: GARANTIAS ──────────────────────────────── -->
                    <div class="tab-pane fade" id="garantias">
                        <h4><i class="fa-solid fa-shield-halved me-2"></i>Garantias</h4>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="semGarantias" onchange="toggleGarantias()">
                            <label class="form-check-label" for="semGarantias">Este equipamento não possui garantias</label>
                        </div>
                        <div id="areaGarantias">
                            <div class="border rounded p-3 mb-3 bg-white">
                                <label class="form-label">Nome da garantia</label>
                                <input type="text" class="form-control mb-3">
                                <label class="form-label">Data de início</label>
                                <input type="date" class="form-control mb-3">
                                <label class="form-label">Data de fim</label>
                                <input type="date" class="form-control mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select mb-3">
                                    <option selected>Ativa</option>
                                    <option>Expirada</option>
                                </select>
                                <input type="file" id="novaGarantia" hidden>
                                <label for="novaGarantia" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-upload me-1"></i> Selecionar PDF</label>
                            </div>
                            <button type="button" class="btn btn-outline-primary"><i class="fa-solid fa-plus me-1"></i> Adicionar Garantia</button>
                        </div>
                    </div>

                    <!-- ── TAB: CONTRATOS ──────────────────────────────── -->
                    <div class="tab-pane fade" id="contratos">
                        <h4><i class="fa-solid fa-file-contract me-2"></i>Contratos</h4>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="semContratos" onchange="toggleContratos()">
                            <label class="form-check-label" for="semContratos">Este equipamento não possui contratos</label>
                        </div>
                        <div id="areaContratos">
                            <div class="border rounded p-3 mb-3 bg-white">
                                <label class="form-label">Nome do contrato</label>
                                <input type="text" class="form-control mb-3">
                                <label class="form-label">Fornecedor associado</label>
                                <select class="form-select mb-3">
                                    <option value="">Selecionar fornecedor</option>
                                    <option>Philips Healthcare</option>
                                    <option>GE Healthcare</option>
                                    <option>Siemens Healthineers</option>
                                    <option>MedTech Solutions</option>
                                </select>
                                <label class="form-label">Data de início</label>
                                <input type="date" class="form-control mb-3">
                                <label class="form-label">Data de fim</label>
                                <input type="date" class="form-control mb-3">
                                <label class="form-label">Valor anual (€)</label>
                                <input type="text" class="form-control mb-3">
                                <input type="file" id="novoContrato" hidden>
                                <label for="novoContrato" class="btn btn-outline-primary btn-sm"><i class="fa-solid fa-upload me-1"></i> Selecionar PDF</label>
                            </div>
                            <button type="button" class="btn btn-outline-primary"><i class="fa-solid fa-plus me-1"></i> Adicionar Contrato</button>
                        </div>
                    </div>

                </div><!-- /tab-content -->
            </form>

            <script>
            // ── Aquisição: mostrar campos por tipo ──────────────────────────
            function mostrarCamposEntrada() {
                const tipo = document.getElementById("tipoEntradaEquipamento").value;
                document.querySelectorAll(".campos-entrada").forEach(b => b.classList.add("d-none"));
                document.querySelectorAll(".campos-entrada-esq").forEach(b => b.classList.add("d-none"));
                const mapa = {
                    "1": ["camposCompra",     "camposCompraEsq"],
                    "2": ["camposAluguer",    "camposAluguerEsq"],
                    "3": ["camposDoacao",     "campsDoacaoEsq"],
                    "4": ["camposEmprestimo", "camposEmprestimoEsq"]
                };
                if (mapa[tipo]) {
                    mapa[tipo].forEach(id => document.getElementById(id)?.classList.remove("d-none"));
                }
            }

            // ── Componentes ─────────────────────────────────────────────────
            let contadorComponentes = 0;

            function adicionarComponente() {
                const i = contadorComponentes++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 bg-white';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Componente ${i + 1}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.border').remove()">
                            <i class="fa-solid fa-trash me-1"></i> Eliminar
                        </button>
                    </div>
                    <label class="form-label">Código do componente</label>
                    <input type="text" name="componentes[${i}][codigo]" class="form-control mb-2" placeholder="Ex: EQ-0001.01">
                    <small class="text-muted d-block mb-3">Formato recomendado: código do equipamento + índice (EQ-0001.01)</small>
                    <label class="form-label">Nome do componente</label>
                    <input type="text" name="componentes[${i}][nome]" class="form-control mb-3" placeholder="Ex: Sensor SpO₂">
                    <label class="form-label">Estado</label>
                    <select name="componentes[${i}][estado]" class="form-select mb-3">
                        <option value="" selected disabled>Selecionar estado</option>
                        <option>Funcional</option>
                        <option>Em manutenção</option>
                        <option>Avariado</option>
                        <option>Substituído</option>
                        <option>Abatido</option>
                    </select>
                    <label class="form-label">Notificação</label>
                    <textarea name="componentes[${i}][notificacao]" class="form-control mb-0" rows="3"></textarea>
                `;
                document.getElementById('listaComponentes').appendChild(div);
            }

            function toggleComponentes() {
                const checked = document.getElementById("semComponentes").checked;
                document.getElementById("areaComponentes").classList.toggle("d-none", checked);
            }

            // ── Consumíveis ─────────────────────────────────────────────────
            let contadorConsumiveis = 0;

            function adicionarConsumivel() {
                const i = contadorConsumiveis++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 bg-white';
                div.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Consumível ${i + 1}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.border').remove()">
                            <i class="fa-solid fa-trash me-1"></i> Eliminar
                        </button>
                    </div>
                    <label class="form-label">Nome do consumível</label>
                    <input type="text" name="consumiveis[${i}][nome]" class="form-control mb-3" placeholder="Ex: Soro Fisiológico 500 ml">
                    <label class="form-label">Stock atual</label>
                    <input type="number" name="consumiveis[${i}][stock_atual]" class="form-control mb-3" placeholder="Ex: 25">
                    <label class="form-label">Stock mínimo</label>
                    <input type="number" name="consumiveis[${i}][stock_minimo]" class="form-control mb-3" placeholder="Ex: 10">
                    <label class="form-label">Última atualização do stock</label>
                    <input type="date" name="consumiveis[${i}][data_atualizacao]" class="form-control mb-3">
                    <label class="form-label">Observações</label>
                    <textarea name="consumiveis[${i}][observacoes]" class="form-control mb-0" rows="3"></textarea>
                `;
                document.getElementById('listaConsumiveis').appendChild(div);
            }

            function toggleConsumiveis() {
                const checked = document.getElementById("semConsumiveis").checked;
                document.getElementById("areaConsumiveis").classList.toggle("d-none", checked);
            }

            // ── Fornecedores ────────────────────────────────────────────────
            let contadorFornecedores = 1;
            const fornecedoresOpcoes = `<?php foreach ($fornecedores as $f): ?><option value="<?= $f['id_fornecedor'] ?>"><?= htmlspecialchars($f['nome_empresa']) ?></option><?php endforeach; ?>`;
            const tiposOpcoes = `<?php foreach ($tiposRelacao as $tr): ?><option value="<?= $tr['id_tipo_relacao'] ?>"><?= htmlspecialchars($tr['tipo']) ?></option><?php endforeach; ?>`;

            function adicionarFornecedor() {
                const i = contadorFornecedores++;
                const div = document.createElement('div');
                div.className = 'border rounded p-3 mb-3 bg-white linha-fornecedor';
                div.innerHTML = `
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Fornecedor</label>
                            <select name="fornecedores[${i}][id_fornecedor]" class="form-select">
                                <option value="">— Sem fornecedor —</option>
                                ${fornecedoresOpcoes}
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Tipo de relação</label>
                            <select name="fornecedores[${i}][id_tipo_relacao]" class="form-select">
                                <option value="">— Selecionar tipo —</option>
                                ${tiposOpcoes}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removerFornecedor(this)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>`;
                document.getElementById('areaFornecedores').appendChild(div);
                document.querySelector('.btn-remover-fornecedor').style.display = '';
            }

            function removerFornecedor(btn) {
                btn.closest('.linha-fornecedor').remove();
                if (document.querySelectorAll('.linha-fornecedor').length === 1) {
                    document.querySelector('.btn-remover-fornecedor').style.display = 'none';
                }
            }

            // ── Garantias / Contratos ───────────────────────────────────────
            function toggleGarantias() {
                const checked = document.getElementById("semGarantias").checked;
                document.getElementById("areaGarantias").classList.toggle("d-none", checked);
            }

            function toggleContratos() {
                const checked = document.getElementById("semContratos").checked;
                document.getElementById("areaContratos").classList.toggle("d-none", checked);
            }
            </script>

        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
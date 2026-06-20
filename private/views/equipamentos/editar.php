<?php

require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();

if (!in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'])) {
    header('Location: ' . BASE_URL . '/login/login.php');
    exit();
}

require_once __DIR__ . '/../../includes/database.php';
$idEquipamentoEncriptado = $_GET['id'] ?? null;
$idEquipamento = aes_decrypt($idEquipamentoEncriptado);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit();
}

$erros = [];
$erro_sistema = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codigo = trim($_POST["codigo"] ?? "");
    $designacao = trim($_POST["designacao"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $marca = trim($_POST["marca"] ?? "");
    $modelo = trim($_POST["modelo"] ?? "");
    $numero_serie = trim($_POST["numero_serie"] ?? "");
    $ano_fabrico = trim($_POST["ano_fabrico"] ?? "");
    $estado = trim($_POST["estado"] ?? "");
    $criticidade = trim($_POST["criticidade"] ?? "");
    $localizacao = trim($_POST["localizacao"] ?? "");
    $observacoes = trim($_POST["observacoes"] ?? "");

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
        $codigo = strtoupper($codigo);
        $designacao = ucwords(strtolower($designacao));
        $numero_serie = strtoupper($numero_serie);
    }

    if (empty($erros)) {
        try {
            $sql = "UPDATE equipamentos SET codigo_interno = :codigo, designacao = :designacao, id_categoria = :categoria, marca = :marca, modelo = :modelo, numero_serie = :numero_serie, ano_fabrico = :ano_fabrico, id_estado = :estado, id_criticidade = :criticidade, id_localizacao = :localizacao, observacoes = :observacoes, updated_at = NOW() WHERE id_equipamento = :id";
            $query = $database->prepare($sql);
            $query->execute([
                ":codigo" => $codigo,
                ":designacao" => $designacao,
                ":categoria" => $categoria,
                ":marca" => $marca,
                ":modelo" => $modelo,
                ":numero_serie" => $numero_serie,
                ":ano_fabrico" => $ano_fabrico !== "" ? $ano_fabrico : null,
                ":estado" => $estado,
                ":criticidade" => $criticidade,
                ":localizacao" => $localizacao,
                ":observacoes" => $observacoes,
                ":id" => $idEquipamento
            ]);
            header("Location: lista.php?guardado=1");
            exit();
        } catch (PDOException $err) {
            $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
        }
    }
}
try {
    $stmt = $database->prepare("SELECT * FROM equipamentos WHERE id_equipamento = :id AND ativo = 1");
    $stmt->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
    $stmt->execute();
    $equipamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $err) {
    $erro_sistema = "Erro ao obter os dados do equipamento: " . $err->getMessage();
    $equipamento = null;
}

$categorias = $database->query("SELECT id_categoria, nome_categoria FROM categorias WHERE ativo = 1 ORDER BY nome_categoria")->fetchAll(PDO::FETCH_ASSOC);
$estados = $database->query("SELECT id_estado, nome_estado FROM estados_equipamento WHERE ativo = 1 ORDER BY nome_estado")->fetchAll(PDO::FETCH_ASSOC);
$criticidades = $database->query("SELECT id_criticidade, nivel FROM criticidades WHERE ativo = 1 ORDER BY id_criticidade")->fetchAll(PDO::FETCH_ASSOC);
$localizacoes = $database->query("SELECT l.id_localizacao, l.edificio, l.piso, l.sala, s.nome_servico FROM localizacoes l INNER JOIN servicos s ON l.id_servico = s.id_servico WHERE l.ativo = 1 ORDER BY l.edificio, l.piso, l.sala")->fetchAll(PDO::FETCH_ASSOC);

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
                    <p class="text-muted mb-0"><?= htmlspecialchars(($equipamento['designacao'] ?? '') . ' • ' . ($equipamento['marca'] ?? '') . ' ' . ($equipamento['modelo'] ?? '') . ' • ' . ($equipamento['codigo_interno'] ?? '')) ?></p>
                </div>
                <div>
                    <a href="detalhes.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" form="formEditarEquipamento" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar Alterações
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
            <form id="formEditarEquipamento" action="editar.php?id=<?= $idEquipamentoEncriptado ?>" method="post" novalidate>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="equipamento">
                        <h4><i class="fa-solid fa-stethoscope me-2"></i>Informação do Equipamento</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Código Interno</label>
                                <input type="text" name="codigo" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['codigo_interno'] ?? '') ?>" required>
                                <label class="form-label">Designação</label>
                                <input type="text" name="designacao" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['designacao'] ?? '') ?>" required>
                                <label class="form-label">Categoria</label>
                                <select name="categoria" class="form-select mb-3" required>
                                    <option value="" disabled>Selecionar categoria</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id_categoria'] ?>" <?= (($equipamento['id_categoria'] ?? '') == $cat['id_categoria']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['nome_categoria']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['marca'] ?? '') ?>" required>
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['modelo'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Número de Série</label>
                                <input type="text" name="numero_serie" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['numero_serie'] ?? '') ?>" required>
                                <label class="form-label">Ano de Fabrico</label>
                                <input type="number" name="ano_fabrico" class="form-control mb-3" value="<?= htmlspecialchars($equipamento['ano_fabrico'] ?? '') ?>">
                                <label class="form-label">Estado</label>
                                <select name="estado" class="form-select mb-3" required>
                                    <option value="" disabled>Selecionar estado</option>
                                    <?php foreach ($estados as $est): ?>
                                        <option value="<?= $est['id_estado'] ?>" <?= (($equipamento['id_estado'] ?? '') == $est['id_estado']) ? 'selected' : '' ?>><?= htmlspecialchars($est['nome_estado']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <label class="form-label">Criticidade</label>
                                <select name="criticidade" class="form-select mb-3" required>
                                    <option value="" disabled>Selecionar criticidade</option>
                                    <?php foreach ($criticidades as $crit): ?>
                                        <option value="<?= $crit['id_criticidade'] ?>" <?= (($equipamento['id_criticidade'] ?? '') == $crit['id_criticidade']) ? 'selected' : '' ?>><?= htmlspecialchars($crit['nivel']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <label class="form-label">Observações</label>
                        <textarea name="observacoes" class="form-control mb-4" rows="4"><?= htmlspecialchars($equipamento['observacoes'] ?? '') ?></textarea>
                        <hr>
                        <h5><i class="fa-solid fa-file-lines me-2"></i>Documentos do Equipamento</h5>
                        <p class="text-muted">Adiciona, substitui ou remove os documentos próprios deste equipamento.</p>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    Manual de Operação Philips IntelliVue MP5
                                    <span class="badge bg-light text-primary border ms-2">Manual</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de documento</label>
                                    <select class="form-select mb-2 tipo-documento" onchange="mostrarOutroDocumento(this)">
                                        <option selected>Manual de utilizador</option>
                                        <option>Ficha Técnica</option>
                                        <option>Certificação</option>
                                        <option>Relatório de uso</option>
                                        <option>Manual de manutenção</option>
                                        <option>Outro</option>
                                    </select>
                                    <input type="text" class="form-control campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ficheiro atual</label>
                                    <p class="mb-1">
                                        <i class="fa-regular fa-file-lines me-2"></i>
                                        manual_operacao_philips_intellivue_mp5.pdf
                                    </p>
                                    <small class="text-muted d-block mb-3">1.24 MB • PDF</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="file" id="manualOperacaoMP5" hidden>
                                    <label for="manualOperacaoMP5" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-upload me-1"></i>
                                        Substituir Ficheiro
                                    </label>
                                    <small class="d-block text-muted mt-2">PDF, JPG ou PNG</small>
                                </div>
                            </div>
                        </div>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    Ficha Técnica Philips IntelliVue MP5
                                    <span class="badge bg-light text-primary border ms-2">Ficha Técnica</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de documento</label>
                                    <select class="form-select mb-2 tipo-documento" onchange="mostrarOutroDocumento(this)">
                                        <option>Manual de utilizador</option>
                                        <option selected>Ficha Técnica</option>
                                        <option>Certificação</option>
                                        <option>Relatório de uso</option>
                                        <option>Manual de manutenção</option>
                                        <option>Outro</option>
                                    </select>
                                    <input type="text" class="form-control campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ficheiro atual</label>
                                    <p class="mb-1">
                                        <i class="fa-regular fa-file-lines me-2"></i>
                                        ficha_tecnica_philips_intellivue_mp5.pdf
                                    </p>
                                    <small class="text-muted d-block mb-3">980 KB • PDF</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="file" id="fichaTecnicaMP5" hidden>
                                    <label for="fichaTecnicaMP5" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-upload me-1"></i>
                                        Substituir Ficheiro
                                    </label>
                                    <small class="d-block text-muted mt-2">PDF, JPG ou PNG</small>
                                </div>
                            </div>
                        </div>
                        <div class="border rounded p-3 mb-4 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    Certificado CE do Equipamento IntelliVue MP5
                                    <span class="badge bg-light text-primary border ms-2">Certificação</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de documento</label>
                                    <select class="form-select mb-2 tipo-documento" onchange="mostrarOutroDocumento(this)">
                                        <option>Manual de utilizador</option>
                                        <option>Ficha Técnica</option>
                                        <option selected>Certificação</option>
                                        <option>Relatório de uso</option>
                                        <option>Manual de manutenção</option>
                                        <option>Outro</option>
                                    </select>
                                    <input type="text" class="form-control campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ficheiro atual</label>
                                    <p class="mb-1">
                                        <i class="fa-regular fa-file-lines me-2"></i>
                                        certificado_ce_intellivue_mp5.pdf
                                    </p>
                                    <small class="text-muted d-block mb-3">450 KB • PDF</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="file" id="certificadoCEmp5" hidden>
                                    <label for="certificadoCEmp5" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-upload me-1"></i>
                                        Substituir Ficheiro
                                    </label>
                                    <small class="d-block text-muted mt-2">PDF, JPG ou PNG</small>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar Documento
                        </button>
                    </div>
                    <div class="tab-pane fade" id="componentes">
                        <h4><i class="fa-solid fa-microchip me-2"></i>Componentes Associados</h4>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h5>Componente 1</h5>
                            <label class="form-label">Código do componente</label>
                            <input type="text" class="form-control mb-2" value="EQ-0001.01">
                            <small class="text-muted d-block mb-3">Formato recomendado: EQ-0001.01</small>
                            <label class="form-label">Nome do componente</label>
                            <input type="text" class="form-control mb-3" value="Sensor SpO₂ Philips M1191BL">
                            <label class="form-label">Estado</label>
                            <select class="form-select mb-3">
                                <option selected>Funcional</option>
                                <option>Em manutenção</option>
                                <option>Avariado</option>
                                <option>Substituído</option>
                                <option>Abatido</option>
                            </select>
                            <label class="form-label">Notificação</label>
                            <textarea class="form-control mb-3" rows="3">Sem notificações</textarea>
                            <hr>
                            <h6>
                                <i class="fa-solid fa-file-lines me-2"></i>
                                Documentos do componente
                            </h6>
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                        Manual do Sensor SpO₂ Philips M1191BL
                                        <span class="badge bg-light text-primary border ms-2">Manual</span>
                                    </h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm">
                                        <i class="fa-solid fa-trash me-1"></i>
                                        Eliminar
                                    </button>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Tipo de documento</label>
                                        <select class="form-select mb-2 tipo-documento" onchange="mostrarOutroDocumento(this)">
                                            <option selected>Manual de componente</option>
                                            <option>Ficha Técnica</option>
                                            <option>Certificação</option>
                                            <option>Relatório de teste</option>
                                            <option>Registo de substituição</option>
                                            <option>Outro</option>
                                        </select>
                                        <input type="text" class="form-control campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Ficheiro atual</label>
                                        <p class="mb-1">
                                            <i class="fa-regular fa-file-lines me-2"></i>
                                            manual_sensor_spo2_m1191bl.pdf
                                        </p>
                                        <small class="text-muted d-block mb-3">620 KB • PDF</small>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="file" id="docSensorSpo2" hidden>
                                        <label for="docSensorSpo2" class="btn btn-outline-primary btn-sm">
                                            <i class="fa-solid fa-upload me-1"></i>
                                            Substituir Ficheiro
                                        </label>
                                        <small class="d-block text-muted mt-2">PDF, JPG ou PNG</small>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded p-3 mb-3">
                                <h6>
                                    <i class="fa-solid fa-plus me-2"></i>
                                    Adicionar documento ao componente
                                </h6>
                                <div class="row align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Nome do documento</label>
                                        <input type="text" class="form-control" placeholder="Ex: Relatório de teste do sensor">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Tipo de documento</label>
                                        <select class="form-select tipo-documento" onchange="mostrarOutroDocumento(this)">
                                            <option selected>Selecionar tipo</option>
                                            <option>Manual de componente</option>
                                            <option>Ficha Técnica</option>
                                            <option>Certificação</option>
                                            <option>Relatório de teste</option>
                                            <option>Registo de substituição</option>
                                            <option>Outro</option>
                                        </select>
                                        <input type="text" class="form-control mt-2 campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ficheiro</label>
                                        <input type="file" id="novoDocumentoComponente" hidden>
                                        <label for="novoDocumentoComponente" class="btn btn-outline-primary w-100">
                                            <i class="fa-solid fa-upload me-1"></i>
                                            Selecionar ficheiro
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary w-100">
                                            <i class="fa-solid fa-plus me-1"></i>
                                            Adicionar
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i>
                                Eliminar componente
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar Componente
                        </button>
                        <hr>
                        <h4><i class="fa-solid fa-box-open me-2"></i>Consumíveis</h4>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h5>Consumível 1</h5>
                            <label class="form-label">Nome do consumível</label>
                            <input type="text" class="form-control mb-3" value="Soro Fisiológico 500 ml">
                            <label class="form-label">Stock atual</label>
                            <input type="number" class="form-control mb-3" value="24">
                            <label class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control mb-3" value="10">
                            <label class="form-label">Última atualização do stock</label>
                            <input type="date" class="form-control mb-3" value="2026-06-09">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control mb-3" rows="3">Necessário para administração intravenosa.</textarea>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i>
                                Eliminar consumível
                            </button>
                        </div>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h5>Consumível 2</h5>
                            <label class="form-label">Nome do consumível</label>
                            <input type="text" class="form-control mb-3" value="Sistema de Perfusão">
                            <label class="form-label">Stock atual</label>
                            <input type="number" class="form-control mb-3" value="35">
                            <label class="form-label">Stock mínimo</label>
                            <input type="number" class="form-control mb-3" value="15">
                            <label class="form-label">Última atualização do stock</label>
                            <input type="date" class="form-control mb-3" value="2026-06-08">
                            <label class="form-label">Observações</label>
                            <textarea class="form-control mb-3" rows="3">Compatível com bomba de infusão.</textarea>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i>
                                Eliminar consumível
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar Consumível
                        </button>
                    </div>
                    <div class="tab-pane fade" id="aquisicao">
                        <h4><i class="fa-solid fa-cart-shopping me-2"></i>Dados de Aquisição / Entrada</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Tipo de entrada</label>
                                <select class="form-select mb-3" id="tipoEntradaEquipamento" onchange="mostrarCamposEntrada()">
                                    <option value="compra" selected>Compra</option>
                                    <option value="aluguer">Aluguer</option>
                                    <option value="doacao">Doação</option>
                                    <option value="emprestimo">Empréstimo</option>
                                </select>
                                <label class="form-label">Data de entrada</label>
                                <input type="date" class="form-control mb-3" value="2023-03-15">
                                <label class="form-label">Entidade associada</label>
                                <input type="text" class="form-control mb-3" value="Philips Healthcare">
                            </div>
                            <div class="col-md-6">
                                <div class="campos-entrada" id="camposCompra">
                                    <label class="form-label">Custo de aquisição</label>
                                    <input type="number" class="form-control mb-3" value="3500">
                                    <label class="form-label">Número da fatura</label>
                                    <input type="text" class="form-control mb-3" value="FT-2023/4587">
                                    <label class="form-label">Método de pagamento</label>
                                    <input type="text" class="form-control mb-3" value="Transferência Bancária">
                                </div>
                                <div class="campos-entrada d-none" id="camposAluguer">
                                    <label class="form-label">Valor mensal</label>
                                    <input type="number" class="form-control mb-3" placeholder="Ex: 250">
                                    <label class="form-label">Data de fim do aluguer</label>
                                    <input type="date" class="form-control mb-3">
                                    <label class="form-label">Condições do aluguer</label>
                                    <textarea class="form-control mb-3" rows="2" placeholder="Ex: manutenção incluída, renovação anual..."></textarea>
                                </div>
                                <div class="campos-entrada d-none" id="camposDoacao">
                                    <label class="form-label">Entidade doadora</label>
                                    <input type="text" class="form-control mb-3" placeholder="Ex: Fundação Saúde+">
                                    <label class="form-label">Valor estimado</label>
                                    <input type="number" class="form-control mb-3" placeholder="Ex: 3500">
                                    <label class="form-label">Condições da doação</label>
                                    <textarea class="form-control mb-3" rows="2" placeholder="Ex: equipamento doado sem custos associados..."></textarea>
                                </div>
                                <div id="camposEmprestimo" class="campos-entrada d-none">
                                    <label class="form-label">Entidade proprietária</label>
                                    <input type="text" class="form-control mb-3" placeholder="Hospital Central de Braga">
                                    <label class="form-label">Data de início do empréstimo</label>
                                    <input type="date" class="form-control mb-3">
                                    <label class="form-label">Data prevista de devolução</label>
                                    <input type="date" class="form-control mb-3">
                                    <label class="form-label">Condições do empréstimo</label>
                                    <textarea class="form-control mb-3" rows="3" placeholder="Ex: utilização temporária durante manutenção do equipamento principal"></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h5><i class="fa-solid fa-file-lines me-2"></i>Documentos da Entrada</h5>
                        <p class="text-muted">Os documentos mudam consoante o tipo de entrada do equipamento.</p>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fa-regular fa-file-pdf me-2 text-danger"></i>
                                    Fatura de Aquisição do Monitor Philips MP5
                                    <span class="badge bg-light text-primary border ms-2">Fatura</span>
                                </h6>
                                <button type="button" class="btn btn-outline-danger btn-sm">
                                    <i class="fa-solid fa-trash me-1"></i>
                                    Eliminar
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Tipo de documento</label>
                                    <select class="form-select mb-2 tipo-documento" onchange="mostrarOutroDocumento(this)">
                                        <option selected>Fatura</option>
                                        <option>Comprovativo de pagamento</option>
                                        <option>Contrato de aluguer</option>
                                        <option>Termo de doação</option>
                                        <option>Termo de empréstimo</option>
                                        <option>Guia de transporte</option>
                                        <option>Auto de receção</option>
                                        <option>Outro</option>
                                    </select>
                                    <input type="text" class="form-control campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Ficheiro atual</label>
                                    <p class="mb-1">
                                        <i class="fa-regular fa-file-lines me-2"></i>
                                        fatura_aquisicao_monitor_mp5_ft20234587.pdf
                                    </p>
                                    <small class="text-muted d-block mb-3">700 KB • PDF</small>
                                </div>
                                <div class="col-md-4">
                                    <input type="file" id="faturaAquisicaoMP5" hidden>
                                    <label for="faturaAquisicaoMP5" class="btn btn-outline-primary btn-sm">
                                        <i class="fa-solid fa-upload me-1"></i>
                                        Substituir Ficheiro
                                    </label>
                                    <small class="d-block text-muted mt-2">PDF, JPG ou PNG</small>
                                </div>
                            </div>
                        </div>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h6>
                                <i class="fa-solid fa-plus me-2"></i>
                                Adicionar novo documento de entrada
                            </h6>
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Nome do documento</label>
                                    <input type="text" class="form-control" placeholder="Ex: Termo de doação do equipamento">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tipo de documento</label>
                                    <select class="form-select tipo-documento" onchange="mostrarOutroDocumento(this)">
                                        <option selected>Selecionar tipo</option>
                                        <option>Fatura</option>
                                        <option>Comprovativo de pagamento</option>
                                        <option>Contrato de aluguer</option>
                                        <option>Termo de doação</option>
                                        <option>Termo de empréstimo</option>
                                        <option>Guia de transporte</option>
                                        <option>Auto de receção</option>
                                        <option>Outro</option>
                                    </select>
                                    <input type="text" class="form-control mt-2 campo-outro-documento d-none" placeholder="Escreve o tipo de documento">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ficheiro</label>
                                    <input type="file" id="novoDocumentoEntrada" hidden>
                                    <label for="novoDocumentoEntrada" class="btn btn-outline-primary w-100">
                                        <i class="fa-solid fa-upload me-1"></i>
                                        Selecionar ficheiro
                                    </label>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-plus me-1"></i>
                                        Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="fornecedor">
                        <h4><i class="fa-solid fa-truck me-2"></i>Fornecedor Associado</h4>
                        <label class="form-label">Selecionar fornecedor</label>
                        <select class="form-select mb-4">
                            <option selected>Philips Healthcare</option>
                            <option>MedTech Solutions</option>
                            <option>GE Healthcare</option>
                            <option>Siemens Healthineers</option>
                        </select>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Nome da empresa:</strong> Philips Healthcare</p>
                                <p><strong>NIF:</strong> 501234567</p>
                                <p><strong>Tipo:</strong> Fabricante</p>
                                <p><strong>Telefone:</strong> 222 000 100</p>
                                <p><strong>Email:</strong> geral@philips-healthcare.pt</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Website:</strong> www.philips.pt</p>
                                <p><strong>Pessoa de contacto:</strong> Ana Martins</p>
                                <p><strong>Contacto direto:</strong> 912345678</p>
                                <p><strong>Morada:</strong> Rua da Saúde, 120</p>
                                <p><strong>Cidade:</strong> Porto</p>
                            </div>
                        </div>
                        <hr>
                        <div class="text-end">
                            <a href="../fornecedores/editar.php" class="btn btn-outline-primary">
                                <i class="fa-solid fa-pen-to-square me-1"></i>
                                Editar Ficha do Fornecedor
                            </a>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="localizacao">
                        <h4><i class="fa-solid fa-location-dot me-2"></i>Localização Associada</h4>
                        <label class="form-label">Selecionar localização existente</label>
                        <select name="localizacao" class="form-select mb-4" required>
                            <option value="" disabled>Selecionar localização</option>
                            <?php foreach ($localizacoes as $loc): ?>
                                <option value="<?= $loc['id_localizacao'] ?>" <?= (($equipamento['id_localizacao'] ?? '') == $loc['id_localizacao']) ? 'selected' : '' ?>><?= htmlspecialchars($loc['edificio'] . ' • ' . $loc['piso'] . ' • Sala ' . $loc['sala'] . ' • ' . $loc['nome_servico']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="alert alert-info mb-0"><i class="fa-solid fa-circle-info me-2"></i>A localização é criada e gerida no módulo de localizações.</div>
                    </div>
                    <div class="tab-pane fade" id="garantias">
                        <h4><i class="fa-solid fa-shield-halved me-2"></i>Garantias</h4>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h5>Garantia 1</h5>
                            <label class="form-label">Nome da garantia</label>
                            <input type="text" class="form-control mb-3" value="Garantia Comercial Philips IntelliVue MP5">
                            <label class="form-label">Data de início</label>
                            <input type="date" class="form-control mb-3" value="2023-03-15">
                            <label class="form-label">Data de fim</label>
                            <input type="date" class="form-control mb-3" value="2026-03-15">
                            <label class="form-label">Estado</label>
                            <select class="form-select mb-3">
                                <option selected>Expirada</option>
                                <option>Ativa</option>
                            </select>
                            <p>
                                <strong>Ficheiro atual:</strong>
                                garantia_comercial_philips_mp5.pdf
                            </p>
                            <input type="file" id="garantiaComercialMP5" hidden>
                            <label for="garantiaComercialMP5" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-upload me-1"></i>
                                Substituir PDF
                            </label>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i>
                                Eliminar garantia
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar Garantia
                        </button>
                    </div>
                    <div class="tab-pane fade" id="contratos">
                        <h4><i class="fa-solid fa-file-contract me-2"></i>Contratos</h4>
                        <div class="border rounded p-3 mb-3 bg-white">
                            <h5>Contrato 1</h5>
                            <label class="form-label">Nome do contrato</label>
                            <input type="text" class="form-control mb-3" value="Contrato de Manutenção Preventiva do Monitor MP5">
                            <label class="form-label">Fornecedor associado</label>
                            <select class="form-select mb-3">
                                <option selected>MedTech Solutions</option>
                                <option>Philips Healthcare</option>
                                <option>GE Healthcare</option>
                            </select>
                            <label class="form-label">Data de início</label>
                            <input type="date" class="form-control mb-3" value="2025-01-01">
                            <label class="form-label">Data de fim</label>
                            <input type="date" class="form-control mb-3" value="2027-12-31">
                            <label class="form-label">Valor anual</label>
                            <input type="text" class="form-control mb-3" value="850 €/ano">
                            <p>
                                <strong>Ficheiro atual:</strong>
                                contrato_manutencao_preventiva_monitor_mp5.pdf
                            </p>
                            <input type="file" id="contratoManutencaoMP5" hidden>
                            <label for="contratoManutencaoMP5" class="btn btn-outline-primary btn-sm">
                                <i class="fa-solid fa-upload me-1"></i>
                                Substituir PDF
                            </label>
                            <button type="button" class="btn btn-outline-danger btn-sm">
                                <i class="fa-solid fa-trash me-1"></i>
                                Eliminar contrato
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            Adicionar Contrato
                        </button>
                    </div>
                </div>
            </form>
            <script>
                function mostrarOutroDocumento(select) {
                    const campoOutro = select.parentElement.querySelector(".campo-outro-documento");
                    if (select.value === "Outro") {
                        campoOutro.classList.remove("d-none");
                    } else {
                        campoOutro.classList.add("d-none");
                        campoOutro.value = "";
                    }
                }
                function mostrarCamposEntrada() {
                    const tipo = document.getElementById("tipoEntradaEquipamento").value;
                    document.querySelectorAll(".campos-entrada").forEach(function (bloco) {
                        bloco.classList.add("d-none");
                    });
                    if (tipo === "compra") {
                        document.getElementById("camposCompra").classList.remove("d-none");
                    } else if (tipo === "aluguer") {
                        document.getElementById("camposAluguer").classList.remove("d-none");
                    } else if (tipo === "doacao") {
                        document.getElementById("camposDoacao").classList.remove("d-none");
                    } else if (tipo === "emprestimo") {
                        document.getElementById("camposEmprestimo").classList.remove("d-none");
                    }
                }
            </script>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
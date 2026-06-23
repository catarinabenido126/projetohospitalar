<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
$perfilAtual = $_SESSION['perfil'] ?? '';

$erro = '';
if (!empty($_SESSION['erro_permissao'])) {
    $erro = $_SESSION['erro_permissao'];
    unset($_SESSION['erro_permissao']);
}

// Restaurar arquivado
if (isset($_GET['restaurar']) && ctype_digit($_GET['restaurar']) && $perfilAtual === 'Administrador') {
    try {
        $database->prepare("UPDATE equipamentos SET ativo = 1, updated_at = NOW() WHERE id_equipamento = :id")
                 ->execute([':id' => $_GET['restaurar']]);
        registar_historico($database, 'Equipamentos', 'Restauro', null, 'Equipamento restaurado.');
    } catch (PDOException $e) {}
    header('Location: lista.php?restaurado=1');
    exit();
}

// Dropdowns para filtros
$categorias   = $database->query("SELECT id_categoria, nome_categoria FROM categorias WHERE ativo = 1 ORDER BY nome_categoria")->fetchAll(PDO::FETCH_ASSOC);
$estados      = $database->query("SELECT id_estado, nome_estado FROM estados_equipamento WHERE ativo = 1 ORDER BY nome_estado")->fetchAll(PDO::FETCH_ASSOC);
$criticidades = $database->query("SELECT id_criticidade, nivel FROM criticidades WHERE ativo = 1 ORDER BY id_criticidade")->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$pesquisa    = trim($_GET['pesquisa']    ?? '');
$categoria   = $_GET['categoria']   ?? '';
$estado      = $_GET['estado']      ?? '';
$criticidade = $_GET['criticidade'] ?? '';

if ($categoria   !== '' && !ctype_digit($categoria))   { $categoria   = ''; }
if ($estado      !== '' && !ctype_digit($estado))      { $estado      = ''; }
if ($criticidade !== '' && !ctype_digit($criticidade)) { $criticidade = ''; }

$sql = "
    SELECT e.id_equipamento, e.codigo_interno, e.designacao,
           c.nome_categoria, ee.nome_estado, cr.nivel,
           l.edificio, l.piso, l.sala
    FROM equipamentos e
    INNER JOIN categorias c           ON e.id_categoria   = c.id_categoria
    INNER JOIN estados_equipamento ee ON e.id_estado      = ee.id_estado
    INNER JOIN criticidades cr        ON e.id_criticidade = cr.id_criticidade
    INNER JOIN localizacoes l         ON e.id_localizacao = l.id_localizacao
    WHERE e.ativo = 1
";
if ($pesquisa !== '')    { $sql .= " AND (e.codigo_interno LIKE :pesquisa OR e.designacao LIKE :pesquisa)"; }
if ($categoria !== '')   { $sql .= " AND e.id_categoria   = :categoria"; }
if ($estado !== '')      { $sql .= " AND e.id_estado      = :estado"; }
if ($criticidade !== '') { $sql .= " AND e.id_criticidade = :criticidade"; }
$sql .= " ORDER BY e.codigo_interno ASC";

try {
    $query = $database->prepare($sql);
    if ($pesquisa !== '')    { $query->bindValue(':pesquisa',    "%$pesquisa%"); }
    if ($categoria !== '')   { $query->bindValue(':categoria',   (int)$categoria,   PDO::PARAM_INT); }
    if ($estado !== '')      { $query->bindValue(':estado',      (int)$estado,      PDO::PARAM_INT); }
    if ($criticidade !== '') { $query->bindValue(':criticidade', (int)$criticidade, PDO::PARAM_INT); }
    $query->execute();
    $equipamentos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem de equipamentos.";
    $equipamentos = [];
}

// Equipamentos arquivados
$equipamentosArquivados = [];
try {
    $qa = $database->query("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao, c.nome_categoria, ee.nome_estado
        FROM equipamentos e
        INNER JOIN categorias c           ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado    = ee.id_estado
        WHERE e.ativo = 0
        ORDER BY e.updated_at DESC
    ");
    $equipamentosArquivados = $qa->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Exportação CSV
if (($_GET['exportar'] ?? '') === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="equipamentos_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Código', 'Designação', 'Categoria', 'Estado', 'Criticidade', 'Edifício', 'Piso', 'Sala'], ';');
    foreach ($equipamentos as $eq) {
        fputcsv($out, [
            $eq['codigo_interno'], $eq['designacao'], $eq['nome_categoria'],
            $eq['nome_estado'], $eq['nivel'],
            'Edifício ' . $eq['edificio'], 'Piso ' . $eq['piso'], 'Sala ' . $eq['sala']
        ], ';');
    }
    fclose($out);
    exit();
}

// Exportação JSON
if (($_GET['exportar'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="equipamentos_' . date('Y-m-d') . '.json"');
    $dados = array_map(fn($eq) => [
        'codigo_interno' => $eq['codigo_interno'],
        'designacao'     => $eq['designacao'],
        'categoria'      => $eq['nome_categoria'],
        'estado'         => $eq['nome_estado'],
        'criticidade'    => $eq['nivel'],
        'localizacao'    => 'Edifício ' . $eq['edificio'] . ' · Piso ' . $eq['piso'] . ' · Sala ' . $eq['sala']
    ], $equipamentos);
    echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// Dados para jsPDF
$dadosPDF = array_map(fn($eq) => [
    htmlspecialchars($eq['codigo_interno']),
    htmlspecialchars($eq['designacao']),
    htmlspecialchars($eq['nome_categoria']),
    htmlspecialchars($eq['nome_estado']),
    htmlspecialchars($eq['nivel']),
    'Ed.' . htmlspecialchars($eq['edificio']) . ' P.' . htmlspecialchars($eq['piso']) . ' S.' . htmlspecialchars($eq['sala']),
], $equipamentos);
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>

<!-- Toast -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1090">
    <div id="toastApp" class="toast align-items-center border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toastMensagem"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Equipamentos</h2>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-danger" id="btnExportarPDF">
                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                    </button>
                    <a href="lista.php?<?= http_build_query(array_merge($_GET, ['exportar' => 'excel'])) ?>" class="btn btn-outline-success">
                        <i class="fa-solid fa-file-excel me-1"></i> Excel
                    </a>
                    <a href="lista.php?<?= http_build_query(array_merge($_GET, ['exportar' => 'json'])) ?>" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-file-code me-1"></i> JSON
                    </a>
                    <?php if (in_array($perfilAtual, ['Administrador', 'Tecnico'])): ?>
                        <a href="novo.php" class="btn btn-success">
                            <i class="fa-solid fa-plus me-1"></i> Novo equipamento
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Código ou designação" value="<?= htmlspecialchars($pesquisa) ?>">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id_categoria'] ?>" <?= ($categoria == $cat['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nome_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <?php foreach ($estados as $est): ?>
                                    <option value="<?= $est['id_estado'] ?>" <?= ($estado == $est['id_estado']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($est['nome_estado']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="criticidade" class="form-label">Criticidade</label>
                            <select class="form-select" id="criticidade" name="criticidade">
                                <option value="">Todas</option>
                                <?php foreach ($criticidades as $crit): ?>
                                    <option value="<?= $crit['id_criticidade'] ?>" <?= ($criticidade == $crit['id_criticidade']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($crit['nivel']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                        <a href="lista.php" class="btn btn-secondary"><i class="fa-solid fa-broom me-1"></i> Limpar</a>
                    </div>
                </form>
            </div>
            <div class="caixa-tabela table-responsive">
                <table id="tabelaEquipamentos" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th><th>Designação</th><th>Categoria</th>
                            <th>Estado</th><th>Criticidade</th><th>Localização</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($equipamentos as $eq): ?>
                        <tr>
                            <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                            <td><?= htmlspecialchars($eq['designacao']) ?></td>
                            <td><?= htmlspecialchars($eq['nome_categoria']) ?></td>
                            <td>
                                <?php $ce = match($eq['nome_estado']) {
                                    'Ativo'         => 'bg-success',
                                    'Em manutenção' => 'bg-warning text-dark',
                                    'Em calibração' => 'bg-info text-dark',
                                    'Inativo'       => 'bg-danger',
                                    'Abatido'       => 'bg-dark',
                                    default         => 'bg-secondary'
                                }; ?>
                                <span class="badge <?= $ce ?>"><?= htmlspecialchars($eq['nome_estado']) ?></span>
                            </td>
                            <td>
                                <?php $cc = match($eq['nivel']) {
                                    'Baixa'           => 'bg-success',
                                    'Média'           => 'bg-warning text-dark',
                                    'Alta'            => 'bg-danger',
                                    'Suporte de vida' => 'bg-danger',
                                    default           => 'bg-secondary'
                                }; ?>
                                <span class="badge <?= $cc ?>"><?= htmlspecialchars($eq['nivel']) ?></span>
                            </td>
                            <td>
                                Edifício <?= htmlspecialchars($eq['edificio']) ?>
                                <br><small class="text-muted">Piso <?= htmlspecialchars($eq['piso']) ?> · Sala <?= htmlspecialchars($eq['sala']) ?></small>
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="detalhes.php?id=<?= aes_encrypt($eq['id_equipamento']) ?>" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="Ver detalhes">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if (in_array($perfilAtual, ['Administrador', 'Tecnico'])): ?>
                                    <a href="editar.php?id=<?= aes_encrypt($eq['id_equipamento']) ?>" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($perfilAtual === 'Administrador'): ?>
                                    <a href="apagar.php?id=<?= aes_encrypt($eq['id_equipamento']) ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Arquivar">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Arquivados -->
            <?php if (!empty($equipamentosArquivados)): ?>
            <div id="secaoArquivados" class="mt-5">
                <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#collapseArquivados" role="button" aria-expanded="false">
                    <i class="fa-solid fa-box-archive me-1"></i> Ver registos arquivados (<?= count($equipamentosArquivados) ?>)
                </a>
                <div class="collapse mt-2" id="collapseArquivados">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle text-muted">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th><th>Designação</th><th>Categoria</th><th>Estado</th>
                                    <?php if ($perfilAtual === 'Administrador'): ?><th class="text-center">Restaurar</th><?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($equipamentosArquivados as $eq): ?>
                                <tr>
                                    <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                    <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                    <td><?= htmlspecialchars($eq['nome_categoria']) ?></td>
                                    <td><?= htmlspecialchars($eq['nome_estado']) ?></td>
                                    <?php if ($perfilAtual === 'Administrador'): ?>
                                        <td class="text-center">
                                            <a href="lista.php?restaurar=<?= $eq['id_equipamento'] ?>" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa-solid fa-rotate-left me-1"></i>Restaurar
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- jsPDF via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
// ── Exportação PDF ────────────────────────────────────────────────────────────
const dadosEquipamentos = <?= json_encode($dadosPDF, JSON_UNESCAPED_UNICODE) ?>;

document.getElementById('btnExportarPDF').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('MediSync — Listagem de Equipamentos', 14, 16);

    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('Gerado em: <?= date('d/m/Y H:i') ?>', 14, 23);

    doc.autoTable({
        startY: 28,
        head: [['Código', 'Designação', 'Categoria', 'Estado', 'Criticidade', 'Localização']],
        body: dadosEquipamentos,
        styles: { fontSize: 8, cellPadding: 3 },
        headStyles: { fillColor: [13, 110, 253], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [245, 247, 250] },
        columnStyles: { 1: { cellWidth: 55 } },
        margin: { left: 14, right: 14 },
    });

    const totalPaginas = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPaginas; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(
            'Página ' + i + ' de ' + totalPaginas,
            doc.internal.pageSize.getWidth() / 2,
            doc.internal.pageSize.getHeight() - 8,
            { align: 'center' }
        );
    }

    doc.save('equipamentos_<?= date('Y-m-d') ?>.pdf');
});

// ── Toasts e inicializações ───────────────────────────────────────────────────
function mostrarToast(mensagem, tipo = 'success') {
    const el = document.getElementById('toastApp');
    document.getElementById('toastMensagem').textContent = mensagem;
    el.className = 'toast align-items-center border-0 text-white ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
    new bootstrap.Toast(el, { delay: 4000 }).show();
}

const p = new URLSearchParams(window.location.search);
if (p.get('criado')     === '1') mostrarToast('Equipamento criado com sucesso.');
if (p.get('guardado')   === '1') mostrarToast('Equipamento guardado com sucesso.');
if (p.get('desativado') === '1') mostrarToast('Equipamento arquivado com sucesso.');
if (p.get('restaurado') === '1') mostrarToast('Equipamento restaurado com sucesso.');
window.history.replaceState({}, document.title, window.location.pathname);

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    $('#tabelaEquipamentos').DataTable({
        searching: false,
        pageLength: 10,
        language: {
            emptyTable: "Não existem registos",
            info: "A mostrar _START_ a _END_ de _TOTAL_ registos",
            infoEmpty: "0 registos",
            lengthMenu: "Mostrar _MENU_ registos",
            zeroRecords: "Sem resultados",
            paginate: { first: "Primeira", last: "Última", next: "Seguinte", previous: "Anterior" }
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
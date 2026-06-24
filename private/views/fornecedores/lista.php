<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
restringir_perfil(['Administrador', 'Tecnico']);
$perfilAtual = $_SESSION['perfil'] ?? '';

if (isset($_GET['restaurar']) && ctype_digit($_GET['restaurar']) && $perfilAtual === 'Administrador') {
    try {
        $database->prepare("UPDATE fornecedores SET ativo = 1, updated_at = NOW() WHERE id_fornecedor = :id")->execute([':id' => $_GET['restaurar']]);
        registar_historico($database, 'Fornecedores', 'Restauro', null, 'Fornecedor restaurado.');
    } catch (PDOException $e) {}
    header('Location: lista.php?restaurado=1');
    exit();
}

$pesquisa = $_GET['pesquisa'] ?? '';
$cidade   = $_GET['cidade']   ?? '';

$sql = "SELECT f.id_fornecedor, f.nome_empresa, f.nif, f.telefone, f.email, f.cidade FROM fornecedores f WHERE f.ativo = 1";
if ($pesquisa != '') { $sql .= " AND (f.nome_empresa LIKE :pesquisa OR f.nif LIKE :pesquisa)"; }
if ($cidade   != '') { $sql .= " AND f.cidade LIKE :cidade"; }
$sql .= " ORDER BY f.nome_empresa ASC";

$erro = '';
try {
    $query = $database->prepare($sql);
    if ($pesquisa != '') $query->bindValue(':pesquisa', "%$pesquisa%");
    if ($cidade   != '') $query->bindValue(':cidade',   "%$cidade%");
    $query->execute();
    $fornecedores = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem.";
    $fornecedores = [];
}

$fornecedoresArquivados = [];
try {
    $fornecedoresArquivados = $database->query("SELECT id_fornecedor, nome_empresa, nif, cidade FROM fornecedores WHERE ativo = 0 ORDER BY updated_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

if (($_GET['exportar'] ?? '') === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="fornecedores_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Nome da Empresa', 'NIF', 'Telefone', 'Email', 'Cidade'], ';');
    foreach ($fornecedores as $f) { fputcsv($out, [$f['nome_empresa'], $f['nif'], $f['telefone'] ?? '', $f['email'] ?? '', $f['cidade'] ?? ''], ';'); }
    fclose($out); exit();
}

if (($_GET['exportar'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="fornecedores_' . date('Y-m-d') . '.json"');
    echo json_encode(array_map(fn($f) => ['nome_empresa' => $f['nome_empresa'], 'nif' => $f['nif'], 'telefone' => $f['telefone'] ?? '', 'email' => $f['email'] ?? '', 'cidade' => $f['cidade'] ?? ''], $fornecedores), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

$dadosPDF = array_map(fn($f) => [
    htmlspecialchars($f['nome_empresa']),
    htmlspecialchars($f['nif']),
    htmlspecialchars($f['telefone'] ?? '—'),
    htmlspecialchars($f['email'] ?? '—'),
    htmlspecialchars($f['cidade'] ?? '—'),
], $fornecedores);
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
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
                <h2 class="mb-0">Listagem de Fornecedores</h2>
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
                    <?php if ($perfilAtual === 'Administrador'): ?>
                        <a href="novo.php" class="btn btn-success">
                            <i class="fa-solid fa-plus me-1"></i> Novo fornecedor
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Nome ou NIF" value="<?= htmlspecialchars($pesquisa) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Ex: Porto" value="<?= htmlspecialchars($cidade) ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                        <a href="lista.php" class="btn btn-secondary"><i class="fa-solid fa-broom me-1"></i> Limpar</a>
                    </div>
                </form>
            </div>
            <div class="caixa-tabela table-responsive">
                <table id="tabelaFornecedores" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome da Empresa</th>
                            <th>NIF</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Cidade</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fornecedores as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['nome_empresa']) ?></td>
                            <td><?= htmlspecialchars($f['nif']) ?></td>
                            <td><?= htmlspecialchars($f['telefone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($f['email'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($f['cidade'] ?? '—') ?></td>
                            <td class="text-center text-nowrap">
                                <a href="detalhes.php?id=<?= aes_encrypt($f['id_fornecedor']) ?>" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="Ver detalhes">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($perfilAtual === 'Administrador'): ?>
                                    <a href="editar.php?id=<?= aes_encrypt($f['id_fornecedor']) ?>" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="tooltip" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="apagar.php?id=<?= aes_encrypt($f['id_fornecedor']) ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Arquivar">
                                        <i class="fa-solid fa-box-archive"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($fornecedoresArquivados)): ?>
            <div class="mt-5">
                <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#collapseArquivados" role="button">
                    <i class="fa-solid fa-box-archive me-1"></i> Ver registos arquivados (<?= count($fornecedoresArquivados) ?>)
                </a>
                <div class="collapse mt-2" id="collapseArquivados">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle text-muted">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome da Empresa</th>
                                    <th>NIF</th>
                                    <th>Cidade</th>
                                    <?php if ($perfilAtual === 'Administrador'): ?>
                                        <th class="text-center">Restaurar</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fornecedoresArquivados as $f): ?>
                                <tr>
                                    <td><?= htmlspecialchars($f['nome_empresa']) ?></td>
                                    <td><?= htmlspecialchars($f['nif']) ?></td>
                                    <td><?= htmlspecialchars($f['cidade'] ?? '—') ?></td>
                                    <?php if ($perfilAtual === 'Administrador'): ?>
                                        <td class="text-center">
                                            <a href="lista.php?restaurar=<?= $f['id_fornecedor'] ?>" class="btn btn-sm btn-outline-secondary">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>

<script>
const dadosFornecedores = <?= json_encode($dadosPDF, JSON_UNESCAPED_UNICODE) ?>;

document.getElementById('btnExportarPDF').addEventListener('click', function () {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('MediSync — Listagem de Fornecedores', 14, 16);
    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('Gerado em: <?= date('d/m/Y H:i') ?>', 14, 23);
    doc.autoTable({
        startY: 28,
        head: [['Nome da Empresa', 'NIF', 'Telefone', 'Email', 'Cidade']],
        body: dadosFornecedores,
        styles: { fontSize: 9, cellPadding: 3 },
        headStyles: { fillColor: [13, 110, 253], textColor: 255, fontStyle: 'bold' },
        alternateRowStyles: { fillColor: [245, 247, 250] },
        margin: { left: 14, right: 14 },
    });
    const totalPaginas = doc.internal.getNumberOfPages();
    for (let i = 1; i <= totalPaginas; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text('Página ' + i + ' de ' + totalPaginas, doc.internal.pageSize.getWidth() / 2, doc.internal.pageSize.getHeight() - 8, { align: 'center' });
    }
    doc.save('fornecedores_<?= date('Y-m-d') ?>.pdf');
});

function mostrarToast(mensagem, tipo = 'success') {
    const el = document.getElementById('toastApp');
    document.getElementById('toastMensagem').textContent = mensagem;
    el.className = 'toast align-items-center border-0 text-white ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
    new bootstrap.Toast(el, { delay: 4000 }).show();
}

const p = new URLSearchParams(window.location.search);
if (p.get('criado')     === '1') mostrarToast('Fornecedor criado com sucesso.');
if (p.get('guardado')   === '1') mostrarToast('Fornecedor guardado com sucesso.');
if (p.get('desativado') === '1') mostrarToast('Fornecedor arquivado com sucesso.');
if (p.get('restaurado') === '1') mostrarToast('Fornecedor restaurado com sucesso.');
window.history.replaceState({}, document.title, window.location.pathname);

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    $('#tabelaFornecedores').DataTable({
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
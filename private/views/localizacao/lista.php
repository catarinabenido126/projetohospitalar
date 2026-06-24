<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
$perfilAtual = $_SESSION['perfil'] ?? '';

if (isset($_GET['restaurar']) && ctype_digit($_GET['restaurar']) && $perfilAtual === 'Administrador') {
    try {
        $database->prepare("UPDATE localizacoes SET ativo = 1, updated_at = NOW() WHERE id_localizacao = :id")->execute([':id' => $_GET['restaurar']]);
        registar_historico($database, 'Localizações', 'Restauro', null, 'Localização restaurada.');
    } catch (PDOException $e) {}
    header('Location: lista.php?restaurado=1');
    exit();
}

$servicos = $database->query("SELECT id_servico, nome_servico FROM servicos WHERE ativo = 1 ORDER BY nome_servico")->fetchAll(PDO::FETCH_ASSOC);

$pesquisa = trim($_GET['pesquisa'] ?? '');
$servico  = $_GET['servico'] ?? '';
if ($servico !== '' && !ctype_digit($servico)) { $servico = ''; }

$sql = "SELECT l.id_localizacao, l.edificio, l.piso, l.sala, l.responsavel, l.contacto, s.nome_servico FROM localizacoes l INNER JOIN servicos s ON l.id_servico = s.id_servico WHERE l.ativo = 1";
if ($pesquisa !== '') { $sql .= " AND (l.edificio LIKE :pesquisa OR l.sala LIKE :pesquisa)"; }
if ($servico  !== '') { $sql .= " AND l.id_servico = :servico"; }
$sql .= " ORDER BY l.edificio ASC, l.piso ASC, l.sala ASC";

$erro = '';
try {
    $query = $database->prepare($sql);
    if ($pesquisa !== '') { $query->bindValue(':pesquisa', "%$pesquisa%"); }
    if ($servico  !== '') { $query->bindValue(':servico', (int)$servico, PDO::PARAM_INT); }
    $query->execute();
    $localizacoes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem.";
    $localizacoes = [];
}

$localizacoesArquivadas = [];
try {
    $localizacoesArquivadas = $database->query("SELECT l.id_localizacao, l.edificio, l.piso, l.sala, s.nome_servico FROM localizacoes l INNER JOIN servicos s ON l.id_servico = s.id_servico WHERE l.ativo = 0 ORDER BY l.updated_at DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

if (($_GET['exportar'] ?? '') === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="localizacoes_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputs($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Edifício', 'Piso', 'Sala', 'Serviço', 'Responsável', 'Contacto'], ';');
    foreach ($localizacoes as $loc) { fputcsv($out, ['Edifício ' . $loc['edificio'], 'Piso ' . $loc['piso'], 'Sala ' . $loc['sala'], $loc['nome_servico'], $loc['responsavel'] ?? '', $loc['contacto'] ?? ''], ';'); }
    fclose($out); exit();
}
if (($_GET['exportar'] ?? '') === 'json') {
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="localizacoes_' . date('Y-m-d') . '.json"');
    echo json_encode(array_map(fn($loc) => ['edificio' => 'Edifício ' . $loc['edificio'], 'piso' => 'Piso ' . $loc['piso'], 'sala' => 'Sala ' . $loc['sala'], 'servico' => $loc['nome_servico'], 'responsavel' => $loc['responsavel'] ?? '', 'contacto' => $loc['contacto'] ?? ''], $localizacoes), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<style>
@media print { .barra-superior,.menu-lateral,.caixa-filtros,nav,.d-flex.justify-content-between>div:last-child { display:none!important; } .col-md-9,.col-lg-10 { width:100%!important;max-width:100%!important;flex:0 0 100%!important; } td .btn,th:last-child,td:last-child { display:none!important; } }
</style>
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
                <h2 class="mb-0">Listagem de Localizações</h2>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-outline-danger" onclick="window.print()"><i class="fa-solid fa-file-pdf me-1"></i> PDF</button>
                    <a href="lista.php?<?= http_build_query(array_merge($_GET, ['exportar' => 'excel'])) ?>" class="btn btn-outline-success"><i class="fa-solid fa-file-excel me-1"></i> Excel</a>
                    <a href="lista.php?<?= http_build_query(array_merge($_GET, ['exportar' => 'json'])) ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-file-code me-1"></i> JSON</a>
                    <?php if ($perfilAtual === 'Administrador'): ?>
                        <a href="novo.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Nova localização</a>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <?php if (!empty($erro)): ?><div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Edifício ou sala" value="<?= htmlspecialchars($pesquisa) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="servico" class="form-label">Serviço</label>
                            <select class="form-select" id="servico" name="servico">
                                <option value="">Todos</option>
                                <?php foreach ($servicos as $s): ?>
                                    <option value="<?= $s['id_servico'] ?>" <?= ($servico == $s['id_servico']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nome_servico']) ?></option>
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
                <table id="tabelaLocalizacoes" class="table table-bordered table-striped align-middle">
                    <thead><tr><th>Edifício</th><th>Piso</th><th>Sala</th><th>Serviço</th><th>Responsável</th><th>Contacto</th><th class="text-center">Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($localizacoes as $loc): ?>
                        <tr>
                            <td><?= htmlspecialchars($loc['edificio']) ?></td>
                            <td><?= htmlspecialchars($loc['piso']) ?></td>
                            <td><?= htmlspecialchars($loc['sala']) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($loc['nome_servico']) ?></span></td>
                            <td><?= htmlspecialchars($loc['responsavel'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($loc['contacto'] ?? '—') ?></td>
                            <td class="text-center text-nowrap">
                                <a href="equipamentos.php?id=<?= aes_encrypt($loc['id_localizacao']) ?>" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip" title="Ver equipamentos"><i class="fa-solid fa-eye"></i></a>
                                <?php if ($perfilAtual === 'Administrador'): ?>
                                    <a href="editar.php?id=<?= aes_encrypt($loc['id_localizacao']) ?>" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="tooltip" title="Editar"><i class="fa-solid fa-pen"></i></a>
                                    <a href="apagar.php?id=<?= aes_encrypt($loc['id_localizacao']) ?>" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Arquivar"><i class="fa-solid fa-box-archive"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($localizacoesArquivadas)): ?>
            <div class="mt-5">
                <a class="text-muted small text-decoration-none" data-bs-toggle="collapse" href="#collapseArquivados" role="button">
                    <i class="fa-solid fa-box-archive me-1"></i> Ver registos arquivados (<?= count($localizacoesArquivadas) ?>)
                </a>
                <div class="collapse mt-2" id="collapseArquivados">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle text-muted">
                            <thead class="table-light"><tr><th>Edifício</th><th>Piso</th><th>Sala</th><th>Serviço</th><?php if ($perfilAtual === 'Administrador'): ?><th class="text-center">Restaurar</th><?php endif; ?></tr></thead>
                            <tbody>
                                <?php foreach ($localizacoesArquivadas as $loc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loc['edificio']) ?></td>
                                        <td><?= htmlspecialchars($loc['piso']) ?></td>
                                        <td><?= htmlspecialchars($loc['sala']) ?></td>
                                        <td><?= htmlspecialchars($loc['nome_servico']) ?></td>
                                        <?php if ($perfilAtual === 'Administrador'): ?>
                                            <td class="text-center"><a href="lista.php?restaurar=<?= $loc['id_localizacao'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-rotate-left me-1"></i>Restaurar</a></td>
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
<script>
function mostrarToast(mensagem, tipo = 'success') {
    const el = document.getElementById('toastApp');
    document.getElementById('toastMensagem').textContent = mensagem;
    el.className = 'toast align-items-center border-0 text-white ' + (tipo === 'success' ? 'bg-success' : 'bg-danger');
    new bootstrap.Toast(el, { delay: 4000 }).show();
}
const p = new URLSearchParams(window.location.search);
if (p.get('criado')     === '1') mostrarToast('Localização criada com sucesso.');
if (p.get('guardado')   === '1') mostrarToast('Localização guardada com sucesso.');
if (p.get('desativado') === '1') mostrarToast('Localização arquivada com sucesso.');
if (p.get('restaurado') === '1') mostrarToast('Localização restaurada com sucesso.');
window.history.replaceState({}, document.title, window.location.pathname);
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    $('#tabelaLocalizacoes').DataTable({ searching: false, pageLength: 10, language: { emptyTable: "Não existem registos", info: "A mostrar _START_ a _END_ de _TOTAL_ registos", infoEmpty: "0 registos", lengthMenu: "Mostrar _MENU_ registos", zeroRecords: "Sem resultados", paginate: { first: "Primeira", last: "Última", next: "Seguinte", previous: "Anterior" } } });
});
</script>
<?php include '../../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$where = [];
$params = [];

if (!empty($_GET['modulo'])) {
    $where[] = "h.modulo = ?";
    $params[] = $_GET['modulo'];
}

if (!empty($_GET['acao'])) {
    $where[] = "h.acao = ?";
    $params[] = $_GET['acao'];
}

if (!empty($_GET['data'])) {
    $where[] = "DATE(h.data_hora) = ?";
    $params[] = $_GET['data'];
}

if (!empty($_GET['pesquisa'])) {
    $where[] = "(h.registo LIKE ? OR h.detalhes LIKE ?)";
    $params[] = "%" . $_GET['pesquisa'] . "%";
    $params[] = "%" . $_GET['pesquisa'] . "%";
}

$sql = "
    SELECT h.*, u.perfil
    FROM historico h
    INNER JOIN utilizadores u ON h.id_utilizador = u.id_utilizador
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY h.data_hora DESC";

$stmt = $database->prepare($sql);
$stmt->execute($params);
$historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>

<div class="container-fluid">
    <div class="row">

        <?php include '../../includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 p-4 area-conteudo">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Histórico de Alterações</h2>
                    <p class="text-muted mb-0">Registo das ações realizadas no sistema.</p>
                </div>
            </div>

            <hr>

            <form method="get" class="mb-4">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <select name="modulo" class="form-select">
                            <option value="">Todos módulos</option>
                            <option value="Fornecedores" <?= (htmlspecialchars($_GET['modulo'] ?? '')) === 'Fornecedores' ? 'selected' : '' ?>>Fornecedores</option>
                            <option value="Equipamentos" <?= (htmlspecialchars($_GET['modulo'] ?? '')) === 'Equipamentos' ? 'selected' : '' ?>>Equipamentos</option>
                            <option value="Localizações" <?= (htmlspecialchars($_GET['modulo'] ?? '')) === 'Localizações' ? 'selected' : '' ?>>Localizações</option>
                            <option value="Autenticação" <?= (htmlspecialchars($_GET['modulo'] ?? '')) === 'Autenticação' ? 'selected' : '' ?>>Autenticação</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="acao" class="form-select">
                            <option value="">Todas ações</option>
                            <option value="Criação" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Criação' ? 'selected' : '' ?>>Criação</option>
                            <option value="Edição" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Edição' ? 'selected' : '' ?>>Edição</option>
                            <option value="Remoção" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Remoção' ? 'selected' : '' ?>>Remoção</option>
                            <option value="Login" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Login' ? 'selected' : '' ?>>Login</option>
                            <option value="Logout" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Logout' ? 'selected' : '' ?>>Logout</option>
                            <option value="Upload" <?= (htmlspecialchars($_GET['acao'] ?? '')) === 'Upload' ? 'selected' : '' ?>>Upload</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <input type="date" name="data" class="form-control" value="<?= htmlspecialchars($_GET['data'] ?? '') ?>">
                    </div>

                    <div class="col-md-3 mb-2">
                        <input type="text" name="pesquisa" class="form-control" placeholder="Pesquisar..." value="<?= htmlspecialchars($_GET['pesquisa'] ?? '') ?>">
                    </div>

                </div>

                <button type="submit" class="btn btn-primary mt-2">Filtrar</button>
                <a href="historico.php" class="btn btn-secondary mt-2">Limpar</a>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Utilizador</th>
                            <th>Módulo</th>
                            <th>Ação</th>
                            <th>Registo</th>
                            <th style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($historico)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Sem registos</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($historico as $h): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($h['data_hora'])) ?></td>
                                    <td><?= htmlspecialchars($h['perfil']) ?></td>
                                    <td><?= htmlspecialchars($h['modulo']) ?></td>
                                    <td>
                                        <?php
                                        $badge = match ($h['acao']) {
                                            'Criação' => 'success',
                                            'Edição'  => 'warning',
                                            'Remoção' => 'danger',
                                            'Login'   => 'info',
                                            'Logout'  => 'dark',
                                            default   => 'secondary'
                                        };
                                        ?>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= htmlspecialchars($h['acao']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($h['registo']) ?></td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detalhes<?= $h['id_historico'] ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php foreach ($historico as $h): ?>
                <div class="modal fade" id="detalhes<?= $h['id_historico'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Detalhes do Registo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Módulo:</strong> <?= htmlspecialchars($h['modulo']) ?></p>
                                <p><strong>Ação:</strong> <?= htmlspecialchars($h['acao']) ?></p>
                                <p><strong>Registo:</strong> <?= htmlspecialchars($h['registo']) ?></p>
                                <p><strong>Detalhes:</strong><br><?= nl2br(htmlspecialchars($h['detalhes'])) ?></p>
                                <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($h['data_hora'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </main>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
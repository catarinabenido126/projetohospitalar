<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$tipos_fornecedor = $database->query("SELECT id_tipo_fornecedor, tipo FROM tipos_fornecedor WHERE ativo = 1 ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);

$pesquisa = $_GET['pesquisa'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$cidade = $_GET['cidade'] ?? '';
$sql = "
    SELECT
        f.id_fornecedor,
        f.nome_empresa,
        f.nif,
        f.telefone,
        f.email,
        f.cidade,
        tf.tipo AS tipo_fornecedor
    FROM fornecedores f
    INNER JOIN tipos_fornecedor tf
        ON f.id_tipo_fornecedor = tf.id_tipo_fornecedor
    WHERE f.ativo = 1
";
if ($pesquisa != '') {
    $sql .= " AND (
        f.nome_empresa LIKE :pesquisa
        OR f.nif LIKE :pesquisa
    )";
}
if ($tipo != '') {
    $sql .= " AND f.id_tipo_fornecedor = :tipo";
}

if ($cidade != '') {
    $sql .= " AND f.cidade LIKE :cidade";
}
$sql .= " ORDER BY f.nome_empresa ASC";
$erro = '';
try {
    $query = $database->prepare($sql);
    if ($pesquisa != '') {
        $query->bindValue(':pesquisa', "%$pesquisa%");
    }

    if ($tipo != '') {
        $query->bindValue(':tipo', $tipo);
    }

    if ($cidade != '') {
        $query->bindValue(':cidade', "%$cidade%");
    }
    $query->execute();
    $fornecedores = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem de fornecedores.";
    $fornecedores = [];
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Fornecedores</h2>
                <div>
                    <button type="button" class="btn btn-outline-danger me-2">
                        <i class="fa-solid fa-file-pdf me-1"></i>
                        PDF
                    </button>
                    <button type="button" class="btn btn-outline-success me-2">
                        <i class="fa-solid fa-file-excel me-1"></i>
                        Excel
                    </button>
                    <a href="novo.php" class="btn btn-success">
                        <i class="fa-solid fa-plus me-1"></i>
                        Novo fornecedor
                    </a>
                </div>
            </div>
            <hr>
            <?php if (!empty($erro)) : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>
            <div id="mensagemSucesso" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Fornecedor guardado com sucesso.
            </div>
            <div id="mensagemCriado" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Fornecedor criado com sucesso.
            </div>
            <p class="text-muted">
                Lista resumida dos fornecedores registados. A ficha completa pode ser consultada nos detalhes.
            </p>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-5 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar fornecedor</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Ex: Philips Healthcare ou NIF">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo" class="form-label">Tipo de fornecedor</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option value="">Todos</option>
                                <?php foreach ($tipos_fornecedor as $tf): ?>
                                    <option value="<?= $tf['id_tipo_fornecedor'] ?>" <?= (($_GET['tipo'] ?? '') == $tf['id_tipo_fornecedor']) ? 'selected' : '' ?>><?= htmlspecialchars($tf['tipo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Ex: Porto" value="<?= htmlspecialchars($_GET['cidade'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-filter me-1"></i>
                            Filtrar
                        </button>
                        <a href="lista.php" class="btn btn-secondary">
                            <i class="fa-solid fa-broom me-1"></i>
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
            <div class="caixa-tabela table-responsive">
                <table id="tabelaFornecedores" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nome da Empresa</th>
                            <th>NIF</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Cidade</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fornecedores as $fornecedor): ?>
                        <tr>
                            <td><?php echo $fornecedor['nome_empresa']; ?></td>
                            <td><?php echo $fornecedor['nif']; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $fornecedor['tipo_fornecedor']; ?></span>
                            </td>
                            <td><?php echo $fornecedor['telefone']; ?></td>
                            <td><?php echo $fornecedor['email']; ?></td>
                            <td><?php echo $fornecedor['cidade']; ?></td>
                            <td class="text-center">
                                <a href="detalhes.php?id=<?php echo $fornecedor['id_fornecedor']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?php echo aes_encrypt($fornecedor['id_fornecedor']); ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="apagar.php?id=<?php echo $fornecedor['id_fornecedor']; ?>" class="btn btn-sm btn-outline-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <script>
                const params = new URLSearchParams(window.location.search);
                if (params.get("criado") === "1") {
                    document.getElementById("mensagemCriado").classList.remove("d-none");
                    setTimeout(() => document.getElementById("mensagemCriado").classList.add("d-none"), 5000);
                }
                if (params.get("guardado") === "1") {
                    document.getElementById("mensagemSucesso").classList.remove("d-none");
                    setTimeout(() => document.getElementById("mensagemSucesso").classList.add("d-none"), 5000);
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            </script>
        </main>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#tabelaFornecedores').DataTable({
        searching: false,
        pageLength: 10,
        language: {
            decimal: "",
            emptyTable: "Não existem registos",
            info: "A mostrar _START_ a _END_ de _TOTAL_ registos",
            infoEmpty: "A mostrar 0 a 0 de 0 registos",
            infoFiltered: "(filtrado de _MAX_ registos)",
            lengthMenu: "Mostrar _MENU_ registos",
            loadingRecords: "A carregar...",
            processing: "A processar...",
            search: "Pesquisar:",
            zeroRecords: "Nenhum registo encontrado",
            paginate: {
                first: "Primeira",
                last: "Última",
                next: "Seguinte",
                previous: "Anterior"
            }
        }
    });
});
</script>
<?php include '../../includes/footer.php'; ?>
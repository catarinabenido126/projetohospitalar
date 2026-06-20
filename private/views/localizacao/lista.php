<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$servicos = $database->query("SELECT id_servico, nome_servico FROM servicos WHERE ativo = 1 ORDER BY nome_servico")->fetchAll(PDO::FETCH_ASSOC);

$pesquisa = $_GET['pesquisa'] ?? '';
$servico = $_GET['servico'] ?? '';
$sql = "
    SELECT
        l.id_localizacao,
        l.edificio,
        l.piso,
        l.sala,
        l.responsavel,
        l.contacto,
        s.nome_servico
    FROM localizacoes l
    INNER JOIN servicos s
        ON l.id_servico = s.id_servico
    WHERE l.ativo = 1
";
if ($pesquisa != '') {
    $sql .= " AND (
        l.edificio LIKE :pesquisa
        OR l.sala LIKE :pesquisa
    )";
}
if ($servico != '') {
    $sql .= " AND l.id_servico = :servico";
}
$sql .= " ORDER BY l.edificio ASC, l.piso ASC, l.sala ASC";
$erro = '';
try {
    $query = $database->prepare($sql);
    if ($pesquisa != '') {
        $query->bindValue(':pesquisa', "%$pesquisa%");
    }

    if ($servico != '') {
        $query->bindValue(':servico', $servico);
    }
    $query->execute();
    $localizacoes = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem de localizações.";
    $localizacoes = [];
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Localizações</h2>
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
                        Nova localização
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
                Localização guardada com sucesso.
            </div>
            <div id="mensagemCriado" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Localização criada com sucesso.
            </div>
            <p class="text-muted">
                Lista resumida das localizações registadas. A ficha completa pode ser consultada nos detalhes.
            </p>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar localização</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Ex: Edifício A ou Sala 101">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="servico" class="form-label">Serviço</label>
                            <select class="form-select" id="servico" name="servico">
                                <option value="">Todos</option>
                                <?php foreach ($servicos as $s): ?>
                                    <option value="<?= $s['id_servico'] ?>" <?= (($_GET['servico'] ?? '') == $s['id_servico']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nome_servico']) ?></option>
                                <?php endforeach; ?>
                            </select>
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
                <table id="tabelaLocalizacoes" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Edifício</th>
                            <th>Piso</th>
                            <th>Sala</th>
                            <th>Serviço</th>
                            <th>Responsável</th>
                            <th>Contacto</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($localizacoes as $localizacao): ?>
                        <tr>
                            <td><?php echo $localizacao['edificio']; ?></td>
                            <td><?php echo $localizacao['piso']; ?></td>
                            <td><?php echo $localizacao['sala']; ?></td>
                            <td>
                                <span class="badge bg-secondary"><?php echo $localizacao['nome_servico']; ?></span>
                            </td>
                            <td><?php echo $localizacao['responsavel']; ?></td>
                            <td><?php echo $localizacao['contacto']; ?></td>
                            <td class="text-center">
                                <a href="detalhes.php?id=<?php echo $localizacao['id_localizacao']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?php echo aes_encrypt($localizacao['id_localizacao']); ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="apagar.php?id=<?php echo $localizacao['id_localizacao']; ?>" class="btn btn-sm btn-outline-danger">
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
    $('#tabelaLocalizacoes').DataTable({
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
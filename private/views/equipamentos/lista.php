<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$pesquisa = $_GET['pesquisa'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$estado = $_GET['estado'] ?? '';
$criticidade = $_GET['criticidade'] ?? '';
$sql = "
    SELECT
        e.id_equipamento,
        e.codigo_interno,
        e.designacao,
        c.nome_categoria,
        ee.nome_estado,
        cr.nivel,
        l.edificio,
        l.piso,
        l.sala
    FROM equipamentos e
    INNER JOIN categorias c
        ON e.id_categoria = c.id_categoria
    INNER JOIN estados_equipamento ee
        ON e.id_estado = ee.id_estado
    INNER JOIN criticidades cr
        ON e.id_criticidade = cr.id_criticidade
    INNER JOIN localizacoes l
        ON e.id_localizacao = l.id_localizacao
    WHERE e.ativo = 1
";
if ($pesquisa != '') {
    $sql .= " AND (
        e.codigo_interno LIKE :pesquisa
        OR e.designacao LIKE :pesquisa
    )";
}
if ($categoria != '') {
    $sql .= " AND c.nome_categoria = :categoria";
}

if ($estado != '') {
    $sql .= " AND ee.nome_estado = :estado";
}

if ($criticidade != '') {
    $sql .= " AND cr.nivel = :criticidade";
}
$sql .= " ORDER BY e.codigo_interno ASC";
$erro = '';
try {
    $query = $database->prepare($sql);
    if ($pesquisa != '') {
        $query->bindValue(':pesquisa', "%$pesquisa%");
    }

    if ($categoria != '') {
        $query->bindValue(':categoria', $categoria);
    }

    if ($estado != '') {
        $query->bindValue(':estado', $estado);
    }

    if ($criticidade != '') {
        $query->bindValue(':criticidade', $criticidade);
    }
    $query->execute();
    $equipamentos = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $erro = "Não foi possível obter a listagem de equipamentos.";
    $equipamentos = [];
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Listagem de Equipamentos</h2>
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
                        Novo equipamento
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
                Equipamento guardado com sucesso.
            </div>
            <div id="mensagemCriado" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Equipamento criado com sucesso.
            </div>
            <div id="mensagemDesativado" class="alert alert-success d-none" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                Equipamento desativado com sucesso.
            </div>
            <p class="text-muted">
                Lista resumida dos equipamentos médicos registados. A ficha completa pode ser consultada nos detalhes.
            </p>
            <div class="caixa-filtros mb-4">
                <form action="lista.php" method="get">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="pesquisa" class="form-label">Pesquisar equipamento</label>
                            <input type="text" class="form-control" id="pesquisa" name="pesquisa" placeholder="Ex: EQ-0001">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="categoria" class="form-label">Categoria</label>
                            <select class="form-select" id="categoria" name="categoria">
                                <option value="">Todas</option>
                                <option>Monitorização</option>
                                <option>Suporte de Vida</option>
                                <option>Diagnóstico</option>
                                <option>Imagiologia</option>
                                <option>Laboratório</option>
                                <option>Terapia</option>
                                <option>Cirurgia</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Todos</option>
                                <option>Ativo</option>
                                <option>Em manutenção</option>
                                <option>Em calibração</option>
                                <option>Inativo</option>
                                <option>Abatido</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="criticidade" class="form-label">Criticidade</label>
                            <select class="form-select" id="criticidade" name="criticidade">
                                <option value="">Todas</option>
                                <option>Baixa</option>
                                <option>Média</option>
                                <option>Alta</option>
                                <option>Suporte de vida</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="localizacao" class="form-label">Localização</label>
                            <select class="form-select" id="localizacao" name="localizacao">
                                <option value="">Todas</option>
                                <option>Urgência</option>
                                <option>UCI</option>
                                <option>Bloco Operatório</option>
                                <option>Consultas</option>
                                <option>Laboratório</option>
                                <option>Radiologia</option>
                                <option>Reabilitação</option>
                                <option>Armazém</option>
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
                <table id="tabelaEquipamentos" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Designação</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Criticidade</th>
                            <th>Localização</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                   <tbody>
                        <?php foreach ($equipamentos as $equipamento): ?>
                        <tr>
                            <td><?php echo $equipamento['codigo_interno']; ?></td>
                            <td><?php echo $equipamento['designacao']; ?></td>
                            <td><?php echo $equipamento['nome_categoria']; ?></td>
                            <td>
                                <?php
                                $classeEstado = 'bg-secondary';
                                switch ($equipamento['nome_estado']) {
                                    case 'Ativo':
                                        $classeEstado = 'bg-success';
                                        break;
                                    case 'Em manutenção':
                                        $classeEstado = 'bg-warning text-dark';
                                        break;
                                    case 'Em calibração':
                                        $classeEstado = 'bg-info text-dark';
                                        break;
                                    case 'Inativo':
                                        $classeEstado = 'bg-danger';
                                        break;
                                    case 'Abatido':
                                        $classeEstado = 'bg-dark';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $classeEstado; ?>">
                                    <?php echo $equipamento['nome_estado']; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $classeCriticidade = 'bg-secondary';
                                switch ($equipamento['nivel']) {
                                    case 'Baixa':
                                        $classeCriticidade = 'bg-success';
                                        break;
                                    case 'Média':
                                        $classeCriticidade = 'bg-warning text-dark';
                                        break;
                                    case 'Alta':
                                        $classeCriticidade = 'bg-danger';
                                        break;
                                    case 'Suporte de vida':
                                        $classeCriticidade = 'bg-danger';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $classeCriticidade; ?>">
                                    <?php echo $equipamento['nivel']; ?>
                                </span>
                            </td>
                            <td>
                                Edifício <?php echo $equipamento['edificio']; ?>
                                <br>
                                <small class="text-muted">
                                    Piso <?php echo $equipamento['piso']; ?> | Sala <?php echo $equipamento['sala']; ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="detalhes.php?id=<?php echo aes_encrypt($equipamento['id_equipamento']); ?>" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="editar.php?id=<?php echo aes_encrypt($equipamento['id_equipamento']); ?>" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <a href="apagar.php?id=<?php echo aes_encrypt($equipamento['id_equipamento']); ?>" class="btn btn-sm btn-outline-danger">
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
                if (params.get("desativado") === "1") {
                    document.getElementById("mensagemDesativado").classList.remove("d-none");
                    setTimeout(() => document.getElementById("mensagemDesativado").classList.add("d-none"), 5000);
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            </script>
        </main>
    </div>
</div>
<script>
$(document).ready(function () {
    $('#tabelaEquipamentos').DataTable({
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
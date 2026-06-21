<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? '';
$idLocalizacao = aes_decrypt($idEncriptado);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit();
}

try {
    $sql = "
        SELECT l.id_localizacao, l.edificio, l.piso, l.sala, l.responsavel, l.contacto, s.nome_servico
        FROM localizacoes l
        INNER JOIN servicos s ON l.id_servico = s.id_servico
        WHERE l.id_localizacao = :id AND l.ativo = 1
    ";
    $query = $database->prepare($sql);
    $query->bindParam(':id', $idLocalizacao, PDO::PARAM_INT);
    $query->execute();
    $localizacao = $query->fetch(PDO::FETCH_ASSOC);

    if (!$localizacao) {
        header('Location: lista.php');
        exit();
    }

    $sqlEquipamentos = "
        SELECT
            e.id_equipamento,
            e.codigo_interno,
            e.designacao,
            c.nome_categoria,
            ee.nome_estado,
            cr.nivel
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        INNER JOIN criticidades cr ON e.id_criticidade = cr.id_criticidade
        WHERE e.id_localizacao = :id AND e.ativo = 1
        ORDER BY e.codigo_interno ASC
    ";
    $queryEquipamentos = $database->prepare($sqlEquipamentos);
    $queryEquipamentos->bindParam(':id', $idLocalizacao, PDO::PARAM_INT);
    $queryEquipamentos->execute();
    $equipamentos = $queryEquipamentos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
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
                    <h2 class="mb-0">
                        Equipamentos na Localização
                    </h2>
                </div>
                <a href="lista.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
            <hr>
            <div class="border rounded p-3 mb-4">
                <h5 class="mb-3">
                    Detalhes da localização
                </h5>
                <div class="row">
                    <div class="col-md-3">
                        <p>
                            <strong>Edifício:</strong><br>
                            Edifício <?= htmlspecialchars($localizacao['edificio']) ?>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Piso:</strong><br>
                            Piso <?= htmlspecialchars($localizacao['piso']) ?>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Sala:</strong><br>
                            Sala <?= htmlspecialchars($localizacao['sala']) ?>
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Serviço:</strong><br>
                            <?= htmlspecialchars($localizacao['nome_servico']) ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <p>
                            <strong>Responsável:</strong><br>
                            <?= !empty($localizacao['responsavel']) ? htmlspecialchars($localizacao['responsavel']) : '—' ?>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-0">
                            <strong>Contacto:</strong><br>
                            <?php if (!empty($localizacao['contacto'])): ?>
                                <i class="fa-solid fa-phone me-1"></i>
                                <?= htmlspecialchars($localizacao['contacto']) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="caixa-tabela table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Designação</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Criticidade</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipamentos)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Sem equipamentos associados a esta localização.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipamentos as $equipamento): ?>
                                <tr>
                                    <td><?= htmlspecialchars($equipamento['codigo_interno']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['designacao']) ?></td>
                                    <td><?= htmlspecialchars($equipamento['nome_categoria']) ?></td>
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
                                        <span class="badge <?= $classeEstado ?>">
                                            <?= htmlspecialchars($equipamento['nome_estado']) ?>
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
                                            case 'Suporte de vida':
                                                $classeCriticidade = 'bg-danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $classeCriticidade ?>">
                                            <?= htmlspecialchars($equipamento['nivel']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="../equipamentos/detalhes.php?id=<?= aes_encrypt($equipamento['id_equipamento']) ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="../equipamentos/editar.php?id=<?= aes_encrypt($equipamento['id_equipamento']) ?>" class="btn btn-sm btn-outline-warning me-1">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                <p class="mb-0 text-muted">
                    <?= count($equipamentos) ?> equipamento(s) nesta localização.
                </p>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
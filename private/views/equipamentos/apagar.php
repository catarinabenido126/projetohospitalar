<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? '';
$idEquipamento = aes_decrypt($idEncriptado);

if (!$idEquipamento || !is_numeric($idEquipamento)) {
    header('Location: lista.php');
    exit();
}

$erro_sistema = "";

try {
    $sql = "
        SELECT e.id_equipamento, e.codigo_interno, e.designacao, c.nome_categoria, ee.nome_estado
        FROM equipamentos e
        INNER JOIN categorias c ON e.id_categoria = c.id_categoria
        INNER JOIN estados_equipamento ee ON e.id_estado = ee.id_estado
        WHERE e.id_equipamento = :id AND e.ativo = 1
    ";
    $query = $database->prepare($sql);
    $query->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
    $query->execute();
    $equipamento = $query->fetch(PDO::FETCH_ASSOC);

    if (!$equipamento) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE equipamentos SET ativo = 0, updated_at = NOW() WHERE id_equipamento = :id";
        $query = $database->prepare($sql);
        $query->bindParam(':id', $idEquipamento, PDO::PARAM_INT);
        $query->execute();
        header('Location: lista.php?desativado=1');
        exit();
    } catch (PDOException $e) {
        $erro_sistema = "Não foi possível desativar o equipamento.";
    }
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Apagar Equipamento</h2>
            <hr>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?= htmlspecialchars($erro_sistema) ?>
                </div>
            <?php endif; ?>
            <p>Tem a certeza que pretende desativar este equipamento? Esta ação não apaga o registo, mas remove-o da listagem.</p>
            <p><strong>Código interno:</strong> <?= htmlspecialchars($equipamento['codigo_interno']) ?></p>
            <p><strong>Designação:</strong> <?= htmlspecialchars($equipamento['designacao']) ?></p>
            <p><strong>Categoria:</strong> <?= htmlspecialchars($equipamento['nome_categoria']) ?></p>
            <p><strong>Estado:</strong> <?= htmlspecialchars($equipamento['nome_estado']) ?></p>
            <form action="apagar.php?id=<?= htmlspecialchars($idEncriptado) ?>" method="post">
                <button type="submit" class="btn btn-danger">
                    Confirmar desativação
                </button>
                <a href="lista.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
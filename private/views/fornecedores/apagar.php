<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();

$idEncriptado = $_GET['id'] ?? '';
$idFornecedor = aes_decrypt($idEncriptado);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit();
}

$erro_sistema = "";

try {
    $sql = "
        SELECT f.id_fornecedor, f.nome_empresa, f.nif, f.email, tf.tipo AS tipo_fornecedor
        FROM fornecedores f
        INNER JOIN tipos_fornecedor tf ON f.id_tipo_fornecedor = tf.id_tipo_fornecedor
        WHERE f.id_fornecedor = :id AND f.ativo = 1
    ";
    $query = $database->prepare($sql);
    $query->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $query->execute();
    $fornecedor = $query->fetch(PDO::FETCH_ASSOC);
    if (!$fornecedor) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE fornecedores SET ativo = 0, updated_at = NOW() WHERE id_fornecedor = :id";
        $query = $database->prepare($sql);
        $query->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
        $query->execute();

        registar_historico(
            $database,
            'Fornecedores',
            'Remoção',
            $fornecedor['nome_empresa'],
            'Fornecedor desativado com sucesso.'
        );

        header('Location: lista.php?desativado=1');
        exit();

    } catch (PDOException $e) {
        $erro_sistema = "Não foi possível desativar o fornecedor.";
    }
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Apagar Fornecedor</h2>
            <hr>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?= htmlspecialchars($erro_sistema) ?>
                </div>
            <?php endif; ?>
            <p>Tem a certeza que pretende desativar este fornecedor? Esta ação não apaga o registo, mas remove-o da listagem.</p>
            <p><strong>Nome da empresa:</strong> <?= htmlspecialchars($fornecedor['nome_empresa']) ?></p>
            <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor['nif']) ?></p>
            <p><strong>Tipo de fornecedor:</strong> <?= htmlspecialchars($fornecedor['tipo_fornecedor']) ?></p>
            <p><strong>Email:</strong> <?= !empty($fornecedor['email']) ? htmlspecialchars($fornecedor['email']) : '—' ?></p>
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
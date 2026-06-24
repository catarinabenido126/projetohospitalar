<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();
restringir_perfil(['Administrador']);

$erro_sistema = "";

// ── Validar ID encriptado ────────────────────────────────────────────────────
$idEncriptado = $_GET['id'] ?? '';
$idFornecedor = aes_decrypt($idEncriptado);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit();
}

$idFornecedor = (int)$idFornecedor;

// ── Carregar fornecedor para confirmação ─────────────────────────────────────
try {
    $sql = "
        SELECT id_fornecedor, nome_empresa, nif
        FROM fornecedores
        WHERE id_fornecedor = :id AND ativo = 1
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

// ── Processar desativação (soft delete) ──────────────────────────────────────
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "
            UPDATE fornecedores
            SET ativo = 0, updated_at = NOW()
            WHERE id_fornecedor = :id
        ";

        $query = $database->prepare($sql);
        $query->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
        $query->execute();

        // Registo no histórico
        registar_historico(
            $database,
            'Fornecedores',
            'Desativação',
            $fornecedor['nome_empresa'],
            'Fornecedor desativado (soft-delete).'
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

            <p>Tem a certeza que pretende desativar este fornecedor? Esta ação não elimina o registo, apenas o remove das listagens ativas.</p>

            <p><strong>Empresa:</strong> <?= htmlspecialchars($fornecedor['nome_empresa']) ?></p>
            <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor['nif']) ?></p>

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
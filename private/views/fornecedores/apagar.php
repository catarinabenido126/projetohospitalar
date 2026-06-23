<?php

require_once __DIR__ . '/../../includes/funcoes.php';
redirect_if_not_logged();
restringir_perfil(['Administrador']);

require_once __DIR__ . '/../../includes/database.php';

$idEncriptado = $_GET['id'] ?? '';
$idFornecedor = aes_decrypt($idEncriptado);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit();
}

try {
    $stmt = $database->prepare("SELECT id_fornecedor, nome_empresa, nif FROM fornecedores WHERE id_fornecedor = :id AND ativo = 1");
    $stmt->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $stmt->execute();
    $fornecedor = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$fornecedor) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    try {
        $database->prepare("UPDATE fornecedores SET ativo = 0, updated_at = NOW() WHERE id_fornecedor = :id")
                 ->execute([':id' => $idFornecedor]);
        registar_historico($database, 'Fornecedores', 'Desativação', $fornecedor['nome_empresa'], 'Fornecedor desativado.');
        header('Location: lista.php?desativado=1');
        exit();
    } catch (PDOException $e) {
        $erro = "Não foi possível desativar o fornecedor: " . $e->getMessage();
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Desativar Fornecedor</h2>
            <hr>
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Tem a certeza que pretende desativar o fornecedor <strong><?= htmlspecialchars($fornecedor['nome_empresa']) ?></strong> (NIF: <?= htmlspecialchars($fornecedor['nif']) ?>)?
                <br><small class="text-muted">O fornecedor ficará arquivado e poderá ser restaurado posteriormente.</small>
            </div>
            <form method="post">
                <button type="submit" name="confirmar" value="1" class="btn btn-danger">
                    <i class="fa-solid fa-box-archive me-1"></i> Confirmar desativação
                </button>
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
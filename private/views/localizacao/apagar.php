<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';

redirect_if_not_logged();
restringir_perfil(['Administrador']);

$idEncriptado = $_GET['id'] ?? '';
$idLocalizacao = aes_decrypt($idEncriptado);

if (!$idLocalizacao || !is_numeric($idLocalizacao)) {
    header('Location: lista.php');
    exit();
}

$erro_sistema = "";

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
        registar_historico(
            $database,
            'Localizações',
            'Remoção',
            $localizacao['edificio'],
            'Localização removida.'
        );
        
    $localizacao = $query->fetch(PDO::FETCH_ASSOC);

    if (!$localizacao) {
        header('Location: lista.php');
        exit();
    }
} catch (PDOException $e) {
    header('Location: lista.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $sql = "UPDATE localizacoes SET ativo = 0, updated_at = NOW() WHERE id_localizacao = :id";
        $query = $database->prepare($sql);
        $query->bindParam(':id', $idLocalizacao, PDO::PARAM_INT);
        $query->execute();
        header('Location: lista.php?desativado=1');
        exit();
    } catch (PDOException $e) {
        $erro_sistema = "Não foi possível desativar a localização.";
    }
}

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Apagar Localização</h2>
            <hr>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?= htmlspecialchars($erro_sistema) ?>
                </div>
            <?php endif; ?>
            <p>Tem a certeza que pretende desativar esta localização? Esta ação não apaga o registo, mas remove-o da listagem.</p>
            <div class="mb-3">
                <strong>Edifício:</strong>
                Edifício <?= htmlspecialchars($localizacao['edificio']) ?>
            </div>
            <div class="mb-3">
                <strong>Piso:</strong>
                Piso <?= htmlspecialchars($localizacao['piso']) ?>
            </div>
            <div class="mb-3">
                <strong>Sala:</strong>
                Sala <?= htmlspecialchars($localizacao['sala']) ?>
            </div>
            <div class="mb-3">
                <strong>Serviço:</strong>
                <?= htmlspecialchars($localizacao['nome_servico']) ?>
            </div>
            <div class="mb-3">
                <strong>Responsável:</strong>
                <?= !empty($localizacao['responsavel']) ? htmlspecialchars($localizacao['responsavel']) : '—' ?>
            </div>
            <div class="mb-3">
                <strong>Contacto:</strong>
                <?= !empty($localizacao['contacto']) ? htmlspecialchars($localizacao['contacto']) : '—' ?>
            </div>
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

<?php
require_once 'includes/funcoes.php';
require_once 'includes/database.php';

redirect_if_not_logged();

$ultimoAcesso  = $_SESSION['ultimo_acesso']  ?? null;
$idUtilizador  = $_SESSION['id_utilizador']  ?? null;

// ── Adicionar lembrete ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['descricao'])) {
    $descricao = trim($_POST['descricao']);
    if ($descricao !== '') {
        try {
            $database->prepare("
                INSERT INTO lembretes (id_utilizador, descricao, concluido, created_at)
                VALUES (:id, :desc, 0, NOW())
            ")->execute([':id' => $idUtilizador, ':desc' => $descricao]);
        } catch (PDOException $e) {}
    }
    header('Location: index.php');
    exit();
}

// ── Concluir lembrete ─────────────────────────────────────────
if (isset($_GET['concluir']) && ctype_digit($_GET['concluir'])) {
    try {
        $database->prepare("
            UPDATE lembretes SET concluido = 1
            WHERE id_lembrete = :id AND id_utilizador = :uid
        ")->execute([':id' => (int)$_GET['concluir'], ':uid' => $idUtilizador]);
    } catch (PDOException $e) {}
    header('Location: index.php');
    exit();
}

// ── Remover lembrete ──────────────────────────────────────────
if (isset($_GET['remover']) && ctype_digit($_GET['remover'])) {
    try {
        $database->prepare("
            DELETE FROM lembretes WHERE id_lembrete = :id AND id_utilizador = :uid
        ")->execute([':id' => (int)$_GET['remover'], ':uid' => $idUtilizador]);
    } catch (PDOException $e) {}
    header('Location: index.php');
    exit();
}

// ── Carregar lembretes do utilizador ──────────────────────────
$lembretes = [];
try {
    $stmt = $database->prepare("
        SELECT id_lembrete, descricao, concluido, created_at
        FROM lembretes
        WHERE id_utilizador = :id
        ORDER BY concluido ASC, created_at DESC
    ");
    $stmt->execute([':id' => $idUtilizador]);
    $lembretes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
?>
<?php include 'includes/header.php'; ?>
<?php include 'includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo pagina-inicial">
            <section class="pagina-inicial-conteudo">
                <div class="banner-inicial">
                    <div class="texto-banner-inicial">
                        <h2>Página Inicial</h2>
                        <p><strong>Bem-vindo à área interna da MediSync.</strong></p>
                        <p>Utilize o menu lateral para aceder aos módulos de gestão do inventário hospitalar.</p>
                    </div>
                </div>

                <div class="lembretes-inicial mt-4">
                    <h4>
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        Lembretes
                    </h4>
                    <div class="caixa-lembretes">
                        <!-- Formulário para adicionar -->
                        <form method="post" action="index.php">
                            <div class="d-flex gap-2 mb-3">
                                <input type="text" class="form-control" name="descricao"
                                    placeholder="Escreva um lembrete..." required>
                                <button type="submit" class="btn btn-primary">Adicionar</button>
                            </div>
                        </form>

                        <!-- Lista de lembretes da BD -->
                        <div id="listaLembretes">
                            <?php if (empty($lembretes)): ?>
                                <p class="text-muted">Sem lembretes registados.</p>
                            <?php else: ?>
                                <?php foreach ($lembretes as $l): ?>
                                <div class="lembrete-item <?= $l['concluido'] ? 'opacity-50' : '' ?>">
                                    <?php if (!$l['concluido']): ?>
                                        <a href="index.php?concluir=<?= $l['id_lembrete'] ?>"
                                           class="text-decoration-none">
                                            <input type="checkbox" class="form-check-input" style="cursor:pointer">
                                        </a>
                                    <?php else: ?>
                                        <input type="checkbox" class="form-check-input" checked disabled>
                                    <?php endif; ?>
                                    <div>
                                        <strong style="<?= $l['concluido'] ? 'text-decoration:line-through' : '' ?>">
                                            <?= htmlspecialchars($l['descricao']) ?>
                                        </strong>
                                        <p><?= date('d/m/Y H:i', strtotime($l['created_at'])) ?></p>
                                    </div>
                                    <a href="index.php?remover=<?= $l['id_lembrete'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Remover este lembrete?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="caixa-ultimo-acesso mb-4">
                    <i class="fa-solid fa-clock me-2"></i>
                    <strong>Último acesso:</strong>
                    <?php if ($ultimoAcesso): ?>
                        <?= date('d/m/Y \à\s H:i', strtotime($ultimoAcesso)) ?>
                    <?php else: ?>
                        Primeiro acesso
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
restringir_perfil(['Administrador', 'Tecnico']);

$idEncriptado = $_GET['id'] ?? '';
$idFornecedor = aes_decrypt($idEncriptado);

if (!$idFornecedor || !is_numeric($idFornecedor)) {
    header('Location: lista.php');
    exit();
}

try {
    $query = $database->prepare("SELECT * FROM fornecedores WHERE id_fornecedor = :id AND ativo = 1");
    $query->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $query->execute();
    $fornecedor = $query->fetch(PDO::FETCH_ASSOC);
    if (!$fornecedor) { header('Location: lista.php'); exit(); }
} catch (PDOException $e) { header('Location: lista.php'); exit(); }

// Equipamentos associados com papel
$equipamentosAssociados = [];
try {
    $s = $database->prepare("
        SELECT e.id_equipamento, e.codigo_interno, e.designacao, trf.tipo AS papel
        FROM equipamento_fornecedor ef
        INNER JOIN equipamentos e               ON ef.id_equipamento  = e.id_equipamento
        INNER JOIN tipos_relacao_fornecedor trf ON ef.id_tipo_relacao = trf.id_tipo_relacao
        WHERE ef.id_fornecedor = :id AND ef.ativo = 1 AND e.ativo = 1
        ORDER BY e.codigo_interno ASC
    ");
    $s->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $s->execute();
    $equipamentosAssociados = $s->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

// Documentos do fornecedor
$documentos = [];
try {
    $s = $database->prepare("
        SELECT d.*, td.tipo AS tipo_documento
        FROM documentos d
        LEFT JOIN tipos_documento td ON d.id_tipo_documento = td.id_tipo_documento
        WHERE d.id_fornecedor = :id AND d.ativo = 1
        ORDER BY d.created_at DESC
    ");
    $s->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $s->execute();
    $documentos = $s->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Detalhes do Fornecedor</h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($fornecedor['nome_empresa']) ?> • NIF <?= htmlspecialchars($fornecedor['nif']) ?></p>
                </div>
                <div>
                    <a href="lista.php" class="btn btn-secondary">Voltar</a>
                    <a href="editar.php?id=<?= aes_encrypt($fornecedor['id_fornecedor']) ?>" class="btn btn-warning">Editar</a>
                </div>
            </div>
            <hr>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Informação principal</h4>
                    <p><strong>Nome da empresa:</strong> <?= htmlspecialchars($fornecedor['nome_empresa']) ?></p>
                    <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor['nif']) ?></p>
                    <h4 class="mt-3">Contactos</h4>
                    <p><strong>Telefone:</strong> <?= !empty($fornecedor['telefone']) ? htmlspecialchars($fornecedor['telefone']) : '—' ?></p>
                    <p><strong>Email:</strong> <?= !empty($fornecedor['email']) ? htmlspecialchars($fornecedor['email']) : '—' ?></p>
                    <p><strong>Website:</strong>
                        <?php if (!empty($fornecedor['website'])): ?>
                            <a href="<?= htmlspecialchars($fornecedor['website']) ?>" target="_blank"><?= htmlspecialchars($fornecedor['website']) ?></a>
                        <?php else: ?>—<?php endif; ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h4>Pessoa de contacto</h4>
                    <p><strong>Nome:</strong> <?= !empty($fornecedor['pessoa_contacto']) ? htmlspecialchars($fornecedor['pessoa_contacto']) : '—' ?></p>
                    <p><strong>Telefone:</strong> <?= !empty($fornecedor['telefone_contacto']) ? htmlspecialchars($fornecedor['telefone_contacto']) : '—' ?></p>
                    <p><strong>Email:</strong> <?= !empty($fornecedor['email_contacto']) ? htmlspecialchars($fornecedor['email_contacto']) : '—' ?></p>
                    <h4 class="mt-3">Morada</h4>
                    <p><?= !empty($fornecedor['morada']) ? htmlspecialchars($fornecedor['morada']) : '—' ?></p>
                    <p><?= !empty($fornecedor['codigo_postal']) ? htmlspecialchars($fornecedor['codigo_postal']) : '' ?> <?= !empty($fornecedor['cidade']) ? htmlspecialchars($fornecedor['cidade']) : '' ?></p>
                    <p><?= !empty($fornecedor['pais']) ? htmlspecialchars($fornecedor['pais']) : '' ?></p>
                </div>
            </div>

            <?php if (!empty($fornecedor['observacoes'])): ?>
                <h4>Observações</h4>
                <p><?= nl2br(htmlspecialchars($fornecedor['observacoes'])) ?></p>
            <?php endif; ?>

            <hr>

            <!-- Equipamentos associados -->
            <h4><i class="fa-solid fa-stethoscope me-2"></i>Equipamentos associados</h4>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead><tr><th>Código</th><th>Equipamento</th><th>Papel</th><th class="text-center">Ver</th></tr></thead>
                    <tbody>
                        <?php if (empty($equipamentosAssociados)): ?>
                            <tr><td colspan="4" class="text-center text-muted">Sem equipamentos associados.</td></tr>
                        <?php else: foreach ($equipamentosAssociados as $eq): ?>
                            <tr>
                                <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($eq['papel']) ?></span></td>
                                <td class="text-center">
                                    <a href="../equipamentos/detalhes.php?id=<?= aes_encrypt($eq['id_equipamento']) ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Ver equipamento">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Documentos do fornecedor -->
            <h4><i class="fa-solid fa-file-lines me-2"></i>Documentos associados</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead><tr><th>Nome</th><th>Tipo</th><th>Data</th><th>Validade</th><th class="text-center">Ações</th></tr></thead>
                    <tbody>
                        <?php if (empty($documentos)): ?>
                            <tr><td colspan="5" class="text-center text-muted">Sem documentos associados a este fornecedor.</td></tr>
                        <?php else: foreach ($documentos as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['nome_documento']) ?></td>
                                <td><?= htmlspecialchars($doc['tipo_documento'] ?? '—') ?></td>
                                <td><?= !empty($doc['data_documento']) ? date('d/m/Y', strtotime($doc['data_documento'])) : '—' ?></td>
                                <td><?= !empty($doc['data_validade']) ? date('d/m/Y', strtotime($doc['data_validade'])) : '—' ?></td>
                                <td class="text-center">
                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>" target="_blank"
                                       class="btn btn-sm btn-outline-primary me-1"
                                       data-bs-toggle="tooltip" title="Ver documento">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars($doc['caminho_ficheiro']) ?>" download
                                       class="btn btn-sm btn-outline-success"
                                       data-bs-toggle="tooltip" title="Descarregar">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});
</script>
<?php include '../../includes/footer.php'; ?>
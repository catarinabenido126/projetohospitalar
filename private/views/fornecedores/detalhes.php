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

try {
    $sql = "
        SELECT f.*, tf.tipo AS tipo_fornecedor
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

    $sqlEquipamentos = "
        SELECT e.id_equipamento, e.codigo_interno, e.designacao
        FROM equipamento_fornecedor ef
        INNER JOIN equipamentos e ON ef.id_equipamento = e.id_equipamento
        WHERE ef.id_fornecedor = :id AND ef.ativo = 1 AND e.ativo = 1
        ORDER BY e.codigo_interno ASC
    ";
    $queryEquipamentos = $database->prepare($sqlEquipamentos);
    $queryEquipamentos->bindParam(':id', $idFornecedor, PDO::PARAM_INT);
    $queryEquipamentos->execute();
    $equipamentosAssociados = $queryEquipamentos->fetchAll(PDO::FETCH_ASSOC);
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
            <h2>Detalhes do Fornecedor</h2>
            <hr>
            <h4>Informação principal</h4>
            <p><strong>Nome da empresa:</strong> <?= htmlspecialchars($fornecedor['nome_empresa']) ?></p>
            <p><strong>NIF:</strong> <?= htmlspecialchars($fornecedor['nif']) ?></p>
            <p><strong>Tipo de fornecedor:</strong> <?= htmlspecialchars($fornecedor['tipo_fornecedor']) ?></p>
            <hr>
            <h4>Contactos</h4>
            <p><strong>Telefone:</strong> <?= !empty($fornecedor['telefone']) ? htmlspecialchars($fornecedor['telefone']) : '—' ?></p>
            <p><strong>Email:</strong> <?= !empty($fornecedor['email']) ? htmlspecialchars($fornecedor['email']) : '—' ?></p>
            <p><strong>Website:</strong> <?= !empty($fornecedor['website']) ? htmlspecialchars($fornecedor['website']) : '—' ?></p>
            <hr>
            <h4>Pessoa de contacto</h4>
            <p><strong>Nome:</strong> <?= !empty($fornecedor['pessoa_contacto']) ? htmlspecialchars($fornecedor['pessoa_contacto']) : '—' ?></p>
            <p><strong>Telefone:</strong> <?= !empty($fornecedor['telefone_contacto']) ? htmlspecialchars($fornecedor['telefone_contacto']) : '—' ?></p>
            <p><strong>Email:</strong> <?= !empty($fornecedor['email_contacto']) ? htmlspecialchars($fornecedor['email_contacto']) : '—' ?></p>
            <hr>
            <h4>Morada</h4>
            <p><strong>Morada:</strong> <?= !empty($fornecedor['morada']) ? htmlspecialchars($fornecedor['morada']) : '—' ?></p>
            <p><strong>Código postal:</strong> <?= !empty($fornecedor['codigo_postal']) ? htmlspecialchars($fornecedor['codigo_postal']) : '—' ?></p>
            <p><strong>Cidade:</strong> <?= !empty($fornecedor['cidade']) ? htmlspecialchars($fornecedor['cidade']) : '—' ?></p>
            <p><strong>País:</strong> <?= !empty($fornecedor['pais']) ? htmlspecialchars($fornecedor['pais']) : '—' ?></p>
            <hr>
            <h4>Observações</h4>
            <p><?= !empty($fornecedor['observacoes']) ? nl2br(htmlspecialchars($fornecedor['observacoes'])) : 'Sem observações registadas.' ?></p>
            <h4>Equipamentos associados</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Equipamento</th>
                            <th class="text-center">Ver equipamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($equipamentosAssociados)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Sem equipamentos associados a este fornecedor.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($equipamentosAssociados as $eq): ?>
                                <tr>
                                    <td><?= htmlspecialchars($eq['codigo_interno']) ?></td>
                                    <td><?= htmlspecialchars($eq['designacao']) ?></td>
                                    <td class="text-center">
                                        <a href="../equipamentos/detalhes.php?id=<?= aes_encrypt($eq['id_equipamento']) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <hr>
            <h4>Documentação do Fornecedor</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Nome do documento</th>
                            <th>Ficheiro</th>
                            <th>Tipo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Sem documentos associados a este registo.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <a href="lista.php" class="btn btn-secondary">
                Voltar
            </a>
            <a href="editar.php?id=<?= aes_encrypt($fornecedor['id_fornecedor']) ?>" class="btn btn-warning">
                Editar
            </a>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
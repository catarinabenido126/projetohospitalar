<?php
require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
restringir_perfil(['Administrador']);

$campos_conteudo = [
    'inicio_titulo', 'inicio_texto',
    'sobre_texto',
    'servico_1_titulo', 'servico_1_descricao',
    'servico_2_titulo', 'servico_2_descricao',
    'servico_3_titulo', 'servico_3_descricao',
    'servico_4_titulo', 'servico_4_descricao',
    'servico_5_titulo', 'servico_5_descricao',
    'servico_6_titulo', 'servico_6_descricao',
    'contacto_titulo', 'contacto_texto', 'contacto_email', 'contacto_telefone',
    'rodape_localizacao', 'rodape_horario', 'rodape_contactos'
];

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($campos_conteudo as $campo) {
            $valor = trim($_POST[$campo] ?? '');
            if (preg_match('/^servico_(\d+)_(titulo|descricao)$/', $campo, $m)) {
                $seccao   = 'servico_' . $m[1];
                $campo_db = $m[2];
            } else {
                $parts    = explode('_', $campo, 2);
                $seccao   = $parts[0];
                $campo_db = $parts[1] ?? '';
            }
            $database->prepare("UPDATE conteudos_publicos c INNER JOIN secoes_publicas s ON s.id_seccao = c.id_seccao SET c.valor = :valor, c.updated_at = NOW() WHERE s.nome_seccao = :seccao AND c.campo = :campo")->execute([':valor' => $valor, ':seccao' => $seccao, ':campo' => $campo_db]);
        }
        header('Location: gestao.php?guardado=1');
        exit();
    } catch (PDOException $e) {
        $erro = 'Não foi possível guardar as alterações.';
    }
}

if (isset($_GET['arquivar']) && ctype_digit($_GET['arquivar'])) {
    try {
        $database->prepare("UPDATE mensagens_contacto SET ativo = 0 WHERE id_mensagem = :id")->execute([':id' => $_GET['arquivar']]);
        header('Location: gestao.php?arquivado=1#mensagens');
        exit();
    } catch (PDOException $e) { $erro = 'Não foi possível arquivar a mensagem.'; }
}

if (isset($_GET['desarquivar']) && ctype_digit($_GET['desarquivar'])) {
    try {
        $database->prepare("UPDATE mensagens_contacto SET ativo = 1 WHERE id_mensagem = :id")->execute([':id' => $_GET['desarquivar']]);
        header('Location: gestao.php?desarquivado=1#mensagens');
        exit();
    } catch (PDOException $e) { $erro = 'Não foi possível desarquivar a mensagem.'; }
}

$conteudo = [];
try {
    $linhas = $database->query("SELECT s.nome_seccao, c.campo, c.valor FROM conteudos_publicos c INNER JOIN secoes_publicas s ON c.id_seccao = s.id_seccao")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($linhas as $linha) {
        $conteudo[$linha['nome_seccao'] . '_' . $linha['campo']] = $linha['valor'];
    }
} catch (PDOException $e) { $erro = 'Não foi possível carregar o conteúdo da página pública.'; }

$mensagens = [];
try {
    $mensagens = $database->query("SELECT * FROM mensagens_contacto WHERE ativo = 1 ORDER BY data_envio DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

$mensagensArquivadas = [];
try {
    $mensagensArquivadas = $database->query("SELECT * FROM mensagens_contacto WHERE ativo = 0 ORDER BY data_envio DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

function valorConteudo($conteudo, $chave) {
    return htmlspecialchars($conteudo[$chave] ?? '');
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div id="mensagemSucesso"     class="alert alert-success d-none"><i class="fa-solid fa-circle-check me-2"></i>Alterações guardadas com sucesso.</div>
            <div id="mensagemArquivado"   class="alert alert-success d-none"><i class="fa-solid fa-circle-check me-2"></i>Mensagem arquivada com sucesso.</div>
            <div id="mensagemDesarquivado" class="alert alert-success d-none"><i class="fa-solid fa-circle-check me-2"></i>Mensagem desarquivada com sucesso.</div>
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-2"></i><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Gestão</h2>
                    <p class="text-muted mb-0">Gestão dos conteúdos da página pública e das mensagens recebidas.</p>
                </div>
                <div class="d-flex gap-2 flex-nowrap">
                    <a href="<?= BASE_URL ?>/public/index.php" target="_blank" class="btn btn-outline-primary"><i class="fa-solid fa-eye me-1"></i> Visualizar Página Pública</a>
                    <button type="submit" form="formGestaoConteudo" class="btn btn-success"><i class="fa-solid fa-floppy-disk me-1"></i> Guardar Alterações</button>
                </div>
            </div>
            <hr>
            <ul class="nav nav-tabs mb-4" id="tabsGestao" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pagina-publica-tab" data-bs-toggle="tab" data-bs-target="#pagina-publica" type="button" role="tab">
                        <i class="fa-solid fa-globe me-2"></i>Gestão da página pública
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mensagens-tab" data-bs-toggle="tab" data-bs-target="#mensagens" type="button" role="tab">
                        <i class="fa-solid fa-envelope me-2"></i>Mensagens recebidas
                        <?php if (!empty($mensagens)): ?><span class="badge bg-primary ms-1"><?= count($mensagens) ?></span><?php endif; ?>
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="tabsGestaoContent">
                <div class="tab-pane fade show active" id="pagina-publica" role="tabpanel">
                    <form id="formGestaoConteudo" action="gestao.php" method="post">
                        <section class="gestao-seccao">
                            <h4>1. Início</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Título Principal</label>
                                    <input type="text" class="form-control mb-3" name="inicio_titulo" value="<?= valorConteudo($conteudo, 'inicio_titulo') ?>">
                                    <label class="form-label">Texto de Apresentação</label>
                                    <textarea class="form-control mb-3" name="inicio_texto" rows="5"><?= valorConteudo($conteudo, 'inicio_texto') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagem Principal</label>
                                    <img src="<?= BASE_URL ?>/assets/img/hospital.png" alt="Imagem principal" class="img-fluid rounded mb-3 imagem-gestao">
                                    <input type="file" id="imagemInicio" hidden>
                                    <label for="imagemInicio" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-1"></i>Alterar imagem</label>
                                    <div class="form-text">A alteração de imagens ainda não está ligada à base de dados.</div>
                                </div>
                            </div>
                        </section>
                        <section class="gestao-seccao">
                            <h4>2. Sobre Nós</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Texto da Secção</label>
                                    <textarea class="form-control mb-3" name="sobre_texto" rows="10"><?= valorConteudo($conteudo, 'sobre_texto') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imagem da Secção</label>
                                    <img src="<?= BASE_URL ?>/assets/img/fundadores.png" alt="Imagem sobre nós" class="img-fluid rounded mb-3 imagem-gestao">
                                    <input type="file" id="imagemSobre" hidden>
                                    <label for="imagemSobre" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-1"></i>Alterar imagem</label>
                                    <div class="form-text">A alteração de imagens ainda não está ligada à base de dados.</div>
                                </div>
                            </div>
                        </section>
                        <section class="gestao-seccao">
                            <h4>3. Serviços</h4>
                            <div class="row g-4">
                                <?php
                                $iconesServicos = [1=>'fa-laptop-medical',2=>'fa-folder-open',3=>'fa-truck-medical',4=>'fa-file-contract',5=>'fa-magnifying-glass',6=>'fa-chart-column'];
                                for ($i = 1; $i <= 6; $i++):
                                ?>
                                    <div class="col-md-4">
                                        <div class="servico-gestao p-3 border rounded h-100">
                                            <div class="text-center mb-3"><i class="fa-solid <?= $iconesServicos[$i] ?> fa-2x"></i></div>
                                            <label class="form-label">Título do Serviço</label>
                                            <input type="text" class="form-control mb-3" name="servico_<?= $i ?>_titulo" value="<?= valorConteudo($conteudo, "servico_{$i}_titulo") ?>">
                                            <label class="form-label">Descrição</label>
                                            <textarea class="form-control mb-3" name="servico_<?= $i ?>_descricao" rows="3"><?= valorConteudo($conteudo, "servico_{$i}_descricao") ?></textarea>
                                        </div>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </section>
                        <section class="gestao-seccao">
                            <h4>4. Contacta-nos</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Título da Secção</label>
                                    <input type="text" class="form-control mb-3" name="contacto_titulo" value="<?= valorConteudo($conteudo, 'contacto_titulo') ?>">
                                    <label class="form-label">Texto de Apresentação</label>
                                    <textarea class="form-control mb-3" name="contacto_texto" rows="3"><?= valorConteudo($conteudo, 'contacto_texto') ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control mb-3" name="contacto_email" value="<?= valorConteudo($conteudo, 'contacto_email') ?>">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control mb-3" name="contacto_telefone" value="<?= valorConteudo($conteudo, 'contacto_telefone') ?>">
                                </div>
                            </div>
                        </section>
                        <section class="gestao-seccao">
                            <h4>5. Rodapé</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Localização</label>
                                    <textarea class="form-control mb-3" name="rodape_localizacao" rows="5"><?= valorConteudo($conteudo, 'rodape_localizacao') ?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Horário</label>
                                    <textarea class="form-control mb-3" name="rodape_horario" rows="5"><?= valorConteudo($conteudo, 'rodape_horario') ?></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Contactos</label>
                                    <textarea class="form-control mb-3" name="rodape_contactos" rows="5"><?= valorConteudo($conteudo, 'rodape_contactos') ?></textarea>
                                </div>
                            </div>
                        </section>
                    </form>
                </div>

                <div class="tab-pane fade" id="mensagens" role="tabpanel">
                    <section class="gestao-seccao">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4>Mensagens recebidas</h4>
                                <p class="text-muted mb-0">Mensagens enviadas através do formulário de contacto da página pública.</p>
                            </div>
                            <span class="badge bg-primary"><?= count($mensagens) ?> mensagens</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr><th>Data</th><th>Nome</th><th>Email</th><th>Assunto</th><th class="text-center">Ações</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mensagens)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">Sem mensagens recebidas.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($mensagens as $msg): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($msg['data_envio']))) ?></td>
                                            <td><?= htmlspecialchars($msg['nome']) ?></td>
                                            <td><?= htmlspecialchars($msg['email']) ?></td>
                                            <td><?= htmlspecialchars($msg['assunto']) ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#mensagem<?= $msg['id_mensagem'] ?>"><i class="fa-solid fa-eye"></i></button>
                                                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Resposta%20MediSync" class="btn btn-sm btn-outline-success me-1"><i class="fa-solid fa-reply"></i></a>
                                                <a href="gestao.php?arquivar=<?= $msg['id_mensagem'] ?>" class="btn btn-sm btn-outline-secondary" title="Arquivar"><i class="fa-solid fa-box-archive"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <section class="gestao-seccao mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4>Mensagens arquivadas</h4>
                                <p class="text-muted mb-0">Mensagens anteriormente arquivadas. Podes desarquivá-las para as tornar visíveis novamente.</p>
                            </div>
                            <span class="badge bg-secondary"><?= count($mensagensArquivadas) ?> arquivadas</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr><th>Data</th><th>Nome</th><th>Email</th><th>Assunto</th><th class="text-center">Ações</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($mensagensArquivadas)): ?>
                                        <tr><td colspan="5" class="text-center text-muted">Sem mensagens arquivadas.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($mensagensArquivadas as $msg): ?>
                                        <tr class="table-secondary">
                                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($msg['data_envio']))) ?></td>
                                            <td><?= htmlspecialchars($msg['nome']) ?></td>
                                            <td><?= htmlspecialchars($msg['email']) ?></td>
                                            <td><?= htmlspecialchars($msg['assunto']) ?></td>
                                            <td class="text-center">
                                                <a href="gestao.php?desarquivar=<?= $msg['id_mensagem'] ?>" class="btn btn-sm btn-outline-warning" title="Desarquivar"><i class="fa-solid fa-box-open me-1"></i> Desarquivar</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <?php foreach ($mensagens as $msg): ?>
                        <div class="modal fade" id="mensagem<?= $msg['id_mensagem'] ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title"><?= htmlspecialchars($msg['assunto']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Nome:</strong> <?= htmlspecialchars($msg['nome']) ?></p>
                                        <p><strong>Email:</strong> <?= htmlspecialchars($msg['email']) ?></p>
                                        <p><strong>Data:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($msg['data_envio']))) ?></p>
                                        <p><strong>Mensagem:</strong></p>
                                        <p><?= nl2br(htmlspecialchars($msg['mensagem'])) ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="mailto:<?= htmlspecialchars($msg['email']) ?>?subject=Resposta%20MediSync" class="btn btn-success"><i class="fa-solid fa-reply me-1"></i>Responder</a>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
        <script>
            const params = new URLSearchParams(window.location.search);
            if (params.get("guardado") === "1") {
                document.getElementById("mensagemSucesso").classList.remove("d-none");
                setTimeout(() => document.getElementById("mensagemSucesso").classList.add("d-none"), 4000);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            if (params.get("arquivado") === "1") {
                document.getElementById("mensagemArquivado").classList.remove("d-none");
                setTimeout(() => document.getElementById("mensagemArquivado").classList.add("d-none"), 4000);
                if (window.location.hash === "#mensagens") {
                    new bootstrap.Tab(document.getElementById("mensagens-tab")).show();
                }
                window.history.replaceState({}, document.title, window.location.pathname);
            }
            if (params.get("desarquivado") === "1") {
                document.getElementById("mensagemDesarquivado").classList.remove("d-none");
                setTimeout(() => document.getElementById("mensagemDesarquivado").classList.add("d-none"), 4000);
                new bootstrap.Tab(document.getElementById("mensagens-tab")).show();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        </script>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
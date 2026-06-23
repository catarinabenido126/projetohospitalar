<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();
restringir_perfil(['Administrador']);

$erros = [];
$erro_sistema = "";
$servicos = $database->query("SELECT id_servico, nome_servico FROM servicos WHERE ativo = 1 ORDER BY nome_servico")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edificio    = trim($_POST["edificio"]    ?? "");
    $piso        = trim($_POST["piso"]        ?? "");
    $sala        = trim($_POST["sala"]        ?? "");
    $servico     = trim($_POST["servico"]     ?? "");
    $responsavel = trim($_POST["responsavel"] ?? "");
    $contacto    = trim($_POST["contacto"]    ?? "");
    $email       = trim($_POST["email"]       ?? "");

    if ($edificio === '') {
        $erros[] = "O campo Edifício é obrigatório.";
    }
    if ($piso === '') {
        $erros[] = "O campo Piso é obrigatório.";
    }
    if (!empty($sala) && !preg_match('/^\d{3}$/', $sala)) {
        $erros[] = "A sala deve conter exatamente 3 dígitos (ex: 101).";
    }
    if (empty($servico) || !ctype_digit($servico)) {
        $erros[] = "Selecione um tipo de localização válido.";
    }
    if (!empty($contacto) && !preg_match('/^\d{9}$/', $contacto)) {
        $erros[] = "Contacto inválido (deve ter 9 dígitos).";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido.";
    }

    if (empty($erros)) {
        $edificio    = trim(preg_replace('/^edif[ií]cio\s+/i', '', $edificio));
        $edificio    = ucwords(strtolower($edificio));
        $piso        = trim(preg_replace('/^piso\s*/i', '', $piso));
        $responsavel = $responsavel !== "" ? ucwords(strtolower($responsavel)) : "";
        $email       = strtolower($email);
    }

    if (empty($erros)) {
        try {
            $sql = "INSERT INTO localizacoes (edificio, piso, sala, id_servico, responsavel, contacto, email, ativo, created_at, updated_at)
                    VALUES (:edificio, :piso, :sala, :servico, :responsavel, :contacto, :email, 1, NOW(), NOW())";
            $database->prepare($sql)->execute([
                ":edificio"    => $edificio,
                ":piso"        => $piso,
                ":sala"        => $sala !== "" ? $sala : null,
                ":servico"     => $servico,
                ":responsavel" => $responsavel !== "" ? $responsavel : null,
                ":contacto"    => $contacto !== "" ? $contacto : null,
                ":email"       => $email !== "" ? $email : null
            ]);
            registar_historico($database, 'Localizações', 'Criação', 'Edifício ' . $edificio . ' • Piso ' . $piso, 'Localização criada com sucesso.');
            header("Location: lista.php?criado=1");
            exit();
        } catch (PDOException $err) {
            $erro_sistema = $err->errorInfo[1] == 1062
                ? "Já existe uma localização registada com este Edifício, Piso e Sala."
                : "Erro ao gravar os dados: " . $err->getMessage();
        }
    }
}
?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Nova Localização</h2>
            <hr>
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <strong>Foram encontrados os seguintes erros:</strong>
                    <ul class="mb-0"><?php foreach ($erros as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger"><strong>Erro:</strong><p class="mb-0"><?= htmlspecialchars($erro_sistema) ?></p></div>
            <?php endif; ?>
            <form action="#" method="post" novalidate>
                <h4>Estrutura física</h4>
                <div class="mb-3">
                    <label for="edificio" class="form-label">Edifício</label>
                    <input type="text" class="form-control" id="edificio" name="edificio"
                           placeholder="Ex: A" value="<?= htmlspecialchars($_POST['edificio'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="piso" class="form-label">Piso </label>
                    <input type="text" class="form-control" id="piso" name="piso"
                           placeholder="Ex: 0" value="<?= htmlspecialchars($_POST['piso'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="sala" class="form-label">Sala</label>
                    <input type="text" class="form-control" id="sala" name="sala"
                           maxlength="3" pattern="[0-9]{3}" placeholder="Ex: 101"
                           value="<?= htmlspecialchars($_POST['sala'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="servico" class="form-label">Tipo de localização</label>
                    <select class="form-select" id="servico" name="servico" required>
                        <option value="" disabled <?= empty($_POST['servico'] ?? '') ? 'selected' : '' ?>>Selecione um tipo de serviço</option>
                        <?php foreach ($servicos as $s): ?>
                            <option value="<?= $s['id_servico'] ?>" <?= (($_POST['servico'] ?? '') == $s['id_servico']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nome_servico']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <h4>Responsável</h4>
                <div class="mb-3">
                    <label for="responsavel" class="form-label">Responsável pela área</label>
                    <input type="text" class="form-control" id="responsavel" name="responsavel"
                           value="<?= htmlspecialchars($_POST['responsavel'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="contacto-loc" class="form-label">Contacto</label>
                    <input type="text" class="form-control" id="contacto-loc" name="contacto"
                           value="<?= htmlspecialchars($_POST['contacto'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="lista.php" class="btn btn-secondary">Cancelar</a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
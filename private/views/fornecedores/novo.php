<?php

require_once __DIR__ . '/../../includes/funcoes.php';
require_once __DIR__ . '/../../includes/database.php';
redirect_if_not_logged();

$erros = [];
$erro_sistema = "";
$tipos_fornecedor = $database->query("SELECT id_tipo_fornecedor, tipo FROM tipos_fornecedor WHERE ativo = 1 ORDER BY tipo")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa = trim($_POST["nomeEmpresa"] ?? "");
    $nif = trim($_POST["nif"] ?? "");
    $tipo_fornecedor = trim($_POST["tipoFornecedor"] ?? "");
    $telefone = trim($_POST["telefone"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $website = trim($_POST["website"] ?? "");
    $pessoa_contacto = trim($_POST["pessoaContacto"] ?? "");
    $telefone_contacto = trim($_POST["telefoneContacto"] ?? "");
    $morada = trim($_POST["morada"] ?? "");
    $codigo_postal = trim($_POST["codigoPostal"] ?? "");
    $cidade = trim($_POST["cidade"] ?? "");
    $pais = trim($_POST["pais"] ?? "");
    $observacoes = trim($_POST["observacoes"] ?? "");

    if (empty($nome_empresa)) {
        $erros[] = "O campo Nome da Empresa é obrigatório.";
    } elseif (preg_match('/^\d+$/', $nome_empresa)) {
        $erros[] = "O Nome da Empresa não pode conter apenas números.";
    }
    if (empty($nif)) {
        $erros[] = "O campo NIF é obrigatório.";
    } elseif (!preg_match('/^\d{9}$/', $nif)) {
        $erros[] = "NIF inválido (deve ter exatamente 9 dígitos).";
    }
    if (empty($tipo_fornecedor) || !ctype_digit($tipo_fornecedor)) {
        $erros[] = "Selecione um tipo de fornecedor válido.";
    }
    if (!empty($telefone) && !preg_match('/^\d{9}$/', $telefone)) {
        $erros[] = "Telefone inválido (deve ter 9 dígitos).";
    }
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "O endereço de email não é válido.";
    }
    if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
        $erros[] = "O endereço de website não é válido.";
    }
    if (!empty($telefone_contacto) && !preg_match('/^\d{9}$/', $telefone_contacto)) {
        $erros[] = "O telefone da pessoa de contacto é inválido (deve ter 9 dígitos).";
    }
    if (!empty($codigo_postal) && !preg_match('/^\d{4}-\d{3}$/', $codigo_postal)) {
        $erros[] = "Código Postal inválido (ex: 4000-007).";
    }
    if (empty($erros)) {
        $email = strtolower($email);
        $cidade = $cidade !== "" ? ucfirst(strtolower($cidade)) : "";
        $pais = $pais !== "" ? ucfirst(strtolower($pais)) : "";
        $pessoa_contacto = $pessoa_contacto !== "" ? ucwords(strtolower($pessoa_contacto)) : "";
    }
    if (empty($erros)) {
        try {
            $sql = "INSERT INTO fornecedores (nome_empresa, nif, id_tipo_fornecedor, telefone, email, website, pessoa_contacto, telefone_contacto, morada, codigo_postal, cidade, pais, observacoes, ativo, created_at, updated_at) VALUES (:nome_empresa, :nif, :tipo_fornecedor, :telefone, :email, :website, :pessoa_contacto, :telefone_contacto, :morada, :codigo_postal, :cidade, :pais, :observacoes, 1, NOW(), NOW())";
            $query = $database->prepare($sql);
            $query->execute([
                ":nome_empresa" => $nome_empresa,
                ":nif" => $nif,
                ":tipo_fornecedor" => $tipo_fornecedor,
                ":telefone" => $telefone !== "" ? $telefone : null,
                ":email" => $email !== "" ? $email : null,
                ":website" => $website !== "" ? $website : null,
                ":pessoa_contacto" => $pessoa_contacto !== "" ? $pessoa_contacto : null,
                ":telefone_contacto" => $telefone_contacto !== "" ? $telefone_contacto : null,
                ":morada" => $morada !== "" ? $morada : null,
                ":codigo_postal" => $codigo_postal !== "" ? $codigo_postal : null,
                ":cidade" => $cidade !== "" ? $cidade : null,
                ":pais" => $pais !== "" ? $pais : null,
                ":observacoes" => $observacoes !== "" ? $observacoes : null
            ]);
            header("Location: lista.php?criado=1");
            exit();
        } catch (PDOException $err) {
            if ($err->errorInfo[1] == 1062) {
                $erro_sistema = "O NIF inserido já corresponde a um fornecedor.";
            } else {
                $erro_sistema = "Erro ao gravar os dados: " . $err->getMessage();
            }
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
            <h2>Novo Fornecedor</h2>
            <hr>
            <?php if (!empty($erros)): ?>
                <div class="alert alert-danger">
                    <strong>Foram encontrados os seguintes erros:</strong>
                    <ul class="mb-0">
                        <?php foreach ($erros as $erro): ?>
                            <li><?= htmlspecialchars($erro) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <?php if (!empty($erro_sistema)): ?>
                <div class="alert alert-danger">
                    <strong>Erro:</strong>
                    <p class="mb-0"><?= htmlspecialchars($erro_sistema) ?></p>
                </div>
            <?php endif; ?>
            <form action="#" method="post" novalidate>
                <h4>Informação principal</h4>
                <div class="mb-3">
                    <label for="nomeEmpresa" class="form-label">Nome da empresa</label>
                    <input type="text" class="form-control" id="nomeEmpresa" name="nomeEmpresa" value="<?= htmlspecialchars($_POST['nomeEmpresa'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nif" class="form-label">NIF</label>
                    <input type="text" class="form-control" id="nif" name="nif" value="<?= htmlspecialchars($_POST['nif'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipoFornecedor" class="form-label">Tipo de fornecedor</label>
                    <select class="form-select" id="tipoFornecedor" name="tipoFornecedor" required>
                        <option value="" disabled <?= empty($_POST['tipoFornecedor'] ?? '') ? 'selected' : '' ?>>Selecione o tipo de fornecedor</option>
                        <?php foreach ($tipos_fornecedor as $tf): ?>
                            <option value="<?= $tf['id_tipo_fornecedor'] ?>" <?= (($_POST['tipoFornecedor'] ?? '') == $tf['id_tipo_fornecedor']) ? 'selected' : '' ?>><?= htmlspecialchars($tf['tipo']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <h4>Contactos</h4>
                <div class="mb-3">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="tel" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($_POST['telefone'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" class="form-control" id="website" name="website" value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
                </div>
                <h4>Pessoa de contacto</h4>
                <div class="mb-3">
                    <label for="pessoaContacto" class="form-label">Nome da pessoa de contacto</label>
                    <input type="text" class="form-control" id="pessoaContacto" name="pessoaContacto" value="<?= htmlspecialchars($_POST['pessoaContacto'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="telefoneContacto" class="form-label">Telefone da pessoa de contacto</label>
                    <input type="tel" class="form-control" id="telefoneContacto" name="telefoneContacto" value="<?= htmlspecialchars($_POST['telefoneContacto'] ?? '') ?>">
                </div>
                <h4>Morada</h4>
                <div class="mb-3">
                    <label for="morada" class="form-label">Morada</label>
                    <input type="text" class="form-control" id="morada" name="morada" value="<?= htmlspecialchars($_POST['morada'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="codigoPostal" class="form-label">Código postal</label>
                    <input type="text" class="form-control" id="codigoPostal" name="codigoPostal" value="<?= htmlspecialchars($_POST['codigoPostal'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text" class="form-control" id="cidade" name="cidade" value="<?= htmlspecialchars($_POST['cidade'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label for="pais" class="form-label">País</label>
                    <input type="text" class="form-control" id="pais" name="pais" value="<?= htmlspecialchars($_POST['pais'] ?? 'Portugal') ?>">
                </div>
                <h4>Observações</h4>
                <div class="mb-3">
                    <label for="observacoes" class="form-label">Observações</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4"><?= htmlspecialchars($_POST['observacoes'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    Guardar
                </button>
                <a href="lista.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </form>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<?php
require_once __DIR__ . '/../private/includes/database.php';

$conteudo = [];

try {
    $linhas = $database->query("
        SELECT s.nome_seccao, c.campo, c.valor
        FROM conteudos_publicos c
        INNER JOIN secoes_publicas s ON c.id_seccao = s.id_seccao
        WHERE c.ativo = 1 AND s.ativo = 1
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($linhas as $linha) {
        $conteudo[$linha['nome_seccao'] . '_' . $linha['campo']] = $linha['valor'];
    }

} catch (PDOException $e) {
    $conteudo = [];
}

function c($conteudo, $chave)
{
    return htmlspecialchars($conteudo[$chave] ?? '');
}

$contacto_enviado = isset($_GET['contacto_enviado']);
$erro_contacto = isset($_GET['erro_contacto']);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediSync</title>
    <!-- favicon -->
    <link rel="shortcut icon" href="../assets/img/logo.png" type="image/png">
      <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <!-- estilos da página -->
    <link rel="stylesheet" href="../assets/css/1241126.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/bootstrap/bootstrap.min.css">
</head>
<body>
    <!-- Navegação -->
    <nav class="bng-navbar">
        <!-- Logo e Nome -->
        <div>
            <img src="../assets/img/logo.png" alt="Logo da MediSync">
        </div>
        <!-- Links centrais -->
        <div class="container-navegacao">
         <a href="#inicio">Início</a>
         <a href="#sobre">Sobre Nós</a>
         <a href="#servicos">Serviços</a>
         <a href="#contacto">Contacta-nos</a>   
        </div>
        <!-- Área Interna -->
         <div class="nav-cliente">
            <a href="../login/login.php">
    <i class="fa-solid fa-lock"></i>
    Área Interna
</a>
        </div>
    </nav>
<!-- Seção "Conteudo da pagina" -->
<!-- Seção "Início" -->
<section class="container-texto-generico" id="inicio">
    <div class="inicio-content">
        <h1>
            <?= c($conteudo, 'inicio_titulo') ?>
        </h1>
        <p>
            <?= c($conteudo, 'inicio_texto') ?>
        </p>
        <img src="../assets/img/hospital.png" alt="Imagem Hospitalar">
    </div>
</section>
<!-- Seção "Sobre Nós" -->
<section id="sobre">
    <h2>Sobre Nós</h2>
    <div class="sobre-container">
        <div class="sobre-texto">
            <?php foreach (explode("\n\n", $conteudo['sobre_texto'] ?? '') as $paragrafo): ?>
                <p><?= nl2br(htmlspecialchars($paragrafo)) ?></p>
            <?php endforeach; ?>
        </div>
        <div class="sobre-imagem">
            <img src="../assets/img/fundadores.png"
                 alt="Fundadores da MediSync">
        </div>
    </div>
</section>

<!-- Seção "Serviços" -->
<section id="servicos">
    <h2>Serviços</h2>
    <div class="servicos-container">
        <?php
        $iconesServicos = [
            1 => 'fa-laptop-medical',
            2 => 'fa-folder-open',
            3 => 'fa-truck-medical',
            4 => 'fa-file-contract',
            5 => 'fa-magnifying-glass',
            6 => 'fa-chart-column'
        ];
        for ($i = 1; $i <= 6; $i++):
        ?>
            <div class="servico">
                <i class="fa-solid <?= $iconesServicos[$i] ?> fa-3x"></i>
                <h3><?= c($conteudo, "servico_{$i}_titulo") ?></h3>
                <p>
                    <?= c($conteudo, "servico_{$i}_descricao") ?>
                </p>
            </div>
        <?php endfor; ?>
    </div>
</section>
<!-- Seção "Contactos" -->
<section id="contacto">
    <h2><?= c($conteudo, 'contacto_titulo') ?></h2>
    <p>
        <?= c($conteudo, 'contacto_texto') ?>
    </p>
    <?php if ($contacto_enviado): ?>
        <div class="alert alert-success" style="max-width: 500px; margin: 0 auto 20px;">
            Mensagem enviada com sucesso. Entraremos em contacto brevemente.
        </div>
    <?php endif; ?>
    <?php if ($erro_contacto): ?>
        <div class="alert alert-danger" style="max-width: 500px; margin: 0 auto 20px;">
            Não foi possível enviar a mensagem. Verifique os dados e tente novamente.
        </div>
    <?php endif; ?>
    <form id="contactForm" action="processa_contacto.php" method="post">
        <label for="nome">Nome:</label>
        <input type="text"
               id="nome"
               name="nome"
               required>
        <label for="email">Email:</label>
        <input type="email"
               id="email"
               name="email"
               required>
        <label for="assunto">Assunto:</label>
        <input type="text"
               id="assunto"
               name="assunto"
               required>
        <label for="mensagem">Mensagem:</label>
        <textarea id="mensagem"
                  name="mensagem"
                  rows="4"
                  required></textarea>
        <button type="submit">
            Enviar Mensagem
        </button>
    </form>
</section>
<!-- Rodapé -->
<footer class="footer-container">
    <div class="footer-section">
        <strong>LOCALIZAÇÃO</strong>
        <p><?= nl2br(c($conteudo, 'rodape_localizacao')) ?></p>
    </div>
    <div class="footer-section">
        <strong>HORÁRIO</strong>
        <p><?= nl2br(c($conteudo, 'rodape_horario')) ?></p>
    </div>
    <div class="footer-section">
        <strong>CONTACTOS</strong>
        <p><?= nl2br(c($conteudo, 'rodape_contactos')) ?></p>
    </div>
</footer>
<script src="../assets/bootstrap/bootstrap.bundle.min.js"></script>
</body> 
</html>
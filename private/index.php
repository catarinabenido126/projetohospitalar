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
                        <p>
                            <strong>Bem-vindo à área interna da MediSync.</strong>
                        </p>
                        <p>
                            Utilize o menu lateral para aceder aos módulos de gestão do inventário hospitalar.
                        </p>
                    </div>
                </div>
                <div class="lembretes-inicial mt-4">
                    <h4>
                        <i class="fa-solid fa-calendar-check me-1"></i>
                        Lembretes
                    </h4>
                    <div class="caixa-lembretes">
                        <div class="d-flex gap-2 mb-3">
                            <input type="text" class="form-control" id="textoLembrete"
                                placeholder="Escreva um lembrete...">
                            <button type="button" class="btn btn-primary" onclick="adicionarLembrete()">
                                Adicionar
                            </button>
                        </div>
                        <div id="listaLembretes">
                            <div class="lembrete-item">
                                <input type="checkbox" class="form-check-input">
                                <div>
                                    <strong>Verificar calibração do Monitor Multiparamétrico X90</strong>
                                    <p>Lembrete criado pelo utilizador</p>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="removerLembrete(this)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="caixa-ultimo-acesso mb-4">
                    <i class="fa-solid fa-clock me-2"></i>
                    <strong>Último acesso:</strong>
                    10/06/2026 às 14:35
                </div>
            </section>
        </main>
    </div>
</div>
<script>
    function adicionarLembrete() {
        let texto = document.getElementById("textoLembrete").value;
        if (texto.trim() === "") {
            alert("Escreva um lembrete antes de adicionar.");
            return;
        }
        let lista = document.getElementById("listaLembretes");
        lista.innerHTML += `
            <div class="lembrete-item">
                <input type="checkbox" class="form-check-input">
                <div>
                    <strong>${texto}</strong>
                    <p>Lembrete criado pelo utilizador</p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerLembrete(this)">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        document.getElementById("textoLembrete").value = "";
    }
    function removerLembrete(botao) {
        botao.parentElement.remove();
    }
</script>
<?php include 'includes/footer.php'; ?>
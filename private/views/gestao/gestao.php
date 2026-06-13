<?php

require_once __DIR__ . '/../../includes/funcoes.php';

redirect_if_not_logged();

?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div id="mensagemSucesso" class="alert alert-success d-none" role="alert"><i class="fa-solid fa-circle-check me-2"></i>Alterações guardadas com sucesso.</div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2>Gestão</h2>
                    <p class="text-muted mb-0">Gestão dos conteúdos da página pública e das mensagens recebidas.</p>
                </div>
                <div class="d-flex gap-2 flex-nowrap">
                    <a href="/public/index.php" target="_blank" class="btn btn-outline-primary">
                        <i class="fa-solid fa-eye me-1"></i>
                        Visualizar Página Pública
                    </a>
                    <a href="gestao.php?guardado=1" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar Alterações
                    </a>
                </div>
            </div>
            <hr>
            <ul class="nav nav-tabs mb-4" id="tabsGestao" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pagina-publica-tab" data-bs-toggle="tab" data-bs-target="#pagina-publica" type="button" role="tab">
                        <i class="fa-solid fa-globe me-2"></i>
                        Gestão da página pública
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="mensagens-tab" data-bs-toggle="tab" data-bs-target="#mensagens" type="button" role="tab">
                        <i class="fa-solid fa-envelope me-2"></i>
                        Mensagens recebidas
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="tabsGestaoContent">
                <div class="tab-pane fade show active" id="pagina-publica" role="tabpanel">
                    <section class="gestao-seccao">
                        <h4>1. Início</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Título Principal</label>
                                <input type="text" class="form-control mb-3" value="Sistema Digital de Apoio ao Inventário Hospitalar">
                                <label class="form-label">Texto de Apresentação</label>
                                <textarea class="form-control mb-3" rows="5">A MediSync é uma plataforma digital criada para apoiar instituições de saúde na organização e consulta do inventário de equipamentos médicos.</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Imagem Principal</label>
                                <img src="/assets/img/hospital.png" alt="Imagem principal" class="img-fluid rounded mb-3 imagem-gestao">
                                <input type="file" id="imagemInicio" hidden>
                                <label for="imagemInicio" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-1"></i>Alterar imagem</label>
                            </div>
                        </div>
                    </section>
                    <section class="gestao-seccao">
                        <h4>2. Sobre Nós</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Texto da Secção</label>
                                <textarea class="form-control mb-3" rows="10">A MediSync foi criada em 2026, no âmbito da Licenciatura em Engenharia Biomédica do ISEP, com o objetivo de desenvolver uma solução web aplicada à gestão de informação hospitalar.

O projeto surgiu da necessidade de tornar mais simples a organização de equipamentos médicos, documentação técnica, fornecedores e dados associados ao funcionamento de um inventário hospitalar.

A plataforma procura contribuir para uma gestão mais centralizada, clara e acessível, apoiando a consulta rápida da informação relevante para os serviços hospitalares.</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Imagem da Secção</label>
                                <img src="/assets/img/fundadores.png" alt="Imagem sobre nós" class="img-fluid rounded mb-3 imagem-gestao">
                                <input type="file" id="imagemSobre" hidden>
                                <label for="imagemSobre" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-1"></i>Alterar imagem</label>
                            </div>
                        </div>
                    </section>
                    <section class="gestao-seccao">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4>3. Serviços</h4>
                        </div>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-laptop-medical fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Inventário de Equipamentos">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Registo e consulta de equipamentos médicos, incluindo identificação, localização, categoria, estado e criticidade.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-folder-open fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Gestão Documental">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Organização de manuais, certificados, relatórios técnicos, faturas e outros documentos associados.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-truck-medical fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Fornecedores">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Associação de equipamentos a fornecedores, fabricantes e entidades responsáveis por assistência técnica.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-file-contract fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Garantias e Contratos">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Consulta de garantias e contratos de manutenção relacionados com os equipamentos hospitalares.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-magnifying-glass fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Pesquisa e Filtros">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Pesquisa de equipamentos por serviço, localização, categoria, estado ou criticidade.</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="servico-gestao p-3 border rounded h-100">
                                    <div class="text-center mb-3"><i class="fa-solid fa-chart-column fa-2x"></i></div>
                                    <label class="form-label">Título do Serviço</label>
                                    <input type="text" class="form-control mb-3" value="Dashboard">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control mb-3" rows="3">Visualização resumida de indicadores úteis para acompanhamento do inventário hospitalar.</textarea>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="gestao-seccao">
                        <h4>4. Contacta-nos</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Título da Secção</label>
                                <input type="text" class="form-control mb-3" value="Contacta-nos">
                                <label class="form-label">Texto de Apresentação</label>
                                <textarea class="form-control mb-3" rows="3">Para mais informações sobre a MediSync, fale connosco!</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control mb-3" value="geral@medisync.pt">
                                <label class="form-label">Telefone</label>
                                <input type="text" class="form-control mb-3" value="+351 222 000 000">
                            </div>
                        </div>
                    </section>
                    <section class="gestao-seccao">
                        <h4>5. Rodapé</h4>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Localização</label>
                                <textarea class="form-control mb-3" rows="5">Instituto Superior de Engenharia do Porto
Rua Dr. António Bernardino de Almeida
Porto, Portugal</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Horário</label>
                                <textarea class="form-control mb-3" rows="5">2ª a 6ª Feira: 9h — 18h
Sábado: Encerrado
Domingo: Encerrado</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contactos</label>
                                <textarea class="form-control mb-3" rows="5">Email: geral@medisync.pt
Telefone: +351 222 000 000</textarea>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="tab-pane fade" id="mensagens" role="tabpanel">
                    <section class="gestao-seccao">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4>Mensagens recebidas</h4>
                                <p class="text-muted mb-0">Mensagens enviadas através do formulário de contacto da página pública.</p>
                            </div>
                            <span class="badge bg-primary">3 mensagens</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Assunto</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>10/06/2026</td>
                                        <td>João Martins</td>
                                        <td>joao.martins@email.com</td>
                                        <td>Pedido de demonstração da plataforma</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#mensagem1"><i class="fa-solid fa-eye"></i></button>
                                            <a href="mailto:joao.martins@email.com?subject=Resposta%20ao%20pedido%20de%20demonstração%20da%20MediSync" class="btn btn-sm btn-outline-success me-1"><i class="fa-solid fa-reply"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-box-archive"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>09/06/2026</td>
                                        <td>Carla Ferreira</td>
                                        <td>carla.ferreira@email.com</td>
                                        <td>Dúvida sobre gestão documental</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#mensagem2"><i class="fa-solid fa-eye"></i></button>
                                            <a href="mailto:carla.ferreira@email.com?subject=Resposta%20sobre%20gestão%20documental%20na%20MediSync" class="btn btn-sm btn-outline-success me-1"><i class="fa-solid fa-reply"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-box-archive"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>08/06/2026</td>
                                        <td>Pedro Almeida</td>
                                        <td>pedro.almeida@email.com</td>
                                        <td>Integração com inventário hospitalar</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#mensagem3"><i class="fa-solid fa-eye"></i></button>
                                            <a href="mailto:pedro.almeida@email.com?subject=Resposta%20sobre%20integração%20com%20inventário%20hospitalar" class="btn btn-sm btn-outline-success me-1"><i class="fa-solid fa-reply"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-box-archive"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <div class="modal fade" id="mensagem1" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Pedido de demonstração da plataforma</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nome:</strong> João Martins</p>
                                    <p><strong>Email:</strong> joao.martins@email.com</p>
                                    <p><strong>Mensagem:</strong></p>
                                    <p>Gostaria de receber mais informações sobre a plataforma MediSync e perceber como poderia ser utilizada para organizar o inventário de equipamentos médicos de uma unidade hospitalar.</p>
                                </div>
                                <div class="modal-footer"><a href="mailto:joao.martins@email.com?subject=Resposta%20ao%20pedido%20de%20demonstração%20da%20MediSync" class="btn btn-success"><i class="fa-solid fa-reply me-1"></i>Responder</a><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="mensagem2" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Dúvida sobre gestão documental</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nome:</strong> Carla Ferreira</p>
                                    <p><strong>Email:</strong> carla.ferreira@email.com</p>
                                    <p><strong>Mensagem:</strong></p>
                                    <p>Boa tarde. Gostaria de saber se a plataforma permite associar manuais, certificados de calibração e contratos de manutenção a cada equipamento.</p>
                                </div>
                                <div class="modal-footer"><a href="mailto:carla.ferreira@email.com?subject=Resposta%20sobre%20gestão%20documental%20na%20MediSync" class="btn btn-success"><i class="fa-solid fa-reply me-1"></i>Responder</a><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="mensagem3" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Integração com inventário hospitalar</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Nome:</strong> Pedro Almeida</p>
                                    <p><strong>Email:</strong> pedro.almeida@email.com</p>
                                    <p><strong>Mensagem:</strong></p>
                                    <p>Gostaria de perceber se a MediSync pode ser adaptada a um inventário hospitalar já existente e se permite consultar equipamentos por localização e criticidade.</p>
                                </div>
                                <div class="modal-footer"><a href="mailto:pedro.almeida@email.com?subject=Resposta%20sobre%20integração%20com%20inventário%20hospitalar" class="btn btn-success"><i class="fa-solid fa-reply me-1"></i>Responder</a><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <script>
            const params = new URLSearchParams(window.location.search);
            if (params.get("guardado") === "1") {
                const alerta = document.getElementById("mensagemSucesso");
                alerta.classList.remove("d-none");
                setTimeout(function () {
                    alerta.classList.add("d-none");
                }, 4000);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        </script>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <h2>Detalhes do Fornecedor</h2>
            <hr>
            <h4>Informação principal</h4>
            <p><strong>Nome da empresa:</strong> Philips Healthcare</p>
            <p><strong>NIF:</strong> 501234567</p>
            <p><strong>Tipo de fornecedor:</strong> Fabricante</p>
            <hr>
            <h4>Contactos</h4>
            <p><strong>Telefone:</strong> 222 000 100</p>
            <p><strong>Email:</strong> geral@philips-healthcare.pt</p>
            <p><strong>Website:</strong> https://www.philips.pt</p>
            <hr>
            <h4>Pessoa de contacto</h4>
            <p><strong>Nome:</strong> Ana Martins</p>
            <p><strong>Telefone:</strong> 912 345 678</p>
            <p><strong>Email:</strong> ana.martins@philips-healthcare.pt</p>
            <hr>
            <h4>Morada</h4>
            <p><strong>Morada:</strong> Rua da Saúde, 120</p>
            <p><strong>Código postal:</strong> 4200-300</p>
            <p><strong>Cidade:</strong> Porto</p>
            <p><strong>País:</strong> Portugal</p>
            <hr>
            <h4>Observações</h4>
            <p>
                Fornecedor associado a equipamentos de monitorização hospitalar.
            </p>
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
                        <tr>
                            <td>EQ-0001</td>
                            <td>Monitor Multiparamétrico</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>EQ-0002</td>
                            <td>Ventilador Pulmonar</td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <a href="lista.php" class="btn btn-secondary">
                Voltar
            </a>
            <a href="editar.php" class="btn btn-warning">
                Editar
            </a>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
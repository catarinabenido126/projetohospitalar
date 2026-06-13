<?php include '../../includes/header.php'; ?>
<?php include '../../includes/nav.php'; ?>
<div class="container-fluid">
    <div class="row">
        <?php include '../../includes/sidebar.php'; ?>
        <main class="col-md-9 col-lg-10 p-4 area-conteudo">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-0">
                        Equipamentos na Localização
                    </h2>
                </div>
                <a href="lista.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Voltar
                </a>
            </div>
            <hr>
            <div class="border rounded p-3 mb-4">
                <h5 class="mb-3">
                    Detalhes da localização
                </h5>
                <div class="row">
                    <div class="col-md-3">
                        <p>
                            <strong>Edifício:</strong><br>
                            Edifício A
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Piso:</strong><br>
                            Piso 1
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Sala:</strong><br>
                            Sala 101
                        </p>
                    </div>
                    <div class="col-md-2">
                        <p>
                            <strong>Serviço:</strong><br>
                            UCI
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <p>
                            <strong>Responsável:</strong><br>
                            Enf. Marta Silva
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-0">
                            <strong>Contacto:</strong><br>
                            <i class="fa-solid fa-phone me-1"></i>
                            912 345 678
                        </p>
                    </div>
                </div>
            </div>
            <div class="caixa-tabela table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Designação</th>
                            <th>Categoria</th>
                            <th>Estado</th>
                            <th>Criticidade</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>EQ-0001</td>
                            <td>Monitor Multiparamétrico</td>
                            <td>Monitorização</td>
                            <td>
                                <span class="badge bg-success">
                                    Ativo
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    Alta
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="../equipamentos/editar.php" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>EQ-0002</td>
                            <td>Ventilador Pulmonar</td>
                            <td>Suporte de Vida</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    Em manutenção
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger">
                                    Suporte de vida
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="../equipamentos/detalhes.php" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="../equipamentos/editar.php" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="mb-0 text-muted">
                        A mostrar 1 a 2 de 2 equipamentos
                    </p>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    Anterior
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">
                                    1
                                </a>
                            </li>
                            <li class="page-item disabled">
                                <a class="page-link" href="#">
                                    Seguinte
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
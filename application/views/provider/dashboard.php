<?php $title = 'Panel Proveedor'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Bienvenido, <?= esc($this->session->userdata('full_name')) ?></h3>
        <p class="text-muted">Gestione solicitudes, cotizaciones y servicios.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($available_requests) ?></h5>
                <p class="card-text">Solicitudes disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($my_requests) ?></h5>
                <p class="card-text">Mis solicitudes asignadas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($quotes) ?></h5>
                <p class="card-text">Cotizaciones</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($services) ?></h5>
                <p class="card-text">Servicios</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Solicitudes Disponibles</h5>
            </div>
            <div class="card-body">
                <?php if (empty($available_requests)): ?>
                    <p class="text-muted">No hay solicitudes disponibles en este momento.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Título</th><th>Oficio</th><th>Ubicación</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($available_requests as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->title) ?></td>
                                    <td><?= esc($r->trade_name) ?></td>
                                    <td class="ficticio small"><?= esc($r->location) ?></td>
                                    <td><a href="/oficios/provider/request/<?= (int) $r->id ?>/respond" class="btn btn-sm btn-success">Responder</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Mis Solicitudes Asignadas</h5>
            </div>
            <div class="card-body">
                <?php if (empty($my_requests)): ?>
                    <p class="text-muted">No tiene solicitudes asignadas.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($my_requests as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->client_name) ?></td>
                                    <td><span class="badge bg-<?= status_badge_class($r->status) ?>"><?= esc($r->status) ?></span></td>
                                    <td>
                                        <a href="/oficios/provider/request/<?= (int) $r->id ?>/respond" class="btn btn-sm btn-outline-primary">Ver solicitud</a>
                                        <?php if ($r->status === 'confirmado'): ?>
                                            <a href="/oficios/provider/quote/create/<?= (int) $r->id ?>" class="btn btn-sm btn-success">Cotizar</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

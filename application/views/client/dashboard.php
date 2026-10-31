<?php $title = 'Panel Cliente'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row">
    <div class="col-12">
        <h3>Bienvenido, <?= esc($this->session->userdata('full_name')) ?></h3>
        <p class="text-muted">Gestione sus solicitudes de servicios de oficios.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-earmark-plus"></i> Crear Solicitud</h5>
                <p class="card-text">Solicite un servicio de oficio (fontanero, electricista, mecánico, etc.)</p>
                <a href="/oficios/client/request/create" class="btn btn-light">Nueva Solicitud</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-earmark-check"></i> Cotizaciones Pendientes</h5>
                <p class="card-text"><?= count($pending_quotes) ?> cotización(es) esperando su aprobación</p>
                <a href="/oficios/client/history" class="btn btn-light">Ver Cotizaciones</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-clock-history"></i> Historial</h5>
                <p class="card-text"><?= count($requests) ?> solicitud(es) registrada(s)</p>
                <a href="/oficios/client/history" class="btn btn-light">Ver Historial</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-list-task"></i> Mis Solicitudes Recientes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($requests)): ?>
                    <p class="text-muted">No tiene solicitudes registradas.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th><th>Oficio</th><th>Título</th><th>Estado</th><th>Fecha</th><th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($requests, 0, 5) as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->trade_name) ?></td>
                                    <td><?= esc($r->title) ?></td>
                                    <td><span class="status-badge badge bg-<?= status_badge_class($r->status) ?>"><?= esc($r->status) ?></span></td>
                                    <td><?= esc($r->created_at) ?></td>
                                    <td><a href="/oficios/client/request/<?= $r->id ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
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

<?php $title = 'Historial'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Mis Solicitudes y Servicios</h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Solicitudes</h5>
            </div>
            <div class="card-body">
                <?php if (empty($requests)): ?>
                    <p class="text-muted">No tiene solicitudes.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Solicitud</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($requests as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->title) ?></td>
                                    <td><span class="badge bg-<?= status_badge_class($r->status) ?>"><?= esc($r->status) ?></span></td>
                                    <td><?= esc($r->created_at) ?></td>
                                    <td><a href="/oficios/client/request/<?= (int) $r->id ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
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
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Servicios Contratados</h5>
            </div>
            <div class="card-body">
                <?php if (empty($services)): ?>
                    <p class="text-muted">No tiene servicios contratados.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Solicitud</th><th>Estado</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($services as $s): ?>
                                <tr>
                                    <td><?= $s->id ?></td>
                                    <td><?= esc($s->request_title) ?></td>
                                    <td><span class="badge bg-<?= status_badge_class($s->status) ?>"><?= esc($s->status) ?></span></td>
                                    <td>
                                        <?php if ($s->status === 'completado'): ?>
                                            <a href="/oficios/client/rating/<?= $s->id ?>" class="btn btn-sm btn-warning">Calificar</a>
                                        <?php endif; ?>
                                        <?php if (empty($s->payment_status) || $s->payment_status !== 'completado'): ?>
                                            <a href="/oficios/client/payment/<?= (int) $s->id ?>" class="btn btn-sm btn-success">Pagar</a>
                                        <?php else: ?>
                                            <span class="badge text-bg-success">Pagado (simulado)</span>
                                        <?php endif; ?>
                                        <a href="/oficios/service/view/<?= $s->id ?>" class="btn btn-sm btn-outline-primary">Ver</a>
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

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Mis Reclamos</h5>
            </div>
            <div class="card-body">
                <?php if (empty($complaints)): ?>
                    <p class="text-muted">No tiene reclamos registrados.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Título</th><th>Estado</th><th>Fecha</th></tr></thead>
                            <tbody>
                                <?php foreach ($complaints as $c): ?>
                                <tr>
                                    <td><?= $c->id ?></td>
                                    <td><?= esc($c->title) ?></td>
                                    <td><span class="badge bg-<?= status_badge_class($c->status) ?>"><?= esc($c->status) ?></span></td>
                                    <td><?= esc($c->created_at) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <a href="/oficios/client/history" class="btn btn-outline-primary">Volver a mis solicitudes</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

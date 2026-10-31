<?php $title = 'Mis Solicitudes'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Solicitudes de Oficios</h3>
        <a href="/oficios/provider/dashboard" class="btn btn-outline-secondary mb-3">Volver al panel</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Disponibles para aceptar</h5>
            </div>
            <div class="card-body">
                <?php if (empty($available)): ?>
                    <p class="text-muted">No hay solicitudes disponibles.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Título</th><th>Oficio</th><th>Ubicación</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($available as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->title) ?></td>
                                    <td><?= esc($r->trade_name) ?></td>
                                    <td class="ficticio small"><?= esc($r->location) ?></td>
                                    <td><a href="/oficios/provider/request/<?= (int) $r->id ?>/respond" class="btn btn-sm btn-success">Aceptar/Rechazar</a></td>
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
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Asignadas a mí</h5>
            </div>
            <div class="card-body">
                <?php if (empty($assigned)): ?>
                    <p class="text-muted">No tiene solicitudes asignadas.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>#</th><th>Cliente</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead>
                            <tbody>
                                <?php foreach ($assigned as $r): ?>
                                <tr>
                                    <td><?= $r->id ?></td>
                                    <td><?= esc($r->client_name) ?></td>
                                    <td><span class="badge bg-<?= status_badge_class($r->status) ?>"><?= esc($r->status) ?></span></td>
                                    <td><?= esc($r->created_at) ?></td>
                                    <td>
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

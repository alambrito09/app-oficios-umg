<?php $title = 'Reportes'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Reportes del Sistema</h3>
        <a href="/oficios/admin/dashboard" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Solicitudes por Estado</h5>
            </div>
            <div class="card-body">
                <?php if (empty($requests_by_status)): ?>
                    <p class="text-muted">Sin datos.</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead><tr><th>Estado</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($requests_by_status as $r): ?>
                            <tr><td><?= esc($r->status) ?></td><td><?= $r->total ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Reclamos por Estado</h5>
            </div>
            <div class="card-body">
                <?php if (empty($complaints_by_status)): ?>
                    <p class="text-muted">Sin datos.</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead><tr><th>Estado</th><th>Total</th></tr></thead>
                        <tbody>
                            <?php foreach ($complaints_by_status as $c): ?>
                            <tr><td><?= esc($c->status) ?></td><td><?= $c->total ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Top Proveedores</h5>
            </div>
            <div class="card-body">
                <?php if (empty($top_providers)): ?>
                    <p class="text-muted">Sin datos.</p>
                <?php else: ?>
                    <table class="table table-sm">
                        <thead><tr><th>Proveedor ID</th><th>Servicios</th></tr></thead>
                        <tbody>
                            <?php foreach ($top_providers as $p): ?>
                            <tr><td><?= $p->provider_id ?></td><td><?= $p->total_services ?></td></tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info small mt-4">
    <span class="simulated-badge">SIMULADO</span> Indicadores disponibles: solicitudes atendidas, tiempo de aceptación, cancelaciones, reclamos. Fórmulas documentadas, no resultados inventados.
</div>

<?php $this->load->view('layouts/footer'); ?>

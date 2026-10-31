<?php $title = 'Panel Administrador'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Panel de Administración</h3>
        <p class="text-muted">Gestión de cuentas, categorías, reportes y reclamos.</p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($total_clients) ?></h5>
                <p class="card-text">Clientes registrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><?= count($total_providers) ?></h5>
                <p class="card-text">Prestadores registrados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title"><?= $pending_requests ?></h5>
                <p class="card-text">Solicitudes pendientes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title"><?= $open_complaints ?></h5>
                <p class="card-text">Reclamos abiertos</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Acciones rápidas</h5>
            </div>
            <div class="card-body">
                <a href="/oficios/admin/users" class="btn btn-outline-primary w-100 mb-2">Gestionar Usuarios</a>
                <a href="/oficios/admin/trades" class="btn btn-outline-primary w-100 mb-2">Gestionar Oficios</a>
                <a href="/oficios/admin/complaints" class="btn btn-outline-primary w-100 mb-2">Gestionar Reclamos</a>
                <a href="/oficios/admin/reports" class="btn btn-outline-primary w-100">Ver Reportes</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Estado del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info small">
                    <span class="simulated-badge">SIMULADO</span> Este sistema es un prototipo académico. Los pagos, notificaciones y mapas están simulados y declarados como tales.
                </div>
                <p class="text-muted small">Fecha del sistema: <?= date('d/m/Y H:i') ?></p>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

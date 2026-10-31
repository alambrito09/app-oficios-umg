<?php $title = 'Mis Servicios'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Mis Servicios Contratados</h3>
        <a href="/oficios/provider/dashboard" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<?php if (empty($services)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center py-4">
            <p class="text-muted">No tiene servicios contratados todavía.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($services as $s): ?>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Servicio #<?= $s->id ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>Cliente:</strong> <?= esc($s->client_name) ?></p>
                    <p><strong>Solicitud:</strong> <?= esc($s->request_title) ?></p>
                    <p><strong>Estado:</strong> <span class="badge bg-<?= status_badge_class($s->status) ?>"><?= esc($s->status) ?></span></p>
                    <p><strong>Total:</strong> Q<?= number_format($s->total_amount, 2) ?></p>
                    <?php if ($s->status === 'programado'): ?>
                        <a href="/oficios/provider/service/<?= $s->id ?>/progress" class="btn btn-primary">Iniciar / Registrar avance</a>
                    <?php elseif ($s->status === 'en_ejecucion'): ?>
                        <a href="/oficios/provider/service/<?= $s->id ?>/progress" class="btn btn-primary">Actualizar avance</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>

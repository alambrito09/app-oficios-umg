<?php $title = 'Reclamos Recibidos'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Reclamos Recibidos</h3>
        <a href="/oficios/provider/dashboard" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<?php if (empty($complaints)): ?>
    <div class="card shadow-sm">
        <div class="card-body text-center py-4">
            <p class="text-muted">No tiene reclamos recibidos.</p>
        </div>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>#</th><th>Título</th><th>Cliente</th><th>Estado</th><th>Fecha</th></tr></thead>
            <tbody>
                <?php foreach ($complaints as $c): ?>
                <tr>
                    <td><?= $c->id ?></td>
                    <td><?= esc($c->title) ?></td>
                    <td><?= esc($c->client_name) ?></td>
                    <td><span class="badge bg-<?= status_badge_class($c->status) ?>"><?= esc($c->status) ?></span></td>
                    <td><?= esc($c->created_at) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>

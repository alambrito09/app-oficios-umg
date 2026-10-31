<?php $title = 'Oficios'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Gestión de Oficios</h3>
        <a href="/oficios/admin/dashboard" class="btn btn-outline-secondary">Volver</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Oficio</th><th>Descripción</th><th>Estado</th></tr></thead>
                <tbody>
                    <?php foreach ($trades as $t): ?>
                    <tr>
                        <td><?= $t->id ?></td>
                        <td><?= esc($t->name) ?></td>
                        <td><?= esc($t->description ?: '-') ?></td>
                        <td><span class="badge bg-<?= $t->is_active ? 'success' : 'secondary' ?>"><?= $t->is_active ? 'Activo' : 'Inactivo' ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="/oficios/admin/dashboard" class="btn btn-outline-secondary">Volver al panel</a>
</div>

<?php $this->load->view('layouts/footer'); ?>

<?php $title = 'Reclamos'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Gestión de Reclamos</h3>
        <?php if ((int) $this->session->userdata('role_id') === 3): ?>
            <a href="/oficios/admin/dashboard" class="btn btn-outline-secondary">Volver al panel</a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Título</th><th>Cliente</th><th>Proveedor</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead>
                <tbody>
                    <?php foreach ($complaints as $c): ?>
                    <tr>
                        <td><?= $c->id ?></td>
                        <td><?= esc($c->title) ?></td>
                        <td><?= esc($c->client_name) ?></td>
                        <td><?= esc($c->provider_name ?: 'No asignado') ?></td>
                        <td><span class="badge bg-<?= status_badge_class($c->status) ?>"><?= esc($c->status) ?></span></td>
                        <td><?= esc($c->created_at) ?></td>
                        <td>
                            <?php if (in_array($this->session->userdata('role_id'), array(3,4))): ?>
                                <a href="/oficios/complaint/resolve/<?= $c->id ?>" class="btn btn-sm btn-outline-primary">Resolver</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

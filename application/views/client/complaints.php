<?php $title = 'Mis reclamos'; $this->load->view('layouts/header', array('title' => $title)); ?>

<h3>Mis reclamos</h3>
<?php if (empty($complaints)): ?>
    <div class="alert alert-info">No tiene reclamos registrados.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Solicitud</th><th>Título</th><th>Estado</th><th>Fecha</th></tr></thead>
            <tbody>
                <?php foreach ($complaints as $complaint): ?>
                    <tr>
                        <td>#<?= (int) $complaint->request_id ?></td>
                        <td><?= esc($complaint->title) ?></td>
                        <td><span class="badge bg-<?= status_badge_class($complaint->status) ?>"><?= esc($complaint->status) ?></span></td>
                        <td><?= esc($complaint->created_at) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>

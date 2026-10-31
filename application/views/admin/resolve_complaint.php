<?php $title = 'Resolver Reclamo #' . $complaint->id; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Resolver Reclamo</h4>
            </div>
            <div class="card-body">
                <p><strong>Título:</strong> <?= esc($complaint->title) ?></p>
                <p><strong>Descripción:</strong> <?= nl2br(esc($complaint->description)) ?></p>
                <p><strong>Cliente:</strong> <?= esc($complaint->client_name) ?></p>
                <p><strong>Estado actual:</strong> <?= esc($complaint->status) ?></p>
                <form action="/oficios/complaint/resolve/<?= $complaint->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Nuevo estado</label>
                        <select name="status" class="form-select" required>
                            <option value="en_proceso">En proceso</option>
                            <option value="resuelto">Resuelto</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Resolución</label>
                        <textarea name="resolution" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Guardar Resolución</button>
                </form>
                <a href="/oficios/admin/complaints" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

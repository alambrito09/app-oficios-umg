<?php $title = 'Nuevo Reclamo'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="bi bi-chat-dots"></i> Presentar Reclamo</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Solicitud:</strong> #<?= $request->id ?> - <?= esc($request->title) ?>
                </div>
                <?php echo validation_errors('<div class="alert alert-danger small">', '</div>'); ?>
                <form action="/oficios/complaint/create/<?= $request->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción del problema</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Evidencia (ruta/descripción)</label>
                        <input type="text" name="evidence" class="form-control" placeholder="Ej. Foto del problema">
                    </div>
                    <button type="submit" class="btn btn-danger w-100">Enviar Reclamo</button>
                </form>
                <a href="/oficios/client/history" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

<?php $title = 'Actualizar Avance'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Registrar Avance — Servicio #<?= $service->id ?></h4>
            </div>
            <div class="card-body">
                <p><strong>Cliente:</strong> <?= esc($service->client_name) ?></p>
                <p><strong>Estado actual:</strong> <?= esc($service->status) ?></p>
                <?php if ($service->progress_notes): ?>
                    <div class="alert alert-info"><strong>Notas previas:</strong><br><?= nl2br(esc($service->progress_notes)) ?></div>
                <?php endif; ?>
                <form action="/oficios/provider/service/<?= (int) $service->id ?>/progress" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Notas de avance</label>
                        <textarea name="notes" class="form-control" rows="4" required placeholder="Describa el avance del trabajo..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Estado del servicio</label>
                        <select name="status" class="form-select" required>
                            <?php if ($service->status === 'programado'): ?>
                                <option value="en_ejecucion">Iniciar servicio</option>
                            <?php else: ?>
                                <option value="en_ejecucion">Continúa en ejecución</option>
                                <option value="completado">Marcar como completado</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Guardar avance</button>
                </form>
                <a href="/oficios/provider/services" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

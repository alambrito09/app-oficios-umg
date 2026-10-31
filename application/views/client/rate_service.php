<?php $title = 'Calificar Servicio'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-star"></i> Calificar Servicio #<?= $service->id ?></h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Regla de negocio:</strong> Solo puede calificar servicios que hayan sido completados.
                </div>
                <form action="/oficios/rating/create/<?= $service->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Puntuación (1 a 5)</label>
                        <select name="score" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="5">5 - Excelente</option>
                            <option value="4">4 - Muy Bueno</option>
                            <option value="3">3 - Bueno</option>
                            <option value="2">2 - Aceptable</option>
                            <option value="1">1 - Deficiente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comentario</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Opine sobre la atención recibida..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Enviar Calificación</button>
                </form>
                <a href="/oficios/client/history" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

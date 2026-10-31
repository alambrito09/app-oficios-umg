<?php $title = 'Nueva Cotización'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="bi bi-file-earmark-text"></i> Preparar Cotización</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Solicitud #<?= $request->id ?></strong>: <?= esc($request->title) ?><br>
                    <strong>Cliente:</strong> <?= esc($request->client_name) ?><br>
                    <strong>Oficio:</strong> <?= esc($request->trade_name) ?><br>
                    <strong>Ubicación:</strong> <span class="ficticio"><?= esc($request->location) ?></span> <span class="simulated-badge">SIMULADO</span>
                </div>
                <div class="alert alert-warning small">
                    <strong>Regla de negocio:</strong> La cotización separa mano de obra, materiales y cargos. Un solo proveedor por solicitud (doble aceptación).
                </div>
                <?php echo validation_errors('<div class="alert alert-danger small">', '</div>'); ?>
                <form action="/oficios/quote/create/<?= $request->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mano de obra (Q)</label>
                            <input type="number" name="labor_cost" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Materiales (Q)</label>
                            <input type="number" name="materials_cost" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cargos adicionales (Q)</label>
                            <input type="number" name="other_charges" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Costo de visita (Q)</label>
                            <input type="number" name="visit_cost" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Válido hasta (fecha)</label>
                        <input type="datetime-local" name="valid_until" class="form-control">
                    </div>
                    <div class="alert alert-info small">
                        <strong>Total calculado:</strong> mano de obra + materiales + cargos + visita = total
                    </div>
                    <button type="submit" class="btn btn-success w-100">Enviar Cotización</button>
                    <a href="/oficios/provider/dashboard" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

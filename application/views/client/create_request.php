<?php $title = 'Crear Solicitud'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Solicitar un servicio</h4>
            </div>
            <div class="card-body">
                <?= validation_errors('<div class="alert alert-danger small">', '</div>') ?>
                <form action="/oficios/client/request/create" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Oficio</label>
                        <select name="trade_id" class="form-select" required>
                            <option value="">Seleccione un oficio</option>
                            <?php foreach ($trades as $trade): ?>
                                <option value="<?= (int) $trade->id ?>" <?= $selected_trade_id === (int) $trade->id ? 'selected' : '' ?>>
                                    <?= esc($trade->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" maxlength="200" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ubicación</label>
                        <input type="text" name="location" class="form-control" maxlength="255" required>
                        <div class="form-text">Ubicación ingresada por el cliente; no usa mapas en tiempo real.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modalidad</label>
                        <select name="modality" class="form-select" required>
                            <option value="inmediata">Inmediata</option>
                            <option value="programada">Programada</option>
                            <option value="diagnostico">Diagnóstico</option>
                            <option value="por_presupuesto">Por presupuesto</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha programada (opcional)</label>
                        <input type="datetime-local" name="scheduled_date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Crear solicitud</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

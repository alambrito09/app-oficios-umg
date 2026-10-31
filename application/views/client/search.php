<?php $title = 'Buscar Servicios'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-search"></i> Buscar Prestadores</h4>
            </div>
            <div class="card-body">
                <form action="/oficios/client/results" method="get">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Oficio</label>
                            <select name="trade_id" class="form-select" required>
                                <option value="">Seleccione un oficio</option>
                                <?php foreach ($trades as $t): ?>
                                    <option value="<?= $t->id ?>"><?= esc($t->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Zona / Ubicación</label>
                            <input type="text" name="location" class="form-control" placeholder="Ej. Zona 1, Mixco">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

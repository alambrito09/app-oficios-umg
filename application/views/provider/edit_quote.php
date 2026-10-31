<?php $title = 'Editar Cotización #' . $quote->id; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Editar Cotización</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Estado actual:</strong> <?= esc($quote->status) ?><br>
                    <strong>Total actual:</strong> Q<?= number_format($quote->total_amount, 2) ?>
                </div>
                <?php echo validation_errors('<div class="alert alert-danger small">', '</div>'); ?>
                <form action="/oficios/quote/edit/<?= $quote->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mano de obra (Q)</label>
                            <input type="number" name="labor_cost" class="form-control" step="0.01" min="0" value="<?= $quote->labor_cost ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Materiales (Q)</label>
                            <input type="number" name="materials_cost" class="form-control" step="0.01" min="0" value="<?= $quote->materials_cost ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cargos adicionales (Q)</label>
                            <input type="number" name="other_charges" class="form-control" step="0.01" min="0" value="<?= $quote->other_charges ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Costo de visita (Q)</label>
                            <input type="number" name="visit_cost" class="form-control" step="0.01" min="0" value="<?= $quote->visit_cost ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Detalle actual</label>
                        <table class="table table-sm table-bordered">
                            <thead><tr><th>Tipo</th><th>Descripción</th><th>Cantidad</th><th>Unitario</th><th>Total</th></tr></thead>
                            <tbody>
                                <?php foreach ($details as $d): ?>
                                <tr>
                                    <td><?= esc($d->item_type) ?></td>
                                    <td><?= esc($d->description) ?></td>
                                    <td><?= $d->quantity ?></td>
                                    <td>Q<?= number_format($d->unit_price, 2) ?></td>
                                    <td>Q<?= number_format($d->total_price, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Actualizar Cotización</button>
                    <a href="/oficios/provider/dashboard" class="btn btn-outline-secondary w-100 mt-2">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

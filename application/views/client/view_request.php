<?php $title = 'Solicitud #' . $request->id; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row">
    <div class="col-12">
        <a href="/oficios/client/history" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver al historial</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?= esc($request->title) ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Oficio:</strong> <?= esc($request->trade_name) ?></p>
                        <p><strong>Especialidad:</strong> <?= esc($request->specialty_name ?: 'No especificada') ?></p>
                        <p><strong>Modalidad:</strong> <?= esc($request->service_modality) ?></p>
                        <p><strong>Ubicación:</strong> <span class="ficticio"><?= esc($request->location) ?></span> <span class="simulated-badge">SIMULADO</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Estado:</strong> <span class="badge bg-<?= status_badge_class($request->status) ?>"><?= esc($request->status) ?></span></p>
                        <p><strong>Fecha:</strong> <?= esc($request->created_at) ?></p>
                        <?php if ($request->assigned_provider_id): ?>
                            <p><strong>Proveedor asignado:</strong> ID <?= $request->assigned_provider_id ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <hr>
                <p><strong>Descripción:</strong><br><?= nl2br(esc($request->description)) ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Cotización</h5>
            </div>
            <div class="card-body">
                <?php if ($quote): ?>
                    <?php if ($quote->status === 'enviado'): ?>
                        <p><strong>Total:</strong> Q<?= number_format($quote->total_amount, 2) ?></p>
                        <p><strong>Mano de obra:</strong> Q<?= number_format($quote->labor_cost, 2) ?></p>
                        <p><strong>Materiales:</strong> Q<?= number_format($quote->materials_cost, 2) ?></p>
                        <p><strong>Otros cargos:</strong> Q<?= number_format($quote->other_charges, 2) ?></p>
                        <p><strong>Costo visita:</strong> Q<?= number_format($quote->visit_cost, 2) ?></p>
                        <hr>
                        <form action="/oficios/quote/approve/<?= (int) $quote->id ?>" method="post">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100">Aprobar cotización</button>
                        </form>
                        <form action="/oficios/quote/reject/<?= (int) $quote->id ?>" method="post" class="mt-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger w-100">Rechazar</button>
                        </form>
                    <?php elseif ($quote->status === 'aprobado'): ?>
                        <div class="alert alert-success">Cotización aprobada. Servicio creado.</div>
                    <?php elseif ($quote->status === 'rechazado'): ?>
                        <div class="alert alert-danger">Cotización rechazada.</div>
                    <?php else: ?>
                        <div class="alert alert-info">Estado: <?= esc($quote->status) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">No hay cotización para esta solicitud todavía.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

<?php $title = 'Servicio #' . $service->id; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="page-heading">
    <div>
        <span class="eyebrow">SERVICIO #<?= (int) $service->id ?></span>
        <h1>Detalle del servicio</h1>
        <p><?= esc($service->request_title) ?></p>
    </div>
    <a href="<?= (int) $this->session->userdata('role_id') === 1 ? '/oficios/client/history' : '/oficios/provider/services' ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0">Seguimiento</h5>
            </div>
            <div class="card-body">
                <p><strong>Solicitud:</strong> <?= esc($service->request_title) ?></p>
                <p><strong>Estado:</strong> <span class="badge bg-<?= status_badge_class($service->status) ?>"><?= esc($service->status) ?></span></p>
                <?php if ($service->started_at): ?><p><strong>Iniciado:</strong> <?= esc($service->started_at) ?></p><?php endif; ?>
                <?php if ($service->completed_at): ?><p><strong>Completado:</strong> <?= esc($service->completed_at) ?></p><?php endif; ?>
                <?php if ($service->progress_notes): ?>
                    <hr>
                    <p><strong>Notas de avance:</strong><br><?= nl2br(esc($service->progress_notes)) ?></p>
                <?php endif; ?>
                <?php if ((int) $this->session->userdata('role_id') === 1 && $service->status === 'completado'): ?>
                    <form action="/oficios/service/close/<?= (int) $service->id ?>" method="post">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-danger">Cerrar servicio</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Cotización Aprobada</h5>
            </div>
            <div class="card-body">
                <?php if ($quote): ?>
                    <p><strong>Total:</strong> Q<?= number_format($quote->total_amount, 2) ?></p>
                    <p><strong>Mano de obra:</strong> Q<?= number_format($quote->labor_cost, 2) ?></p>
                    <p><strong>Materiales:</strong> Q<?= number_format($quote->materials_cost, 2) ?></p>
                    <p><strong>Otros cargos:</strong> Q<?= number_format($quote->other_charges, 2) ?></p>
                    <p><strong>Costo visita:</strong> Q<?= number_format($quote->visit_cost, 2) ?></p>
                    <hr>
                    <?php if ($payment && $payment->status === 'completado'): ?>
                            <div class="alert alert-success">
                                <strong><i class="bi bi-check-circle me-1"></i> Pago simulado registrado</strong>
                                <div class="small mt-1">Método: <?= esc(ucfirst(str_replace('_', ' ', $payment->payment_method))) ?></div>
                                <div class="small">Referencia de demostración: <?= esc($payment->transaction_id) ?></div>
                                <?php if ($payment->paid_at): ?><div class="small">Fecha: <?= esc($payment->paid_at) ?></div><?php endif; ?>
                            </div>
                    <?php else: ?>
                            <div class="simulation-notice mb-3">
                                <span class="simulated-badge">SIMULADO</span>
                                <p class="mb-0 mt-2">El pago se registra aquí como demostración. No hay cobro real ni pasarela bancaria.</p>
                            </div>
                            <?php if ((int) $this->session->userdata('role_id') === 1): ?>
                                <a href="/oficios/client/payment/<?= (int) $service->id ?>" class="btn btn-success w-100">
                                    <i class="bi bi-credit-card me-1"></i> Ir a pagar
                                </a>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Pago pendiente</span>
                            <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

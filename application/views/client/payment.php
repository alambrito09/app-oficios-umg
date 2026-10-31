<?php $title = 'Pago simulado'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="page-heading">
    <div>
        <span class="eyebrow">CLIENTE · SERVICIO #<?= (int) $service->id ?></span>
        <h1>Completar pago</h1>
        <p>Selecciona un método de demostración para registrar el pago en el prototipo.</p>
    </div>
    <a href="/oficios/service/view/<?= (int) $service->id ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Volver al servicio
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="simulation-notice mb-4">
                    <span class="simulated-badge">SIMULADO</span>
                    <strong>Este flujo no cobra dinero real.</strong>
                    <p class="mb-0 mt-1">No ingreses números de tarjeta, claves, cuentas bancarias ni otros datos financieros.</p>
                </div>

                <?= form_error('payment_method', '<div class="alert alert-danger">', '</div>') ?>
                <form action="/oficios/client/payment/<?= (int) $service->id ?>" method="post">
                    <?= csrf_field() ?>
                    <fieldset>
                        <legend class="h5 mb-3">Método de demostración</legend>
                        <?php foreach ($payment_methods as $method => $label): ?>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="<?= esc($method) ?>"
                                    <?= set_radio('payment_method', $method, $method === 'efectivo_simulado') ?>>
                                <span>
                                    <strong><?= esc($label) ?></strong>
                                    <small>Solo registra una transacción ficticia para la demostración.</small>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                    <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">
                        Registrar pago simulado
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card order-summary">
            <div class="card-body p-4">
                <span class="eyebrow">RESUMEN</span>
                <h2 class="h5 mt-2"><?= esc($service->request_title) ?></h2>
                <p class="text-muted">Servicio #<?= (int) $service->id ?></p>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total de la cotización</span>
                    <strong class="price">Q<?= number_format((float) $service->total_amount, 2) ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

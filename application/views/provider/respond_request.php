<?php $title = 'Responder Solicitud #' . $request->id; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">¿Acepta esta solicitud?</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Título:</strong> <?= esc($request->title) ?><br>
                    <strong>Oficio:</strong> <?= esc($request->trade_name) ?><br>
                    <strong>Ubicación:</strong> <span class="ficticio"><?= esc($request->location) ?></span> <span class="simulated-badge">SIMULADO</span><br>
                    <strong>Modalidad:</strong> <?= esc($request->service_modality) ?><br>
                    <strong>Descripción:</strong> <?= nl2br(esc($request->description)) ?>
                </div>
                <div class="alert alert-warning small">
                    <strong>Regla de doble aceptación:</strong> Solo un proveedor puede aceptar cada solicitud. Si otro proveedor ya la aceptó, no podrá hacerlo.
                </div>
                <form action="/oficios/service_request/respond/<?= $request->id ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" name="action" value="accept" class="btn btn-success w-100 mb-2">Aceptar</button>
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger w-100">Rechazar</button>
                </form>
                <a href="/oficios/provider/requests" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

<?php $title = 'Restablecer Contraseña'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-key"></i> Nueva Contraseña</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    El enlace es temporal y de un solo uso. Si no solicitó este cambio, cierre esta página.
                </div>
                <form action="/oficios/auth/reset" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" id="reset-token" value="<?= esc($token) ?>">
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Restablecer contraseña</button>
                </form>
                <a href="/oficios/auth/login" class="btn btn-outline-secondary w-100 mt-2">Volver al login</a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var token = window.location.hash.substring(1);
    if (token) {
        document.getElementById('reset-token').value = token;
        window.history.replaceState(null, '', window.location.pathname);
    }
})();
</script>

<?php $this->load->view('layouts/footer'); ?>

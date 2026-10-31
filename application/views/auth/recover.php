<?php $title = 'Recuperar Cuenta'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-key"></i> Recuperar Cuenta</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Para completar la recuperación, el administrador debe configurar un servidor SMTP. El enlace se envía al correo de la cuenta y nunca se muestra en esta pantalla.
                </div>
                <?php echo validation_errors('<div class="alert alert-danger small">', '</div>'); ?>
                <form action="/oficios/auth/recover" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Solicitar instrucciones</button>
                </form>
                <div class="mt-3 text-center small">
                    <a href="/oficios/auth/login">Volver al inicio de sesión</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

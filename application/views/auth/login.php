<?php $title = 'Iniciar sesión'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión</h4>
            </div>
            <div class="card-body">
                <?php echo validation_errors('<div class="alert alert-danger small">', '</div>'); ?>
                <form action="/oficios/auth/login" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Usuario</label>
                        <input type="text" name="username" class="form-control" placeholder="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
                <div class="mt-3 text-center small">
                    <p>¿No tiene cuenta? <a href="/oficios/auth/register">Regístrese aquí</a></p>
                    <p>¿Olvidó la contraseña? <a href="/oficios/auth/recover">Recuperar cuenta</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

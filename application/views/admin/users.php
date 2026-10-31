<?php $title = 'Usuarios'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="page-heading">
    <div>
        <span class="eyebrow">ADMINISTRACIÓN</span>
        <h1>Usuarios</h1>
        <p>Crear cuentas y administrar su acceso sin perder el historial.</p>
    </div>
    <a href="/oficios/admin/dashboard" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Panel</a>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Crear usuario</h5></div>
            <div class="card-body">
                <div class="alert alert-info small">Se pueden crear cuentas de cliente o prestador. La contraseña temporal debe compartirse de forma segura.</div>
                <?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
                <form action="/oficios/admin/users/create" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="full_name">Nombre completo</label>
                        <input class="form-control" id="full_name" name="full_name" value="<?= set_value('full_name') ?>" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="username">Usuario</label>
                        <input class="form-control" id="username" name="username" value="<?= set_value('username') ?>" required maxlength="80" autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo</label>
                        <input class="form-control" id="email" name="email" type="email" value="<?= set_value('email') ?>" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">Teléfono <span class="text-muted">(opcional)</span></label>
                        <input class="form-control" id="phone" name="phone" value="<?= set_value('phone') ?>" maxlength="30">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="role_id">Rol</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Selecciona un rol</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= (int) $role->id ?>" <?= set_select('role_id', $role->id) ?>><?= esc(ucfirst($role->name)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña temporal</label>
                        <input class="form-control" id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
                        <div class="form-text">Mínimo 8 caracteres. No se guarda ni se muestra nuevamente en esta pantalla.</div>
                    </div>
                    <button class="btn btn-primary w-100" type="submit"><i class="bi bi-person-check me-1"></i> Crear cuenta activa</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cuentas registradas</h5>
                <span class="badge text-bg-light"><?= count($users) ?> usuarios</span>
            </div>
            <div class="card-body">
                <div class="alert alert-warning small">
                    Desactivar equivale a retirar el acceso: se conserva el historial y puede reactivarse. No se borran físicamente cuentas con datos relacionados.
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Usuario</th><th>Nombre / correo</th><th>Rol</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <?php
                                $status_class = $u->status === 'activo' ? 'success' : ($u->status === 'suspendido' ? 'warning text-dark' : 'secondary');
                                $is_self = (int) $u->id === (int) $this->session->userdata('user_id');
                            ?>
                            <tr>
                                <td><strong><?= esc($u->username) ?></strong><small class="d-block text-muted">#<?= (int) $u->id ?></small></td>
                                <td><?= esc($u->full_name) ?><small class="d-block text-muted"><?= esc($u->email) ?></small></td>
                                <td><?= esc(ucfirst($u->role_name)) ?></td>
                                <td><span class="badge bg-<?= $status_class ?>"><?= esc(ucfirst($u->status)) ?></span></td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php if ((int) $u->role_id === 2): ?>
                                            <a href="/oficios/admin/verify/<?= (int) $u->id ?>" class="btn btn-sm btn-outline-primary">Verificar</a>
                                        <?php endif; ?>
                                        <?php if (!$is_self): ?>
                                            <form action="/oficios/admin/users/<?= (int) $u->id ?>/status" method="post" class="d-flex gap-1">
                                                <?= csrf_field() ?>
                                                <select name="status" class="form-select form-select-sm" aria-label="Estado de <?= esc($u->username) ?>">
                                                    <?php foreach (array('activo' => 'Activo', 'inactivo' => 'Inactivo', 'suspendido' => 'Suspendido') as $value => $label): ?>
                                                        <option value="<?= $value ?>" <?= $u->status === $value ? 'selected' : '' ?>><?= $label ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Guardar</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="small text-muted">Cuenta actual</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Todavía no hay usuarios registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

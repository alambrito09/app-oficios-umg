<?php $title = 'Verificar Proveedor'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Verificación de Proveedor</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning small">
                    <strong>Regla de negocio:</strong> La etiqueta "verificado" indica qué se revisó (datos, experiencia, identidad). No es garantía absoluta de calidad o conducta.
                </div>
                <p><strong>Usuario:</strong> <?= esc($user->full_name) ?> (<?= esc($user->username) ?>)</p>
                <?php if ($profile): ?>
                    <p><strong>Cobertura:</strong> <span class="ficticio"><?= esc($profile->coverage_area) ?></span> <span class="simulated-badge">SIMULADO</span></p>
                    <p><strong>Estado actual:</strong> <?= $profile->verification_status ?></p>
                    <p><strong>Notas previas:</strong> <?= esc($profile->verification_notes ?: 'Ninguna') ?></p>
                <?php endif; ?>
                <form action="/oficios/admin/verify/<?= $user->id ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Estado de verificación</label>
                        <select name="verified" class="form-select" required>
                            <option value="1">Verificado</option>
                            <option value="0">Rechazado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas de verificación (quién revisó, qué datos, fecha)</label>
                        <textarea name="notes" class="form-control" rows="3" required placeholder="Ej. Revisado por admin: cédula y experiencia verificadas el 2026-10-28"></textarea>
                    </div>
                    <button type="submit" class="btn btn-info w-100">Guardar Verificación</button>
                </form>
                <a href="/oficios/admin/users" class="btn btn-outline-secondary w-100 mt-2">Volver</a>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

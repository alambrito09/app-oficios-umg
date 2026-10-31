<?php $title = 'Perfil del Proveedor'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-4">
    <div class="col-12">
        <a href="/oficios/client/search" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver a resultados</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-light">
                <h4 class="mb-0"><?= esc($provider->full_name) ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Teléfono:</strong> <?= esc($provider->phone ?: 'No disponible') ?></p>
                        <p><strong>Correo:</strong> <?= esc($provider->email) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Experiencia:</strong> <?= esc($profile->experience_years ?: 0) ?> años</p>
                        <p><strong>Cobertura:</strong> <span class="ficticio"><?= esc($profile->coverage_area ?: 'No especificada') ?></span> <span class="simulated-badge">SIMULADO</span></p>
                        <?php if ($profile->is_verified): ?>
                            <span class="badge bg-success">Verificado</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">No verificado</span>
                        <?php endif; ?>
                    </div>
                </div>
                <hr>
                <h6>Oficios:</h6>
                <?php if (!empty($trades)): ?>
                    <?php foreach ($trades as $t): ?>
                        <span class="badge bg-secondary"><?= esc($t->name) . ($t->is_primary ? ' (principal)' : '') ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-muted">No especificados</span>
                <?php endif; ?>
                <hr>
                <h6>Biografía:</h6>
                <p><?= esc($profile->bio ?: 'Sin descripción') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Calificaciones</h5>
            </div>
            <div class="card-body">
                <?php if (empty($ratings)): ?>
                    <p class="text-muted">Sin calificaciones todavía.</p>
                <?php else: ?>
                    <?php foreach ($ratings as $r): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><?= esc($r->score) ?> / 5</span>
                            <small class="text-muted"><?= esc($r->client_name ?: 'Cliente') ?></small>
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar bg-warning" style="width:<?= ($r->score/5)*100 ?>%"></div>
                        </div>
                        <p class="small mt-1"><?= esc($r->comment ?: '') ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

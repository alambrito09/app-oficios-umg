<?php $title = 'Resultados de Búsqueda'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row">
    <div class="col-12">
        <h3>Resultados</h3>
        <a href="/oficios/client/search" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Volver a buscar</a>
    </div>
</div>

<?php if (!isset($results) || empty($results)): ?>
    <div class="row">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-body text-center py-4">
                    <i class="bi bi-search" style="font-size:3rem; color:#ffc107;"></i>
                    <h4 class="mt-3">No se encontraron prestadores disponibles</h4>
                    <p class="text-muted">Intente con otro oficio o zona.</p>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($results as $r): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= esc($r->full_name) ?></h5>
                    <p class="text-muted"><i class="bi bi-tools"></i> <?= esc($r->trade_name) ?></p>
                    <p class="card-text small"><?= esc($r->bio ?: 'Sin descripción disponible.') ?></p>
                    <p><i class="bi bi-geo-alt"></i> <?= esc($r->coverage_area ?: 'Cobertura no especificada') ?></p>
                    <p><span class="badge bg-success">Disponible</span>
                        <?php if ($r->is_verified): ?><span class="badge bg-primary">Verificado</span><?php endif; ?>
                    </p>
                    <a href="/oficios/client/profile/<?= (int) $r->provider_id ?>" class="btn btn-outline-primary btn-sm">Ver perfil</a>
                    <a href="/oficios/client/request/create?trade_id=<?= (int) $r->trade_id ?>" class="btn btn-primary btn-sm">Crear solicitud</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>

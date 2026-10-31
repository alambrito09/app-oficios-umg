<?php $title = 'Mi Perfil'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row mb-3">
    <div class="col-12">
        <h3>Perfil de Prestador</h3>
        <a href="/oficios/auth/provider_profile_setup" class="btn btn-primary mb-3"><i class="bi bi-pencil"></i> Editar Perfil</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Información del Perfil</h5>
            </div>
            <div class="card-body">
                <?php if (!$profile): ?>
                    <div class="alert alert-warning">No tiene perfil de proveedor configurado. <a href="/oficios/auth/provider_profile_setup">Configure su perfil</a>.</div>
                <?php else: ?>
                    <p><strong>Experiencia:</strong> <?= esc($profile->experience_years) ?> años</p>
                    <p><strong>Cobertura:</strong> <span class="ficticio"><?= esc($profile->coverage_area) ?></span> <span class="simulated-badge">SIMULADO</span></p>
                    <p><strong>Disponibilidad:</strong> <span class="badge bg-<?= $profile->availability_status === 'disponible' ? 'success' : 'secondary' ?>"><?= esc($profile->availability_status) ?></span></p>
                    <?php if ($profile->is_verified): ?>
                        <span class="badge bg-success">Verificado</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Pendiente de verificación</span>
                    <?php endif; ?>
                    <p><strong>Notas de verificación:</strong> <?= esc($profile->verification_notes ?: 'N/A') ?></p>
                    <p class="text-muted small">La etiqueta "verificado" indica qué se revisó, no es garantía absoluta de calidad.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Mis Oficios</h5>
            </div>
            <div class="card-body">
                <?php if (empty($trades)): ?>
                    <p class="text-muted">No tiene oficios registrados.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($trades as $t): ?>
                        <li class="list-group-item"><?= esc($t->name) . ($t->is_primary ? ' <span class="badge bg-primary float-end">Principal</span>' : '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Disponibilidad Semanal</h5>
            </div>
            <div class="card-body">
                <?php $saved_days = array_map('intval', array_column($availabilities, 'day_of_week')); ?>
                <form action="/oficios/provider/set_availability" method="post" class="mb-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="days[]" value="">
                    <fieldset>
                        <legend class="form-label">Actualizar disponibilidad semanal</legend>
                        <?php foreach (array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado') as $day => $label): ?>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="days[]" value="<?= $day ?>"
                                    <?= in_array($day, $saved_days, TRUE) ? 'checked' : '' ?>>
                                <span class="form-check-label"><?= $label ?></span>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                    <div class="row mt-2">
                        <div class="col-sm-4">
                            <label class="form-label">Desde</label>
                            <input type="time" name="start_time" class="form-control" value="08:00" required>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">Hasta</label>
                            <input type="time" name="end_time" class="form-control" value="17:00" required>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Guardar horarios</button>
                        </div>
                    </div>
                </form>
                <?php if (empty($availabilities)): ?>
                    <div class="alert alert-info">No tiene disponibilidad configurada. Configure su perfil para establecer horarios.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Día</th><th>Horario</th><th>Disponible</th></tr></thead>
                            <tbody>
                                <?php
                                $dias = array('Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado');
                                foreach ($availabilities as $a): ?>
                                <tr>
                                    <td><?= $dias[$a->day_of_week] ?? $a->day_of_week ?></td>
                                    <td><?= esc($a->start_time) ?> - <?= esc($a->end_time) ?></td>
                                    <td><span class="badge bg-<?= $a->is_available ? 'success' : 'secondary' ?>"><?= $a->is_available ? 'Sí' : 'No' ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

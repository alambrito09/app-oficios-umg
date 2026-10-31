<?php $title = 'Configurar perfil'; $this->load->view('layouts/header', array('title' => $title)); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Perfil del prestador</h4>
            </div>
            <div class="card-body">
                <?= validation_errors('<div class="alert alert-danger small">', '</div>') ?>
                <form action="/oficios/auth/provider_profile_setup" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Años de experiencia</label>
                        <input type="number" min="0" name="experience_years" class="form-control"
                            value="<?= esc($profile ? $profile->experience_years : 0) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biografía</label>
                        <textarea name="bio" class="form-control" rows="4" required><?= esc($profile ? $profile->bio : '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Área de cobertura</label>
                        <input type="text" name="coverage_area" class="form-control" required
                            value="<?= esc($profile ? $profile->coverage_area : '') ?>">
                    </div>
                    <fieldset class="mb-3">
                        <legend class="form-label">Oficios que ofrece</legend>
                        <?php foreach ($trades as $trade): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="trade_ids[]"
                                    value="<?= (int) $trade->id ?>" id="trade-<?= (int) $trade->id ?>"
                                    <?= in_array((int) $trade->id, $selected_trade_ids, TRUE) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="trade-<?= (int) $trade->id ?>">
                                    <?= esc($trade->name) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                    <button type="submit" class="btn btn-primary w-100">Guardar perfil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>

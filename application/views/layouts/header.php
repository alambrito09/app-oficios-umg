<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'App Oficios' ?> - App Oficios Bajo Demanda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css?v=1.1.0') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
        <a class="navbar-brand" href="/oficios/"><i class="bi bi-tools"></i> App Oficios</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($this->session->userdata('logged_in')): ?>
                    <?php $role = $this->session->userdata('role_id'); ?>
                    <?php if ($role == 1): ?>
                        <li class="nav-item"><a class="nav-link" href="/oficios/client/dashboard"><i class="bi bi-house"></i> Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/client/search"><i class="bi bi-search"></i> Buscar Servicios</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/client/history"><i class="bi bi-clock-history"></i> Historial</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/client/complaints"><i class="bi bi-chat-dots"></i> Reclamos</a></li>
                    <?php elseif ($role == 2): ?>
                        <li class="nav-item"><a class="nav-link" href="/oficios/provider/dashboard"><i class="bi bi-house"></i> Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/provider/requests"><i class="bi bi-inboxes"></i> Solicitudes</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/provider/services"><i class="bi bi-briefcase"></i> Mis Servicios</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/provider/profile"><i class="bi bi-person"></i> Perfil</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/provider/complaints"><i class="bi bi-chat-dots"></i> Reclamos</a></li>
                    <?php elseif ($role == 3): ?>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/dashboard"><i class="bi bi-house"></i> Panel Admin</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/users"><i class="bi bi-people"></i> Usuarios</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/trades"><i class="bi bi-wrench-adjustable"></i> Oficios</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/complaints"><i class="bi bi-chat"></i> Reclamos</a></li>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/reports"><i class="bi bi-bar-chart"></i> Reportes</a></li>
                    <?php elseif ($role == 4): ?>
                        <li class="nav-item"><a class="nav-link" href="/oficios/admin/complaints"><i class="bi bi-chat"></i> Reclamos</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if ($this->session->userdata('logged_in')): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= esc($this->session->userdata('full_name')) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <form action="/oficios/auth/logout" method="post" class="px-3">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-link dropdown-item p-0">Cerrar sesión</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/oficios/auth/login"><i class="bi bi-box-arrow-in-right"></i> Iniciar sesión</a></li>
                    <li class="nav-item"><a class="nav-link" href="/oficios/auth/register"><i class="bi bi-person-plus"></i> Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container app-main py-4">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= esc($this->session->flashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= esc($this->session->flashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <?= esc($this->session->flashdata('info')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $body ?? '' ?>

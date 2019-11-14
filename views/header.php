<?php
$user = !is_null($session) && $session->get('auth.user') !== null ? $session->get('auth.user') : null;
$role =  !is_null($user) ? $user->getRole()->getTitle() : null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

</head>

<body>
    <div id="app">
        <div class="main-wrapper">
            <?php if (isset($user)) :  ?>
                <div class="navbar-bg"></div>
                <nav class="navbar navbar-expand-lg main-navbar">
                    <form class="form-inline mr-auto">
                        <ul class="navbar-nav mr-3">
                            <li>
                                <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                                    <i class="fas fa-bars"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none">
                                    <i class="fas fa-search"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="search-element">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search" data-width="250">
                            <button class="btn" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    <ul class="navbar-nav navbar-right">

                        <li class="dropdown">
                            <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                                <img alt="image" src="../../build/img/avatar/avatar-1.png" class="rounded-circle mr-1">
                                <div class="d-sm-none d-lg-inline-block"><?= is_null($user) == false ? $user->getFirstname() . " " . $user->getLastname() : ""; ?></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-divider"></div>
                                <form action="<?= $router->generateUri('login.out') ?>" method="POST">
                                    <button type="submit" class="dropdown-item has-icon text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout

                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="main-sidebar">
                    <aside id="sidebar-wrapper">
                        <div class="sidebar-brand">
                            <a href="index.html">Stisla </a>
                        </div>
                        <div class="sidebar-brand sidebar-brand-sm">
                            <a href="index.html">St</a>
                        </div>
                        <?php
                            //dd($current_menu);
                            ?>
                        <ul class="sidebar-menu">
                            <?php if (isset($role) && $role === "ROLE_ADMIN") :  ?>
                                <li class="<?= isset($current_menu) && $current_menu === 'service' ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('service.index') ?>">
                                        <i class="fas fa-th-large"></i>
                                        <span>Service</span>
                                    </a>
                                </li>

                                <li class="<?= isset($current_menu) && $current_menu === 'specialty' ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('specialty.index') ?>">
                                        <i class="fas fa-columns"></i>
                                        <span>Specialité</span>
                                    </a>
                                </li>
                                <li class="<?= isset($current_menu) && $current_menu === 'medecin' ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('medecin.index') ?>">
                                        <i class="far fa-user"></i>
                                        <span>Medecin</span>
                                    </a>
                                </li>
                                <li class="<?= isset($current_menu) && $current_menu === 'secretary' ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('secretary.index') ?>">
                                        <i class="far fa-user"></i>
                                        <span>Secretaire</span>
                                    </a>
                                </li>
                            <?php endif  ?>
                            <?php if (isset($role) &&  $role === "ROLE_ADMIN" || $role === "ROLE_SECRETARY") :  ?>
                                <li class="<?= isset($current_menu) && $current_menu === 'patient' ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('patient.index') ?>">
                                        <i class="fas fa-pencil-ruler"></i>
                                        <span>patient</span>
                                    </a>
                                </li>
                            <?php endif  ?>
                            <?php if (isset($role) &&  $role === "ROLE_ADMIN" || $role === "ROLE_SECRETARY" || $role === "ROLE_MEDECIN") :  ?>
                                <li class="<?= isset($current_menu) && $current_menu === 'rendezVous'  ? 'active' : ''; ?>">
                                    <a class="nav-link" href="<?= $router->generateUri('rendezVous.index') ?>">
                                        <i class="fas fa-pencil-ruler"></i>
                                        <span>rendez vous</span>
                                    </a>
                                </li>
                            <?php endif  ?>
                        </ul>
                    </aside>
                </div>
            <?php endif ?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
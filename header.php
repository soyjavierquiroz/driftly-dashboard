<?php
if (!defined('ABSPATH')) exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php bloginfo('name'); ?> – Backoffice Driftly</title>

    <?php
    // Carga directa del CSS principal del dashboard (forzado, sin depender de hooks)
    $dashboard_css_path = get_template_directory() . '/assets/css/dashboard.css';
    $dashboard_css_uri  = get_template_directory_uri() . '/assets/css/dashboard.css';

    $css_version = file_exists($dashboard_css_path) ? filemtime($dashboard_css_path) : time();
    ?>
    <link rel="stylesheet"
          href="<?php echo esc_url($dashboard_css_uri . '?v=' . $css_version); ?>" />

    <?php wp_head(); ?>
</head>
<body <?php body_class('d-body'); ?>>

<?php if (function_exists('wp_body_open')) wp_body_open(); ?>

<div class="d-app-shell">

    <!-- SIDEBAR -->
    <aside class="d-sidebar">
        <div class="d-sidebar__logo">
            <div class="d-sidebar__logo-mark"></div>
            <div class="d-sidebar__logo-text">
                <span class="d-sidebar__logo-title">Driftly</span>
                <span class="d-sidebar__logo-subtitle">Panel de Control</span>
            </div>
        </div>

        <nav class="d-sidebar__nav">
            <?php
            $role = function_exists('driftly_get_user_role') ? driftly_get_user_role() : '';
            $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            ?>

            <?php if ($role === 'vds' || $role === 'admin' || $role === '') : ?>
                <a href="<?php echo esc_url(home_url('/vds/dashboard')); ?>"
                   class="d-sidebar__nav-item <?php echo str_contains($current_path, 'vds/dashboard') ? 'is-active' : ''; ?>">
                    <span class="d-sidebar__nav-icon">🏠</span>
                    <span class="d-sidebar__nav-label">Dashboard</span>
                </a>

                <a href="<?php echo esc_url(home_url('/vds/catalogo')); ?>"
                   class="d-sidebar__nav-item <?php echo str_contains($current_path, 'vds/catalogo') ? 'is-active' : ''; ?>">
                    <span class="d-sidebar__nav-icon">📦</span>
                    <span class="d-sidebar__nav-label">Catálogo maestro</span>
                </a>

                <a href="<?php echo esc_url(home_url('/vds/mis-productos')); ?>"
                   class="d-sidebar__nav-item <?php echo str_contains($current_path, 'vds/mis-productos') ? 'is-active' : ''; ?>">
                    <span class="d-sidebar__nav-icon">🛒</span>
                    <span class="d-sidebar__nav-label">Mis productos</span>
                </a>
            <?php endif; ?>

            <?php if ($role === 'proveedor' || $role === 'admin') : ?>
                <div class="d-sidebar__section-title">Proveedor</div>

                <a href="<?php echo esc_url(home_url('/proveedor/dashboard')); ?>"
                   class="d-sidebar__nav-item <?php echo str_contains($current_path, 'proveedor/dashboard') ? 'is-active' : ''; ?>">
                    <span class="d-sidebar__nav-icon">📊</span>
                    <span class="d-sidebar__nav-label">Dashboard Proveedor</span>
                </a>

                <a href="<?php echo esc_url(home_url('/proveedor/productos')); ?>"
                   class="d-sidebar__nav-item <?php echo str_contains($current_path, 'proveedor/productos') ? 'is-active' : ''; ?>">
                    <span class="d-sidebar__nav-icon">📦</span>
                    <span class="d-sidebar__nav-label">Mis productos</span>
                </a>
            <?php endif; ?>

        </nav>
    </aside>

    <!-- LAYOUT PRINCIPAL -->
    <div class="d-main-shell">

        <!-- HEADER SUPERIOR -->
        <header class="d-header">
            <button class="d-header__menu-toggle js-d-header-menu-toggle" type="button">
                ☰
            </button>

            <div class="d-header__title">
                <span class="d-header__title-main">Backoffice Driftly</span>
                <span class="d-header__title-pill">MVP</span>
            </div>

            <div class="d-header__spacer"></div>

            <div class="d-header__user">
                <?php if (is_user_logged_in()) :
                    $current_user = wp_get_current_user();
                    $role_label = function_exists('driftly_get_role_label')
                        ? driftly_get_role_label()
                        : 'Usuario Driftly';
                    ?>
                    <div class="d-header__user-info">
                        <span class="d-header__user-name">
                            <?php echo esc_html($current_user->display_name ?: $current_user->user_login); ?>
                        </span>
                        <span class="d-header__user-role">
                            <?php echo esc_html($role_label); ?>
                        </span>
                    </div>
                    <a class="d-header__logout" href="<?php echo esc_url(wp_logout_url()); ?>">
                        Cerrar sesión
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="d-main">

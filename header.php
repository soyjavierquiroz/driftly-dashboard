<?php
if (!defined('ABSPATH')) exit;

// Detectar rol Driftly
$role = function_exists('driftly_get_user_role') ? driftly_get_user_role() : '';
$current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Helper para marcar menú activo
function driftly_is_active($segment, $current_path) {
    return str_contains($current_path, $segment) ? 'is-active' : '';
}

// Helper para crear ítem del menú
function driftly_menu_item($url, $icon, $label, $segment, $current_path) {
    return sprintf(
        '<a href="%s" class="d-sidebar__nav-item %s">
            <span class="d-sidebar__nav-icon">%s</span>
            <span class="d-sidebar__nav-label">%s</span>
        </a>',
        esc_url(home_url($url)),
        driftly_is_active($segment, $current_path),
        $icon,
        esc_html($label)
    );
}

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php bloginfo('name'); ?> – Backoffice</title>

    <?php
    // CSS principal directo
    $dashboard_css_path = get_template_directory() . '/assets/css/dashboard.css';
    $dashboard_css_uri  = get_template_directory_uri() . '/assets/css/dashboard.css';
    $css_version = file_exists($dashboard_css_path) ? filemtime($dashboard_css_path) : time();
    ?>
    <link rel="stylesheet" href="<?php echo esc_url($dashboard_css_uri . '?v=' . $css_version); ?>" />

    <?php wp_head(); ?>
</head>

<body <?php body_class('d-body'); ?>>
<?php if (function_exists('wp_body_open')) wp_body_open(); ?>

<div class="d-app-shell">

    <!-- SIDEBAR -->
    <aside class="d-sidebar">

        <div class="d-sidebar__logo" style="padding: 10px; text-align:center;">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="d-sidebar__logo-link">
                <img src="https://exitosos.com/wp-content/uploads/drenvex.png"
                     alt="Drenvex"
                     class="d-sidebar__logo-img"
                     style="max-width: 140px; height: auto; display:block; margin:0 auto;">
            </a>
        </div>

        <nav class="d-sidebar__nav">

            <?php if ($role === 'vds' || $role === 'admin') : ?>
                <div class="d-sidebar__section-title">Vendedor Digital</div>

                <?= driftly_menu_item('/vds/dashboard', '🏠', 'Dashboard', 'vds/dashboard', $current_path); ?>
                <?= driftly_menu_item('/vds/catalogo', '📦', 'Catálogo Maestro', 'vds/catalogo', $current_path); ?>
                <?= driftly_menu_item('/vds/mis-productos', '🛒', 'Mis productos', 'vds/mis-productos', $current_path); ?>
                <?= driftly_menu_item('/vds/configuracion', '🏪', 'Mi tienda', 'vds/configuracion', $current_path); ?>
                <?= driftly_menu_item('/vds/perfil', '👤', 'Mi perfil', 'vds/perfil', $current_path); ?>

                <!-- Academia externa -->
                <a href="https://edu.exitosos.com/"
                   class="d-sidebar__nav-item"
                   target="_blank"
                   rel="noopener">
                    <span class="d-sidebar__nav-icon">🎓</span>
                    <span class="d-sidebar__nav-label">Academia</span>
                </a>
            <?php endif; ?>

            <?php if ($role === 'proveedor' || $role === 'admin') : ?>
                <div class="d-sidebar__section-title">Proveedor</div>

                <?= driftly_menu_item('/proveedor/dashboard', '📊', 'Dashboard Proveedor', 'proveedor/dashboard', $current_path); ?>
                <?= driftly_menu_item('/proveedor/productos', '📦', 'Mis productos', 'proveedor/productos', $current_path); ?>
                <?= driftly_menu_item('/proveedor/configuracion', '⚙️', 'Configuración', 'proveedor/configuracion', $current_path); ?>
                <?= driftly_menu_item('/proveedor/perfil', '👤', 'Mi perfil', 'proveedor/perfil', $current_path); ?>
            <?php endif; ?>

        </nav>
    </aside>

    <!-- LAYOUT PRINCIPAL -->
    <div class="d-main-shell">

        <!-- HEADER SUPERIOR -->
        <header class="d-header">

            <button class="d-header__menu-toggle js-d-header-menu-toggle" type="button">☰</button>

            <div class="d-header__title">
                <span class="d-header__title-main">Backoffice</span>
                <span class="d-header__title-pill">MVP</span>
            </div>

            <div class="d-header__spacer"></div>

            <!-- USER + GAMIPRESS + LOGOUT -->
            <div class="d-header__user">

                <?php if (is_user_logged_in()) :
                    $current_user = wp_get_current_user();
                    $role_label = function_exists('driftly_get_role_label')
                                    ? driftly_get_role_label()
                                    : 'Usuario Driftly';
                ?>

                    <!-- Nombre + Rol -->
                    <div class="d-header__user-info">
                        <span class="d-header__user-name">
                            <?= esc_html($current_user->display_name ?: $current_user->user_login); ?>
                        </span>
                        <span class="d-header__user-role"><?= esc_html($role_label); ?></span>
                    </div>

                    <!-- GAMIPRESS Rank -->
                    <?php if ($role === 'vds') : ?>
                        <div class="d-header__rank">
                            <?= do_shortcode('[gamipress_user_rank type="vexer-rank" prev_rank="no" current_rank="yes" next_rank="no" title="no" link="no" thumbnail="yes" layout="none" align="center"]'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Logout -->
                    <a class="d-header__logout" href="<?= esc_url(wp_logout_url()); ?>">
                        Cerrar sesión
                    </a>

                <?php endif; ?>
            </div>

        </header>

        <main class="d-main">

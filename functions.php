<?php
/**
 * Funciones del tema Driftly Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Carga de CSS y JS del tema Driftly Dashboard
 */
function driftly_dashboard_enqueue_assets() {

    $theme      = wp_get_theme();
    $version    = $theme->get('Version');
    $theme_uri  = get_template_directory_uri();
    $theme_path = get_template_directory();

    // Tipografía Inter
    wp_enqueue_style(
        'driftly-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Estilos principales
    wp_enqueue_style(
        'driftly-dashboard',
        $theme_uri . '/assets/css/dashboard.css',
        [],
        file_exists($theme_path . '/assets/css/dashboard.css') ? filemtime($theme_path . '/assets/css/dashboard.css') : $version
    );

    // style.css
    wp_enqueue_style(
        'driftly-base-style',
        get_stylesheet_uri(),
        ['driftly-dashboard'],
        file_exists($theme_path . '/style.css') ? filemtime($theme_path . '/style.css') : $version
    );

    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'driftly-dashboard-js',
        $theme_uri . '/assets/js/dashboard.js',
        ['jquery'],
        file_exists($theme_path . '/assets/js/dashboard.js') ? filemtime($theme_path . '/assets/js/dashboard.js') : $version,
        true
    );

    wp_localize_script('driftly-dashboard-js', 'DriftlyDashboard', [
        'ajaxUrl'   => admin_url('admin-ajax.php'),
        'logoutUrl' => wp_logout_url(),
    ]);
}
add_action('wp_enqueue_scripts', 'driftly_dashboard_enqueue_assets', 20);


/**
 * Ocultar admin bar excepto administradores
 */
function driftly_hide_admin_bar_for_non_admin() {
    if (!current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'driftly_hide_admin_bar_for_non_admin');


/**
 * Activar menús públicos
 */
function driftly_dashboard_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');

    register_nav_menus([
        'dashboard_sidebar' => __('Menú Sidebar Dashboard', 'driftly-dashboard'),
        'public_primary'    => __('Menú Público Principal', 'driftly-dashboard'),
        'public_footer'     => __('Menú Público Footer', 'driftly-dashboard'),
    ]);
}
add_action('after_setup_theme', 'driftly_dashboard_theme_setup');


/**
 * Redirección post-login por rol
 */
function driftly_login_redirect($redirect_to, $request, $user) {
    if (!isset($user->roles) || empty($user->roles)) return $redirect_to;

    $role = $user->roles[0];

    if ($role === 'vds')        return home_url('/vds/dashboard');
    if ($role === 'proveedor')  return home_url('/proveedor/dashboard');
    if ($role === 'administrator') return admin_url();

    return $redirect_to;
}
add_filter('login_redirect', 'driftly_login_redirect', 100, 3);


/**
 * BLOQUEO TOTAL DE WP-ADMIN PARA VDS Y PROVEEDOR
 * (bloquea profile.php, index.php y cualquier URL interna)
 */
function driftly_hard_block_wp_admin() {

    if (!is_user_logged_in()) return;

    // Administradores SI pueden entrar
    if (current_user_can('administrator')) return;

    $role = driftly_get_user_role();

    // Si no es admin → bloquear acceso
    if ($role === 'vds' || $role === 'proveedor') {

        // Permitir AJAX
        if (wp_doing_ajax()) return;

        $request_uri = $_SERVER['REQUEST_URI'];

        // Cualquier intento de entrar al admin
        if (strpos($request_uri, '/wp-admin') !== false) {
            wp_redirect(home_url('/'));
            exit;
        }
    }
}
add_action('init', 'driftly_hard_block_wp_admin');


/**
 * Redirección desde "/" según rol
 */
function driftly_redirect_home_to_dashboard() {

    if (current_user_can('administrator')) return;

    $requested_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($requested_path !== '') return;

    if (!is_user_logged_in()) return;
    if (!function_exists('driftly_get_user_role')) return;

    $role = driftly_get_user_role();

    if ($role === 'vds') {
        wp_redirect(home_url('/vds/dashboard'));
        exit;
    }

    if ($role === 'proveedor') {
        wp_redirect(home_url('/proveedor/dashboard'));
        exit;
    }
}
add_action('template_redirect', 'driftly_redirect_home_to_dashboard');

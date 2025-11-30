<?php
/**
 * Funciones del tema Driftly Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Carga de CSS y JS del tema Driftly Dashboard
 */
function driftly_dashboard_enqueue_assets() {

    $theme      = wp_get_theme();
    $version    = $theme->get( 'Version' );
    $theme_uri  = get_template_directory_uri();
    $theme_path = get_template_directory();

    // ============================
    // ESTILOS
    // ============================

    // Tipografía Inter
    wp_enqueue_style(
        'driftly-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Estilos principales del dashboard
    wp_enqueue_style(
        'driftly-dashboard',
        $theme_uri . '/assets/css/dashboard.css',
        [],
        file_exists( $theme_path . '/assets/css/dashboard.css' ) ? filemtime( $theme_path . '/assets/css/dashboard.css' ) : $version
    );

    // style.css básico del tema
    wp_enqueue_style(
        'driftly-base-style',
        get_stylesheet_uri(),
        [ 'driftly-dashboard' ],
        file_exists( $theme_path . '/style.css' ) ? filemtime( $theme_path . '/style.css' ) : $version
    );

    // ============================
    // SCRIPTS
    // ============================

    wp_enqueue_script( 'jquery' );

    wp_enqueue_script(
        'driftly-dashboard-js',
        $theme_uri . '/assets/js/dashboard.js',
        [ 'jquery' ],
        file_exists( $theme_path . '/assets/js/dashboard.js' ) ? filemtime( $theme_path . '/assets/js/dashboard.js' ) : $version,
        true
    );

    wp_localize_script(
        'driftly-dashboard-js',
        'DriftlyDashboard',
        [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'logoutUrl' => wp_logout_url(),
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'driftly_dashboard_enqueue_assets', 20 );

/**
 * Ocultar admin bar para todos excepto administradores
 */
function driftly_hide_admin_bar_for_non_admin() {
    if ( ! current_user_can( 'administrator' ) ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'driftly_hide_admin_bar_for_non_admin' );

/**
 * Redirigir la home según rol al backoffice Driftly
 *  - VDS      → /vds/dashboard
 *  - Proveedor→ /proveedor/dashboard (cuando lo implementemos)
 *  - Admin    → /admin-driftly/dashboard
 */
function driftly_redirect_home_to_dashboard() {

    if ( is_admin() ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        // Visitante → dejamos que vea la home pública normal
        return;
    }

    if ( ! is_front_page() ) {
        return;
    }

    if ( ! function_exists( 'driftly_get_user_role' ) ) {
        return;
    }

    $role = driftly_get_user_role();

    if ( $role === 'vds' ) {
        wp_redirect( home_url( '/vds/dashboard' ) );
        exit;
    }

    if ( $role === 'proveedor' ) {
        wp_redirect( home_url( '/proveedor/dashboard' ) );
        exit;
    }

    if ( $role === 'admin' ) {
        wp_redirect( home_url( '/admin-driftly/dashboard' ) );
        exit;
    }
}
add_action( 'template_redirect', 'driftly_redirect_home_to_dashboard' );

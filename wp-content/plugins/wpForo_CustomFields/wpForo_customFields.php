<?php
/*
Plugin Name: Custom wpForo Fields
Description: A plugin to add custom fields to wpForo. Using custom fields, custom forms can also be
created.
Version: pre-1.0
Author: Panagiotis Papadopoulos
*/
ob_clean();
ob_start();
//Init Global Constants/Functions
require_once plugin_dir_path(__FILE__) . 'Globals.php';
/**
 * Inject Global styles and scripts.
 */
function inject_css_js($hook_suffix) {
    /** Global JS/CSS Files */
    add_filter('script_loader_tag', 'add_defer', 10, 3);
    wp_enqueue_style('bootstrapCSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrapJS','https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js');
    wp_enqueue_script('globalsJS', plugin_dir_url(__FILE__) . 'page_content/JS/globals.js');
    wp_enqueue_style('globalsCSS', plugin_dir_url(__FILE__) . 'page_content/CSS/globals.css');
    wp_localize_script('globalsJS', 'WPF_CUSTOM_API', array(
        'baseUrl' => esc_url_raw( rest_url('wpforo_custom_fields/v1') ),
        'nonce' => wp_create_nonce('wp_rest'),
    ));

    /* Page specific JS/CSS */
    if($hook_suffix == 'wpforo-fields_page_custom-wpforo-forms-view'){
        wp_enqueue_script('viewFormsJS', plugin_dir_url(__FILE__) . 'page_content/JS/viewForms.js', array('bootstrapJS'));
        wp_enqueue_style('viewFormsCSS', plugin_dir_url(__FILE__) . 'page_content/CSS/viewForms.css');
    }
    elseif($hook_suffix == 'wpforo-fields_page_custom-wpforo-forms-edit'){
        wp_enqueue_script('editFormsJS', plugin_dir_url(__FILE__) . 'page_content/JS/editForms.js', array('bootstrapJS'));
    }
    elseif($hook_suffix == 'wpforo-fields_page_custom-wpforo-forms-attach'){
        wp_enqueue_script('attachFormJS', plugin_dir_url(__FILE__) . 'page_content/JS/attachForm.js', array('bootstrapJS'));
    }
    elseif($hook_suffix == 'toplevel_page_custom-wpforo-fields'){
        wp_enqueue_script('viewFieldsJS', plugin_dir_url(__FILE__) . 'page_content/JS/viewFields.js', array('bootstrapJS'));
    }
    elseif($hook_suffix == 'wpforo-fields_page_custom-wpforo-fields-edit'){
        wp_enqueue_script('editFieldJS', plugin_dir_url(__FILE__) . 'page_content/JS/editFields.js', array('bootstrapJS'));
        wp_enqueue_style('editFieldCSS', plugin_dir_url(__FILE__) . 'page_content/CSS/editFields.css');
    }
}
/**
 * Add defer attribute to scripts.
 */
function add_defer($tag, $handle, $src) {
    $defer = array('bootstrapJS', 'globalsJS', 'viewFormsJS','globalsJS','viewFieldsJS');
    if (in_array($handle, $defer)) {
        return '<script src="' . $src . '" defer="defer" type="text/javascript"></script>' . "\n";
    }
    return $tag;
}
/**
 * Add menu for admin.
 */
function wp_fieldsMenu(){
    add_menu_page(
        'View WpForo Fields',
        'WpForo Fields',
        'manage_options',
        'custom-wpforo-fields',
        'go_to_MainPage',
        'dashicons-admin-comments',
        20
    );
    add_submenu_page(
        'custom-wpforo-fields',
        'Edit WpForo Fields',
        'Edit WpForo Fields',
        'manage_options',
        'custom-wpforo-fields-edit',
        'go_to_EditPage'
    );
    add_submenu_page(
        'custom-wpforo-fields',
        'View WpForo Forms',
        'View WpForo Forms',
        'manage_options',
        'custom-wpforo-forms-view',
        'go_to_ViewFormsPage'
    );
    add_submenu_page(
        'custom-wpforo-fields',
        'Edit WpForo Forms',
        'Edit WpForo Forms',
        'manage_options',
        'custom-wpforo-forms-edit',
        'go_to_EditFormsPage'
    );
    add_submenu_page(
        'custom-wpforo-fields',
        'Attach WpForo Forms',
        'Attach WpForo Forms',
        'manage_options',
        'custom-wpforo-forms-attach',
        'go_to_attachFormPage'
    );
}
/**
 * Go to the main page.
 */
function go_to_MainPage(){
    go_to_page('viewFields');
}
/**
 * Go to the edit page.
 */
function go_to_EditPage(){
    go_to_page('editField');
}
/**
 * Go to the edit forms page.
 */
function go_to_EditFormsPage(){
    go_to_page('editForms');
}
/**
 * Go to the view forms page.
 */
function go_to_ViewFormsPage(){
    go_to_page('viewForms');
}
function go_to_attachFormPage(){
    go_to_page('attachForm');
}
/**
 * Go to a page. Page name is given as a parameter.
 * @param string $page Name of the page to go to.
 * @return void
 */
function go_to_page($page){
    include_once plugin_dir_path(__FILE__).'Globals.php';
    include_once $GLOBALS['page_functions_path'] . $page . '.php';
    include_once $GLOBALS['page_content_path'] . $page . '.php';
    exit;
}
function on_activate() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    //Call the install file
    include_once plugin_dir_path(__FILE__).'Globals.php';
    include_once plugin_dir_path(__FILE__) .'install.php';
}
function uninstallPlugin() {
    include_once plugin_dir_path(__FILE__).'Globals.php';
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS']}");
    $wpdb->query("DROP TABLE IF EXISTS {$GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS']}");
    $wpdb->query("DROP TABLE IF EXISTS {$GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS']}");
    $wpdb->query("DROP TABLE IF EXISTS {$GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS']}");
    $wpdb->query("DROP TABLE IF EXISTS {$GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS']}");
}
function register_REST_apis(){
    
    add_action('init', function () {
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            // The request is a preflight request. Send a 200 response and exit.
            status_header(200);
            exit();
        }
    });
    require_once plugin_dir_path(__FILE__).'API/get-form-fields.php';
}
// Page paths
$GLOBALS['page_content_path'] = plugin_dir_path(__FILE__) . 'page_content/';
$GLOBALS['page_functions_path'] = plugin_dir_path(__FILE__) . 'page_functions/';
//Wordpress Hooks
add_action('admin_enqueue_scripts', 'inject_css_js');//Add bootstrap
add_action('admin_menu', 'wp_fieldsMenu');//Add menu for admin
add_action('rest_api_init', 'register_REST_apis');//Register REST API endpoints
add_action('rest_api_init', function() {
    remove_filter('rest_pre_serve_request', 'rest_send_cors_headers');
}, 15);
register_activation_hook(__FILE__, 'on_activate');
register_uninstall_hook(__FILE__, 'uninstallPlugin');
//Hook for wpForo fields
require_once plugin_dir_path(__FILE__) . 'injections.php';
ob_clean();
?>
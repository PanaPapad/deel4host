<?php
/*
Plugin Name: Custom wpForo Fields
Description: A plugin to add custom fields to wpForo. Using custom fields, custom forms can also be
created.
Version: 0.2
Author: Panagiotis Papadopoulos
*/
ob_clean();
ob_start();
/**
 * Inject Global styles and scripts.
 */
function inject_styles_code() {
    wp_enqueue_style('bootstrapCSS', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrapJS','https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js');
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
        'Edit WpForo Forms',
        'Edit WpForo Forms',
        'manage_options',
        'custom-wpforo-forms-edit',
        'go_to_EditFormsPage'
    );
    add_submenu_page(
        'custom-wpforo-fields',
        'View WpForo Forms',
        'View WpForo Forms',
        'manage_options',
        'custom-wpforo-forms-view',
        'go_to_ViewFormsPage'
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
/**
 * Go to a page. Page name is given as a parameter.
 * @param string $page Name of the page to go to.
 * @return void
 */
function go_to_page($page){
    include_once 'Globals.php';
    include $GLOBALS['page_functions_path'] . $page . '.php';
    include $GLOBALS['page_content_path'] . $page . '.php';
    exit;
}
function check_for_tables(){
    // Check if the tables exist
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $fields_table_name = $table_prefix . 'custom_wpforo_fields';
    $forms_table_name = $table_prefix . 'custom_wpforo_forms';
    $junction_table_name = $table_prefix . 'custom_wpforo_form_fields';
    $forum_form_table_name = $table_prefix . 'custom_wpforo_forum_forms';
    $custom_post_table_name = $table_prefix . 'custom_wpforo_posts';
    
    $fields_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$fields_table_name'") == $fields_table_name;
    if(!$fields_table_exists){
        create_field_table();
    }
    $forms_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$forms_table_name'") == $forms_table_name;
    if(!$forms_table_exists){
        create_form_table();
    }
    $junction_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$junction_table_name'") == $junction_table_name;
    if(!$junction_table_exists){
        create_field_form_table();
    }
    $forum_form_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$forum_form_table_name'") == $forum_form_table_name;
    if(!$forum_form_table_exists){
        create_forum_form_table();
    }
    $custom_post_table_exists = $wpdb->get_var("SHOW TABLES LIKE '$custom_post_table_name'") == $custom_post_table_name;
    if(!$custom_post_table_exists){
        create_post_table();
    }
}
function on_activate() {
    // Create the tables
    check_for_tables();
    //create_field_table();
    //create_form_table();
    //create_field_form_table();
}
function create_field_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the fields table
    $fields_table_name = $table_prefix . 'custom_wpForo_fields';
    $fields_table_sql = "CREATE TABLE $fields_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        field_name VARCHAR(25) NOT NULL,
        field_type VARCHAR(30) NOT NULL,
        field_label VARCHAR(50) NOT NULL,
        field_placeholder VARCHAR(50) NOT NULL DEFAULT '',
        field_fa_icon VARCHAR(50) NOT NULL DEFAULT '',
        field_description VARCHAR(255) NOT NULL DEFAULT '',
        field_default_value VARCHAR(255) NOT NULL DEFAULT '',
        field_required BOOLEAN NOT NULL,
        field_options VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($fields_table_sql);
}
function create_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the forms table
    $forms_table_name = $table_prefix . 'custom_wpForo_forms';
    $forms_table_sql = "CREATE TABLE $forms_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        form_name VARCHAR(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta($forms_table_sql);
}
function create_field_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the junction table for form-field relationships
    $junction_table_name = $table_prefix . 'custom_wpForo_form_fields';
    $junction_table_sql = "CREATE TABLE $junction_table_name (
        form_id INT(11) UNSIGNED NOT NULL,
        field_id INT(11) UNSIGNED NOT NULL,
        PRIMARY KEY (form_id, field_id),
        FOREIGN KEY (form_id) REFERENCES {$wpdb->prefix}custom_wpforo_forms(id),
        FOREIGN KEY (field_id) REFERENCES {$wpdb->prefix}custom_wpforo_fields(id)
    ) $charset_collate;";
    dbDelta($junction_table_sql);
}
function create_forum_form_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    //Each forum can have ony ONE form
    $forum_form_table_name = $table_prefix . 'custom_wpforo_forum_forms';
    $forum_form_table_sql = "CREATE TABLE $forum_form_table_name (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        forum_id INT(10) UNSIGNED NOT NULL,
        form_id INT(11) UNSIGNED NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY (forum_id),
        FOREIGN KEY (forum_id) REFERENCES {$wpdb->prefix}wpforo_forums(forumid),
        FOREIGN KEY (form_id) REFERENCES {$wpdb->prefix}custom_wpforo_forms(id)
    ) $charset_collate;";
    dbDelta($forum_form_table_sql);
}
function create_post_table(){
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    $table_prefix = $wpdb->prefix;
    // Create the posts table
    $posts_table_name = $table_prefix . 'custom_wpforo_posts';
    $posts_table_sql = "CREATE TABLE $posts_table_name (
        post_id BIGINT(20) UNSIGNED NOT NULL,
        field_id INT(11) UNSIGNED NOT NULL,
        field_value VARCHAR(255) NOT NULL,
        PRIMARY KEY (post_id, field_id),
        FOREIGN KEY (post_id) REFERENCES {$wpdb->prefix}wpforo_posts(postid),
        FOREIGN KEY (field_id) REFERENCES {$wpdb->prefix}custom_wpforo_fields(id)
    ) $charset_collate;";
    dbDelta($posts_table_sql);
}
function inject_wpforo_fields($fields, $type, $forum){
    // Fetch the fields from the database
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $fields_table_name = $table_prefix . 'custom_wpForo_fields';
    $fields['customBody'] = [
        'fieldKey'       => 'customBody',
        'type'           => 'text',
        'isDefault'      => 1,
        'isRemovable'    => 0,
        'isRequired'     => 1,
        'isEditable'     => 1,
        'title'          => wpforo_phrase( 'Custom Field', false ),
        'placeholder'    => wpforo_phrase( 'Custom Field', false ),
        'minLength'      => 0,
        'maxLength'      => 0,
        'faIcon'         => '',
        'name'           => 'Custom Body',
        'cantBeInactive' => [ 'topic', 'post', 'comment', 'reply' ],
        'canEdit'        => [1,2,3,4,5],
        'canView'        => [1,2,3,4,5],
        'can'            => '',
        'isSearchable'   => 1,
    ];
    return $fields;
}
function inject_wpforo_forms($fields, $forum, $guest){
    $fields[0][0][2] = 'customBody';
    return $fields;
}
function add_custom_fields_to_post($postArgs, $forum){
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    //Get the post id
    $postId = WPF()->db->insert_id;
    //Get form id
    $formId = $wpdb->get_var("SELECT formid FROM {$wpdb->prefix}custom_wpForo_forum_form WHERE forumid = $forum->forumid");
    //Get custom field list
    $customFields = $wpdb->get_results("SELECT field_id FROM {$wpdb->prefix}custom_wpForo_form_fields WHERE form_id = $formId");
    
    //Insert the custom field into the database
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i];
        $field = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_wpForo_fields WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $fieldValue = $postArgs[$fieldName];
        $wpdb->insert("{$wpdb->prefix}custom_wpForo_post", array(
            'post_id' => $postId,
            'field_id' => $fieldId,
            'field_value' => $fieldValue
        ));
    }
    $table_name = $table_prefix . 'custom_wpForo_post';
}
function uninstallPlugin() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_forum_forms");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_posts");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_form_fields");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_forms");
}
// Page paths
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
$GLOBALS['page_content_path'] = plugin_dir_path(__FILE__) . 'page_content/';
$GLOBALS['page_functions_path'] = plugin_dir_path(__FILE__) . 'page_functions/';
//Wordpress Hooks
add_action('admin_enqueue_scripts', 'inject_styles_code');//Add bootstrap
add_action('admin_menu', 'wp_fieldsMenu');//Add menu for admin
register_activation_hook(__FILE__, 'on_activate');
register_uninstall_hook(__FILE__, 'uninstallPlugin');
//Hook for wpForo fields
add_filter('wpforo_post_after_init_fields','inject_wpforo_fields',10,3);
add_filter('wpforo_get_topic_fields_structure','inject_wpforo_forms',10,3);
add_action('wpforo_after_add_topic','add_custom_fields_to_post',10,2);
ob_clean();
?>
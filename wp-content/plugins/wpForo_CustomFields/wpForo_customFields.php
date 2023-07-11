<?php
/*
Plugin Name: Custom wpForo Fields
Description: A plugin to add custom fields to wpForo. Using custom fields, custom forms can also be
created.
Version: 0.3
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
    include_once 'Globals.php';
    include $GLOBALS['page_functions_path'] . $page . '.php';
    include $GLOBALS['page_content_path'] . $page . '.php';
    exit;
}
function on_activate() {
    //Call the install file
    include_once 'install.php';
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
    $table_name = $wpdb->prefix . 'custom_wpforo_forum_forms';
    //Get form id
    $query = "SELECT form_id FROM $table_name WHERE forum_id = {$forum['forumid']}";
    $formId = $wpdb->get_var($query);
    //Get custom field list
    $customFields = $wpdb->get_results("SELECT field_id FROM {$wpdb->prefix}custom_wpForo_form_fields WHERE form_id = $formId");
    
    //Insert the custom field into the database
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $field = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_wpForo_fields WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $fieldValue = $postArgs[$fieldName];
        $result = $wpdb->insert("{$wpdb->prefix}custom_wpForo_posts", array(
            'post_id' => $postId,
            'field_id' => $fieldId,
            'field_value' => $fieldValue
        ));
        echo $wpdb->last_error;
    }
    $table_name = $table_prefix . 'custom_wpForo_post';
}
function inject_post_field($postContent,$post){
    global $wpdb;
    $table_prefix = $wpdb->prefix;
    $table_name = $table_prefix . 'custom_wpforo_posts';
    $postId = $post['postid'];
    $customFields = $wpdb->get_results("SELECT field_id, field_value FROM $table_name WHERE post_id = $postId");
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $fieldValue = $customFields[$i]->field_value;
        $field = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}custom_wpForo_fields WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $postContent .= '<p><b>' . $fieldName . '</b>: ' . $fieldValue . '</p>';
    }
    return $postContent;
}
function uninstallPlugin() {
    global $wpdb;
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_forum_forms");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_posts");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_form_fields");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_forms");
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}custom_wpforo_fields");
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
add_filter('wpforo_content','inject_post_field',10,3);
add_action('wpforo_after_add_topic','add_custom_fields_to_post',10,2);
ob_clean();
?>
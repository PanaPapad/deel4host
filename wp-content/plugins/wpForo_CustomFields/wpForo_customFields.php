<?php
/*
Plugin Name: Custom wpForo Fields
Description: A plugin to add custom fields to wpForo. Using custom fields, custom forms can also be
created.
Version: 0.9
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
function inject_wpforo_fields($fields, $type, $forum){
    global $wpdb;
    $fields_table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $query = "SELECT * FROM $fields_table_name";
    $customFields = $wpdb->get_results($query);
    for($i=0;$i<count($customFields);$i++){
        $field = $customFields[$i];
        $fields[$field->field_name] = [
            'fieldKey'       => $field->field_name,
            'type'           => $field->field_type,
            'isDefault'      => 0,
            'isRemovable'    => 1,
            'isRequired'     => (int)$field->field_required,
            'isEditable'     => 1,
            'label'          => wpforo_phrase( $field->field_label, false ),
            'title'          => wpforo_phrase( $field->field_label, false ),
            'placeholder'    => wpforo_phrase( $field->field_placeholder, false ),
            'minLength'      => 0,
            'maxLength'      => 0,
            'faIcon'         => $field->field_fa_icon,
            'name'           => $field->field_name,
            'cantBeInactive' => [ 'topic', 'post', 'comment', 'reply' ],
            'canEdit'        => [1,2,3,4,5],
            'canView'        => [1,2,3,4,5],
            'can'            => '',
            'isSearchable'   => 1,
        ];
    }
    return $fields;
}
function inject_wpforo_forms($fields, $forum, $guest){
    global $wpdb;
    $forum_form_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    $form_fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];

    $formIdQuery = "SELECT form_id FROM $forum_form_table WHERE forum_id = {$forum['forumid']}";
    $formId = $wpdb->get_var($formIdQuery);
    //if there is no form for this forum, return the default fields
    if(!$formId){
        return $fields;
    }

    $formFieldsQuery = "SELECT field_id FROM $form_fields_table WHERE form_id = $formId";
    $formFields = $wpdb->get_results($formFieldsQuery);
    //if there are no fields for this form, return the default fields
    if(!$formFields){
        return $fields;
    }

    for($i=0;$i<count($formFields);$i++){
        $fieldId = $formFields[$i]->field_id;
        $fieldName = $wpdb->get_var("SELECT field_name FROM $fields_table WHERE id = $fieldId");
        $fields[0][0][2+$i] = $fieldName;
    }
    return $fields;
}
function add_custom_fields_to_post($postArgs, $forum){
    global $wpdb;
    //Get the post id
    $postId = WPF()->db->insert_id;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    //Get form id
    $query = "SELECT form_id FROM $table_name WHERE forum_id = {$forum['forumid']}";
    $formId = $wpdb->get_var($query);
    //Get custom field list
    $customFields = $wpdb->get_results("SELECT field_id FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS']} WHERE form_id = $formId");
    
    //Insert the custom field into the database
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $field = $wpdb->get_row("SELECT * FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS']} WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $fieldValue = $postArgs[$fieldName];
        $result = $wpdb->insert("{$GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS']}", array(
            'post_id' => $postId,
            'field_id' => $fieldId,
            'field_value' => $fieldValue
        ));
        echo $wpdb->last_error;
    }
}
function inject_post_field($postContent,$post){
    global $wpdb;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['POSTS'];
    $postId = $post['postid'];
    $customFields = $wpdb->get_results("SELECT field_id, field_value FROM $table_name WHERE post_id = $postId");
    for($i=0;$i<count($customFields);$i++){
        $fieldId = $customFields[$i]->field_id;
        $fieldValue = $customFields[$i]->field_value;
        $field = $wpdb->get_row("SELECT * FROM {$GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS']} WHERE id = $fieldId");
        $fieldName = $field->field_name;
        $postContent .= '<p><b>' . $fieldName . '</b>: ' . $fieldValue . '</p>';
    }
    return $postContent;
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
// Page paths
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
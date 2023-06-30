<?php
// API endpoint: wpForo_CustomFields/API/edit-field.php

/** Debugging
* ini_set('display_errors', 1);
* ini_set('display_startup_errors', 1);
* error_reporting(E_ALL);
*/
//Load only basic WordPress functionality
define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/deel4host/wp-load.php');

//Only allow POST requests
if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    http_response_code(400);//Bad request
    exit;
}
//Only Admins can access this endpoint
if (!current_user_can('administrator')){
    http_response_code(403);//Forbidden
    exit;
}

// Check the nonce for security
check_admin_referer('custom_fields_nonce_action', 'custom_fields_nonce');
// Sanitize and store the form data as needed
$field_name = sanitize_text_field($_POST['field_name']);
$field_type = sanitize_text_field($_POST['field_type']);
$field_label = sanitize_text_field($_POST['field_label']);
$field_description = sanitize_textarea_field($_POST['field_description']);
$field_default = sanitize_text_field($_POST['field_default']);
$field_required = isset($_POST['field_required']) ? 1 : 0;
$field_options = sanitize_text_field($_POST['field_options']);
// Save the field data to the database
$data = array(
    'field_name' => $field_name,
    'field_type' => $field_type,
    'field_label' => $field_label,
    'field_description' => $field_description,
    'field_default_value' => $field_default,
    'field_required' => $field_required,
    'field_options' => $field_options,
);
$data_format = array(
    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%d',
    '%s',
);
global $wpdb;
$table_prefix = $wpdb->prefix;
$fields_table_name = $table_prefix . 'custom_wpForo_fields';
// Check if this is an edit
if(isset($_GET['edit_field'])){
    $index = $_GET['edit_field'];
    //Update Data
    $success = $wpdb->update(
        $fields_table_name,
        $data,
        array(
            'id' => $index,
        ),
        $data_format,
        array(
            '%d',
        )
    );
}
else{
    //Insert Data
    $success = $wpdb->insert(
        $fields_table_name,
        $data,
        $data_format
    );
}


// Set the appropriate headers
header('Content-Type: application/json');

// Send response
echo json_encode($data);

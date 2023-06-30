<?php
// API endpoint: wpForo_CustomFields/API/get-form-fields.php

/** Debugging
* ini_set('display_errors', 1);
* ini_set('display_startup_errors', 1);
* error_reporting(E_ALL);
*/
//Load only basic WordPress functionality
define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

//Only Admins can access this endpoint
if (!current_user_can('administrator')){
    http_response_code(403);//Forbidden
    exit;
}
//Only allow GET requests
if (!($_SERVER['REQUEST_METHOD'] === 'GET')) {
    http_response_code(400);//Bad request
    exit;
}
if(!isset($_GET['form_id'])){
    http_response_code(400);//Bad request
    echo 'form_id not set';
    exit;
}
header('Content-Type: application/json');
global $wpdb;
$form_fields_table = $wpdb->prefix . 'custom_wpForo_form_fields';
$fields_table = $wpdb->prefix . 'custom_wpForo_fields';
$field_ids = $wpdb->get_results("SELECT field_id FROM $form_fields_table WHERE form_id = " . $_GET['form_id'], ARRAY_A);
//Fetch the fields from the DB
$fields = array();
for ($i = 0; $i < count($field_ids); $i++) {
    $field = $wpdb->get_row("SELECT * FROM $fields_table WHERE id = " . $field_ids[$i]['field_id']);
    array_push($fields, $field);
}
// Send response
echo json_encode($fields);
?>
<?php
// API endpoint: wpForo_CustomFields/API/delete-form.php

/** Debugging
* ini_set('display_errors', 1);
* ini_set('display_startup_errors', 1);
* error_reporting(E_ALL);
*/
//Load only basic WordPress functionality
define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

//Only allow GET requests
if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
    http_response_code(400);//Bad request
    exit;
}
//Only Admins can access this endpoint
if (!current_user_can('administrator')){
    http_response_code(403);//Forbidden
    exit;
}
//Get from id from POST request
$form_id = $_POST['form_id'];

// Perform your data retrieval logic here
global $wpdb;
$formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
$success = $wpdb->delete($formsTable, array('id' => $form_id));
if(!$success){
    http_response_code(500);
    echo 'Error deleting form.';
    exit;
}
if($wpdb->rows_affected == 0){
    http_response_code(404);
    echo 'Form not found.';
    exit;
}
echo 'Form deleted successfully.';
?>
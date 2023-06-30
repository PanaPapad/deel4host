<?php
// API endpoint: wpForo_CustomFields/API/template.php

/** Debugging
* ini_set('display_errors', 1);
* ini_set('display_startup_errors', 1);
* error_reporting(E_ALL);
*/
//Load only basic WordPress functionality
define('WP_USE_THEMES', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

//Only allow GET requests
if (!($_SERVER['REQUEST_METHOD'] === 'GET')) {
    http_response_code(400);//Bad request
    exit;
}
//Only Admins can access this endpoint
if (!current_user_can('administrator')){
    http_response_code(403);//Forbidden
    exit;
}
/** Remove  */
http_response_code(501);//Not implemented
echo 'Not implemented';
exit;
/** Remove */

// Perform your data retrieval logic here
$data = array(
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3'
);
// Set the appropriate headers
header('Content-Type: application/json');

// Send response
echo json_encode($data);
?>
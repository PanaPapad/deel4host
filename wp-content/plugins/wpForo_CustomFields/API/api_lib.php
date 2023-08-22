<?php
//attach form route
register_rest_route( 'wpforo_custom_fields/v1', '/attach_form', array(
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => 'attach_form',
	'permission_callback' => function (WP_REST_Request $request) {
		return current_user_can( 'manage_options' );
	}
));
//Get form fields route
register_rest_route( 'wpforo_custom_fields/v1', '/form_fields', array(
	'methods' => WP_REST_Server::READABLE,
	'callback' => 'get_form_fields',
	'permission_callback' => function (WP_REST_Request $request) {
		return current_user_can( 'manage_options' );
	}
));
//Delete form route
register_rest_route( 'wpforo_custom_fields/v1', '/form', array(
    'methods' => WP_REST_Server::DELETABLE,
    'callback' => 'delete_form',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));
register_rest_route('wpforo_custom_fields/v1', '/form',array(
    'methods' => 'PUT',
    'callback' => 'edit_form',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));
register_rest_route('wpforo_custom_fields/v1', '/form',array(
    'methods' => 'POST',
    'callback' => 'create_form',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));
register_rest_route('wpforo_custom_fields/v1', '/form',array(
    'methods' => 'GET',
    'callback' => 'get_form',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));
register_rest_route('wpforo_custom_fields/v1', '/fields',array(
    'methods' => 'GET',
    'callback' => 'get_all_fields',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));
/**
 * Proccess attach form to forum request.
 * Data should be in the form of an array of objects with the following properties:
 * form_id: The id of the form to attach.
 * forum_id: The id of the forum to attach to.
 * Example:
 * [{ "form_id": 1, "forum_id": 1 }, { "form_id": 2, "forum_id": 2 }]
 * @param WP_REST_Request $request Full data about the request.
 */
function attach_form( WP_REST_Request $request ) {
	// Perform your data retrieval logic here
    //begin transaction
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    //Parse the request body
    $data_array = json_decode($request->get_body(), true);
    for($i = 0; $i < count($data_array); $i++){
        $data = $data_array[$i];
        $form_id = $data['form_id'];
        $forum_id = $data['forum_id'];
        $success = add_forum_form_record($form_id, $forum_id);
        //Check for errors
        if(!$success || $success == 0){
            $wpdb->query('ROLLBACK');
            return new WP_REST_Response( 'Failed to attach form to forum. Error: '. $wpdb->last_error, 500 );
        }
    }
	// Return response
    $wpdb->query('COMMIT');
	return new WP_REST_Response( null,200 );
}
/**
 * Add a record to the junction table.
 * @param $form_id The id of the form to attach.
 * @param $forum_id The id of the forum to attach to.
 * @return bool True if successful, false otherwise.
 */
function add_forum_form_record($form_id, $forum_id){
    global $wpdb;
    $junctionTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    $forum_has_record = $wpdb->get_row("SELECT * FROM $junctionTable WHERE forum_id = $forum_id");
    $success = true;
    if($forum_has_record){
        //Same form
        if($forum_has_record->form_id == $form_id){
            $success = true;
        }
        //No form
        elseif($form_id === 'none'){
            $success = $wpdb->delete($junctionTable, array('forum_id' => $forum_id));
        }
        //Different form
        else{
            $success = $wpdb->update($junctionTable, array('form_id' => $form_id), array('forum_id' => $forum_id));
        }
    }
    //No record
    else{
        $success = $wpdb->insert($junctionTable, array('forum_id' => $forum_id, 'form_id' => $form_id));
    }
    return $success;
}
/**
 * Proccess get form fields request.
 * The form id should be provided as a query parameter, as follows:
 * form_id=<int>
 * @param WP_REST_Request $request Full data about the request.
 */
function get_form_fields( WP_REST_Request $request ) {
	// Perform your data retrieval logic here
	if(!isset($_GET['form_id'])){
        return new WP_REST_Response( 'No form id provided.', 400 );
    }
    $form_id = $_GET['form_id'];
    global $wpdb;
    $junction_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $fields = $wpdb->get_results("SELECT * FROM $junction_table WHERE form_id = $form_id");
    $data = array();
    foreach($fields as $field){
        $field_id = $field->field_id;
        $field_data = $wpdb->get_row("SELECT * FROM $fields_table WHERE id = $field_id");
        array_push($data, $field_data);
    }
	// Return response
	return new WP_REST_Response( $data, 200 );
}
/**
 * Proccess delete form request.
 * Data should be in the form of an array of objects with the following properties:
 * form_id: The id of the form to delete.
 * Example:
 * [{ "form_id": 1 }, { "form_id": 2 }]
 * @param WP_REST_Request $request Full data about the request.
 */
function delete_form( WP_REST_Request $request ) {
    // Perform your data retrieval logic here
    //begin transaction
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    //Parse the request body
    $data_array = json_decode($request->get_body(), true);
    $deleted_ids = array();
    for($i = 0; $i < count($data_array); $i++){
        $data = $data_array[$i];
        $form_id = $data['form_id'];
        $success = $wpdb->delete($GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'], array('id' => $form_id));
        //Check for errors
        if(!$success || $success == 0){
            $wpdb->query('ROLLBACK');
            return new WP_REST_Response( 'Failed to delete form. Error: '. $wpdb->last_error, 500 );
        }
        array_push($deleted_ids,$form_id);
    }
    // Return response
    $wpdb->query('COMMIT');
    return new WP_REST_Response('Forms with id(s): ['.implode(', ', $deleted_ids).'] deleted successfully.', 200);
}
function edit_form(WP_REST_Request $request){
    //Check if request has json content type
    if($request->get_header('Content-Type') !== 'application/json'){
        return new WP_REST_Response( 'Request must have json content type.', 400 );
    }
    //Parse the request body
    $data_array = json_decode($request->get_body(), true);
    if(!isset($data_array['form_name'])){
        return new WP_REST_Response( 'No form name provided.', 400 );
    }
    //Check for form id
    if(!isset($data_array['form_id'])){
        return new WP_REST_Response( 'No form id provided.', 400 );
    }
    $form_id = $data_array['form_id'];
    $form_name = $data_array['form_name'];
    if(!isset($data_array['form_fields'])){
        return new WP_REST_Response( 'No form fields provided.', 400 );
    }
    $form_fields = $data_array['form_fields'];
    //begin transaction
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    // Update the form
    $success=$wpdb->update($GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'], array('form_name' => $form_name), array('id' => $form_id));
    if (!$success && $success !== 0) {
        // Rollback on failure
        $wpdb->query('ROLLBACK');
        return new WP_REST_Response( 'Failed to update form. Error: '. $wpdb->last_error, 500 );
    }
    // Delete the form fields
    $success = $wpdb->delete($GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'], array('form_id' => $form_id));
    if (!$success && $success !==0) {
        // Rollback on failure
        $wpdb->query('ROLLBACK');
        return new WP_REST_Response( 'Failed to update form. Error: '. $wpdb->last_error, 500 );
    }
    // Insert the form fields
    for($i = 0; $i < count($form_fields); $i++){
        $field_id = $form_fields[$i];
        $success = $wpdb->insert($GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'], array('form_id' => $form_id, 'field_id' => $field_id));
        if (!$success) {
            // Rollback on failure
            $wpdb->query('ROLLBACK');
            return new WP_REST_Response( 'Failed to update form. Error: '. $wpdb->last_error, 500 );
        }
    }
    // Commit on success
    $wpdb->query('COMMIT');
    return new WP_REST_Response( 'Form '.$form_name.' updated successfully.', 200 );
}
function create_form(WP_REST_Request $request){
    //Check if request has json content type
    if($request->get_header('Content-Type') !== 'application/json'){
        return new WP_REST_Response( 'Request must have json content type.', 400 );
    }
    //Parse the request body
    $data_array = json_decode($request->get_body(), true);
    if(!isset($data_array['form_name'])){
        return new WP_REST_Response( 'No form name provided.', 400 );
    }
    $form_name = $data_array['form_name'];
    if(!isset($data_array['form_fields'])){
        return new WP_REST_Response( 'No form fields provided.', 400 );
    }
    $form_fields = $data_array['form_fields'];
    //begin transaction
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    // Insert the form
    $success = $wpdb->insert($GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'], array('form_name' => $form_name));
    if (!$success) {
        // Rollback on failure
        $wpdb->query('ROLLBACK');
        return new WP_REST_Response( 'Failed to create form. Error: '. $wpdb->last_error, 500 );
    }
    // Get the form id
    $form_id = $wpdb->insert_id;
    // Insert the form fields
    for($i = 0; $i < count($form_fields); $i++){
        $field_id = $form_fields[$i];
        $success = $wpdb->insert($GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'], array('form_id' => $form_id, 'field_id' => $field_id));
        if (!$success) {
            // Rollback on failure
            $wpdb->query('ROLLBACK');
            return new WP_REST_Response( 'Failed to create form. Error: '. $wpdb->last_error, 500 );
        }
    }
    // Commit on success
    $wpdb->query('COMMIT');
    return new WP_REST_Response( 'Form '.$form_name.' created successfully.', 200 );
}
function get_form(WP_REST_Request $request){
    $response_data = array();
    //Check for form id
    if(!isset($_GET['form_id'])){
        return new WP_REST_Response( 'No form id provided.', 400 );
    }
    $form_id = $_GET['form_id'];
    //Get form name
    global $wpdb;
    $forms_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $form_name = $wpdb->get_var("SELECT form_name FROM $forms_table WHERE id = $form_id");
    $response_data['form_name'] = $form_name;
    $junction_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $fields = $wpdb->get_results("SELECT * FROM $junction_table WHERE form_id = $form_id");
    $data = array();
    foreach($fields as $field){
        $field_id = $field->field_id;
        $field_data = $wpdb->get_row("SELECT id FROM $fields_table WHERE id = $field_id");
        array_push($data, $field_data);
    }
    $response_data['form_fields'] = $data;
    return new WP_REST_Response( $response_data, 200 );
}
function get_all_fields(WP_REST_Request $request){
    //Get all fields
    global $wpdb;
    $data = array();
    $fields_table = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $fields = $wpdb->get_results("SELECT * FROM $fields_table");
    foreach($fields as $field){
        array_push($data, $field);
    }
    return new WP_REST_Response( $data, 200 );
}
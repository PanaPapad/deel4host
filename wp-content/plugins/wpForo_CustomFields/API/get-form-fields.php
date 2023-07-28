<?php
register_rest_route( 'wpforo_custom_fields/v1', '/form_fields', array(
	'methods' => WP_REST_Server::ALLMETHODS,
	'callback' => 'get_fields',
	'permission_callback' => function (WP_REST_Request $request) {
		return current_user_can( 'manage_options' );
	}
));
function get_fields( WP_REST_Request $request ) {
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
        $field_data = $wpdb->get_results("SELECT * FROM $fields_table WHERE id = $field_id");
        array_push($data, $field_data);
    }
	// Return response
	return new WP_REST_Response( $data, 200 );
}
?>
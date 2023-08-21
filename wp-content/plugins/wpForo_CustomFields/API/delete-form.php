<?php
register_rest_route( 'wpforo_custom_fields/v1', '/delete_form', array(
    'methods' => WP_REST_Server::DELETABLE,
    'callback' => 'delete_form',
    'permission_callback' => function (WP_REST_Request $request) {
        return current_user_can( 'manage_options' );
    }
));

function delete_form( WP_REST_Request $request ) {
    // Perform your data retrieval logic here
    //begin transaction
    global $wpdb;
    $wpdb->query('START TRANSACTION');
    //Parse the request body
    $data_array = json_decode($request->get_body(), true);
    for($i = 0; $i < count($data_array); $i++){
        $data = $data_array[$i];
        $form_id = $data['form_id'];
        $success = $wpdb->delete($GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'], array('id' => $form_id));
        //Check for errors
        if(!$success || $success == 0){
            $wpdb->query('ROLLBACK');
            return new WP_REST_Response( 'Failed to delete form. Error: '. $wpdb->last_error, 500 );
        }
    }
    // Return response
    $wpdb->query('COMMIT');
    return new WP_REST_Response('Forms with ids: '.implode(', ', $data_array).' deleted successfully.', 200);
}
?>
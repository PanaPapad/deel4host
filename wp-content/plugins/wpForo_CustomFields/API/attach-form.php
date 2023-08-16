<?php
register_rest_route( 'wpforo_custom_fields/v1', '/attach_form', array(
	'methods' => WP_REST_Server::CREATABLE,
	'callback' => 'attach_form',
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
        $success = add_record($form_id, $forum_id);
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
function add_record($form_id, $forum_id){
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
?>
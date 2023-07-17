<?php
function list_field_options(){
    $form_id = -1;
    if(isset($_GET['edit_form'])){
        $form_id= $_GET['edit_form'];
    }
    global $wpdb;
    //get all custom fields that are not in the form
    $fieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $formFieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $query = "SELECT $fieldsTable.*
        FROM $fieldsTable
        WHERE id NOT IN (
            SELECT field_id
            FROM $formFieldsTable AS t1
            WHERE t1.form_id = $form_id
        )";
    $custom_fields = $wpdb->get_results($query, ARRAY_A);
    for($i = 0; $i < count($custom_fields); $i++){
        echo '<option value="' . $custom_fields[$i]['id'] . '">'
            . $custom_fields[$i]['field_name'] . ' : ' . $custom_fields[$i]['field_type'] . '</option>';
    }
}
function process_form(){
    // Check if the user has submitted the form
    if (!isset($_POST['Add_Custom_WpForo_Form'])) {
        return;
    }
    // Check the nonce for security
    check_admin_referer('custom_forms_nonce_action', 'custom_forms_nonce');
    // Sanitize data
    $form_name = sanitize_text_field($_POST['form_name']);
    $field_ids = sanitize_text_field($_POST['field_ids']);
    $form_fields = explode(',', $field_ids);

    // Start a transaction so we can rollback if necessary
    global $wpdb;
    //$wpdb->show_errors(true); //For debugging
    $wpdb->query('START TRANSACTION');
    // Insert or update the form
    if(isset($_GET['edit_form'])){
        $success = updateForm($_GET['edit_form'],$form_name, $form_fields);
    }
    else{
        $success = insertForm($form_name, $form_fields);
    }

    if (!$success) {
        // Rollback on failure
        //$wpdb->print_error(); //For debugging
        $wpdb->query('ROLLBACK');
        wp_redirect(add_query_arg('custom_form_saved', '0',  $GLOBALS['noArgsUrl']));
        exit;
    }
    // Commit the transaction
    $wpdb->query('COMMIT');
    wp_redirect(add_query_arg('custom_form_saved', '1',  $GLOBALS['noArgsUrl']));
    exit;
}
function setEditInputs(){
    //Check for edit_form query arg
    if(!isset($_GET['edit_form'])){
        return;
    }
    $form_id = $_GET['edit_form'];
    global $wpdb;
    $formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $formFieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    $fieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    //get form name
    $query = "SELECT form_name
        FROM $formsTable
        WHERE id = $form_id";
    $form_name = $wpdb->get_var($query);
    //get form fields
    $query = "SELECT t1.id 'ID',t1.field_name 'Name',t1.field_type 'Type'
        FROM $formFieldsTable as t0
        INNER JOIN $fieldsTable as t1
        ON t0.field_id = t1.id
        WHERE t0.form_id = $form_id";
    $form_fields = $wpdb->get_results($query, ARRAY_A);
    $data = json_encode(array(
        'form_name' => $form_name,
        'form_fields' => $form_fields
    ));
    //set form name input
    call_js_fn_onload("setData($data)");
}
function insertForm($form_name,$form_fields){
    global $wpdb;
    $formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $formFieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];

    //insert form
    $query = "INSERT INTO $formsTable (form_name)
        VALUES ('$form_name')";
    $success = $wpdb->query($query);
    if($success===false){
        return false;
    }

    //Bullk insert form fields
    $form_id = $wpdb->insert_id;
    $values = array();
    for ($i = 0; $i < count($form_fields); $i++) {
        $values[] = "($form_id, {$form_fields[$i]})";
    }
    // Construct the bulk insert query
    $query = "INSERT INTO $formFieldsTable (form_id, field_id) VALUES " . implode(',', $values);
    $success = $wpdb->query($query);
    return true;
}
function updateForm($form_id,$form_name,$form_fields){
    global $wpdb;
    $formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $formFieldsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORM_FIELDS'];
    //update form
    $query = "UPDATE $formsTable
        SET form_name = '$form_name'
        WHERE id = $form_id";
    $success = $wpdb->query($query);
    if($success===false){
        return false;
    }
    //delete form fields
    $query = "DELETE FROM $formFieldsTable
        WHERE form_id = $form_id";
    $success = $wpdb->query($query);
    if($success===false){
        return false;
    }
    //If no fields are selected, return true
    if($form_fields[0]===''){
        return true;
    }
    //insert form fields
    $values = array();
    for ($i = 0; $i < count($form_fields); $i++) {
        $values[] = "($form_id, {$form_fields[$i]})";
    }
    // Construct the bulk insert query
    $query = "INSERT INTO $formFieldsTable (form_id, field_id) VALUES " . implode(',', $values);

    // Execute the bulk insert query
    $success = $wpdb->query($query);

    if ($success === false) {
        return false;
    }
    return true;
}
$GLOBALS['noArgsUrl'] = remove_query_arg(
    array(
        'edit_form',
        'custom_form_saved',
    ),
    wp_get_referer()
);
process_form();
setEditInputs();
?>
<?php
/**
 * Process the submitted form.
 */
function process_form() {
    // Check if our form has been submitted
    if (!isset($_POST['Add_Custom_WpForo_Field'])) {
        return;
    }
    // Check the nonce for security
    check_admin_referer('custom_fields_nonce_action', 'custom_fields_nonce');
    // Sanitize and store the form data as needed
    $field_name = sanitize_text_field($_POST['field_name']);
    $field_name = str_replace(' ', '_', $field_name);
    $field_type = sanitize_text_field($_POST['field_type']);
    $field_label = sanitize_text_field($_POST['field_label']);
    $field_description = sanitize_textarea_field($_POST['field_description']);
    $field_default = sanitize_text_field($_POST['field_default']);
    $field_required = isset($_POST['field_required']) ? 1 : 0;
    $field_options = sanitize_text_field($_POST['field_options']);
    $field_placeholder = sanitize_text_field($_POST['field_placeholder']);
    $field_fa_icon = sanitize_text_field($_POST['field_fa_icon']);
    // Save the field data to the database
    $data = array(
        'field_name' => $field_name,
        'field_type' => $field_type,
        'field_label' => $field_label,
        'field_placeholder' => $field_placeholder,
        'field_fa_icon' => $field_fa_icon,
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
        '%s',
        '%s',
        '%d',
        '%s',
    );
    global $wpdb;
    $fields_table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
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
    if (!$success) {
        // There was an error saving your data
        wp_redirect(add_query_arg('custom_field_saved', '0',  $GLOBALS['noArgsUrl']));
        exit;
    }
    // Redirect back to the form page with a success message
    wp_redirect(add_query_arg('custom_field_saved', '1',  $GLOBALS['noArgsUrl']));
    exit;
}
/**
 * Delete a custom field from the database.
 */
function deleteCustomField() {
    if (!isset($_GET['delete_field'])) {
        return;
    }
    $id = $_GET['delete_field'];
    global $wpdb;
    $success = $wpdb->delete(
        $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'],
        array(
            'id' => $id,
        ),
        array(
            '%d',
        )
    );
    if ($success) {
        wp_redirect(add_query_arg('custom_field_deleted', '1', $GLOBALS['noArgsUrl']));
        exit;
    } 
    else {
        //$error = $wpdb->last_error; //For debugging purposes
        wp_redirect(add_query_arg('custom_field_deleted', '0', $GLOBALS['noArgsUrl']));
        exit;
    }
}
/**
 * Set the edit form inputs to the values of the form being edited.
 */
function setEditInputs(){
    if(!isset($_GET['edit_field'])){
        return;
    }
    global $wpdb;
    $id = $_GET['edit_field'];
    //get custom fields from custom_wpforo_fields table
    $tableName = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $custom_fields = $wpdb->get_row("SELECT * FROM $tableName WHERE id = $id");

    //convert to json and send to js
    $fieldList = json_encode($custom_fields);
    call_js_fn_onload("setFieldValues($fieldList)");
}
/** BELOW CODE RUNS ON PHP ENTRY */
$GLOBALS['noArgsUrl'] = remove_query_arg(
    array(
    'custom_field_saved',
    'custom_field_deleted',
    'edit_field',
    'delete_field',
    ),
    wp_get_referer()
);
process_form();
deleteCustomField();
setEditInputs();
?>
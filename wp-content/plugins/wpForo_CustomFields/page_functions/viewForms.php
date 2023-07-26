<?php
/**
 * Create a table of all the custom forms.
 */
function list_custom_forms(){
    global $wpdb;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $forms = $wpdb->get_results("SELECT * FROM $table_name");
    //if there are no forms, display a message
    if(empty($forms)){
        echo '<tr>';
        echo '<td colspan="4">No forms found.</td>';
        echo '</tr>';
    }
    foreach($forms as $form){
        echo '<tr>';
        echo '<td>' . $form->form_name . '</td>';
        //Create view fields btn
        echo '<td><input type="button" class="btn btn-primary" value="View Fields" onclick="getFormFields('.$form->id.')" /></td>';
        //Create edit btn
        echo '<td><input type="button" class="btn btn-primary" value="Edit" onclick="editForm('.$form->id.')" /></td>';
        //Create delete btn
        echo '<td><input type="button" class="btn btn-danger" value="Delete" onclick="deleteForm('.$form->id.')"/></td>';
        echo '</tr>';
    }
}

function deleteForm(){
    //Check that the user has submitted a form
    if(!isset($_POST['delete_form'])){
        return;
    }
    //Check nonce
    if(!wp_verify_nonce( $_POST['delete_form_nonce'], 'delete_form' )){
        return;
    }
    //Get id
    if(!isset($_POST['form_id'])){
        
        return;
    }
    $form_id = $_POST['form_id'];
    global $wpdb;
    $formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $success = $wpdb->delete($formsTable, array('id' => $form_id));
    if(!$success){
        http_response_code(500);
        call_js_fn_onload('showToast(0, "Error", '.$wpdb->last_error.')');
        exit;
    }
    if($wpdb->rows_affected == 0){
        http_response_code(204);
        call_js_fn_onload('showToast(0, "Error", "Form not found.")');
        exit;
    }
    call_js_fn_onload('showToast(1, "Success", "Form deleted successfully.")');
}
deleteForm();
?>
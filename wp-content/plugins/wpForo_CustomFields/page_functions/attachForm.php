<?php
/**
 * Create a table with all the forums and their attached forms.
 */
function print_attachTable(){
    global $wpdb;
    $forumsTable = $wpdb->prefix . 'wpforo_forums';
    $formsTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORMS'];
    $junctionTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    //Get all forum names and their attached forms if they have any
    //Forums that don't have any forms attached will be displayed as well
    $forums = $wpdb->get_results("SELECT $forumsTable.forumid,title,form_id,form_name FROM $forumsTable LEFT JOIN $junctionTable ON $forumsTable.forumid = $junctionTable.forum_id LEFT JOIN $formsTable ON $junctionTable.form_id = $formsTable.id");
    $forms = $wpdb->get_results("SELECT id,form_name FROM $formsTable");
    //Tranform the results into an array of objects with only the id and form_name
    $forms = array_map(function($form){
        return (object) array('id' => $form->id, 'form_name' => $form->form_name);
    }, $forms);
    foreach($forums as $forum){
        $forumName = $forum->title;
        $formName = $forum->form_name;
        echo "<tr><td data-id='$forum->forumid'>$forumName</td>";
        if($formName == null){
            echo "<td>None</td>";
        }else{
            echo "<td data-id='$forum->form_id'>$formName</td>";
        }
        //edit button
        echo "<td><button type='button' class='btn btn-primary' onclick='enableEdit(this)'>Edit</button></td>";
        echo "</tr>";
    }
    injectObject('forms', $forms);
}
/**
 * Process the submitted form.
 */
function processForm(){
    //Check if form was submitted
    if(!isset($_POST['Save_Changes'])){
        return;
    }
    //Check nonce
    if(!isset($_POST['custom_fields_nonce']) || !wp_verify_nonce($_POST['custom_fields_nonce'], 'custom_fields_nonce_action')){
        return;
    }
    //Get the form data
    $dataString = stripslashes($_POST['forum_form_relations']);
    $relations = json_decode($dataString, true);
    //$error = json_last_error_msg(); //For debugging
    //Update the database
    global $wpdb;
    $junctionTable = $GLOBALS['CUSTOM_WPFORO_TABLES']['FORUM_FORMS'];
    $wpdb->query("DELETE FROM $junctionTable");
    foreach($relations as $relation){
        $wpdb->insert($junctionTable, array('forum_id' => $relation['forumId'], 'form_id' => $relation['formId']));
    }

}
processForm();
?>
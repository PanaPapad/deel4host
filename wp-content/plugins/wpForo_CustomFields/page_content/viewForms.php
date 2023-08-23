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
        echo '<tr id="formRow'.$form->id.'">';
        echo '<td>' . $form->form_name . '</td>';
        //Create view fields btn
        echo '<td><input type="button" class="btn btn-primary" value="View Fields" onclick="getFormFields('.$form->id.')" /></td>';
        //Create edit btn
        echo '<td><input type="button" class="btn btn-primary" value="Edit" onclick="editForm('.$form->id.')" /></td>';
        //Create delete btn
        echo '<td><input type="button" class="btn btn-danger" value="Delete" onclick="prepareDelete('.$form->id.')"/></td>';
        echo '</tr>';
    }
}
?>
<div class="wrap">
	<h2>View wpForo Forms</h2>
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="w-75" scope="col">Form Name</th>
				<th class="w-auto" scope="col">View Fields</th>
				<th class="w-auto" scope="col">Edit</th>
				<th class="w-auto" scope="col">Delete</th>
			</tr>
		</thead>
			<tbody>
				<?php list_custom_forms(); ?>
			</tbody>
	</table>
</div>
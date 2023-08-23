<?php
/**
 * Create a table with all the custom fields
 */
function listCreatedFields() {
    global $wpdb;
    $table_name = $GLOBALS['CUSTOM_WPFORO_TABLES']['FIELDS'];
    $custom_fields = $wpdb->get_results("SELECT * FROM $table_name");
    //Create table header
    echo '<tr>';
    echo '<th>Field Name</th>';
    echo '<th>Field Type</th>';
    echo '<th>Field Label</th>';
    echo '<th>Field Description</th>';
    echo '<th>Field Required</th>';
    echo '<th>Field Default</th>';
    echo '<th>Field Options</th>';
    echo '<th>Edit</th>';
    echo '<th>Delete</th>';
    echo '</tr>';
    if (empty($custom_fields)) {
        echo '<tr><td colspan="9">No custom fields created yet.</td></tr>';
        return;
    }
    for($i = 0; $i < count($custom_fields); $i++) {
        $field_name = $custom_fields[$i]->field_name;
        $field_type = $custom_fields[$i]->field_type;
        $field_label = $custom_fields[$i]->field_label;
        $field_description = $custom_fields[$i]->field_description;
        $field_required = $custom_fields[$i]->field_required;
        //Cast to string
        $field_required = $field_required == '0' ? "Yes" : "No";
        $field_default = $custom_fields[$i]->field_default_value;
        $field_options = $custom_fields[$i]->field_options;

        $edit_url = add_query_arg('field_id', $custom_fields[$i]->id, admin_url('admin.php?page=custom-wpforo-fields-edit'));
        $delete_url = add_query_arg('delete_field', $custom_fields[$i]->id, admin_url('admin.php?page=custom-wpforo-fields-edit'));

        //create table row
        echo '<tr>';
        echo '<td>' . $field_name . '</td>';
        echo '<td>' . $field_type . '</td>';
        echo '<td>' . $field_label . '</td>';
        echo '<td>' . $field_description . '</td>';
        echo '<td>' . $field_required . '</td>';
        echo '<td>' . $field_default . '</td>';
        if ($field_type != 'select' && $field_type != 'radio') {
            $field_options = 'N/A';
        }
        echo '<td>' . $field_options . '</td>';
        echo '<td><a href="' . $edit_url . '">Edit</a></td>';
        echo '<td><a class="disableClick" href="' . $delete_url . '">Delete</a></td>';
        echo '</tr>';
    }
}
?>
<div class="wrap">
    <h2>Custom wpForo Fields</h2>
    <table id="fieldsTable" class="table table-striped table-hover">
        <?php
        listCreatedFields();
        ?>
    </table>
    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Warning</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="modalMessage" class="modal-body">
                    Are you sure you want to delete this field?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmBtn" type="button" class="btn btn-primary">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
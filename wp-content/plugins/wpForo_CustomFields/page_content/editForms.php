<?php
//JS files to inject
//JS files need to be under the page_content/JS dir
$jsList = array(
    'globals.js',
    'editForms.js',
);
inject_js($jsList);
// CSS files to inject
// CSS files need to be under the page_content/CSS dir
$cssList = array(
    'globals.css',
);
inject_css($cssList);
?>
<div class="wrap">
    <!-- Create/Edit form -->
    <h2>Edit wpForo Forms</h2>
    <form id="creationForm" method="POST">
        <?php
        // Add nonce for security and authentication.
        wp_nonce_field('custom_forms_nonce_action', 'custom_forms_nonce');
        ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="form_name">Form Name:</label>
                <input type="text" id="form_name" name="form_name" class="form-control" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <select id="fieldSelect" class="form-control">
                    <option value="-1" selected>Select Field</option>
                    <?php list_field_options(); ?>
                </select>
                <input type="button" value="Add Field" class="btn btn-primary" id="addFieldBtn">
            </div>
        </div>
        <div class="form-row">
            <div id="field_container" class="form-group col-md-6">
                <table class="table table-striped" id="fieldsTable">
                    <thead>
                        <th>Field ID</th>
                        <th>Field Name</th>
                        <th>Field Type</th>
                        <th>Remove</th>
                    </thead>
                    <tbody id="fieldsTableBody">
                        <!-- Table body will be populated by JS -->

                    </tbody>
                </table>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <input type="submit" value="Create/Save Custom Form" name="Add_Custom_WpForo_Form" class="btn btn-primary">
            </div>
        </div>
    </form>
</div>
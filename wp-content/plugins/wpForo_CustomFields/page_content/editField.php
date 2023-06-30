    <?php
    //JS files to inject
    //JS files need to be under the page_content/JS dir
    $jsList = array(
        'globals.js',
        'editFields.js',
    );
    inject_js($jsList);
    $cssList = array(
        'globals.css',
        'editFields.css',
    );
    inject_css($cssList)
    ?>
    <div class="wrap">
        <h2>Custom wpForo Fields</h2>
        <form method="post">
            <?php
            // Add nonce for security and authentication.
            wp_nonce_field('custom_fields_nonce_action', 'custom_fields_nonce');
            ?>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="field_name">Field Name:</label>
                    <input type="text" id="field_name" name="field_name" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="field_type">Field Type:</label>
                </div>
                <div class="form-group col-md-6">
                    <select id="field_type" name="field_type" class="form-control" required>
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="radio">Radio</option>
                        <option value="checkbox">Checkbox</option>
                        <option value="select">Select</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="field_label">Field Label:</label>
                    <input type="text" id="field_label" name="field_label" class="form-control" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="field_description">Field Description:</label>
                    <textarea id="field_description" name="field_description" class="form-control"></textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="field_default">Default Value:</label>
                    <input type="text" id="field_default" name="field_default" class="form-control">
                </div>
            </div>

            <div class="form-row align-items-start">
                <div class="form-group col-md-6">
                    <div class="form-check">
                        <input type="checkbox" id="field_required" name="field_required" class="form-check-input">
                        <label for="field_required" class="form-check-label">Required</label>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="field_options">Options (for select, radio, and checkbox):</label>
                    <input type="text" id="field_options" name="field_options" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <input type="submit" value="Add/Save Custom Field" name="Add_Custom_WpForo_Field" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
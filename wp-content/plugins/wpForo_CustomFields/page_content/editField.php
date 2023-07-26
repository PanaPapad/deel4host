<?php
//JS files to inject
//JS files need to be under the page_content/JS dir
$jsList = array(
	'globals.js',
	'editFields.js',
);
inject_js( $jsList );
$cssList = array(
	'globals.css',
	'editFields.css',
);
inject_css( $cssList )
	?>

<div class="wrap">
	<h2>Custom wpForo Fields</h2>
	<form method="post">
		<?php
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_fields_nonce_action', 'custom_fields_nonce' );
		?>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label for="field_name">Field Name:</label>
				<input title="The name of the field." type="text" id="field_name" name="field_name" class="form-control" required>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label title="Field input type." for="field_type">Field Type:</label>
			</div>
			<div class="form-group col-md-2">
				<select id="field_type" name="field_type" class="form-control" required>
					<option value="text">Text</option>
					<option value="tinymce">Tiny MCE</option>
					<option value="textarea">Textarea</option>
					<option value="tel">Telephone</option>
					<option value="file">File</option>
					<option value="url">URL</option>
					<option value="radio">Radio</option>
					<option value="checkbox">Checkbox</option>
					<option value="select">Select</option>
				</select>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label for="field_label">Field Label:</label>
				<input title="The text to be displayed on the field label." type="text" id="field_label" name="field_label" class="form-control" required>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label for="field_placeholder">Field Placeholder:</label>
				<input title="Placeholder text for field." type="text" id="field_placeholder" name="field_placeholder" class="form-control">
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<label for="field_fa-icon">Field Icon: <a rel="noopener noreferrer" target="_blank" href="https://fontawesome.com/icons">See Icons</a></label>
				<input title="Font-Awsome icon to be displayed next to the field label. Leave blank for no icon." placeholder="fa-solid fa-bars"  type="text" id="field_fa_icon" name="field_fa_icon" class="form-control">
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4" hidden>
				<label for="field_description">Field Description:</label>
				<textarea title="Brief description for the field." id="field_description" name="field_description" class="form-control" hidden></textarea>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2" hidden>
				<label for="field_default">Default Value:</label>
				<input title="Default value for the field." type="text" id="field_default" name="field_default" class="form-control" hidden>
			</div>
		</div>

		<div class="form-row align-items-start">
			<div class="form-group col-md-2">
				<div class="form-check">
					<input title="Check if field is required to be filled" type="checkbox" id="field_required" name="field_required" class="form-check-input">
					<label for="field_required" class="form-check-label">Required</label>
				</div>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-4">
				<label for="field_options">Options (for select, checkbox and radio):</label>
				<input title="Value options for the field. Values are comma separated" type="text" id="field_options" name="field_options" class="form-control" disabled>
			</div>
		</div>

		<div class="form-row">
			<div class="form-group col-md-2">
				<input type="submit" value="Add/Save Custom Field" name="Add_Custom_WpForo_Field"
					class="btn btn-primary">
			</div>
		</div>
	</form>
</div>
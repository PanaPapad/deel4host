<div class="wrap">
	<h2>Custom wpForo Fields</h2>
	<div class="form-row">
		<div class="form-group col-md-2">
			<label for="field_name">Field Name:</label>
			<input title="The name of the field." type="text" id="field_name" name="field_name" class="form-control"
				required>
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
			<input title="The text to be displayed on the field label." type="text" id="field_label" name="field_label"
				class="form-control" required>
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-2">
			<label for="field_placeholder">Field Placeholder:</label>
			<input title="Placeholder text for field." type="text" id="field_placeholder" name="field_placeholder"
				class="form-control">
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-2">
			<label for="field_fa-icon">Field Icon: <a rel="noopener noreferrer" target="_blank"
					href="https://fontawesome.com/icons">See Icons</a></label>
			<input title="Font-Awsome icon to be displayed next to the field label. Leave blank for no icon."
				placeholder="fa-solid fa-bars" type="text" id="field_fa_icon" name="field_fa_icon" class="form-control">
		</div>
	</div>

	<div class="form-row align-items-start">
		<div class="form-group col-md-2">
			<div class="form-check">
				<input title="Check if field is required to be filled" type="checkbox" id="field_required"
					name="field_required" class="form-check-input">
				<label for="field_required" class="form-check-label">Required</label>
			</div>
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-4">
			<label for="field_options">Options (for select, checkbox and radio):</label>
			<input title="Value options for the field. Values are comma separated" type="text" id="field_options"
				name="field_options" class="form-control" disabled>
		</div>
	</div>

	<div class="form-row">
		<div class="form-group col-md-2">
			<button id="submitBtn" type="button" class="btn btn-primary">Save Custom Field</button>
		</div>
	</div>
</div>
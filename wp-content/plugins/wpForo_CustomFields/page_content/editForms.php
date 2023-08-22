<div class="wrap">
	<!-- Create/Edit form -->
	<h2>Edit wpForo Forms</h2>
	<div class="row">
		<div class="col-md-3">
			<label for="form_name">Form Name:</label>
			<input type="text" id="form_name" name="form_name" class="form-control" required>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<select title="Select a Field" id="fieldSelect" class="form-control">
				<option value="-1" selected>Select Field</option>
			</select>
		</div>
		<div class="col-md-1">
			<button type="button" class="btn btn-primary" id="addFieldBtn">Add Field</button>
		</div>
	</div>
	<div class="row">
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
			<button id="saveFormBtn" type="button" class="btn btn-primary">Save Form</button>
		</div>
	</div>
</div>
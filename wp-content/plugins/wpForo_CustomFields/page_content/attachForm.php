<div class="wrap">
	<!-- Table of forums and their attached forms -->
	<h1>Forums - Forms</h1>
	<table id="relationsTable" class="table table-striped">
		<thead>
			<tr>
				<th class='col-6'>Forum</th>
				<th class='col-5'>Form</th>
				<th class='col'>Edit</th>
			</tr>
		</thead>
		<tbody>
			<?php
			print_attachTable();
			?>
		</tbody>
	</table>
	<?php
	//Example php code injection. Best used for small snippets.
	//Define functions in page_functions/*.php
	//example_function_call();
	?>
	<form id="submitForm" method="POST">
		<?php
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_fields_nonce_action', 'custom_fields_nonce' );
		?>
		<input type="submit" value="Save Changes" name="Save Changes" class="btn btn-primary">
	</form>
</div>
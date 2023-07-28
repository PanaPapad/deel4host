<div class="wrap">
	<h2>View wpForo Forms</h2>
    <form method="POST" id="delete-form">
        <?php wp_nonce_field( 'delete_form', 'delete_form_nonce' ); ?>
    </form>
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
<div class="wrap">
	<!-- Table of forums and their attached forms -->
	<h1>Forums - Forms</h1>
	<table id="relationsTable" class="table table-striped">
		<thead>
			<tr>
				<th class='col-6'>Forum</th>
				<th class='col-6'>Form</th>
				<!-- <th class='col'>Edit</th> -->
			</tr>
		</thead>
		<tbody id="relationsTableBody">
			<?php
			print_attachTable();
			?>
		</tbody>
	</table>
	<!-- Save changes button -->
	<button id="saveChangesBtn" class="btn btn-primary" onclick="saveChanges()">Save Changes</button>
</div>
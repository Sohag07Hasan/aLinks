<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2> Manage your bulk operations here </h2>
	
	<?php 
		if($_POST['alinks-bulk-operation-submitted'] == "Y"){
			echo "<div class=\"updated\"> <p> $msg </p> </div>";			
		}
	?>
	
	<form action="" method="post">
	
		<input type="hidden" name="alinks-bulk-operation-submitted" value="Y" />
		
		<table class="form-table">
			<tr>
				<td colspan="2"> Manage your bulk operations here. Please select an operation. Before doing anything please make sure</td>
			</tr>
			
			<tr>
				<td>
					<!-- 
						<input type="radio" id="alinks-drafts" name="alinks-bulk-operation" value="1"> <label for="alinks-drafts"> Drafts </label> <br/>
					
						<input type="radio" id="alinks-trash" name="alinks-bulk-operation" value="2"> <label for="alinks-trash"> Trash </label> <br/>
					
						<input type="radio" id="alinks-publish" name="alinks-bulk-operation" value="3"> <label for="alinks-publish"> Publish </label> <br/>
					 -->
					<input type="radio" id="alinks-delete" name="alinks-bulk-operation" value="4"> <label for="alinks-delete"> Bulk Delete <br/> <code>By using this option you will delete ALL.  (This cannot be undone) </code> </label> <br/>
				</td>
			</tr>
			
			<tr>
				<td> <input type="submit" value="proceed" class="button-primary"> </td>
			</tr>
				
		</table>
		
	</form>
	
</div>
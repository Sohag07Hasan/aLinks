<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Import and Export</h2>
	
	<?php
		self::print_log();
	 ?>
	
	<form action="" method="post" enctype="multipart/form-data">
		<table class="form-table">
			<tr>
				<td> Please Upload a XML file  </td>
			</tr>
			<tr>
				<th> <input type="file" name="alinks-FileUpload" > </th>
				<td> <input type="submit" value="Upload" class="button-secondary"> </td>
			</tr>
		</table>
	</form>
	
</div>
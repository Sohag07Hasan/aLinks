<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Import and Export</h2>
	
	<?php
		self::print_log();
	 ?>
	
	<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="alinks-xml-type" value="1" />	
		<table class="form-table">
			<tr>
				<td colspan="2"> <h4>Type1 : <a href="<?php echo $xml_type_1?>" target="_blank">Click</a> here for sample <h4> </td>
			</tr>
			<tr>
				<th> <input type="file" name="alinks-FileUpload" > </th>
				<td> <input type="submit" value="Upload" class="button-secondary"> </td>
			</tr>
		</table>
	</form>
	<br/>
	<hr/>
	
	<form action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="alinks-xml-type" value="2" />
		<table class="form-table">
			<tr>
				<td colspan="2"> <h4>Type 2 : <a href="<?php echo $xml_type_2?>" target="_blank">Click</a> here for sample <h4> </td>
			</tr>
			<tr>
				<th> <input type="file" name="alinks-FileUpload" > </th>
				<td> <input type="submit" value="Upload" class="button-secondary"> </td>
			</tr>
		</table>
	</form>
	
</div>
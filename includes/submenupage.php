<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>Global Settings for aLinks</h2>
	
	<?php 
		if($_POST['aLinks-options-save'] == "Y"):
			echo "<div class=\"updated\"><p>Saved.....</p></div>";
		endif;
	?>
	
	<form action="" method="post">
		<input type="hidden" name="aLinks-options-save" value="Y" />
		<table class="form-table">
			<tr>
				<th> <label for="aLinks-maximumLinksperpost"> Maximum Links Per Post Per keyPhrase</label> </th>
				<td> <input type="text" name="aLinks-maximumLinksperpost" id="aLinks-maximumLinksperpost" value="<?php echo $options['max_link_p_post'];?>" /> </td>
				<td colspan="2"> <code> -1 is for the unlimited link</code> </td>
			</tr>
			
			<tr>
				<th> <label for="aLinks-maximumLinks"> Maximum Links for a keyPhrase throughout the site</label> </th>
				<td> <input type="text" name="aLinks-maximumLinks" id="aLinks-maximumLinks" value="<?php echo $options['max_links'];?>" /> </td>
				<td colspan="2"> <code> -1 is for the unlimited link</code> </td>
			</tr>
			
			<tr>
				<th> <label for="aLinks-radomizeLinks"> Want to randomize the links?</label> </th>
				<td> <input <?php checked('Y', $options['randomize']); ?> type="checkbox" name="aLinks-radomizeLinks" value="Y"> </td>
				<td colspan="2"> <code> it will take a random number between 1 to the "maximum links per post per keyPhrase" </code> </td>
			</tr>
			
			<tr>
				<th> <lable for="aLinks-rowurl-position"></lable> Position or the raw url </th>
				<td> 
					<select name="aLinks-rowurl-position">
						<option <?php selected("0", $options['raw_url_position']); ?> value="0">Begining</option>
						<option <?php selected("1", $options['raw_url_position']); ?> value="1">Middle</option>
						<option <?php selected("2", $options['raw_url_position']); ?> value="3">End</option>
					</select> 
				</td>
				<td colspan="2"> <code>This service is currently unavailable</code> </td>
			</tr>
			
			<tr>
				<td> <input type="submit" value="Save" class="button-primary" /> </td>
			</tr>
		</table>
	</form>
</div>
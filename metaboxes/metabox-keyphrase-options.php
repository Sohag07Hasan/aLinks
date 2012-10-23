<!-- 
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#aLinks-random-option').click(function(){
			
		});
	});
	
</script>
-->
<p><input <?php checked($options[self::metakey_option], "1");?> type="radio" name="<?php echo self::metakey_option?>" value="1" /> Anchored </p>
<p><input <?php checked($options[self::metakey_option], "2");?> type="radio" name="<?php echo self::metakey_option?>" value="2" /> Raw URL </p>
<p><input <?php checked($options[self::metakey_option], "3");?> type="radio" name="<?php echo self::metakey_option?>" value="3" /> Changed </p>
<p>
	<input <?php checked($options[self::metakey_option], "4");?> id="aLinks-random-option" type="radio" name="<?php echo self::metakey_option?>" value="4" /> Random
	<select name="<?php echo self::metakey_randomness; ?>"> 
		<option <?php selected($options[self::metakey_randomness], "25")?> value="25"> 25% randomness </option>
		<option <?php selected($options[self::metakey_randomness], "50")?> value="50"> 50% randomness </option>
		<option <?php selected($options[self::metakey_randomness], "75")?> value="75"> 75% randomness</option>
		<option <?php selected($options[self::metakey_randomness], "100")?> value="100"> 100% randomness</option>
	</select>
</p>


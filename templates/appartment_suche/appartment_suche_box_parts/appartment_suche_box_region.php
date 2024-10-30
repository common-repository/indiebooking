<div class="ui-widget">
	<?php
	$regionFieldId = "rs_ib_region_input";
	if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
		$regionFieldId = $regionFieldId.$startpagenumber;
	}
	?>
	<select id="<?php echo $regionFieldId; ?>" name="search_location" class="formular_icon_region" placeholder="<?php _e("region", "indiebooking"); ?>">
        <option value=""></option>
        <?php
        foreach ($locationdesc as $loc) { ?>
    		<option value="<?php echo esc_attr($loc[0]);?>"><?php echo trim($loc[0]);?></option>
    <?php
		}
	?>
	</select>
</div>
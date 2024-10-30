<div class="col-xs-12 buchungsplugin_startseite_filter_form form_icon icon_feature">
	<?php
	$featureFieldId = "search_features";
	if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
		$featureFieldId = $featureFieldId.$startpagenumber;
	}
	?>
	<select id="<?php echo $featureFieldId; ?>" name="search_features[]" multiple="multiple">
		<?php foreach ($features as $key => $feature) {
			     $select = "";
			     if ($feature == "0") {
			     	$feature = __("ALL", "indiebooking");
			     }  else { ?>
					<option value="<?php echo esc_attr($key); ?>">
						<?php echo $feature; ?>
					</option>
			<?php } ?>
		<?php
			}
			?>
    </select>
</div>
<?php
$anzPersonenFieldId = "search_anzahl_personen";
if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
	$anzPersonenFieldId = $anzPersonenFieldId.$startpagenumber;
}
?>
<div class="col-xs-12 buchungsplugin_startseite_filter_form form_icon icon_personen">
	<select id="<?php echo $anzPersonenFieldId; ?>" class="form-control"
		name="search_anzahl_personen[]" tabindex="1"><!-- multiple="multiple" -->
			<option value="0"><?php _e("Any number of people", 'indiebooking'); ?>
			<?php
			for ($i = 1; $i <= $maxAnzPersonen; $i++) { ?>
			    <option value="<?php echo esc_attr($i)?>">
			    	<?php echo $i; ?>
			    	</option>
				<?php } ?>
	</select>
</div>

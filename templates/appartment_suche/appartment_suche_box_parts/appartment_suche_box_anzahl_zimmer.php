<?php
$anzZimmerFieldId = "search_anzahl_zimmer";
if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
	$anzZimmerFieldId = $anzZimmerFieldId.$startpagenumber;
}
?>
<div class="col-xs-12 buchungsplugin_startseite_filter_form form_icon icon_zimmer"
		  <?php echo $hiddenAnzZimmer;?>>
	<select id="<?php echo $anzZimmerFieldId; ?>" name="search_anzahl_zimmer[]"><!-- multiple="multiple" -->
    	<option value="0"><?php _e("Number of Rooms", 'indiebooking'); ?></option>
    	<?php
    		for ($i = 1; $i <= $maxAnzZimmer; $i++) { ?>
    		    <option value="<?php echo esc_attr($i);?>">
    				<?php echo $i; ?>
    			</option>
    	<?php } ?>
    </select>
</div>
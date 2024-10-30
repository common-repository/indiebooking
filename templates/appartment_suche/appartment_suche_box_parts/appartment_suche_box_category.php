<div class="col-xs-12 buchungsplugin_startseite_filter_form form_icon icon_kategorie">
	<?php
	$categoryFieldId = "search_categorie";
	if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
		$categoryFieldId = $categoryFieldId.$startpagenumber;
	}
	?>
	<select id="<?php echo $categoryFieldId; ?>" name="search_categorie[]" multiple="multiple">
		<?php
		foreach ($categories as $categorieName) {
			     $value  = $categorieName;
			     $select = "";
			     if ($categorieName == "0") {
			     	?>
			     	<?php
// 			         $categorieName = __("ALL", "indiebooking");
			     } else { ?>
				<option value="<?php echo esc_attr($value); ?>">
					<?php echo $categorieName; ?>
				</option>
			<?php } ?>
		<?php
		}
		?>
    </select>
</div>
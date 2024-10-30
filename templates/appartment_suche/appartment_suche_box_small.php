<?php
/*
* Indiebooking - die Buchungssoftware fuer Ihre Homepage!
 * Copyright (C) 2016  ReWa Soft GmbH
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
 */
?>
<?php
    $fullyBookedDays    = json_encode($fullyBookedDays);
    $freeBookingRanges  = json_encode($freeBookingRanges);
?>
<span id="synchronize_zabuto_calendars" data-synchronize="true"></span><!-- wird fuer die uebersichit gebraucht -->
<div class="container-full background_darkgreen">
    <div class="container buchungsplugin_unterseite_datum">
        <div class="form-group">
            <div class="row rs_ib_floating_datepicker_container"
            	data-notbookable='<?php echo esc_attr($fullyBookedDays); ?>'
            	data-freeranges='<?php echo esc_attr($freeBookingRanges); ?>'>
            	
                <div class="col-sm-6 col-xs-12">
                    <h4 class="buchungsplugin_h5"><?php _e("Choose your booking date", 'indiebooking');?></h4>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <div class="input-group">
                        <input type="email" readonly="true"
                        		class="rs_ib_floating_datepicker rs_ib_floating_datepicker_from form-control form_icon icon_kalender rewa_datepicker"
                        		id="search_booking_date_from"
                        		value="<?php echo esc_attr($from); ?>"
                        		placeholder="<?php _e("arrival date", "indiebooking"); ?>">
                        <span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                    <div class="input-group">
                        <input type="email" readonly="true"
                        		class="rs_ib_floating_datepicker rs_ib_floating_datepicker_to form-control form_icon icon_kalender rewa_datepicker"
                        		id="search_booking_date_to"
                        		value="<?php echo esc_attr($to); ?>"
                        		placeholder="<?php _e("departure", "indiebooking"); ?>">
                        <span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-full background_grey">
    <div class="container buchungsplugin_unterseite_filter">
        <div class="form-group">
            <div class="row">
                <div class="col-sm-2 hidden-xs">
                    <h5 class="buchungsplugin_h5"><?php _e("Filter", "indiebooking");?></h5>
                </div>
                <div class="col-sm-1 col-xs-6 pull-right">
                    <div class="form-group">
                        <a id="btnSearchAppartment" class="btn green ibfkt_indiebooking_searchApartmentBtn pull-right"  href="#" role="button">
                        	<?php _e("Search", 'indiebooking');?>
                    	</a>
                    </div>
                </div>
                <div class="col-sm-3 hidden-xs pull-right" <?php echo $hiddenRegion;?>>
                   	<div id="rs_inddiebooking_small_autocomplete_region" class="ui-widget">
                    	<select id="rs_ib_region_input" name="search_location" class="formular_icon_region" placeholder="<?php _e("region", "indiebooking"); ?>">
                    		<option value=""></option>
                        	<?php
                        	   foreach ($locationdesc as $loc) {
                        	       $select = "";
                        	       if ($loc[0] == $searchLoc) {
                        	           $select = "selected='selected'";
                        	       }
                        	       ?>
                        	       <option value="<?php echo esc_attr($loc[0]);?>" <?php echo $select; ?>><?php echo trim($loc[0]);?></option>
                        	   <?php }
                        	?>
                    	</select>
                	</div>
            	</div>
            	<div class="col-sm-2 col-xs-6 pull-right" <?php echo $hiddenCategory; ?>>
                    <div class="buchungsplugin_unterseite_filter_formheader"><?php _e('Category', 'indiebooking');?></div>
            		<div class="col-xs-no-padding col-xs-9 buchungsplugin_unterseite_filter_form">
                        <select id="search_categorie" name="search_categorie[]" multiple="multiple">
                			<?php foreach ($categories as $categorieName) {
                			     $value      = $categorieName;
                			     $select     = "";
                			     if ($categorieName == "0") {
                			         $categorieName = __("ALL", "indiebooking");
                			     } else {
                    			     $select     = "";
                    			     if (isset($selCategory) && is_array($selCategory)) {
                        			     if (in_array(strval($value), $selCategory)) {
                        			         $select = "selected='selected'";
                        			     }
                    			     }
                        			?>
                        			<option value="<?php echo esc_attr($value);?>"<?php echo $select; ?>>
                        				<?php echo $categorieName; ?>
                    				</option>
                			<?php
                			     }
                			}
                			?>
                        </select>
                    </div>
            	</div>
            	<div class="col-sm-2 col-xs-6 pull-right" <?php echo $hiddenFilterFeatures; ?>>
                    <div class="buchungsplugin_unterseite_filter_formheader"><?php _e('Feature', 'indiebooking');?></div>
            		<div class="col-xs-no-padding col-xs-9 buchungsplugin_unterseite_filter_form">
                        <select id="search_features" name="search_features[]" multiple="multiple">
                            <?php
                            foreach ($features as $key => $feature) {
                            	$select = "";
                            	if ($feature == "0") {
                            		$feature = __("ALL", "indiebooking");
                            	}  else {
                            		if (isset($selFeatures) && is_array($selFeatures)) {
                            			if (in_array(strval($key), $selFeatures)) {
                            				$select = "selected='selected'";
                            			}
                            		}
                            		?>
    			         			<option value="<?php echo esc_attr($key); ?>"<?php echo $select; ?>>
    			         				<?php echo $feature; ?>
			         				</option>
    			     			<?php
                            	} ?>
                            	<?php
                            }
                            ?>
                        </select>
                    </div>
            	</div>
                <div class="col-sm-2 col-xs-6 pull-right" <?php echo $hiddenAnzZimmer; ?>>
            		<div class="buchungsplugin_unterseite_filter_formheader"><?php _e('Rooms', 'indiebooking');?></div>
            		<div class="col-xs-no-padding col-xs-9 buchungsplugin_unterseite_filter_form">
                        <select id="search_anzahl_zimmer" name="search_anzahl_zimmer[]"><!-- multiple="multiple" -->
                        	<option value="0"><?php _e("No Entry", 'indiebooking'); ?></option>
                			<?php
                			for ($i = 1; $i <= $maxAnzZimmer; $i++) {
                			     $select = "";
                			     if (in_array(strval($i), $selAnzRoom)) {
                			         $select = "selected='selected'";
                			     }
                			    ?>
                			    <option value="<?php echo esc_attr($i);?>"<?php echo $select; ?>>
                			    	<?php echo $i; ?>
            			    	</option>
            				<?php } ?>
                        </select>
                    </div>
            	</div>
				<div class="col-sm-2 col-xs-6 pull-right" <?php echo $hiddenAnzPersonen; ?>>
                    <div class="buchungsplugin_unterseite_filter_formheader"><?php _e("Persons", "indiebooking");?></div>
            		<div class="col-xs-no-padding col-xs-9 buchungsplugin_unterseite_filter_form">
                    	<select id="search_anzahl_personen" name="search_anzahl_personen[]" tabindex="1"><!-- multiple="multiple" -->
                			<option value="0"><?php _e("No Entry", 'indiebooking'); ?></option>
                			<?php
                			for ($i = 1; $i <= $maxAnzPersonen; $i++) {
                			    $select = "";
                			    if (strcmp($selNrGuest,$i) == 0) {
                			        $select = "selected='selected'";
                			    }
                			    ?>
                			    <option value="<?php echo esc_attr($i)?>"<?php echo $select; ?>>
                			    	<?php echo $i; ?>
            			    	</option>
            				<?php } ?>
                    	</select>
                	</div>
            	</div>
            </div>
        </div>
    </div>
</div>
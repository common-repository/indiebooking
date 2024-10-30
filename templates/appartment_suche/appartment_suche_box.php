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
    
    $includeIndex		= 0;
    $includeIndex2		= 0;
    $rsib_includeFilter	= array();
    
    $showAnzPerson		= empty($hiddenAnzPersonen);
    $showAnzZimmer		= empty($hiddenAnzZimmer);
    $showFilterFeature	= empty($hiddenFilterFeatures);
    $showRegion			= empty($hiddenRegion);
    $showCategory		= empty($hiddenCategory);
    
    if ($showAnzPerson) {
    	$rsib_includeFilter[$includeIndex][$includeIndex2++] = "appartment_suche_box_parts/appartment_suche_box_anzahl_personen.php";
    	if ($includeIndex2 == 2) {
    		$includeIndex++;
    		$includeIndex2 = 0;
    	}
    }
    if ($showAnzZimmer) {
    	$rsib_includeFilter[$includeIndex][$includeIndex2++] = "appartment_suche_box_parts/appartment_suche_box_anzahl_zimmer.php";
    	if ($includeIndex2 == 2) {
    		$includeIndex++;
    		$includeIndex2 = 0;
    	}
    }
    if ($showFilterFeature) {
    	$rsib_includeFilter[$includeIndex][$includeIndex2++] = "appartment_suche_box_parts/appartment_suche_box_features.php";
    	if ($includeIndex2 == 2) {
    		$includeIndex++;
    		$includeIndex2 = 0;
    	}
    }
    if ($showRegion) {
    	$rsib_includeFilter[$includeIndex][$includeIndex2++] = "appartment_suche_box_parts/appartment_suche_box_region.php";
    	if ($includeIndex2 == 2) {
    		$includeIndex++;
    		$includeIndex2 = 0;
    	}
    }
    if ($showCategory) {
    	$rsib_includeFilter[$includeIndex][$includeIndex2++] = "appartment_suche_box_parts/appartment_suche_box_category.php";
    	if ($includeIndex2 == 2) {
    		$includeIndex++;
    		$includeIndex2 = 0;
    	}
    }
    
    $allDates = json_encode($apavailabledates);
?>
<div id="buchungsplugin_startseite_such_box" class="ibfkt_indiebooking_search_box_container container">
	<div class="row rowpadding">
		<div class="col-lg-7 col-md-8 col-sm-10 background_darkgreen" style="opacity:0.95;">
			<div class="row">
				<div class="col-sm-8 inner_padding">
					<div class="headline"><?php
						_e("Search and book", "indiebooking");
						?>
					</div><br>
					<div class="form-group">
						<?php
						$searchBookingDateFormFrom 		= "search_booking_date_from";
						$searchBookingDateFormTo 		= "search_booking_date_to";
                        if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
                        	$searchBookingDateFormFrom 	= $searchBookingDateFormFrom.$startpagenumber;
                        	$searchBookingDateFormTo 	= $searchBookingDateFormTo.$startpagenumber;
                        }
                        ?>
						<div class="row rs_ib_floating_datepicker_container"
								data-notbookable='<?php echo esc_attr($fullyBookedDays); ?>'
								data-freeranges='<?php echo esc_attr($freeBookingRanges); ?>'>
							<div class="col-xs-6">
								<div class="input-group">
									<input type="email" readonly="true"
										id="<?php echo $searchBookingDateFormFrom; ?>"
										class="rs_ib_floating_datepicker rs_ib_floating_datepicker_from form-control form_icon icon_kalender rewa_datepicker"
										value="<?php echo esc_attr($from); ?>"
										placeholder="<?php _e("arrival date", "indiebooking"); ?>">
									<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
								</div>
							</div>
                            <div class="col-xs-6">
                            	<div class="input-group">
                            		<input type="email" readonly="true"
                            			id="<?php echo $searchBookingDateFormTo; ?>"
                            			class="rs_ib_floating_datepicker rs_ib_floating_datepicker_to form-control form_icon icon_kalender rewa_datepicker"
                            			value="<?php echo esc_attr($to); ?>"
                            			placeholder="<?php _e("departure", "indiebooking"); ?>">
                            		<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
                        		</div>
                            </div>
						</div>
					</div>
					<?php
					foreach ($rsib_includeFilter as $filterarray) {
						?>
						<div class="form-group">
							<div class="row">
							<?php
							foreach ($filterarray as $filter) { ?>
								<div class="col-xs-6">
									<?php include ($filter);?>
								</div>
								<?php
							}
							?>
							</div>
						</div>
					<?php
					}
					?>
					<div class="form-group">
                        <p>
                        	<?php
                        	$btnSearchApartmentId 		= "btnSearchAppartment";
                        	$ib_calender_div			= "buchungsplugin_startseite_verfuegbarkeit";
                        	$ib_calendar_id				= "rs-indiebooking-full-startpage-calendar"; //"my-calendar-buchungsplugin";
                        	if (isset($startpagenumber) && !is_null($startpagenumber) && $startpagenumber > 1) {
                        		$btnSearchApartmentId 	= $btnSearchApartmentId.$startpagenumber;
                        		$ib_calender_div		= $ib_calender_div.$startpagenumber;
                        		$ib_calendar_id			= $ib_calendar_id.$startpagenumber;
                        	} else {
                        		$startpagenumber 		= 1;
                        	}
                        	?>
                        	<a id="<?php echo $btnSearchApartmentId; ?>" class="ibfkt_indiebooking_searchApartmentBtn pull-left"
                        		href="#" data-ib-startpagenumber="<?php echo $startpagenumber; ?>" role="button">
                        		<?php _e("Search / Check Availability", 'indiebooking');?>
                        	</a>
                    	</p>
                        <p>
                        	<a id="btnBookMore" href="" class="btn btn-primary rs-ib-btn-search-result" role="button" style="display: none;">
                        		<?php _e("Check Availability and start booking", 'indiebooking'); ?>
                    		</a>
                		</p>
                    </div>
                    <div class="clear"></div>
				</div>
                <div id="<?php echo $ib_calender_div; ?>" class="col-sm-4 inner_padding  ibfc_single_zabuto_calendar">
                	<div class="headline"><?php _e("Availability", "indiebooking"); ?></div><br>
					<div id="<?php echo $ib_calendar_id; ?>" class="rs_ib_calendar_data_container"
							data-notbookable='<?php echo esc_attr($fullyBookedDays); ?>'
							data-freeranges='<?php echo esc_attr($freeBookingRanges); ?>'
							data-availablePeriods='<?php echo esc_attr($allDates); ?>'
							data-singleCalendar='1'>
					</div>
                </div>
			</div>
		</div>
	</div>
</div>
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
/**
 * Diese Datei ist das Template fuer die Anzeige eines ganzen Apartments.
 */
if (!isset($buchungVon)) {
    $buchungVon	 			= "";
}
if (!isset($buchungBis)) {
    $buchungBis 			= "";
}
if (!isset($appartmentId) || is_null($appartmentId)) {
    $appartmentId   		= get_the_ID();
}
$inquiry					= false;
$bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
if (isset($bookingInquiriesKz) && !is_null($bookingInquiriesKz) && $bookingInquiriesKz == "on") {
	$inquiry				= true;
}
if (!$inquiry) {
	$custom                 = get_post_custom( $appartmentId );
	$modelAppartment        = new RS_IB_Model_Appartment($custom, $appartmentId);
	$apartmentIsInquiry		= $modelAppartment->getOnlyInquire();
	if (isset($apartmentIsInquiry) && !is_null($apartmentIsInquiry) && $apartmentIsInquiry == "on") {
		$inquiry			= true;
	}
}


$isBookable 				= apply_filters('rs_indiebooking_is_apartment_bookable', get_the_ID()); ?>

<div class="appartment_box item_box" data-appartmentId="<?php echo esc_attr(get_the_ID()); ?>">
    <article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class("rsappartment_buchung"); ?>
    		data-appartmentId="<?php echo esc_attr(get_the_ID()); ?>">
    		
    	<div class="toggle_item">
        	<div class="entry-content">
        		<div id="booking_status_box_menu" data-pagekz="99" style="display: none;"></div>
                <div class="container">
                    <div class="row row-eq-height">
                        <div class="col-md-8 col-sm-7">
    						<?php do_action('rs_indiebooking_single_rsappartment_gallery'); ?>
                        </div>
                        <div class="col-md-4 col-sm-5">
							<?php do_action("rs_indiebooking_single_rsappartment_smallesprice");?>
                            <div class="apartment_detail_buchen background_lightgreen">
                                <h5><?php _e("availability", "indiebooking");?></h5>
                                <?php do_action('rs_indiebooking_single_rsappartment_dates'); ?>
                        	</div>
                        </div>
                    </div>
                </div>
        		<div class="container apartment_info_container">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="apartment_detail padding_all background_lightgreen">
                                <h5 id="subnav_buchung" class="anchor">
                                	<?php
                                	if (!$inquiry) {
                                		_e("book", "indiebooking");
                                	} else {
                                		_e("inquire", 'indiebooking');
                                	}
                                	?>
                                </h5>
                                <div class="form-group date_container">
									<?php do_action('rs_indiebooking_single_rsappartment_from_to_dates', $buchungVon, $buchungBis); ?>
                                </div>
                                <?php
                                    do_action('rs_indiebooking_show_apartment_booking_extra_info', $appartmentId);
                                ?>
                            </div>
                            <?php if ($isBookable) {?>
                            <div class="hidden-md hidden-lg margin-top">
                                <a href="#" class="btnAppartmentBuchen btn green pull-right">
                                	<?php
                                    if (!$inquiry) {
                                		_e("book", "indiebooking");
                                	} else {
                                		_e("inquire", 'indiebooking');
                                	}
                                	?>
                                </a>
                                <br>
                            </div>
                            <?php } ?>
                            <div class="apartment_detail">
                            	<?php do_action('rs_indiebooking_single_rsappartment_degression_data');?>
                            	<br class="clear">
                            </div>
                            <div class="apartment_detail">
							    <?php
							        $apid = get_the_ID();
							        do_action('rs_indiebooking_single_rsappartment_prices');
							    ?>
						    </div>
                        	<?php do_action('rs_indiebooking_single_rsappartment_description'); ?>
                        	<?php
                        	   do_action('rs_indiebooking_single_rsappartment_extra_infos');
                    	   ?>
                        </div>
                        <div class="col-md-4">
                            <nav class="nav-details" data-spy="affix" data-offset-top="800">
                                <ul>
									<?php do_action("rs_indiebooking_single_apartment_side_navigation", $appartmentId);?>
                                </ul>
    							<?php if ($isBookable) { ?>
                                <div class="hidden-xs margin-top">
                                    <a href="#" class="btnAppartmentBuchen btn green">
                                    <?php
	                                    if (!$inquiry) {
	                                		_e("book", "indiebooking");
	                                	} else {
	                                		_e("inquire", 'indiebooking');
	                                	}
                                	?>
                                    </a>
                                    <br>
                                </div>
            					<?php } ?>
                            </nav>
                        </div>
                    </div>
                </div>
        		<div class="appartment_head_content hidden">
            		<div class="section">
                        <div class="container">
                        	<div class="row">
                        		<div class="col-md-12">
                        			<div class="section">
                    					<div class="row">
                    						<div class="col-md-4"></div>
                    						<div class="col-md-4">
                    							<?php // do_action('rs_indiebooking_single_rsappartment_prices');?>
                    						</div>
                    						<div class="col-md-4">
                    						</div>
                    					</div>
                        			</div>
                        		</div>
                        	</div>
                        </div>
                    </div>
        		</div>
        	</div><!-- .entry-content -->
    	</div>
    </article><!-- #post -->
</div>

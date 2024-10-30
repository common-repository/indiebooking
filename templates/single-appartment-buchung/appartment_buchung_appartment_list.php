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
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/* @var $buchungKopf RS_IB_Model_Buchungskopf */
/* @var $teilkopf RS_IB_Model_Teilbuchungskopf */
?>
<article <?php post_class(); ?> data-appartmentId="<?php echo esc_attr($appartmentId);?>">
	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_appartment_header", $appartmentName);?>
	<?php do_action("rs_indiebooking_single_rsappartment_buchung_appartment_header", $appartmentId);?>
	<div class="toggle_item">
    	<div class="entry-content">
			<div>
                <div class="row">
                    <div class="col-md-7">
                        <?php do_action('rs_indiebooking_list_rsappartment_gallery', $appartmentId); ?>
    				</div>
    				<div class="col-md-5">
    					<div class="apartment_detail_buchen nopadding">
                            <h5><?php _e("Availability", 'indiebooking');?></h5>
    						<?php
    							do_action('rs_indiebooking_list_rsappartment_dates', $appartmentId);
    						?>
    					</div>
					</div>
    			</div>
                <div class="moreBookingInfoBox">
					<div class="apartment_detail padding_all nopadding">
            			<h5 style="padding-bottom:7px;"><?php _e("Appartment booking info", 'indiebooking'); ?></h5>
              			<?php do_action('rs_indiebooking_list_rsappartment_from_to_dates', $appartmentId); ?>
              			<?php do_action('rs_indiebooking_show_apartment_booking_extra_info', $appartmentId); ?>
                	</div>
                </div>
        		<div class="hidden"><!-- class="appartment_body_content container" -->
        			<?php do_action('rs_indiebooking_single_rsappartment_description', $appartmentId); ?>
        		</div>
			</div>
		</div>
	</div><!-- .entry-content -->
</article><!-- #post -->

<?php
/*
 * Indiebooking - the Booking Software for your Homepage!
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
/**
 * The default template for displaying a single Appartment_buchung
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */
/* @var $coupon RS_IB_Model_Gutschein */
if (isset($buchung)) {
    $postId = $buchung->getPostId();
} else {
    $postId = get_the_ID();
}
?>
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header", 3, $postId); ?>
<div id="booking_overview_box">
<div class="col-lg-3 col-md-3 col-sm-3 col-3 rs_ib_sidebar well">
	<?php do_action("rs_indiebooking_single_rsappartment_buchung_countdown", $postId); ?>
	<div id="current_price_box">
		<div class="small_modal"></div>
		<div id="current_price_box_data"></div>
	</div>
</div>
<div class="col-lg-9 col-md-9 col-sm-9 col-9">
    <article id="post-<?php echo $postId; ?>" <?php post_class(); ?>>
        <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo $postId;?>">
    	<div class="entry-content">
    		<?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons" ,$postId, 1); ?>
    		<br class="clear" />
    		<br />
            <div class="panel panel-default">
            	<div class="panel-heading">
            		<h3 class="panel-title"><?php _e("Booking Infos", 'indiebooking')?></h3>
            	</div>
            	<div class="panel-body">
    				<?php do_action("rs_indiebooking_single_rsappartment_buchung_time_range", $postId); ?>
    				<?php //do_action("rs_indiebooking_single_rsappartment_buchung_options", $postId); ?>
				</div>
			</div>
			<?php do_action("rs_indiebooking_single_rsappartment_buchung_detail_payment", $postId); ?>
			<div id="final_contact_box" class="panel panel-default">
            	<div class="panel-heading">
            		<h3 class="panel-title"><?php _e("Contact data", 'indiebooking')?></h3>
            	</div>
            	<div class="panel-body">
    				<?php //do_action("rs_indiebooking_single_rsappartment_buchung_contact", $postId, 'disabled="disabled"'); ?>
    				<?php do_action("rs_indiebooking_single_rsappartment_buchung_contact_data", $postId, 'disabled="disabled"'); ?>
    			</div>
    		</div>
<!--     		<a id="btnTestPreise">test</a> -->
			<?php //do_action("rs_indiebooking_single_rsappartment_buchung_full_prices", $postId); ?>
			
    		<?php
    // 			showBooking($buchung);
    		?>
            <div id="booking_box">
            	<?php
            	$buchungsStatus         = get_post_status($postId);
            	if ($buchungsStatus !== 'trash' && $buchungsStatus !== 'rs_ib-canceled') { ?>
                <span class="right_button">
                	<?php do_action("rs_indiebooking_single_rsappartment_buchung_payment_button", $postId);
                	/*
                        <a id="btnAppartmentZahlungspflichtigBuchen" class="btn btn-primary btn-lg"><?php _e("book with obligation to pay", 'indiebooking')?></a>
                	 */
                	?>
                </span>
                <?php } ?>
                <br class="clear" />
            </div>
    	</div><!-- .entry-content -->
<!--     	<div class="modal"></div> -->
    </article><!-- #post -->
</div>
</div>
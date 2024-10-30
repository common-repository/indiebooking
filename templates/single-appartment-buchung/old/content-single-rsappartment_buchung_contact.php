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
if (!isset($postId)) {
    $postId = get_the_ID();
}
?>
<?php //echo $postId; ?>
<?php //do_action("rs_indiebooking_single_rsappartment_buchung_countdown", $postId); ?>
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header", 2, $postId); ?>
<div class="col-lg-3 col-md-3 col-sm-3 col-3 rs_ib_sidebar">
    <?php do_action("rs_indiebooking_single_rsappartment_buchung_countdown", $postId); ?>
	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_time_range"); ?>
	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_options"); ?>
	<div id="current_price_box">
		<div class="small_modal"></div>
		<div id="current_price_box_data"></div>
	</div>
<!-- 	<a id="btnTestPreise">aktualisieren</a>	 -->
</div>
<div class="col-lg-9 col-md-9 col-sm-9 col-9">
    <article id="post-<?php echo $postId; ?>" <?php post_class(); ?>>
        <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo $postId;?>">
    	<div class="entry-content">
    		<?php //var_dump($buchungKopf); ?>
    		<?php //showBooking($buchung); ?>
    		<?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons"); ?>
    		<?php //do_action("rs_indiebooking_single_rsappartment_buchung_appartment_list"); ?>
    		<?php do_action("rs_indiebooking_single_rsappartment_buchung_zahlungsart", $postId); ?>
    		<?php do_action("rs_indiebooking_single_rsappartment_buchung_contact_data", $postId, ""); ?>
    		<?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons"); ?>
    </article><!-- #post -->
</div>

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
/* @var $coupon RS_IB_Model_Gutschein */
if (isset($buchung)) {
    $postId = $buchung->getPostId();
} else {
    $postId = get_the_ID();
}
?>
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header", 3, $postId); ?>
<div id="booking_overview_box">
	<div class="container">
        <div class="row margin-no">
            <div class="col-md-8 col-sm-7 col-xs-12 nopadding-left nopadding-right-xs">
                <article id="post-<?php echo $postId; ?>" <?php post_class(); ?>>
                    <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo esc_attr($postId);?>">
                    <div class="entry-content">
                        <div class="booking_overview_box">
                            <h2><?php _e("Booking Infos", 'indiebooking')?></h2>
                            <?php do_action("rs_indiebooking_single_rsappartment_buchung_time_range", $postId); ?>
                        </div>
                        <div class="booking_overview_box">
                            <h2><?php _e("detailed payment infos", 'indiebooking'); ?></h2>
                            <?php do_action("rs_indiebooking_single_rsappartment_buchung_detail_payment", $postId); ?>
                        </div>
                        <div class="booking_overview_box">
	                        <h2><?php _e("Custom message", 'indiebooking')?></h2>
							<textarea id="rs_indiebooking_booking_custom_message" rows="4" cols="50"
									maxlength="500" class="col-md-12 col-xs-12 booking_custom_message"></textarea>
							<div id="rs_indiebooking_charNum">
								<span class="countable">500</span>/500
							</div>
							<br class="clear" />
                        </div>
                        <div class="booking_overview_box">
                            <h2><?php _e("Contact data", 'indiebooking'); ?></h2>
                            <?php do_action("rs_indiebooking_single_rsappartment_buchung_contact_data", $postId, 'disabled="disabled"'); ?>
                        </div>
                    </div><!-- .entry-content -->
                </article><!-- #post -->
            </div>
            <div id="rightControllContainer" class="col-md-4 col-sm-5 col-xs-12 nopadding">
                <div class="background_lightgreen padding_all">
                    <?php do_action("rs_indiebooking_single_rsappartment_buchung_countdown", $postId); ?>
                </div>
                <div style="margin-top: 20px;">
                    <div id="booking_box" class="row" style="margin-bottom:20px;">
<!--                     	<div class="col-md-12 col-sm-12 col-xs-12"> -->
						<div>
	                        <?php
	                            $buchungsStatus         = get_post_status($postId);
	                            if ($buchungsStatus !== 'trash' && $buchungsStatus !== 'rs_ib-canceled') { ?>
	                                <span class="right_button1">
	                                    <?php do_action("rs_indiebooking_single_rsappartment_buchung_payment_button", $postId); ?>
	                                </span>
	                        <?php } ?>
	                    	<?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons" ,$postId, 1); ?>
                    	</div>
                    </div>
                    <div class="small_modal"></div>
                </div>
            </div>
        </div>
	</div><!-- container -->
</div><!-- #booking_overview_box -->
<!-- aus dem Buchungsheader -- stehen lassen!!! -->
</div><!-- row -->
</div><!-- main_container -->
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
?>
	<?php do_action("rs_indiebooking_single_rsappartment_buchung_header",1, get_the_ID()); ?>
    <div class="container">
        <div class="row margin-no">
            <div class="col-md-8 col-sm-7 col-xs-12 nopadding-left nopadding-right-xs">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo esc_attr(get_the_ID());?>">
                    <div class="entry-content">
                        <?php do_action("rs_indiebooking_single_rsappartment_buchung_appartment_list"); ?>
                    </div>
                </article><!-- #post -->
            </div>
            <div id="rightControllContainer" class="col-md-4 col-sm-5 col-xs-12 nopadding">
                <div class="background_lightgreen padding_all">
                    <?php do_action("rs_indiebooking_single_rsappartment_buchung_countdown", get_the_ID()); ?>
                    <div id="current_price_box">
                        <div class="small_modal"></div>
                        <div id="current_price_box_data"></div>
                    </div>
                </div>
                <div style="margin-top: 20px;">
                	<div class="small_modal"></div>
                    <?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons", get_the_ID()); ?>
                </div>
            </div>
        </div>
    </div>
<!-- aus dem Buchungsheader -- stehen lassen!!! -->
</div><!-- row -->
</div><!-- main_container -->

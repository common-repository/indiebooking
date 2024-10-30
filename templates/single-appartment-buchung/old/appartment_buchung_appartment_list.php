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
/* @var $buchungKopf RS_IB_Model_Buchungskopf */
/* @var $teilkopf RS_IB_Model_Teilbuchungskopf */
?>
<article <?php post_class(); ?> data-appartmentId="<?php echo $appartmentId?>">
    <?php //$ajax_nonce = wp_create_nonce( "my-special-string" );?>
	<?php do_action("rs_indiebooking_single_rsappartment_header", $appartmentName);?>
	<div class="toggle_item">
    	<div class="entry-content">
<!--     		<div class="appartment_head_content container"> -->
			<div>
    		<div class="col-lg-12 col-md-12 col-sm-12 col-12">
    			<!-- <h1><?php //echo $appartmentName;?></h1> -->
    			<div class="col-lg-9 col-md-9 col-sm-9 col-9">
    			<?php do_action('rs_indiebooking_list_rsappartment_gallery', $appartmentId); ?>
    			</div>
    			<div class="booking_info_box col-lg-3 col-md-3 col-sm-3 col-3">
    				<?php do_action('rs_indiebooking_list_rsappartment_dates', $appartmentId);
    				    $postType = get_post_type( get_the_ID() );
                        if ($postType == "rsappartment") {
                    ?>
    				<span class="right_button">
    					<div class="bottom_button">
    						<a class="btnAppartmentBuchen btn btn-primary btn-lg"><?php _e("start booking", 'indiebooking')?></a>
    					</div>
    				</span>
    				<?php } ?>
    				<br class="clear" />
    			</div>
                <div class="moreBookingInfoBox">
                	<div class="panel panel-default">
                		<div class="panel-heading">
                			<h3 class="panel-title"><?php _e("Appartment booking info", 'indiebooking'); ?></h3>
                		</div>
                    	<div class="panel-body">
                			<?php do_action('rs_indiebooking_list_rsappartment_options', $appartmentId); ?>
                            <!-- <a class="btnAppartmentBuchen btn btn-primary btn-lg"><?php //_e("start booking", 'indiebooking')?></a> -->
                    	</div>
                	</div>
                </div>
        		<div><!-- class="appartment_body_content container" -->
        			<?php do_action('rs_indiebooking_single_rsappartment_description', $appartmentId); ?>
        		</div>
			</div>
			</div>
        	<?php //do_action('rs_indiebooking_single_rsappartment_summary'); ?>
    		<div id="countdownBooking_1" class="countdown"></div>
    	</div><!-- .entry-content -->
	</div>
</article><!-- #post -->

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
?>
<?php //do_action("rs_indiebooking_single_rsappartment_buchung_countdown", get_the_ID()); ?>
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header",1, get_the_ID()); ?>
<div class="col-lg-3 col-md-3 col-sm-3 col-3 rs_ib_sidebar">
<!-- rs_indiebooking_single_rsappartment_buchung_countdown -->
    <?php do_action("rs_indiebooking_single_rsappartment_buchung_countdown", get_the_ID()); ?>
	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_time_range"); ?>
	<?php //do_action("rs_indiebooking_single_rsappartment_buchung_options"); ?>
	<div id="current_price_box">
		<div class="small_modal"></div>
		<div id="current_price_box_data"></div>
	</div>
	<a id="btnTestPreise">aktualisieren</a>
</div>
<div class="col-lg-9 col-md-9 col-sm-9 col-9">
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<!--     	<span class="right_button">                  -->
        	<!-- <a id="btnSaveBooking" class="btn btn-primary btn-lg btn_rewa"><?php //_e("next", 'indiebooking')?></a> -->
<!--         </span>  -->
        <?php //do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons", the_ID()); ?>
        <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php the_ID();?>">

    	<div class="entry-content">
    		<?php //var_dump($buchungKopf); ?>
    		<?php //showBooking($buchung); ?>
<!--     		<br /><br /> -->
    		<div class="col-lg-12 col-md-12 col-sm-12 col-12">
    			<?php do_action("rs_indiebooking_single_rsappartment_buchung_appartment_list"); ?>
    		</div>
    		<?php //do_action("rs_indiebooking_single_rsappartment_buchung_contact"); ?>
    <!--         <div id="booking_box"> -->
    <!--             <div id="full_price_display_box"> -->
    <!--                 <table id="full_price_display_table"></table>   -->
    <!--             </div>  -->
    <!--             <br class="clear" />           -->
    <!--         </div> -->
    <!-- <div class="col-lg-3 col-md-3 col-sm-3 col-3">Hier kommt die Sidebar hin</div> -->
    	</div><!-- .entry-content -->
    	<?php do_action("rs_indiebooking_single_rsappartment_buchung_controll_buttons", get_the_ID()); ?>
<!--     	<div class="modal"></div> -->
    </article><!-- #post -->
</div>

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
<article id="post-<?php get_the_ID(); ?>" <?php post_class(); ?>>
    <input type="hidden" id="appartmentPostId" name="appartmentPostId" value="">
    <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php the_ID();?>">
	<?php do_action("rs_indiebooking_single_rsappartment_buchung_header", 4, get_the_ID()); ?>
	<div class="alert alert-success" role="alert">
    	<strong><?php _e("Thanks for Booking.", 'indiebooking'); ?></strong>
    	<br />
    	<?php _e("Your BookingId is #".get_the_ID(), 'indiebooking'); ?>
    	<br />
    	<?php _e("You will get an Mail in the next 30 Minutes.", 'indiebooking');
    	?>
	</div>
</article><!-- #post -->
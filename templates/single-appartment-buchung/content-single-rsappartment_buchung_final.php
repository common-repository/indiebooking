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
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header", 4, get_the_ID()); ?>
<input id="rs_ib_link_to_start_page" type="hidden" value="<?php echo esc_url(home_url( '/' )); ?>" />
<div class="container" onload="rs_indiebooking_start_timeout_for_reload()">
    <article id="post-<?php echo esc_attr(get_the_ID()); ?>" <?php post_class(); ?>>
        <input type="hidden" id="appartmentPostId" name="appartmentPostId" value="">
        <input type="hidden" id="bookingPostId" name="bookingPostId" value="<?php echo esc_attr(get_the_ID());?>">
    	<div class="alert alert-success" role="alert">
        	<strong><?php
        	if (!$inquiry) {
        		_e("Thanks for Booking.", 'indiebooking');
        	} else {
        		_e("Thanks for your inquiry.", 'indiebooking');
        	}
        	?></strong>
        	<br />
        	<?php echo (__("Your BookingId is", 'indiebooking'))." #".$buchungNr; ?>
        	<br />
        	<?php _e("You will get an Mail in the next 30 Minutes.", 'indiebooking');
        	?>
    	</div>
    </article><!-- #post -->
</div><!-- container -->
<!-- aus dem Buchungsheader -- stehen lassen!!! -->
</div><!-- row -->
</div><!-- main_container -->
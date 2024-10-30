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
?>
<span class="right_button">
	<a class="btn lightgreen btnCancelBooking" style="margin-right:15px;"><?php _e("cancel", 'indiebooking')?></a>
	<?php if (!is_null($btnKz) && $btnKz != 1) { ?>
		<!--
		<a id="btnSaveBooking" class="btn green btnSaveBooking" style="margin-right:15px;">
			<?php //_e("next", 'indiebooking')?>
		</a>
		 -->
		<button id="btnSaveBooking" type="submit" class="btn green btnSaveBooking" style="margin-right:15px;">
			<?php _e("next", 'indiebooking')?>
		</button>
	<?php }
    //do_action("rs_indiebooking_show_booking_control_buttons", $postId);
	?>
</span>
<div class="indiebooking_express_payments">
<?php
	do_action("rs_indiebooking_show_booking_express_payment_buttons", $postId);
?>
</div>
<?php

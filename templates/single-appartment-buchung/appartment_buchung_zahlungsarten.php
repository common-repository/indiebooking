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
$hidePaymentBox = "";
if ($inquiry) {
	$hidePaymentBox = "hidden='hidden'";
}
?>
<div class="contact_box" <?php echo $hidePaymentBox; ?>>
	<div id="zahlungsarten_data_box" class="">
		<h2><?php _e("Select your payment method: ", 'indiebooking'); ?></h2>
		<?php
		if (!isset($paypalPlusKz)) {
			$paypalPlusKz	 		= "";
		}
		if (!isset($testpaypalKz)) {
			$testpaypalKz 			= "";
		}
	// 	$paypalKz					= "";
	// 	$paypalPlusKz				= "";
		if (!isset($buchungPostId) || is_null($buchungPostId)) {
			$buchungPostId			= 0;
		}
		if (!isset($zahlart) || is_null($zahlart) || $zahlart == "") {
			$zahlart		= "INVOICE";
		}
		
		do_action("rs_indiebooking_show_payment_boxes", $zahlart, $paypalPlusKz, $buchungPostId);
		?>
	</div>
	<?php
// 	do_action("rs_indiebooking_include_payment_extra_elements", $buchungPostId, $paypalPlusKz);
	?>
</div>
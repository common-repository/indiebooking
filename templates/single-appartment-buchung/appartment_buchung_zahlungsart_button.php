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

$status         = get_post_status($buchungPostId);
// $paymentId      = "";
// $success        = "";
// $payPalToken    = "";
// $payPalPayerId  = "";
$agbChecked     = "";
// $buchungBtnDisp = 'style="display: none"';
$buchungBtnDisp = '';
if ($agbAccepted > 0) {
    $agbChecked = 'checked = "checked"';
    $buchungBtnDisp = "";
}
//?paypalexpresssuccess=true&paymentId=PAY-8KK22719AB2363055LB3ESBI&token=EC-7W426072MB9536033&PayerID=EKMPM2RSABUZ2
/*
 * Update Carsten Schmitt 01.10.2018
 * Die Paypal-Daten wurden in die template methods ausgelagert um diese datei schmaler zu machen.
 */
?>
<input id="payPal_token" hidden="hidden" value="<?php echo esc_attr($payPalToken); ?>">
<input id="payPal_paymentId" hidden="hidden" value="<?php echo esc_attr($paymentId); ?>">
<input id="payPal_payerId" hidden="hidden" value="<?php echo esc_attr($payPalPayerId); ?>">
<!-- <div class="col-md-12 col-sm-12 col-xs-12"> -->
	<label>
		<input id="ctrl_agree" class="Disabler" type="checkbox"
		   <?php echo $agbChecked; ?> value="1" name="agree" data-toggle="popover"
			data-placement="top" data-trigger="manual"
			data-content="<?php _e("Please accept the terms", "indiebooking"); ?>">
		&nbsp;<?php _ex("I accept the", 'terms of use and data protection', "indiebooking");?>&nbsp;
	</label>
	<a id="agb_link" target="" href=""><?php _e("terms of use and data protection", 'indiebooking');?></a>
<!-- </div> -->
<!-- Nutzungs- und Datenschutzbedingungen  -->
<br /><br />
<!-- <div class="col-md-6 col-sm-6 col-xs-6"> -->
<?php
if (!$inquirie) {
	if ('true' == $success && '' !== $paymentId) {
	    if ("PAYPAL" == $zahlart || "PAYPALEXPRESS" == $zahlart) {
	        $btnText = __("complete booking and confirm payment", 'indiebooking');
	    } else {
	        $btnText = __("book with obligation to pay", 'indiebooking');
	    }
	    ?>
		<a id="btnAppartmentZahlungspflichtigBuchen" class="btn btn-primary payment_pay_btn btn-lg" <?php echo esc_attr($buchungBtnDisp); ?>>
			<?php echo $btnText; ?>
		</a>
	<?php
	} elseif ('rs_ib-almost_booked' == $status && "PAYPAL" == $zahlart) {
		//wird jetzt komplett im payments plugin gemacht
	} elseif ('rs_ib-almost_booked' == $status &&
			('CREDITCARD' == strtoupper($zahlart) || "STRIPECREDITCARD" == strtoupper($zahlart)
					|| 'STRIPESOFORT' == strtoupper($zahlart)
					|| 'STRIPEGIROPAY' == strtoupper($zahlart)
					|| 'STRIPESEPADIRECTDEBIT' == strtoupper($zahlart))) {
		do_action("rs_indiebooking_show_stripe_payment_button", $buchungPostId);
	} elseif ('AMAZONPAYMENTS' == strtoupper($zahlart) || 'AMAZONPAYMENTSEXPRESS' == strtoupper($zahlart)) {
// 		do_action("rs_indiebooking_show_amazonpayments_payment_button", $buchungPostId);
		$btnText = __("book with obligation to pay", 'indiebooking');
		?>
	    <a id="btnAppartmentZahlungspflichtigBuchen" class="btn btn-primary payment_pay_btn btn-lg">
	    	<?php echo $btnText; ?>
		</a>
		<?php
	} elseif ('rs_ib-almost_booked' == $status && 'INVOICE' == $zahlart) {
	    $btnText = __("book with obligation to pay", 'indiebooking');
	    ?>
	    <a id="btnAppartmentZahlungspflichtigBuchen" class="btn btn-primary payment_pay_btn btn-lg">
	    	<?php echo $btnText; ?>
		</a>
	<?php
	} else {
	    echo "Zahlungsart konnte nicht festgestellt werden.";
	}
} else {
	$btnText = __("Send booking inquirie", 'indiebooking');
	?>
	<a id="btnAppartmentSendInquirie" class="btn btn-primary payment_pay_btn btn-lg">
		<?php echo $btnText; ?>
	</a>
<?php
}
?>
<!-- </div> -->
<div id="dialog_agb" title="<?php _e("Terms and Conditions", 'indiebooking')?>" style="display:none">
    <?php
    echo apply_filters('the_excerpt',$agbText);
    ?>
</div>
<?php
//ist das notwendig?
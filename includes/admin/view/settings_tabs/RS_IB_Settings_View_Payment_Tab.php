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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function rs_indiebooking_show_payment_setting_tab() {
	$payperInvoiceKz		= "";
	$stripeCreditCardKz		= "";
	$paypalKz				= "";
	$zahlart				= "";
	$defaultZahlart			= "";
	$zahlarten				= array();
	$invoiceMinDays			= 0;
	$invoiceLoggedKz		= "";
	
	$paymentPluginArray		= array(
			'indiebooking-paypal/indiebooking-paypal.php',
			'indiebooking-stripe/indiebooking-stripe.php',
	);

	$paymentPluginActiveKz	= false;
	foreach ($paymentPluginArray as $paymentPlugin) {
		$paymentPluginActiveKz = is_plugin_active($paymentPlugin);
		if ($paymentPluginActiveKz)
			break;
	}
	
	$defaultZahlart			= get_option( 'rs_indiebooking_settings_default_payment_method' );
	
	$paymentlData 			= get_option( 'rs_indiebooking_settings_payment');
	if ($paymentlData) {
		$payperInvoiceKz	= (key_exists('payperinvoice_kz', $paymentlData)) ? esc_attr__( $paymentlData['payperinvoice_kz'] ) : "on";
		$invoiceLoggedKz	= (key_exists('invoice_loggeduser_kz', $paymentlData)) ? esc_attr__( $paymentlData['invoice_loggeduser_kz'] ) : "";
	} else {
		$payperInvoiceKz 	= "on";
	}
	$invoiceChecked         = "";
	if ($payperInvoiceKz == "on") {
		$invoiceChecked     = 'checked="checked"';
	}
	
	$invoiceLoggedChecked	= "";
	if ($invoiceLoggedKz == "on") {
		$invoiceLoggedChecked	= 'checked="checked"';
	}
	
	$depositChecked			= "";
	$depositDays			= "0";
	$depositValue			= "0";
	if ($paymentlData) {
		$depositKz			= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
		$depositDays 		= (key_exists('deposit_days', $paymentlData)) ? esc_attr__( $paymentlData['deposit_days'] ) : "0";
		$depositValue 		= (key_exists('deposit_value', $paymentlData)) ? esc_attr__( $paymentlData['deposit_value'] ) : "0";
	} else {
		$depositKz 			= "off";
	}
	if ($depositKz == "on") {
		$depositChecked		= 'checked="checked"';
	}
	
	if ($paymentPluginActiveKz) {
		$paymentData 				= get_option( 'rs_indiebooking_settings_payment');
		if ($paymentData) {
			$payperInvoiceKz		= (key_exists('payperinvoice_kz', $paymentData)) ? esc_attr__( $paymentData['payperinvoice_kz'] ) : "";
			$invoiceMinDays			= (key_exists('invoice_availability', $paymentData)) ? esc_attr__( $paymentData['invoice_availability'] ) : 0;
		}
		if (is_plugin_active('indiebooking-stripe/indiebooking-stripe.php')) {
			$stripeData            	= get_option( 'rs_indiebooking_settings_stripe');
			if ($stripeData) {
				$stripeCreditCardKz	= (key_exists('stripe_kz', $stripeData)) ? esc_attr__( $stripeData['stripe_kz'] ) : "";
			}
		}
		if (is_plugin_active('indiebooking-paypal/indiebooking-paypal.php')) {
			$paypalData         	= get_option( 'rs_indiebooking_settings_paypal');
			if ($paypalData) {
				$paypalKz       	= (key_exists('paypal_kz', $paypalData)) ?  esc_attr__( $paypalData['paypal_kz'] ) : "";
			}
		}

		if ($stripeCreditCardKz == "on") {
			array_push($zahlarten, array(
				'value' => 'STRIPECREDITCARD',
				'label' => __('Stripe creditcard', 'indiebooking'),
			));
		}
		if ($paypalKz == "on") {
			array_push($zahlarten, array(
				'value' => 'PAYPAL',
				'label' => __('Paypal', 'indiebooking'),
			));
		}
		if ($payperInvoiceKz == "on") {
			array_push($zahlarten, array(
				'value' => 'INVOICE',
				'label' => __('Invoice', 'indiebooking'),
			));
		}
	}
	if (sizeof($zahlarten) <= 0) {
		array_push($zahlarten, array(
			'value' => 'INVOICE',
			'label' => __('Invoice', 'indiebooking'),
		));
	}
	if (!isset($defaultZahlart) || is_null($defaultZahlart) || $defaultZahlart == '') {
		$defaultZahlart = 'INVOICE';
	}
?>
	<div class="rsib_container-fluid">
		<div class="rsib_row">
			<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
				<div class="ibui_h2wrap">
					<h2 class="ibui_h2">
						<?php _ex('Basic settings', 'payment', 'indiebooking'); ?>
					</h2>
				</div>
				<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left; margin-right: 10px">
					<?php _e('Default payment method', 'indiebooking'); ?>
				</label>
				<select id="rs_indiebooking_settings_default_payment_method"
	                		class="rewa_combobox input-group-field-no-bs ibui_select"
	                		name="rs_indiebooking_settings_default_payment_method">
	                	<?php
	                    foreach ($zahlarten as $zahlartValue) {
	                       	if ($defaultZahlart == $zahlartValue['value']) {
		                       	$selected = 'selected="selected"';
		                    } else {
	                       		$selected = "";
	                      	}
	                    ?>
	                	<option <?php echo $selected;?>
	                    		value="<?php echo esc_attr($zahlartValue['value']);?>">
	                    		<?php echo $zahlartValue['label']; ?>
	                	</option>
	                <?php
	                }
	        		?>
				</select>
				<br /><br />
			</div>
			<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
				<div class="ibui_h2wrap">
					<h2 class="ibui_h2">
						<?php _ex('Deposit', 'anzahlung', 'indiebooking'); ?>
					</h2>
				</div>
				<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left; margin-right: 10px">
					<?php _ex('active deposit', 'anzahlung', 'indiebooking'); ?>
				</label>
			    <div class="ibui_switchbtn" style="float:left;">
			    	<input id="cb_activate_deposit" class="ibui_switchbtn_input ibfc_switchbtn_input"
			    		type="checkbox" <?php echo $depositChecked; ?> />
			    	<label for="cb_activate_deposit"></label>
                    <input id='cb_activate_deposit_kz' type='hidden'
                    		name='rs_indiebooking_settings_payment[activedeposit_kz]'
                    		value="<?php echo $depositKz; ?>" >
			     </div>
			     <label class="ibui_label">
			     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
			     			title="
			     			<?php
			     			_e('Check this Box if you want to give the ability to pay a deposit instead of pay the full price at the time of booking', 'indiebooking');
			     			?>">
			    	</span>
				</label>
				<br /><br />
				<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left; margin-right: 10px">
					<?php _e('Final Payment - days before arrival', 'indiebooking'); ?>
				</label>
				<input id="rs_indiebooking_settings_deposit_days" class="tooltipItem input-group-field-no-bs ibui_input"
						name="rs_indiebooking_settings_payment[deposit_days]" maxlength="3" value="<?php echo $depositDays; ?>"
						title="">
			     <label class="ibui_label" style="padding-left:110px;">
			     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
			     			title="
			     			<?php
			     			_e('This value defines the days before arrival to which the final invoice will be sent to the customer', 'indiebooking');
			     			?>">
			    	</span>
				</label>
				<br /><br />
				<div class="rsib_form-group">
					<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
						<?php _ex('deposit value (in %)', 'anzahlung', 'indiebooking'); ?>
					</label>
					<div class="rsib_col-sm-8">
						<input class="ibui_input onlyNumber" name="rs_indiebooking_settings_payment[deposit_value]"
								style="width: 50px;"
								value="<?php echo $depositValue; ?>" type="text">
					     <label class="ibui_label">
					     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
					     			title="
					     			<?php
					     			_e('Defines the value of the deposit. It is a percentage of the invoice amount.', 'indiebooking');
					     			?>">
					    	</span>
						</label>
					</div>
				</div>
			</div>
		</div>
		<div class="rsib_row" style="margin-top: 10px;">
			<div class="ibui_h2wrap ibui_toggle_header">
				<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
				<h2 class="ibui_h2">
					<?php _e('Invoice payment settings', 'indiebooking'); ?>
				</h2>
			</div>
			<div class="ibui_toggle_content" style="display: none;" >
				<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
					<div class="ibui_tabitembox">
						<?php
						$availabilitySpecialAttr = "";
						$displayOption			 = "";
						$indiebooking_inv_settings = array(
							//'tinymce' => true,
							//'teeny' => true,
							'media_buttons' => FALSE,
							'editor_class'=>'email_editor',
							'editor_height' => 400
						);
						$invoiceTermsOfPaymentTxt      = get_option('rs_indiebooking_settings_invoice_terms_of_payment_txt');
						if (!$paymentPluginActiveKz) {
							$invoiceMinDays				= 0;
							$availabilitySpecialAttr 	= "readonly='readonly'";
							$displayOption				= 'display:none';
						}
						?>
						<?php if ($paymentPluginActiveKz) { ?>
							<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left; margin-right: 10px">
								<?php _e('Pay per invoice', 'indiebooking'); ?>
							</label>
						    <div class="ibui_switchbtn" style="float:left;">
						    	<input id="cb_activate_invoice" class="ibui_switchbtn_input ibfc_switchbtn_input"
						    		type="checkbox" <?php echo $invoiceChecked; ?> />
						    	<label for="cb_activate_invoice"></label>
			                    <input id='cb_activate_invoice_kz' type='hidden'
			                    		name='rs_indiebooking_settings_payment[payperinvoice_kz]'
			                    		value="<?php echo $payperInvoiceKz; ?>" >
						     </div>
						     <label class="ibui_label">
						     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
						     			title="
						     			<?php
						     			_e('Check this Box if you want to give the ability to pay per invoice', 'indiebooking');
						     			?>">
						    	</span>
							</label>
							<br /><br />
							<div class="rsib_form-group" style="<?php echo $displayOption;?>">
								<label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
									<?php //_e('only available if booking start is at least ', 'indiebooking'); ?>
									<?php _e('payment availability condition', 'indiebooking'); ?>:
								</label>
			                    <div class="rsib_col-sm-8">
			                    	<input id="rs_indiebooking_settings_invoice_availability"
			                        		class="onlyInteger appartment_admin_number_spinner indiebooking_admin_payment_number_spinner input-group-field-no-bs ibui_input"
			                        		name="rs_indiebooking_settings_payment[invoice_availability]"
			                        		style="min-width:45px; maxlength="2" <?php echo $availabilitySpecialAttr; ?>
			                        		value="<?php echo esc_attr($invoiceMinDays); ?>" title=""/>
			                        		
				                    <label style="padding-left:110px;"for="rs_indiebooking_settings_invoice_availability" class="ibui_label">
				                    	<?php _e('Days', 'indiebooking'); ?>
				                    	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
				                    		title="<?php _e('Specifies the days,that the booking must start in the future for activation the invoice payment method.', 'indiebooking'); ?>">
				                    	</span>
				                	</label>
			                    </div>
		                    </div>
		                    <br class="clear" />
							<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left; margin-right: 10px">
								<?php _e('Only for logged useres', 'indiebooking'); ?>
							</label>
	                        <div class="ibui_switchbtn" style="float:left;">
	                        	<input id="cb_activate_invoice_loggeduser" class="ibui_switchbtn_input ibfc_switchbtn_input"
	                            		name="rs_indiebooking_settings_payment[invoice_loggeduser_kz]"
	                            		value="<?php echo $invoiceLoggedKz; ?>"
	                            		type="checkbox" <?php echo $invoiceLoggedChecked; ?> />
	                            <label for="cb_activate_invoice_loggeduser"></label>
	                        </div>
	                        <label class="ibui_label">
	                        	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
	                        		title="
	                        		<?php
	                        		_e("Check this Box if you want to give the ability to pay per invoice only for logged users", 'indiebooking');
	                        		?>">
	                    		</span>
	                		</label>
	                        <br /><br />
						<?php
		                } ?>
	                    <?php
	                    if (!is_plugin_active('indiebooking-paypal/indiebooking-paypal.php')
	                    	|| !is_plugin_active('indiebooking-stripe/indiebooking-stripe.php')) {
	                    ?>
	                    <div class="ibui_pro_notice" style="margin: 50px 0px 0px 0px;">
	                    	<?php _e("You want to have more payment options?")?><br>
	                    	<?php _e("Look at the pro extension plugin")?><br>
	                        <a href="http://www.indiebooking.de" target="_blank">www.indiebooking.de</a>
	                    </div>
	                    <?php } ?>
					</div>
				</div>
				<div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md2">
			        <div class="rsib_form-group">
			        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
			        		<?php _e("terms of payment for invoice payments", 'indiebooking');?>:
			            </label>
			            <span class="email_editor">
			            <?php
			            	wp_editor($invoiceTermsOfPaymentTxt,"rs_indiebooking_settings_invoice_terms_of_payment_txt",$indiebooking_inv_settings);
			            ?>
			            </span>
			        </div>
				</div>
				<br class="clear" />
			</div><!-- toggle content -->
	    </div>
	</div>
<?php }
rs_indiebooking_show_payment_setting_tab();
?>
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

$priceIsNet               	= "";
$bankName                 	= "";
$iban                     	= "";
$bic                      	= "";
$kontoInhaber             	= "";
$companyName              	= "";
$companyStreet            	= "";
$company_zip_code         	= "";
$companyLocation          	= "";
$companyCountry           	= "";
$companyWebsite           	= "";
$companyEmail             	= "";
$companyPhone             	= "";
$companyFax               	= "";
$company_tax_number       	= "";
$company_ust_id           	= "";
$pdf_image_id             	= "";
$icon_image_id            	= "";
$dankeText                	=	"";
$googleApiKey             	= "";
$debitorKonto				= "";

$googleData                         = get_option( 'rs_indiebooking_settings_google');

if ($googleData) {
    $googleApiKey = (key_exists('ib_google_api_key', $googleData)) ?  esc_attr( $googleData['ib_google_api_key']) : "";
}

$options                            = get_option( 'rs_indiebooking_settings' );
$bankData                           = get_option( 'rs_indiebooking_settings_bankdata' );


$showWelcomeBannerKz                = get_option('rs_indiebooking_settings_show_welcome_kz');
$bookingInquiriesKz					= get_option('rs_indiebooking_settings_booking_inquiries_kz');
$bookingByCategorieKz				= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
$allowStatistics                    = get_option('rs_indiebooking_settings_allow_statistics_kz');

$timeForBooking                     = get_option( 'rs_indiebooking_settings_time_to_book' );
if (!$timeForBooking) {
    $timeForBooking                 = 15;
}

$futureAvailability					= get_option( 'rs_indiebooking_settings_future_availability');
if (!$futureAvailability) {
	$futureAvailability             = 2;
}

$invoiceNrStructure                 = get_option( 'rs_indiebooking_settings_invoice_number_structure' );
$invoiceNrStartsBy                  = get_option( 'rs_indiebooking_settings_invoice_number_startsby' );

/* *****************************************************************
 ******************BANK DATEN AUSLESEN*****************************
 *******************************************************************/
if ($bankData) {
    $bankName             = (key_exists('bank_name', $bankData))      ?  esc_attr__( $bankData['bank_name'] )     : "";
    $iban                 = (key_exists('bank_iban', $bankData))      ?  esc_attr__( $bankData['bank_iban'] )     : "";
    $bic                  = (key_exists('bank_bic', $bankData))       ?  esc_attr__( $bankData['bank_bic'] )      : "";
    $kontoInhaber         = (key_exists('bank_account', $bankData))   ?  esc_attr__( $bankData['bank_account'] )  : "";
    
    
    if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
    	$debitorKonto     = (key_exists('debitorkto', $bankData))   ?  esc_attr__( $bankData['debitorkto'] )  		: "";
    } else {
    	$debitorKonto     = "";
    }
}

/*
 ******************************************************************
 ******************OPTIONS DATEN AUSLESEN**************************
 ******************************************************************
 */
if ($options) {
    $priceIsNet           = (key_exists('netto_kz', $options))           ?  esc_attr__( $options['netto_kz'] )          : "";
    $companyName          = (key_exists('company_name', $options))       ?  esc_attr__( $options['company_name'] )      : "";
    $companyStreet        = (key_exists('company_street', $options))     ?  esc_attr__( $options['company_street'] )    : "";
    $companyLocation      = (key_exists('company_location', $options))   ?  esc_attr__( $options['company_location'] )  : "";
    $companyCountry       = (key_exists('company_country', $options))    ?  esc_attr__( $options['company_country'] )   : "";
    $companyWebsite       = (key_exists('company_website', $options))    ?  esc_attr__( $options['company_website'] )   : "";
    $companyEmail         = (key_exists('company_email', $options))      ?  esc_attr__( $options['company_email'] )     : "";
    $companyPhone         = (key_exists('company_phone', $options))      ?  esc_attr__( $options['company_phone'] )     : "";
    $companyFax           = (key_exists('company_fax', $options))        ?  esc_attr__( $options['company_fax'] )       : "";
    $company_zip_code     = (key_exists('company_zip_code', $options))   ?  esc_attr__( $options['company_zip_code'] )  : "";
    $company_ust_id       = (key_exists('company_ust_id', $options))     ?  esc_attr__( $options['company_ust_id'] )    : "";
    $pdf_image_id         = (key_exists('pdf_image_id', $options))       ?  esc_attr__( $options['pdf_image_id'] )      : "";
    $icon_image_id        = (key_exists('icon_image_id', $options))      ?  esc_attr__( $options['icon_image_id'] )     : "";
    $company_tax_number   = (key_exists('company_tax_number', $options)) ?  esc_attr__( $options['company_tax_number']) : "";
    $dankeText            = (key_exists('thankstxt', $options))          ?  esc_attr__( $options['thankstxt'])          : "";
    $companyOrPrivate     = (key_exists('private_or_company', $options)) ?  esc_attr__( $options['private_or_company']) : "";
}
//         		$mail_options         = get_option( 'rs_indiebooking_settings_mail' );
//         		$mail_name            = esc_attr__( $mail_options['mail_name'] );
//         		$mail_adress          = esc_attr__( $mail_options['mail_adress'] );
//         		$smtp                 = esc_attr__( $mail_options['mail_smtp'] );


$checked                  = "";
if ($priceIsNet == "") {
    $priceIsNet           = "off";
} elseif ($priceIsNet === "on") {
    $checked              = 'checked="checked"';
}


$checkedPrivatOrCompany1    = 'checked="checked"';
$checkedPrivatOrCompany2    = '';
if ($companyOrPrivate == 'private') {
    $checkedPrivatOrCompany1    = '';
    $checkedPrivatOrCompany2    = 'checked="checked"';
}
?>

<div id="rs_indiebooking_datenschutz_dialog" class="ibui_widget rs_ib_taxonomy_popup"
	data-dialogTitle="<?php _e("Diagnostics & Privacy", 'indiebooking');?>"
	style="display:none">
	<div style="width: 100%; height: 400px; overflow:auto;">
		<?php
        echo "<h2>";
		_e("About Diagnostics & Privacy", 'indiebooking');
		echo "</h2>";
        _e("indiebooking would like your help improving the quality and performance of its products and services.", 'indiebooking');
        echo "<br />";
        _e("The indiebooking-plugin can automatically collect diagnostic and usage information and send it to indiebooking for analysis.",
            'indiebooking');
        _e("The information is sent only with your consent and is submitted to indiebooking.", 'indiebooking');
        echo "<br /><br />";
        _e("If you opt-in to sharing diagnostic data with app developers, Indiebooking may share your crash data with app developers ".
            "so they can improve their products.", 'indiebooking');
        echo "<br /><br />";
        _e("If you opt-in to sending diagnostic and usage information to indiebooking, it may include the following information:",
            'indiebooking');
        echo '<ol style="list-style-type: circle;">';
            echo "<li>";
        	   _e("Details about plugin or system crashes, freezes, or kernel panics", 'indiebooking');
        	echo "</li>";
        	echo "<li>";
        	   _e("Information about events in your Wordpress Installation", 'indiebooking');
        	echo "</li>";
        	echo "<li>";
        	   _e("Usage information (for example, data about how you use indiebooking and third-party software and services)",
        	       'indiebooking');
        	echo "</li>";
    	echo "</ol>";
        _e("Diagnostic and usage data contains your Wordpress and software specifications, ".
            "including information about plugins active in your Wordpress installation.", 'indiebooking');
        echo "<br />";
        _e("Personal data is either not logged at all in the reports generated by your Wordpress Installation, ".
            "is subject to privacy preserving techniques such as differential privacy, ".
            "or is removed from any reports before they're sent to indiebooking.", 'indiebooking');
        echo "<br /><br />";
        _e("Information is sent to indiebooking using your Internet connection", 'indiebooking');
        echo "<h2>";
        _e("Opt-out of automatic reporting", 'indiebooking');
        echo "</h2>";
        _e("You can change your reporting options at any time:", 'indiebooking');
        echo "<br />";
        _e("Navigate to Indiebooking &#8594; settings. Under the section 'General Options' you can deactivate the reporting option.",
            'indiebooking');
        echo "<br />";
        _e("Diagnostic and usage information will no longer be sent to indiebooking.", 'indiebooking');
        echo "<h2>";
        _e("Privacy policy", 'indiebooking');
        echo "</h2>";
        _e("By using these features, you agree and consent to indiebooking's and its agents' transmission, collection, ".
            "maintenance, processing, and use of this information as described above. ", 'indiebooking');
        echo "<br /><br />";
        _e("At all times, information collected by indiebooking will be treated in accordance with indiebooking's Privacy Policy, ".
            "which can be found at ", 'indiebooking');
        _e('<a href="http://www.indiebooking.de/datenschutz_ib/datenschutz_en.html">Data protection Indiebooking</a>', 'indiebooking');
        ?>
	</div>
</div>
 
<div id="rs_ib_admin_settings">
    <div class="rsib_container-fluid">
        <div class="rsib_row">
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_xs rsib_nopadding_md2">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Company Information', 'indiebooking'); ?></h2></div>
                    <div class="rsib_form-horizontal">
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Privat or company", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                                <input type="radio" id="ibfc_is_company" name="rs_indiebooking_settings[private_or_company]" value="company"
                                        <?php echo $checkedPrivatOrCompany1; ?>>
                            	<label for="ibfc_is_company" class="" >
                            		<?php _e("I am a company", 'indiebooking');?>
                        		</label>
                                <input type="radio" id="ibfc_is_privatperson" name="rs_indiebooking_settings[private_or_company]" value="private"
                                        <?php echo $checkedPrivatOrCompany2; ?>>
                            	<label for="ibfc_is_privatperson" class="" >
                            		<?php _e("I am a privat person", 'indiebooking');?>
                        		</label>
                    		</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Company", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_name' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_name]' value="<?php echo $companyName; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Company Street", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_street' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_street]' value="<?php echo $companyStreet; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Company Zip Code", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_zip_code' class='ibui_input' type='text' maxlength="10"
                            			name='rs_indiebooking_settings[company_zip_code]' value="<?php echo $company_zip_code; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Company Location", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_location' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_location]' value="<?php echo $companyLocation; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Company Country", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_country' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_country]' value="<?php echo $companyCountry; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Tax Number", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_tax_number' class='ibui_input' maxlength="20" type='text'
                            			name='rs_indiebooking_settings[company_tax_number]' value="<?php echo $company_tax_number; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("VAT Number", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_ust_id' class='ibui_input' type='text' maxlength="20"
                            			name='rs_indiebooking_settings[company_ust_id]' value="<?php echo $company_ust_id; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Phone", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_phone' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_phone]' value="<?php echo $companyPhone; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Fax", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_fax' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_fax]' value="<?php echo $companyFax; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Website", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_website' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_website]' value="<?php echo $companyWebsite; ?>">
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("E-Mail", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8">
                            	<input id='rs_indiebooking_settings_company_email' class='ibui_input' type='text'
                            			name='rs_indiebooking_settings[company_email]' value="<?php echo $companyEmail; ?>">
                			</div>
                        </div>
                        <?php
                        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
                        ?>
	                        <div class="rsib_form-group">
	                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
	                            	<?php _e("Debitor", 'indiebooking');?>:
	                            </label>
	                            <div class="rsib_col-xs-8">
	                            	<input id='rs_indiebooking_settings_debitor_kto' class='ibui_input' type='text'
	                            			name='rs_indiebooking_settings_bankdata[debitorkto]' value="<?php echo $debitorKonto; ?>">
	                			</div>
	                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2" style="padding-top:40px;"><?php _e("Account Details", 'indiebooking');?></h2></div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Bank name", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8"><input id='rs_indiebooking_settings_bank_name' class='ibui_input' type='text' name='rs_indiebooking_settings_bankdata[bank_name]' value="<?php echo $bankName; ?>"></div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Account holder", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8"><input id='rs_indiebooking_settings_bank_account' class='ibui_input' type='text' name='rs_indiebooking_settings_bankdata[bank_account]' value="<?php echo $kontoInhaber; ?>"></div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("IBAN", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8"><input id='rs_indiebooking_settings_bank_iban' class='ibui_input' type='text' maxlength="35" name='rs_indiebooking_settings_bankdata[bank_iban]' value="<?php echo $iban; ?>"></div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("BIC", 'indiebooking');?>:</label>
                            <div class="rsib_col-xs-8"><input id='rs_indiebooking_settings_bank_bic' class='ibui_input' type='text' maxlength="15" name='rs_indiebooking_settings_bankdata[bank_bic]' value="<?php echo $bic; ?>"></div>
                        </div>
                </div>

            </div>
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_xs rsib_nopadding_md2">
            <!-- Backdrop funktioniert noch nicht! -->
                <div id="rs_ib_general_options_tour" class="ibui_tabitembox">
                    <div class="ibui_h2wrap">
                    	<h2 class="ibui_h2"><?php _e("General Options", 'indiebooking');?></h2>
                    	<span ></span>
                	</div>
                        <!--
                        <div>
                            <input id="rs_indiebooking_settings_netto" class="ibui_checkbox ibui_tooltip_item" type="checkbox" title="<?php //_e('Defines if the given prices are net or gros', 'indiebooking'); ?>" name="cb_price_netto" <?php //echo $checked; ?>>
                            <label for="rs_indiebooking_settings_netto"><?php //_e('Price is net', 'indiebooking'); ?></label>
                            <input id='rs_indiebooking_settings_netto_kz' type='hidden' name='rs_indiebooking_settings[netto_kz]' value="<?php //echo $priceIsNet; ?>">
                        </div>
                         -->
                   	<div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php //_e('Allow anonymous usage statistics', 'indiebooking'); ?>
                        	<?php //_e('Send diagnostic and usage data to indiebooking', 'indiebooking'); ?>
                        	<?php _e('Allow send diagnostic and usage data to indiebooking', 'indiebooking'); ?>
                    	</label>
                        <div class="rsib_col-xs-8">
                            <div class="ibui_switchbtn" style="float:left;">
                               <input id="allowUsingStatisticsKz" class="ibui_switchbtn_input ibfc_switchbtn_input"
                               			name="rs_indiebooking_settings_allow_statistics_kz"
                               			value="<?php echo $allowStatistics; ?>"
                               			type="checkbox" <?php echo ($allowStatistics == "on") ? "checked='checked'" : "";?> />
                               <label for="allowUsingStatisticsKz"></label>
                            </div>
                            <label class="ibui_label">
                        		<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                        			title="
                        			<?php
                        			_e("Help indiebooking improve its products and services by automatically sending diagnostics ".
                        			    "and usage data Diagnostic data may include locations. ", 'indiebooking');
                        			_e("Help Wordpress developers improve Wordpress and its plugins by allowing indiebooking to ".
                        			    "share crash data with them.", 'indiebooking');
                        			?>">
                    			</span>
                			</label>
                			<label id="rs_indiebooking_show_datenschutz" class="ibui_add_btn">
                				<?php _e("Show Diagnostics & Privacy", 'indiebooking'); ?>
                			</label>
                        </div>
                   	</div>
                   	<div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php _e('Show welcome banner', 'indiebooking'); ?>
                        </label>
                        <div class="rsib_col-xs-8">
                            <div class="ibui_switchbtn">
                               <input id="showWelcomeBannerKz" class="ibui_switchbtn_input ibfc_switchbtn_input"
                               			name="rs_indiebooking_settings_show_welcome_kz"
                               			value="<?php echo $showWelcomeBannerKz; ?>"
                               			type="checkbox" <?php echo ($showWelcomeBannerKz == "on") ? "checked='checked'" : "";?> />
                               <label for="showWelcomeBannerKz"></label>
                            </div>
                        </div>
                   	</div>
                   	<div class="rsib_form-group">
                   	<!-- Platzhalter -->
                   	</div>
                   	<div class="rsib_form-group">
						<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left;">
							<?php _e('Bookings over category', 'indiebooking'); ?>
						</label>
						<div class="rsib_col-xs-8">
							<div class="ibui_switchbtn ibui_switchbtn_active" style="float: left;">
								<input id="rs_indiebooking_settings_booking_by_categorie_kz" class="ibui_switchbtn_input ibfc_switchbtn_input"
										name="rs_indiebooking_settings_booking_by_categorie_kz"
										value="<?php echo $bookingByCategorieKz; ?>" type="checkbox"
										<?php echo ($bookingByCategorieKz == "on") ? "checked='checked'" : "";?> />
								<label for="rs_indiebooking_settings_booking_by_categorie_kz"></label>
							</div>
	                        <label class="ibui_label">
								<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
	                       			title="<?php _e('Specifies that all bookings are going over there catgeory.
											So indiebooking chooses the best apartment to be booked', 'indiebooking'); ?>">
	                    		</span>
	                		</label>
			                <br />
		                </div>
	                </div>
                   	<div class="rsib_form-group">
						<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label" style="float:left;">
							<?php _e('All bookings are inquiries', 'indiebooking'); ?>
						</label>
						<div class="rsib_col-xs-8">
							<div class="ibui_switchbtn ibui_switchbtn_active" style="float: left;">
								<input id="rs_indiebooking_settings_booking_inquiries" class="ibui_switchbtn_input ibfc_switchbtn_input"
										name="rs_indiebooking_settings_booking_inquiries_kz"
										value="<?php echo $bookingInquiriesKz; ?>" type="checkbox"
										<?php echo ($bookingInquiriesKz == "on") ? "checked='checked'" : "";?> />
								<label for="rs_indiebooking_settings_booking_inquiries"></label>
							</div>
	                        <label class="ibui_label">
								<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
	                       			title="<?php _e('Specifies that all bookings are inquiries. So you have to accept them
	                       					before they are real bookings', 'indiebooking'); ?>">
	                    		</span>
	                		</label>
			                <br />
		                </div>
	                </div>
                   	<?php
                   	if ($invoiceNrStructure == "") {
                   	    $invoiceNrStructure = "1";
                   	}
                   	?>
                    <div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php _e('invoice number structure', 'indiebooking'); ?>:
                    	</label>
                        <div class="rsib_col-sm-8">
                            <?php
//                             if ($invoiceNrStructure == "") {
                            if ($numberOfBookings == 0) {
                            ?>
                            <label style="padding-left:10px;" class="ibui_label"><!-- for="rs_indiebooking_settings_time_to_book" -->
                        		<?php _e('This setting can only be made till the first booking is started!!', 'indiebooking'); ?>
                          	</label>
                            <?php } else {
                			echo "<label class='ibui_label'>";
                                switch($invoiceNrStructure) {
                                    case "1":
                                        _e("continually number start by 1. (e.g. 20)", 'indiebooking');
                                        break;
                                    case "2":
                                        _e("continually start by ", 'indiebooking');
                                        echo $invoiceNrStartsBy;
                                        break;
                                    case "3":
                                        _e("year as prefix + continually number start by 1 (e.g. 201620)", 'indiebooking');
                                        break;
                                }
                            echo "</label>";
                            }?>
                            <label class="ibui_label">
                        		<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                        			title="<?php _e('Defines the structure of your invoice number.'
                        			    .' To avoid incorrect invoice numbers, this setting can only be made till the first booking is started!'
                        			    , 'indiebooking'); ?>">
                    			</span>
                			</label>
                        </div>
                    </div>
					<div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php _e('currency', 'indiebooking'); ?>:
                    	</label>
                        <div class="rsib_col-sm-8">
                            <select id="rs_indiebooking_settings_currency"
                            		class="rewa_combobox input-group-field-no-bs ibui_select"
                            		name="rs_indiebooking_settings_currency">
                                <?php
                                	$currency		= rs_ib_currency_util::getCurrentCurrency();
									$currencyArray 	= rs_ib_currency_util::getAvailableCurrencyArray();
                                    foreach ($currencyArray as $currencyValue) {
                                        if ($currency == $currencyValue) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = "";
                                        }
                                    ?>
                                        <option <?php echo $selected;?>
                                        		value="<?php echo esc_attr($currencyValue);?>">
                                        			<?php echo $currencyValue; ?>
                                		</option>
                                	<?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
//                     if ($invoiceNrStructure == "") {
                    if ($numberOfBookings == 0) {
                    ?>
					<div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php //_e('invoice number structure', 'indiebooking'); ?><!-- : -->
                    	</label>
                    	<?php
                    	$invoiceStruct1Checked = "";
                    	$invoiceStruct2Checked = "";
                    	$invoiceStruct3Checked = "";
                    	switch($invoiceNrStructure) {
                    	    case "1":
                    	        $invoiceStruct1Checked = "checked";
                    	        break;
                    	    case "2":
                    	        $invoiceStruct2Checked = "checked";
                    	        break;
                    	    case "3":
                    	        $invoiceStruct3Checked = "checked";
                    	        break;
                    	}
                    	?>
                        <div class="rsib_col-sm-8">
                            <input type="radio" name="rs_indiebooking_settings_invoice_number_structure" value="1" <?php echo $invoiceStruct1Checked; ?>>
                            		<?php _e("continually number start by 1. (e.g. 20)", 'indiebooking');?>
                            		<br>
                            <input type="radio" name="rs_indiebooking_settings_invoice_number_structure" value="2" <?php echo $invoiceStruct2Checked; ?>>
                            		<?php _e("continually start by", 'indiebooking');?>
                        	<input id='rs_indiebooking_settings_invoicenr_starts' style="width: 50px;"
                        			class="onlyNumber ibui_input" type='text'
                        			name='rs_indiebooking_settings_invoice_number_startsby'
                        			value="0">
                        			<br />
                            <input type="radio" name="rs_indiebooking_settings_invoice_number_structure" value="3" <?php echo $invoiceStruct3Checked; ?>>
                            	<?php _e("year as prefix + continually number start by 1 (e.g. 201620)", 'indiebooking');?>
                        </div>
                    </div>
                    <?php
                    } ?>
                    <div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php //_e('Time for booking', 'indiebooking'); ?>
                        	<?php _e('Time for booking step', 'indiebooking'); ?>:
                        </label>
                        <div class="rsib_col-sm-8">
                        <!-- rsib_col-xs-3 rsib_nopadding_left  -->
                        	<input id='rs_indiebooking_settings_time_to_book' style="width: 50px;"
                        			class="onlyNumber ibui_input" type='text'
                        			name='rs_indiebooking_settings_time_to_book'
                        			value="<?php echo esc_attr($timeForBooking); ?>">
                            <label style="padding-left:10px;"for="rs_indiebooking_settings_time_to_book" class="ibui_label">
                        		<?php _e('Minutes', 'indiebooking'); ?>
                        		<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                        			title="<?php _e('Specifies the time that the user has, to end the current booking step', 'indiebooking'); ?>">
                    			</span>
                			</label>
                        </div>
                    </div>
                    <div class="rsib_form-group">
                        <label style="float:left;" class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                        	<?php _e('Future availability ', 'indiebooking'); ?>:
                        </label>
                        <div class="rsib_col-sm-8">
                            <input id="rs_indiebooking_settings_future_availability"
                            		class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                            		name="rs_indiebooking_settings_future_availability"
                            		style="min-width:45px; maxlength="2"
                            		value="<?php echo esc_attr($futureAvailability); ?>" title=""/>
	                        <label style="padding-left:110px;"for="rs_indiebooking_settings_future_availability" class="ibui_label">
	                        	<?php _e('Years', 'indiebooking'); ?>
	                        	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
	                        		title="<?php _e('Specifies the years that your Objects will be available in the future', 'indiebooking'); ?>">
	                    		</span>
	                		</label>
                        </div>
                    </div>
                </div>
                <div id="rs_ib_general_options_google_tour" class="ibui_tabitembox">
                    <span id="rs_indiebooking_hopscotch_settings_general_4"></span>
                    <div class="ibui_h2wrap">
                    	<h2 class="ibui_h2" style="padding-top:40px;"><?php _e('Google Api', 'indiebooking'); ?></h2>
                	</div>
                    <div class="rsib_form-group">
                        <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Api Key", 'indiebooking');?>:</label>
                        <div class="rsib_col-xs-8">
                        	<input id='rs_indiebooking_settings_google_apikey' class='ibui_input' type='text'
                        			name='rs_indiebooking_settings_google[ib_google_api_key]'
                        			value="<?php echo $googleApiKey; ?>">
                            <label style="padding-left:10px;"for="rs_indiebooking_settings_google_apikey" class="ibui_label">
                        		<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                        			title="<?php _e("In order to make it easier for your customers to enter their addresses by means of automatic suggestions, please create a Google Api Key and enter it in the field.", 'indiebooking'); ?>">
                    			</span>
                			</label><br>
                			<p><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
                				<?php _e("How to create a google api key", 'indiebooking');?>
            				</a></p>
            			</div>
                    </div>
                </div>
				<?php do_action("rs_indiebooking_admin_settings_google_informations"); ?>
            </div>
        </div>
    </div>
</div>
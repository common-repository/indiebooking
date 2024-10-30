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

add_action("rs_indiebooking_show_zahlungsinformation_tab", "rs_indiebooking_show_zahlungsinformation_tab", 5, 4);
add_action("rs_indiebooking_show_zahlungsinformation_tab_saison", "rs_indiebooking_show_zahlungsinformation_tab_saison_none", 5, 4);

if ( ! function_exists('rs_indiebooking_show_zahlungsinformation_tab_saison_none')) {
    function rs_indiebooking_show_zahlungsinformation_tab_saison_none($post = 0, $appartment = 0, $mwsts = 0, $bookedDates = 0) {
        //do nothing
    }
}

if ( ! function_exists( 'rs_indiebooking_show_zahlungsinformation_tab' ) ) {
	/* @var $appartment RS_IB_Model_Appartment */
function rs_indiebooking_show_zahlungsinformation_tab($post, $appartment, $mwsts, $bookedDates) {
	$waehrung           = rs_ib_currency_util::getCurrentCurrency();
    $ib_options         = get_option( 'rs_indiebooking_settings' );
    $priceIsNet     	= "off";
    if (!$ib_options) {
        $default_settings = array(
            'netto_kz' => "off"
        );
        add_option('rs_indiebooking_settings', $default_settings);
    } elseif (key_exists('netto_kz', $ib_options)) {
        $priceIsNet     = esc_attr__( $ib_options['netto_kz'] );
    } else {
        $priceIsNet     = "off";
    }
    $checked            = "";
    $styleLblNet        = 'style="display: none;"';
    $styleLblGross      = '';
    if ($priceIsNet === "on") {
        $checked        = 'checked="checked"';
        $styleLblGross  = 'style="display: none;"';
        $styleLblNet    = '';
    }
    $calcMwst 			= 0;
    foreach ($mwsts as $mwst) {
    	if ($mwst->getMwstId() == $appartment->getMwstId()) {
    		$calcMwst 	= ($mwst->getMwstValue() / 100);
    	}
	}
?>
        <!--<label><?php //_e('Price is net', 'indiebooking'); ?>:</label>-->
        <!-- <input id="cb_appartment_price_netto" class="tooltipItem" type="checkbox" title="<?php //_e('Defines if the given price is net or gros', 'indiebooking'); ?>" name="cb_appartment_price_netto"  <?php //echo $checked; ?>> -->
        <!-- <input id='cb_appartment_price_netto_kz' type='hidden' value='<?php //echo $priceIsNet; ?>' name='cb_appartment_price_netto_kz'>-->
    <div class="rsib_container-fluid">
        <div class="rsib_row">
            <div class="rsib_col-lg2-7 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap">
                    	<h2 class="ibui_h2"><?php _e('Default Price', 'indiebooking'); ?></h2>
                        <span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                    		title="<?php
                    		  _e('The price is given as price per night', 'indiebooking');
                    		?>">
                		</span>
                	</div>
                    <div id="default_price_container">
                        <?php
                            $calcPrice = $appartment->getPreis();
                            $appartment->calculatePricesAndTaxes($calcPrice, $tax, $calcMwst, $priceIsNet);
                            $defaultBookingComRateId = $appartment->getBookingComDefaultRateId();
                        ?>
                        <table id="appartment_default_prices_table" class="rs_ib_input_table sortable rsib_table ibui_table">
                        <!--<table id="appartment_default_prices_table" class="rs_ib_input_table sortable widefat rsib_table ibui_table">-->
                            <thead>
                                <tr>
                                    <th <?php echo $styleLblNet; ?> class="appartment_net_price_label">
                                    	<?php _e('Net price', 'indiebooking'); ?>
                                    </th>
                                    <th <?php echo $styleLblGross; ?> class="appartment_gross_price_label">
                                    	<?php _e('Gross price', 'indiebooking'); ?>
                                    </th>
                                    <th <?php echo $styleLblNet; ?> class="appartment_gross_price_label2">
                                    	<?php _e('Gross price', 'indiebooking'); ?>
                                    </th>
                                    <th <?php echo $styleLblGross; ?> class="appartment_net_price_label2" style="text-align:right;">
                                    	<?php _e('Net price', 'indiebooking'); ?>
                                    </th>
                                    <th style="text-align:right;"><?php _e('Tax', 'indiebooking'); ?></th>
                                    <?php if (is_plugin_active('indiebooking-booking.com/indiebooking-booking.com.php')) { ?>
                                    <th style="text-align:right;"><?php _e('booking.com rate id', 'indiebooking'); ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="rates" class="ui-sortable">
                                <tr class="appartment_price_table_row ui-sortable-handle">
                                    <td class="priceInput tableInput">
                                    	<div class="ib-currency-price-box">
	                                    	<input id="appartment_price" type="text" name="appartment_price"
	                                    			class="ib_currency_input given_price onlyNumber ibui_input"
	                                    			value="<?php echo number_format(floatval(esc_attr($appartment->getPreis())), 2, ',', '.'); ?>" />
 											<span class="ib_currency"><?php echo $waehrung; ?></span>
                                    	</div>
                        			</td>
                                    <td>
                                    	<div class="ib-currency-price-box">
	                                    	<input id="appartment_calculated_price" type="text" name="appartment_calculated_price"
	                                    			class="ib_currency_input calculated_price onlyNumber"
	                                    			value="<?php echo esc_attr($calcPrice); ?>" disabled="disabled" />
 											<span class="ib_currency"><?php echo $waehrung; ?></span>
                                    	</div>
                        			</td>
                                    <td>
                                    	<div class="ib-currency-price-box">
	                                    	<input id="appartment_calculated_tax" type="text" name="appartment_calculated_tax"
	                                    			class="ib_currency_input calculated_tax onlyNumber"
	                                    			value="<?php echo esc_attr($tax); ?>" disabled="disabled" />
 											<span class="ib_currency"><?php echo $waehrung; ?></span>
                                    	</div>
                        			</td>
                        			<?php if (is_plugin_active('indiebooking-booking.com/indiebooking-booking.com.php')) { ?>
						            	<td class="tableInput">
							        		<input type="text" class="onlyNumber"
							        			name="default_bookingcom_rateid"
							        			value="<?php echo $defaultBookingComRateId; ?>" />
						            	</td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php //do_action("rs_indiebooking_show_zahlungsinformation_tab_kaution", $post, $appartment);?>
                    <?php do_action("rs_indiebooking_show_zahlungsinformation_tab_aufschlag", $post, $appartment);?>
<!-- 	            	<div class="ibui_tabitembox"> -->
	                	<?php do_action("rs_indiebooking_show_zahlungsinformation_tab_degression", $post, $appartment);?>
	                	<?php //do_action("rs_indiebooking_show_zahlungsinformation_tab_saison", $post, $appartment, $mwsts, $bookedDates);?>
<!-- 	                </div> -->
                    <div><?php do_action("rs_indiebooking_show_zahlungsinformation_tab_saison", $post, $appartment, $mwsts, $bookedDates);?></div>
                </div>
            </div>
            <div class="rsib_col-lg2-5 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_md">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Tax Key for this Apartment', 'indiebooking'); ?></h2></div>
                    <div class="rsib_form-group" style="max-width: 220px;">
                        <label class="rsib_col-xs-3 rsib_nopadding_left ibui_text_left ibui_label"><?php _e('Tax', 'indiebooking'); ?></label>
                        <div class="rsib_col-xs-9 rsib_nopadding_left">
                            <select id="appartment_price_mwst" class="rewa_combobox input-group-field-no-bs ibui_select" name="appartment_price_mwst">
                                <?php
                                    $calcMwst = 0;
                                    foreach ($mwsts as $mwst) {
                                        if ($mwst->getMwstId() == $appartment->getMwstId()) {
                                            $selected = 'selected="selected"';
                                            $calcMwst = ($mwst->getMwstValue() / 100);
                                        } else {
                                            $selected = "";
                                        }
                                    ?>
                                        <option data-mwst="<?php echo $mwst->getMwstValue(); ?>" <?php echo $selected;?>
                                        		value="<?php echo esc_attr($mwst->getMwstId());?>"><?php echo $mwst->getMwstValue()."%"; ?>
                                		</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
				<?php
				if (is_plugin_active('indiebooking-booking.com/indiebooking-booking.com.php')) {
					do_action("rs_indiebooking_apartment_bookingcom_aufschlag", $post, $appartment, $waehrung);
				}
				?>
<!--                 <div class="rsib_col-xs-12 rsib_col-md-6 rsib_nopadding_left"> -->
				<?php do_action("rs_indiebooking_show_zahlungsinformation_tab_kaution", $post, $appartment, $waehrung);?>
<!--                 </div> -->
                <div class="ibui_tabitembox" style="margin-bottom:0px;">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Your selected payment method', 'indiebooking'); ?></h2></div>
                    <ul>
                    	<?php do_action("rs_indiebooking_show_selected_payment_method");?>
                    </ul>
                    <?php
                    if (!is_plugin_active('indiebooking-paypal/indiebooking-paypal.php')
                    	|| !is_plugin_active('indiebooking-stripe/indiebooking-stripe.php')) {
                    ?>
                    <div class="ibui_pro_notice" style="margin: 50px 0px 0px 0px;">
                    	<?php _e("You want to have more payment options?")?><br>
                    	<?php _e("Look at the pro extension plugin")?><br>
                        <!--
                        Sie wollen weitere Zahlungsmöglichkeiten bei Ihren Apartments hinzufügen?<br>
                        Schauen Sie doch mal in die Pro-Version<br>
                        -->
                        <a href="http://www.indiebooking.de" target="_blank">www.indiebooking.de</a>
                    </div>
                    <?php } ?>
                 </div>
            </div>
        </div>
        <!--<div class="rsib_row">
            <div class="rsib_col-lg-12 rsib_col-md-12 rsib_nopadding_right rsib_nopadding_md"
            		style="max-width: 800px;">
            	<div class="ibui_tabitembox">
                	<?php //do_action("rs_indiebooking_show_zahlungsinformation_tab_degression", $post, $appartment);?>
                	
                </div>
            </div>
        </div>-->
    </div>
<?php }
}?>
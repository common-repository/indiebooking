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
<?php
/* @var $buchungKopf RS_IB_Model_Buchungskopf */
/* @var $teilKopf    RS_IB_Model_Teilbuchungskopf */
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><?php _e("Full prices", 'indiebooking')?></h3>
	</div>
	<div class="panel-body">
			<?php
			var_dump($buchungKopf);
// 			var_dump($buchungObj);
        	 if (!isset($waehrung)) {
        	     $waehrung = "";
        	 }
	       ?>
        <table id="full_price_table">
        		<tbody>
        		<tr>
        			<td class="myTableRow"><?php _e('Gross (full)', 'indiebooking');?></td>
        			<td class="myTableRow appartment_price_table_definition"><?php echo number_format($buchungObj->getFullBrutto(), 2, ',', '.').$waehrung; ?></td>
        		</tr>
        	 <?php
        	 foreach ($buchungKopf->getTeilkoepfe() as $teilKopf) {
        	     if ($aktionToCalc->getCalcType() == RS_IB_Model_Appartmentaktion::AKTION_CALC_TOTAL) {
        	         if ($aktionToCalc->getExpelPriceKz() == "on") {
        	             $typeSign = $waehrung;
        	             if ($aktionToCalc->getValueType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_PERCENT) {
        	                 $typeSign = '%';
        	             }
        	             ?>
                          <tr>
                              <td class="myTableRow">- <?php echo $aktionToCalc->getName(); ?></td>
                              <td class="myTableRow appartment_price_table_definition">- <?php echo number_format($aktionToCalc->getPreis(), 2, ',', '.').$typeSign; ?></td>
                          </tr>
                          <?php
        	         }
        	     }
        	 }
             ?>
        	 <?php foreach ($buchungObj->getCoupons() as $couponKey => $coupon) {
        	     if ($coupon->getGutscheinKz() == "off") {
        	        $typeSign = $waehrung;
                    if ($coupon->getType() == 2) { //PROZENT
                        $typeSign = "%";
                    }
                ?>
             <tr>
                 <td class="myTableRow">- <?php echo $coupon->getCode(); ?></td>
                 <td class="myTableRow appartment_price_table_definition">- <?php echo number_format($coupon->getValue(), 2, ',', '.').$typeSign; ?></td>
             </tr>
             <?php }
        	 } ?>
             <?php foreach ($buchungObj->getAllTaxes() as $taxKey => $taxValue) { ?>
             <tr>
                 <td class="myTableRow"><?php echo __('Gross', 'indiebooking') . " (".$taxKey."%)"; ?></td>
                 <td class="myTableRow appartment_price_table_definition"><?php echo number_format($taxValue["brutto"], 2, ',', '.').$waehrung; ?></td>
             </tr>
             <?php } ?>
             <tr>
                 <td class="myTableRow"><?php _e('Net', 'indiebooking');?></td>
                 <td class="myTableRow appartment_price_table_definition"><?php echo number_format($buchungObj->getCalculatedNetto(), 2, ',', '.').$waehrung; ?></td>
             </tr>
             <?php foreach ($buchungObj->getAllTaxes() as $taxKey => $taxValue) { ?>
             <tr>
                 <td class="myTableRow"><?php echo $taxKey."%"; ?></td>
                 <td class="myTableRow appartment_price_table_definition"><?php echo number_format($taxValue["taxValue"], 2, ',', '.').$waehrung; ?></td>
             </tr>
             <?php } ?>
             <tr>
                 <td class="myTableRow"><?php _e('Gross', 'indiebooking');?></td>
                 <td class="myTableRow appartment_price_table_definition"><?php echo number_format($buchungObj->getCalculatedBrutto(), 2, ',', '.').$waehrung; ?></td>
             </tr>
        	 <?php foreach ($buchungObj->getCoupons() as $couponKey => $coupon) {
        	     if ($coupon->getGutscheinKz() == "on") { ?>
             <tr>
                 <td class="myTableRow">- <?php echo $coupon->getCode(); ?></td>
                 <td class="myTableRow appartment_price_table_definition">- <?php echo number_format($coupon->getValue(), 2, ',', '.').$waehrung; ?></td>
             </tr>
             <?php }
        	 } ?>
        	 <tr>
                 <td class="myTableRowBold"><?php _e('Payable amount', 'indiebooking');?></td>
                 <td class="myTableRowBold appartment_price_table_definition"><?php echo number_format($buchungObj->getCalculatedEndBrutto(), 2, ',', '.').$waehrung; ?></td>
             </tr>
             </tbody>
        </table>
    </div>
	<div id="coupon_code_box">
		<div class="form-group">
    		<div class="col-xs-2">
    			<label for="apartment_coupon_code" class="control-label"><?php _e("Coupon Code", 'indiebooking')?></label>
    		</div>
    		<div class="col-xs-10">
                <div id="coupon_input_group" class="input-group">
    				<input class="form-control" type="text" id="apartment_coupon_code" value="" placeholder="<?php _e("Coupon Code", 'indiebooking'); ?>"/>
                  	<span class="input-group-addon" id="btn_coupon_span"><a id="btnApplyCouponCode" class="btn btn-primary btn-lg"><?php _e("apply", 'indiebooking')?></a></span>
                </div>
            </div>
    	</div>
    	<br class="clear">
	</div>
</div>

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
/* @var $buchungObj RS_IB_Model_Appartment_Buchung */
/* @var $aktion RS_IB_Model_Appartmentaktion */
/* @var $position RS_IB_Buchungsposition */
/* @var $optionPositions RS_IB_Buchungsposition */
/* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
?>
<?php
if (sizeof($optionPositions) > 0) {
//todo auslagern in action??
$waehrung = rs_ib_currency_util::getCurrentCurrency();
?>
<h3><?php _e("Booked options", 'indiebooking')?></h3>
<table>
    <thead>
        <tr>
        	<th>&nbsp;</th>
            <th class="myTableRow"><?php _e("option", 'indiebooking')?></th>
            <th class="myTableRow"><?php _e("price", 'indiebooking')?></th>
            <th class="myTableRow"><?php _e("calculation", 'indiebooking')?></th>
            <th class="myTableRow"><?php _e("tax", 'indiebooking')?></th>
            <!--<th class="myTableRow"><?php //_e("net", 'indiebooking')?></th>
            <th class="myTableRow"><?php //_e("taxes", 'indiebooking')?></th>-->
            <th class="myTableRow"><?php _e("gross", 'indiebooking')?></th>
        </tr>
    </thead>
    <tbody>
    <?php
    $tdClass    = "myTableRow";
    foreach ($optionPositions as $optionPosition) {
        $option = $optionPosition->getPositionObject();
        ?>
        <tr>
        	<td><?php echo $optionPosition->getName(); ?></td>
            <td class="myTableRow"><?php echo $option['name']; ?></td>
            <td class="myTableRow appartment_price_table_definition"><?php echo $option['price'].$waehrung; ?></td>
            <?php if (class_exists("RS_IB_Model_Appartmentoption")) { ?>
            	<td class="myTableRow"><?php echo RS_IB_Model_Appartmentoption::getCalculationTypeString($option['calculation']); ?></td>
            <?php } else { ?>
            	<td class="myTableRow">&nbsp;</td>
            <?php } ?>
            <td class="myTableRow appartment_price_table_definition"><?php echo number_format($option['mwstPercent'], 2, ',','.');?>%</td>
            <!-- <td class="myTableRow appartment_price_table_definition"><?php //echo number_format($position->getNetto(), 2, ',', '.').$waehrung;?></td>
            <td class="myTableRow appartment_price_table_definition"><?php //echo number_format($position->getTax(), 2, ',', '.').$waehrung;?></td> -->
            <td class="<?php echo $tdClass; ?> appartment_price_table_definition"><?php echo number_format($optionPosition->getBrutto(), 2, ',', '.').$waehrung;?></td>
        </tr>
    <?php } ?>
	</tbody>
</table>
<br />
<?php } ?>

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
    $optionen = $buchung->getOptions();
?>
<table id="possibleOptionsTable" class="borderTable">
    <tr class="borderTable">
        <th class="borderTable"><?php _e("Possible Options", 'indiebooking')?></th>
        <th class="borderTable"><?php _e('Tax', 'indiebooking'); ?></th>
    </tr>
    <?php
    if (class_exists("RS_IB_Model_Appartmentoption")) {
        foreach ($appartment->getOptionen() as $appartmentOption) {
            foreach ($allMwst as $curMwSt) {
                if ($curMwSt->getMwstId() == $appartmentOption->getMwSt()) {
                    $currentMwst = $curMwSt->getMwstValue();
                    break;
                }
            }
            $optionLabel = $appartmentOption->getName() . " - " . $appartmentOption->getPreis() . '(' . RS_IB_Model_Appartmentoption::getCalculationTypeString($appartmentOption->getCalcType()) . ')';
            $checked = '';
            foreach ($optionen as $book_option) {
                if ($appartmentOption->getTermId() == $book_option['id']) {
                    $checked = 'checked = "checked"';
                    break;
                }
            }
        ?>
        <tr class="borderTable" data-optionId = "<?php echo $appartmentOption->getTermId(); ?>" data-price="<?php echo $appartmentOption->getPreis(); ?>" data-tax="<?php echo $currentMwst; ?>" data-calculation="<?php echo $appartmentOption->getCalcType();?>">
            <td class="borderTable"><input type='checkbox' <?php echo $checked; ?>class="cb_choose_option"/>&nbsp;&nbsp;<?php echo $optionLabel; ?></td>
            <td class="borderTable"><?php echo $currentMwst ." %"?></td>
        </tr>
        <?php }
    }?>
</table>
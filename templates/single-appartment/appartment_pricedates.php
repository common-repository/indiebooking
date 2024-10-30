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
<div id="appartmentPriceDateBox">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e("Appartment prices", 'indiebooking'); ?></h3>
		</div>
    	<div class="panel-body">
        	<?php if (!is_null($appartmentdates) && sizeof($appartmentdates) > 0) {
        		$showApartmentPrices = true;
        		if (sizeof($appartmentdates) == 1) {
        			$checkfirstprice = $appartmentdates[0]['price'];
        			$showApartmentPrices = ($checkfirstprice != "" && intval($checkfirstprice) > 0);
        		}
        		if ($showApartmentPrices) {
        		?>
        	<table>
        		<tr>
        			<th class="daterow"><?php _e("From", 'indiebooking');?></th>
        			<th class="daterow"><?php _e("To", 'indiebooking');?></th>
        			<th style="text-align: right;"><?php _e("Price per Night", 'indiebooking');?></th>
        		</tr>
        		<?php foreach ($appartmentdates as $date) { ?>
        		<tr>
        			<td class="daterow"><?php echo $date['from']; ?></td>
        			<td class="daterow"><?php echo $date['to'];  ?></td>
        			<td style="text-align: right;"><?php echo $date['price']; ?>
        				<?php rs_ib_currency_util::getCurrentCurrency(); ?>
        			</td>
        		</tr>
        		<?php }?>
        	</table>
        	<?php }
			} ?>
    	</div>
	</div>
</div>
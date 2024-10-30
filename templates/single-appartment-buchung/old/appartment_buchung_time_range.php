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
//echo $buchungObj->getStartDate();
/* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
?>
<div id="booked_timerange_box">
    <table>
        <thead>
            <tr>
            	<th class="rs_ib_global_booking_info_td"><?php _e('Appartment', 'indiebooking')?></th>
                <th class="rs_ib_global_booking_info_td"><?php _e('booked from', 'indiebooking')?></th>
                <th class="rs_ib_global_booking_info_td"><?php _e('booked to', 'indiebooking')?></th>
                <th class="rs_ib_global_booking_info_td"><?php _e('number of nights', 'indiebooking')?></th>
            </tr>
        </thead>
        <tbody>
        	<?php foreach ($teilKoepfe as $teilKopf) {
        	$von   = $teilKopf->getTeilbuchung_von();
        	$bis   = $teilKopf->getTeilbuchung_bis();
        	if ($von instanceof DateTime) {
        	    $von = $von->format("d.m.Y");
        	}
        	if ($bis instanceof DateTime) {
        	    $bis = $bis->format("d.m.Y");
        	}
        	?>
            <tr>
            	<td><?php echo $teilKopf->getAppartment_name();?></td>
                <td><?php echo $von?></td>
                <td><?php echo $bis;?></td>
                <td><?php echo $teilKopf->getNumberOfNights();?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
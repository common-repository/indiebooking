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
/**
 * $contact muss zuvor inkludiert sein.
 */

/* @var $buchungObj RS_IB_Model_Appartment_Buchung */
/* @var $aktion RS_IB_Model_Appartmentaktion */
/* @var $position RS_IB_Buchungsposition */
/* @var $optionPositions RS_IB_Buchungsposition */
/* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
?>
<div id="booked_timerange_box">
    <table>
        <thead>
            <tr>
                <th><?php _e('booked from', 'indiebooking')?></th>
                <th><?php _e('booked to', 'indiebooking')?></th>
                <th><?php _e('number of nights', 'indiebooking')?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo $buchungObj->getStartDate();?></td>
                <td><?php echo $buchungObj->getEndDate();?></td>
                <td><?php echo $buchungObj->getAnzahlNeachte();?></td>
            </tr>
        </tbody>
    </table>
</div>
<?php

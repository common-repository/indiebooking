<?php
/*
* Indiebooking - die Buchungssoftware fuer Ihre Homepage!
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
/* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
?>
<div id="booked_timerange_box_sm">
    <table class="table table-condensed table-responsive">
        <thead>
            <tr>
            	<th class=""><?php _e('Appartment', 'indiebooking')?></th>
                <th class=""><?php _e('booked from', 'indiebooking')?></th>
                <th class=""><?php _e('booked to', 'indiebooking')?></th>
                <th class=""><?php _e('number of nights', 'indiebooking')?></th>
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
            	<td><?php
	            	if (!$showCategoryAsName) {
	            		echo $teilKopf->getAppartment_name();
	            	} else {
	            		echo $teilKopf->getAppartment_category_name();
	            	}
            	?>
            	</td>
                <td><?php echo $von?></td>
                <td><?php echo $bis;?></td>
                <td><?php echo $teilKopf->getNumberOfNights();?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<div id="booked_timerange_box_xs">
	<?php
	foreach ($teilKoepfe as $teilKopf) {
    	$von   = $teilKopf->getTeilbuchung_von();
    	$bis   = $teilKopf->getTeilbuchung_bis();
    	if ($von instanceof DateTime) {
    	    $von = $von->format("d.m.Y");
    	}
    	if ($bis instanceof DateTime) {
    	    $bis = $bis->format("d.m.Y");
    	}
	?>
    <table class="table table-condensed table-responsive">
        <thead>
            <tr>
            	<th class=""><?php echo $teilKopf->getAppartment_name();?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php _e('booked from', 'indiebooking')?>:</td>
                <td><?php echo $von?></td>
            </tr>
            <tr>
                <td><?php _e('booked to', 'indiebooking')?>:</td>
                <td><?php echo $bis;?></td>
            </tr>
            <tr>
                <td><?php _e('number of nights', 'indiebooking')?>:</td>
                <td><?php echo $teilKopf->getNumberOfNights();?></td>
            </tr>
        </tbody>
    </table>
<?php } ?>
</div>
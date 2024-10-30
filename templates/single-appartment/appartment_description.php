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
?>
<div class="apartment_detail">
    <h2 id="subnav_beschreibung" class="anchor"><?php _e("description", "indiebooking");?></h2>
    <?php echo apply_filters('the_excerpt',$description); ?>
</div>
<div class="apartment_detail">
	<?php
	if (isset($maxAnzPers) && intval($maxAnzPers) > 0
		|| isset($wohnflaeche) && intval($wohnflaeche) > 0
		|| isset($anzahlZimmer) && intval($anzahlZimmer) > 0
		|| ((isset($anzahlDoppelBetten) && intval($anzahlDoppelBetten) > 0)
				|| (isset($anzahlEinzelBetten) && intval($anzahlEinzelBetten) > 0))) {
	?>
	<!-- Details und Ausstattung -->
    <h2 id="subnav_details" class="anchor"><?php _e("Details and features", "indiebooking");?></h2>
    <table class="table table-condensed  table-striped table-responsive">
        <tbody>
        	<?php
        	if (isset($maxAnzPers) && intval($maxAnzPers) > 0) { ?>
            <tr>
                <th width="30%"><?php _e("max. number of people", 'indiebooking'); ?></th><!-- Max. Personenzahl -->
                <td><?php echo $maxAnzPers; ?></td>
            </tr>
            <?php }
            if (isset($wohnflaeche) && intval($wohnflaeche) > 0) { ?>
            <tr>
                <th><?php _e("living space", 'indiebooking'); ?></th><!-- Wohnflaeche -->
                <td><?php echo $wohnflaeche; ?>&nbsp;m&sup2;</td>
            </tr>
            <?php }
            if (isset($anzahlZimmer) && intval($anzahlZimmer) > 0) { ?>
            <tr>
                <th><?php _e("rooms", 'indiebooking'); ?></th><!-- Zimmer -->
                <td><?php echo $anzahlZimmer;?></td>
            </tr>
            <?php }
            if ((isset($anzahlDoppelBetten) && intval($anzahlDoppelBetten) > 0)
				|| (isset($anzahlEinzelBetten) && intval($anzahlEinzelBetten) > 0)) { ?>
            <tr>
                <th><?php _e("beds", "indiebooking");?></th>
                <td>
                	<?php echo $anzahlDoppelBetten; ?>
                	<?php _e("Double beds", "indiebooking");?>
                	<br>
                	<?php echo $anzahlEinzelBetten; ?>
                	<?php _e("Single beds", "indiebooking");?>
            	</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php
	}
    ?>
    <?php
    	do_action("rs_indiebooking_show_apartment_features", $appartment->getPostId());
    ?>
</div>
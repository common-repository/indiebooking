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
if (intval($maxAnzPersonen) > 0) {
?>
<h5><?php _e("Any number of people", 'indiebooking'); ?></h5>
<div class="row">
    <div class="col-xs-6 col-md-2">
    	<div class="col-xs-12 buchungsplugin_startseite_filter_form form_icon icon_personen">
            <select class="rs_ib_buchung_anzahl_personen form-control" name="rs_ib_buchung_anzahl_personen" tabindex="1"><!-- multiple="multiple" -->
            	<?php
            	for ($i = 1; $i <= $maxAnzPersonen; $i++) {
            	   $selected = "";
            	   if ($curAnzPersonen == $i) {
            	       $selected = 'selected="selected"';
            	   }
            	    ?>
            	    <option value="<?php echo $i?>"<?php echo $selected; ?>><?php echo $i; ?>
            	<?php } ?>
            </select>
        </div>
    </div>
</div>
<br />
<?php } ?>
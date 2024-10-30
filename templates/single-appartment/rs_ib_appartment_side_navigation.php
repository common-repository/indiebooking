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
<li class="active"><!-- Fotos &amp; Verfuegbarkeit -->
	<a href="#subnav_fotos">
		<?php _e('Photos', 'indiebooking'); ?>&nbsp;&amp;&nbsp;<?php _e('Availability', 'indiebooking'); ?>
	</a>
</li>
<li><!-- Buchungszeitraum &amp; Optionen -->
	<a href="#subnav_buchung">
		<?php _e('Booking period', 'indiebooking'); ?>
	</a>
</li>
<li><!-- Beschreibung -->
	<a href="#subnav_beschreibung">
		<?php _e('Description', 'indiebooking'); ?>
	</a>
</li>
<?php
if ($showDetail) {
?>
<li><!-- Ausstattung -->
	<a href="#subnav_details">
		<?php _e('Equipment', 'indiebooking'); ?>
	</a>
</li>
<?php
}
?>
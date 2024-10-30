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
<div class="apartment_detail_buchen background_lightgreen">
	<h5><?php _e("Price", 'indiebooking')?></h5>
	<?php if ($istBuchbar) { ?>
		<p class="apartment_detail_preis">
			<?php printf(_x('as of %1$s %2$s', 'Price as of', 'indiebooking'), $smallesprice, $waehrung); ?>
			<!--
			<?php //_e("as of", 'indiebooking');?>&nbsp;
			<?php //echo $smallesprice; ?>&nbsp;
			<?php //echo $waehrung; ?>
			 -->
		</p>
    	<!-- <p class="apartment_detail_preisinfo"><?php //_e("per Person / per Night", 'indiebooking')?></p> -->
    	<p class="apartment_detail_preisinfo"><?php _e("per Night", 'indiebooking')?></p>
    <?php } else { ?>
    	<p class="apartment_detail_preis"><?php _e("currently not bookable", 'indiebooking');?></p>
    <?php } ?>
</div>
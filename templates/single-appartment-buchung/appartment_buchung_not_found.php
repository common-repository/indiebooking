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
$timeout = 1500;
if ($reloaddirect == 1) {
	$timeout = 0;
}
?>
<?php do_action("rs_indiebooking_single_rsappartment_buchung_header",-2, get_the_ID()); ?>
<div class="container text-center" style="padding-bottom:50px;">
    <div class="alert alert-danger text-center" role="alert" style="margin:30px 0px 40px 0px;">
    	<strong><?php _e("Booking was canceled", 'indiebooking'); ?>.<br><?php _e("You are returned to the Home", 'indiebooking'); ?></strong><!-- Sie werden zurück zur Startseite geleitet. -->
    </div>
    <a href="<?php echo get_option('home'); ?>" class="btn green"><?php _e("Back to Home", "indiebooking"); ?></a><!-- Zurück zur Startseite -->
</div>

<script type="text/javascript">
<!--
//-->
function returnToStartPage() {
	location.href="<?php echo esc_url(home_url( '/' )); ?>";
   	/*location.href="<?php //echo get_option('home'); ?>";*/
}
window.setTimeout("returnToStartPage()", <?php echo $timeout; ?>);
</script>
<?php
//ist das notwendig?
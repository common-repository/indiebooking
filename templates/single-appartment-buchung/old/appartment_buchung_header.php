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
<header class="entry-header">
	<?php if ( is_single() ) : ?>
	<h1 class="entry-title"><?php //the_title(); //<-- gibt den Titel aus?></h1>
	<?php else : ?>
	<h1 class="entry-title">
		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php //the_title(); ?></a>
	</h1>
	<?php endif; // is_single() ?>
	<?php if ( has_post_thumbnail() && ! post_password_required() && ! is_attachment() ) : ?>
<!-- 		<div class="entry-thumbnail"> -->
		<?php //the_post_thumbnail(); //<-- gibt das Bild aus.?>
<!-- 		</div> -->
	<?php endif; ?>
	<div class="entry-meta">
		<?php //twentythirteen_entry_meta(); ?>
		<?php //edit_post_link( __( 'Edit', 'twentythirteen' ), '<span class="edit-link">', '</span>' ); ?>
	</div><!-- .entry-meta -->
<!-- 	<span>hier Button um weitere Appartments hinzuzufuegen</span> -->
	<?php
	$clickclass     = "navigate_possible";
	$progressclass1 = "progress_ok1";
	$progressclass2 = "progress_ok2";
	$progressclass3 = "progress_ok3";
	$progressclass4 = ""; //progress_ok4
// 	echo "Hierher ".$biggestPagekz;
	?>
	<div id="booking_status_box_menu" data-pagekz="<?php echo $pagekz; ?>">
		<ul class="nav nav-tabs nav-justified">
    		<li role="presentation" class="status_box <?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> <?php echo ($biggestPagekz >= 1 && $active1 == "") ? $progressclass1 : "";?> <?php echo $active1;?>"><a class="<?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> navbtnBookingInfo" ><?php _e("Booking info", 'indiebooking'); ?></a></li>
    		<li role="presentation" class="status_box <?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> <?php echo ($biggestPagekz >= 1 && $active2 == "") ? $progressclass2 : "";?> <?php echo $active2;?>"><a class="<?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> navbtnBookingContact"><?php _e("Booking Contact", 'indiebooking'); ?></a></li>
    		<li role="presentation" class="status_box <?php echo ($biggestPagekz >= 2) ? $clickclass : "";?> <?php echo ($biggestPagekz >= 2 && $active3 == "") ? $progressclass3 : "";?> <?php echo $active3;?>"><a class="<?php echo ($biggestPagekz >= 2) ? $clickclass : "";?> navbtnBookingOverview"><?php _e("Booking overview", 'indiebooking'); ?></a></li>
    		<li role="presentation" class="status_box <?php echo ($biggestPagekz >= 4) ? $clickclass : "";?> <?php echo ($biggestPagekz >= 4 && $active4 == "") ? $progressclass4 : "";?> <?php echo $active4;?>"><a class="<?php echo ($biggestPagekz >= 4) ? $clickclass : "";?> navbtnBookingConfirmation"><?php _e("confirmation", 'indiebooking'); ?></a></li>
		</ul>
	</div>
</header><!-- .entry-header -->
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
/**
 * The Template for displaying all single apartments.
 *
 * Override this template by copying it to yourtheme/templates/single-rsappartment.php
 *
 * @author 		Indiebooking
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php get_header(); ?>
<?php do_action('indiebooking_before_main_content');?>
<!-- 		<div class="main_container container"> -->
	<?php //do_action("rs_indiebooking_single_rsappartment_header", get_the_title());?>
	
	<?php do_action("rs_indiebooking_single_rsappartment_header", get_the_ID());?>
	<section id="apartment_detail">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php
			$apid = get_the_ID();
			 cRS_Template_Loader::rs_ib_get_template_part('content', 'single-rsappartment');
		      ?>
		<?php endwhile; // end of the loop. ?>
	</section>
<?php do_action('indiebooking_after_main_content');?>
<?php
get_footer(  );

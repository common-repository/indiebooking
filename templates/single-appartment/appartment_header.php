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
$postId = get_the_ID();
?>
<input type="hidden" id="appartmentPostId" name="appartmentPostId" value="<?php echo esc_attr($postId);?>">
<input type="hidden" id="bookingPostId" name="bookingPostId" value="">
<div class="modal"></div>
<header id="unterseite">
	<?php //do_action("rs_indiebooking_show_navbar", 3); ?>
	<?php
		if (function_exists('rs_indiebooking_show_indiebooking_default_header_menu')) {
			rs_indiebooking_show_indiebooking_default_header_menu();
		}
	?>
    <div id="headerimage"></div>
</header>

<section id="text_wordpress">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-centered">
                <?php //if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
                    <h1 id="subnav_fotos">
                    	<?php //the_title();?>
						<?php
						if ($showCategoryAsName) {
							echo $firstCategoryName;
						} else {
							the_title();
						}
						?>
                    </h1>
                    <?php //the_content(); ?>
                <?php //endwhile; ?>
            </div>
        </div>
    </div>
</section>
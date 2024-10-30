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
<div class="col-md-4 col-sm-6 col-xs-12 item_aparment">
    <div class="item_image">
		<?php
// 		  do_action('rs_indiebooking_single_appartment_profile_picture', null);
		do_action('rs_indiebooking_list_rsappartment_gallery', null, $showAction);
	    ?>
    </div>
    <div class="item_text">
        <h3><a href="<?php the_permalink() ?>">
        	<?php //the_title(); ?>
			<?php
			if ($showCategoryAsName) {
				echo $firstCategoryName;
			} else {
				the_title();
			}
			?>
        </a></h3>
        <div class="item_description">
            <?php echo $kurztext; ?><br>
            <a href="<?php the_permalink() ?>"><?php _e("More infos", "indiebooking");?></a><br>
	        <?php //echo $minAufenthalt; ?>
        </div>
        <div class="item_info">
        	<?php if ($price !== "") { ?>
            <p class="price">
            	<?php printf(
            	    /* TRANSLATORS: %1$s = price %2$s = currency */
            	   __('as of %1$s %2$s per night', 'indiebooking'),
            	    $price,
            	    $waehrung
        	    );
            	?>
        	</p>
            <a href="<?php the_permalink() ?>" class="btn green">
            	<?php
					if (!$inquiry) {
            			_e("book", 'indiebooking');
					} else {
						_e("inquire", 'indiebooking');
					}
            	?>
			</a>
            <?php } else { ?>
            <p class="price">
            	<?php _e("currently not bookable", 'indiebooking');?>
            </p>
            <?php } ?>
        </div>
    </div>
</div>
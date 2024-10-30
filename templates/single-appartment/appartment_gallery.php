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
$bildcount 	= 0;
$count 		= 0;
if ( ! empty( $attachments ) ) {
	$bildcount = sizeof($attachments);
}
if (intval($bildcount) > 0) {
?>
<div class="appartment_gallerie container item_image_detail">
	<?php
	$carouselId = "carousel-example-generic".$appartmentId;
	?>
    <div id="<?php echo $carouselId; ?>" data-interval="5000" class="rs_ib_carousel carousel slide" data-ride="carousel">
  <!-- Indicators --> <!-- Positionsanzeiger -->
		<ol class="carousel-indicators">
		<?php
  		for ($bild = 0; $bild <= $bildcount; $bild++) { ?>
  		    <li data-target="#fullcarousel-example" data-slide-to="<?php echo esc_attr($bild); ?>"
  		    	class="<?php echo ($bild == 0) ? "active" : "";?>">
	    	</li>
  		<?php } ?>
  		</ol>
    	<div class="carousel-inner" role="listbox">
                <?php
        	    if ( ! empty( $attachments ) ) {
        	        foreach ( $attachments as $attachment_id ) {
        	            $imageThumb1 	= wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
        	            $imageThumb 	= wp_get_attachment_image_src( $attachment_id, '362' );
        	            $imageFull 		= wp_get_attachment_image_src( $attachment_id, 'full' );
        	            ?>
    					<div class="item <?php echo ($count == 0) ? "active" : ""?> apartmentimage ib_lazy_load_apartmentimage ibloading"
    							data-imageUrl = "<?php echo esc_url($imageFull[0]);?>">
    					</div><!-- style="background-image:url(<?php //echo esc_url($imageFull[0]);?>)" -->
        	            <?php
        	            $count++;
        	        }
        	    }
    	    ?>
    	</div>
        <!-- Schalter -->
        <?php if ($count > 1) { ?>
        <a class="left carousel-control" href="#<?php echo esc_attr($carouselId); ?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only"><?php _e("back", 'indiebooking'); ?></span>
        </a>
        <a class="right carousel-control" href="#<?php echo esc_attr($carouselId); ?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only"><?php _e("continue", 'indiebooking'); ?></span>
        </a>
        <?php } ?>
    </div>
</div><!-- #appartment_gallerie -->
<?php
}?>
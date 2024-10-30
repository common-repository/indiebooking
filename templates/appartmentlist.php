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
/*
 * Diese Datei in den Ordner des Themes kopieren, um die Anzeige der gefundenen Appartments zu veraendern.
 */
if (!isset($isBookable)) {
    $isBookable = false;
}
?>
<div class="rs_ib_apartmentItem row_bordered_item nopadding-bottom">
    <div class="row">
        <div class="col-md-4 col-sm-3 col-xs-12">
            <?php
                do_action('rs_indiebooking_list_rsappartment_gallery', null);
            ?>
            <?php //echo $minAufenthalt; ?>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-12">
            <div class="inner_padding_xs">
                <h2>
                	<a href="<?php the_permalink(); ?>">
                		<?php
                		if ($showCategoryAsName) {
	                		echo $firstCategoryName;
                		} else {
	               			the_title();
                		}
                		?>
                	</a>
            	</h2>
                <?php if ($isBookable == true) { ?>
                	<p class="price_large">
                	<?php
                	$waehrung = rs_ib_currency_util::getCurrentCurrency();
                	printf(_x('as of %1$s %2$s', 'Price as of', 'indiebooking'), $bookPrice, $waehrung);
                	?>
            		</p>
                <?php } else { ?>
                   <p class="price_large"><?php _e("currently not bookable", 'indiebooking');?></p>
                <?php } ?>
                <?php echo $kurztext; ?>
                <br>
                <a href="<?php the_permalink() ?>"><?php _e("read more", "indiebooking"); ?></a>
                <br>
            </div>
        </div>
        <div class="col-md-3 col-sm-4 col-xs-12 item_uebersicht">
        	<?php do_action("rs_indiebooking_show_apartment_list_extra_infos", $isBookable)?>
        </div>
    </div>
    <div class="row background_lightgreen inner_padding" style="margin-top:15px;">
    	<?php
    	   do_action("rs_indiebooking_show_apartment_list_item_footer_infos", $isBookable);
	   ?>
    </div>
</div>
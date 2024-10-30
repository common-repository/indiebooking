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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="rsib_container-fluid">
	<div class="rsib_row">
        <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2">
        	<div class="ibui_tabitembox">
	        	<a class="ibui_add_btn" id="btnPrintTestBooking">
	        		<?php _e("test print", "indiebooking");?>
	        	</a>
        	</div>
        </div>
	</div>
    <div class="rsib_row">
        <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap"><h2 class="ibui_h2">Logo</h2></div>
                <?php
                    $hasImage       = false;
                    $image          = wp_get_attachment_image_src( $pdf_image_id, 'medium' );
                    $image_url      = $image[0];
                    if (!is_null($pdf_image_id) && $pdf_image_id !== "" && $pdf_image_id > 0) {
                        $hasImage   = true;
                    }
                ?>
                <div>
                    <label for="option_image_id"><?php _e("Logo for your prints", 'indiebooking');?></label>
                    <input id="option_image_id" type="hidden" name="rs_indiebooking_settings[pdf_image_id]"
                    		value="<?php echo esc_attr($pdf_image_id); ?>" class="regular-text">
                </div>
                <div id="rs_ib_pdf_logo_container" style="margin: 20px 0px 20px 0px;">
                    <?php if ($hasImage) { ?>
                    <img class="rs_ib_pdf_logo" src="<?php echo esc_url($image_url); ?>" />
                    <?php } ?>
                </div>
                <!-- im beiden input-buttons class "button-secondary" entfernt -->
                <input id="upload_pdf_img-btn" type="button" name="upload-btn" class="ibui_btn ibui_btn_lightgrey" value="<?php _e("Upload Image", "Bild hochladen");?>">
                <input id="delete_pdf_img-btn" type="button" name="delete-btn" class="ibui_btn ibui_btn_lightgrey" value="<?php _e("Remove Image", "Bild entfernen");?>"
                        <?php if (!$hasImage) echo 'style="display: none;"'; ?>>
                <br /><br />
                <div style="visibility: hidden;">
                	<input class="ibui_input" id='rs_indiebooking_settings_paypal_danketext' type='text'
                			name='rs_indiebooking_settings[thankstxt]' value="<?php echo esc_attr($dankeText); ?>"
                			style="min-width: 600px;">
                </div>
            </div>
        </div>
        <?php
        do_action("rs_indiebooking_extendend_print_settings");
        ?>
    </div>
</div>
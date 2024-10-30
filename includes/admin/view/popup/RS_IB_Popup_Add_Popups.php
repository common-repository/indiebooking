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

if ( ! function_exists( 'rs_ib_create_taxonomyPopups' ) ) {
function rs_ib_create_taxonomyPopups() {
    global $post;
    
    add_action("rs_indiebooking_create_admin_popup", "rs_ib_createAddCategoryPopup");
}
}
?>
<?php
if ( ! function_exists( 'rs_ib_createAddCategoryPopup' ) ) {
function rs_ib_createAddCategoryPopup() {
    ?>
    <div id="rs_ib_taxonomy_category_popup" data-taxonomy="rsappartmentcategories"
    		class="ibui_widget ui-widget rs_ib_taxonomy_popup" data-dialogTitle="<?php _e("Create or Edit Category", 'indiebooking');?>">
<!--     	<input class="rs_ib_popup_action" type="hidden" value="" name="action"> -->
<!-- 		<input class="rs_ib_popup_tag_id"   type="hidden" value="" name="tag_ID"> -->
<!-- 		<input class="rs_ib_popup_taxonomy" type="hidden" value="" name="taxonomy"> -->
<!-- 		<input class="rs_ib_popup_original_http_refer" type="hidden" value="" name="_wp_original_http_referer"> -->
<!-- 		<input class="rs_ib_popup_wpnonce" type="hidden" value="" name="_wpnonce"> -->
<!-- 		<input class="rs_ib_popup_wp_http_referer" type="hidden" value="" name="_wp_http_referer">    	 -->
        <div class="ibui_tabitembox">
            <div class="form-field form-required term-name-wrap">
                <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Name', 'indiebooking'); ?></h2></div>
                <!--<label for="category-tag-name">Name</label>-->
                <input id="category-tag-name" type="text" aria-required="true" class="ibui_input" size="40" value="" name="tag-name">
            </div>
        </div>
        <div class="ibui_tabitembox" style="margin-bottom:0px;">
            <div class="form-field term-description-wrap">
                <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Description', 'indiebooking'); ?></h2></div>
                <!--<label for="category-tag-description">Beschreibung</label>-->
                <textarea id="category-tag-description" cols="40" rows="10" class="ibui_input" name="description"></textarea>
            </div>
        </div>
    </div>
    <?php
}
}
?>
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

add_action("rs_indiebooking_show_stammdaten2_tab", "rs_indiebooking_show_appartment_stammdaten2_tab", 10, 1);
add_action("ibui_indiebooking_show_apartment_option_tab", "rs_indiebooking_show_appartment_stammdaten2_tab_option", 10, 1);

if ( ! function_exists( 'rs_indiebooking_show_appartment_stammdaten2_tab_option' ) ) {
    function rs_indiebooking_show_appartment_stammdaten2_tab_option($post) {
        ?>
        <div class="ibui_pro_notice">
        	<?php _e("You want to add some options to your apartments?")?><br>
        	<?php _e("Look at the pro extension plugin")?><br>
            <!--
                Sie wollen weitere Optionen bei Ihren Apartments hinzufügen?<br>
                Schauen Sie doch mal in die Pro-Version
             -->
            <a href="http://www.indiebooking.de" target="_blank">www.indiebooking.de</a>
        </div>
    <?php }
}

if ( ! function_exists( 'rs_indiebooking_show_appartment_stammdaten2_tab' ) ) {
function rs_indiebooking_show_appartment_stammdaten2_tab($post) {
?>
    <div class="rsib_container-fluid">
        <div class="rsib_row">
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap">
                    	<h2 class="ibui_h2">
                    		<?php _e('Category', 'indiebooking'); ?>
                		</h2>
            		</div>
                    <?php
            		  $taxonomieArray = array (
            		      "id" => RS_IB_Model_Appartmentkategorie::RS_TAXONOMY
            		  );
            		  RS_IB_Admin_Appartment::RS_IB_mytaxonomy_metabox($post, $taxonomieArray);?>
        			<?php //include 'settings_tabs/RS_IB_Settings_View_Company_Tab.php';?>
                    <br>
                    <a href="#" id="rs_ib_btn_create_category" class="ibui_add_btn">
                    	<?php _e('Create new category', 'indiebooking'); ?>
                	</a>
                </div>
            </div>
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_md">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2">
                    	<?php _e('Possible Options', 'indiebooking'); ?></h2>
                	</div>
					<?php do_action("ibui_indiebooking_show_apartment_option_tab", $post);?>
				</div>
            </div>
        </div>
    </div>
<?php }
}?>
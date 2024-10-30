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
<!-- <div id="rs_indiebooking_hopscotch_agb_1"> -->
    <div class="rsib_container-fluid">
        <div class="rsib_row">
            <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Insert your terms and conditions: ', 'indiebooking');?></h2></div>
                    <span class="condition_editor">
                        <?php
                            $condition_settings = array(
        //                         'tinymce' => true,
        //                         'teeny' => true,
                                'editor_height' => 500,
                                'media_buttons' => FALSE,
                                'editor_class'  =>'condition_editor'
                            );
                            $agbText = get_option('rs_indiebooking_settings_booking_agb_txt'); // this var may contain previous data that was stored in mysql.
                            wp_editor($agbText,"rs_indiebooking_settings_booking_agb_txt",$condition_settings); //array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
        //                     add_filter( 'teeny_mce_buttons', 'my_editor_buttons', 10, 2 );
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
<!-- </div> -->

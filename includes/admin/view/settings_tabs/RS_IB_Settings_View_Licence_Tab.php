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
            <div class="rsib_col-lg2-12 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_xs">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Licence informations', 'indiebooking'); ?></h2></div>
                    <label>
                    <?php
                    _e("Under this section you see all of your licence information for possible indiebooking addons\n
                    		and you are able to enter your licenses", 'indiebooking');
                    ?>
                    </label>
                    <br /><br />
                    <div class="rsib_form-horizontal">
                    	<?php
                    	do_action("rs_indiebooking_show_licence_information");
                    	?>
                    </div>
                </div>
            </div>
        </div>
    </div>

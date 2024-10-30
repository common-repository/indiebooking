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
        <div class="rsib_col-lg2-6 rsib_nopadding_left rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap">
                	<h2 class="ibui_h2"><?php _e("First steps", 'indiebooking');?></h2>
                </div>
                <?php
                _e("For the right use of the Theme, you need two pages.", 'indiebooking');
                ?><br /><?php
                _e("One of the pages needs the template 'Startseite'. This is your Startpage / Welcomepage.", 'indiebooking');
                ?><br /><?php
                _e("You need to set this page in the Theme options as static start page", 'indiebooking');
                ?><br /><br /><?php
                _e("The second page needs the template 'Apartment&uuml;bersicht'. This is your Apartmentoverviewpage.", 'indiebooking');
                ?><br /><?php
                _e("On the Apartmentoverviewpage you have to insert the shortcode '[rs_ib_show_apartment_overview]'.", 'indiebooking');
                ?><br /><?php
                _e("Then all of your apartments are getting shown.", 'indiebooking');
                ?><br /><br /><br /><?php
                _e("The best way to learn how indiebooking works, is to start the tour.", 'indiebooking');
                ?>
                <br /><br />
				<label id="ibfc_btn_start_tour2" class="ibui_add_btn"><?php _e("start tour", 'indiebooking');?></label>
				<br /><br />
				<?php
				_e("In our support section on www.indiebooking.de you will find many tutorial videos.", 'indiebooking');
				?>
				<a href="https://indiebooking.de/support" target="_blank"><?php
					_e("Click here to go to the support area.", 'indiebooking');
				?></a>
            </div>
        </div>
        <div class="rsib_col-lg-6 rsib_col-md-12 rsib_nopadding_right rsib_nopadding_md">
        	<div class="ibui_tabitembox">
        		<div class="ibui_pro_notice">
                	<a href="http://www.indiebooking.de" target="_blank">www.indiebooking.de</a>
                </div>
        	</div>
        </div>
    </div>
</div>

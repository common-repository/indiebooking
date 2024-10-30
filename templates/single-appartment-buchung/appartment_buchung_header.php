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
?>
<div class="main_container">
	<div class="row margin-no">
        <header id="unterseite" class="entry-header buchungsprozess">
        	<?php
        	if (function_exists('rs_indiebooking_show_indiebooking_default_header_menu')) {
        		rs_indiebooking_show_indiebooking_default_header_menu();
        	}
        	?>
            <div id="headerimage" class="hidden-xs"></div>
            <h1 class="entry-title hidden"></h1>
        </header><!-- .entry-header -->
        <?php
        if ($pagekz > -2) {
            $clickclass     = esc_attr("navigate_possible");
            $progressclass1 = esc_attr("progress_ok1");
            $progressclass2 = esc_attr("progress_ok2");
            $progressclass3 = esc_attr("progress_ok3");
            $progressclass4 = ""; //progress_ok4
        ?>
        <section id="buchung_header">
        	<span id="synchronize_zabuto_calendars" data-synchronize="true"></span>
            <div id="booking_status_box_menu" class="container" data-pagekz="<?php echo $pagekz; ?>">
                <div class="row">
                    <div class="col-md-12">
                        <ul class="booking_status_process">
                            <li role="presentation" class="col-xs-3 status_box
                                <?php echo ($biggestPagekz >= 1) ? $clickclass : "";?>
                                <?php echo ($biggestPagekz >= 1 && $active1 == "") ? $progressclass1 : "";?>
                                <?php echo $active1;?>">
                                <a class="<?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> navbtnBookingInfo" >
                                	<span class="glyphicon glyphicon-edit"></span>
                                	<span class="hidden-xs">
                                		<?php _e("Accommodation and Options", 'indiebooking'); ?>
                            		</span>
                        		</a>
                    		</li>
                            <li role="presentation" class="col-xs-3 status_box
                                <?php echo ($biggestPagekz >= 1) ? $clickclass : "";?>
                                <?php echo ($biggestPagekz >= 1 && $active2 == "") ? $progressclass2 : "";?>
                                <?php echo $active2;?>">
                                <a class="<?php echo ($biggestPagekz >= 1) ? $clickclass : "";?> navbtnBookingContact">
                                	<span class="glyphicon glyphicon-credit-card"></span>
                                	<span class="hidden-xs"><?php _e("Your data and payment", 'indiebooking'); ?></span>
                            	</a>
                        	</li>
                            <li role="presentation" class="col-xs-3 status_box
                                <?php echo ($biggestPagekz >= 2) ? $clickclass : "";?>
                                <?php echo ($biggestPagekz >= 2 && $active3 == "") ? $progressclass3 : "";?>
                                <?php echo $active3;?>">
                                <a class="<?php echo ($biggestPagekz >= 2) ? $clickclass : "";?> navbtnBookingOverview">
                                	<span class="glyphicon glyphicon-th-list"></span>
                                	<span class="hidden-xs"><?php _e("Overview", 'indiebooking'); ?></span>
                            	</a>
                        	</li>
                            <li role="presentation" class="col-xs-3 status_box <?php echo ($biggestPagekz >= 4) ? $clickclass : "";?>
                                <?php echo ($biggestPagekz >= 4 && $active4 == "") ? $progressclass4 : "";?>
                                <?php echo $active4;?>">
                                <a class="<?php echo ($biggestPagekz >= 4) ? $clickclass : "";?> navbtnBookingConfirmation">
                                	<span class="glyphicon glyphicon-ok"></span>
                                	<span class="hidden-xs"><?php _e("confirmation", 'indiebooking'); ?></span>
                            	</a>
                        	</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <?php }?>
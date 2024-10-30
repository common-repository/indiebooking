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

add_action("rs_indiebooking_show_hello_view_box", array('rs_indiebooking_hello_view_box', 'createHelloViewBox'));

// if ( ! class_exists( 'rs_indiebooking_hello_view_box' ) ) :
class rs_indiebooking_hello_view_box {

    public static function createHelloViewBox() {
        $showWelcome            = get_option('rs_indiebooking_settings_show_welcome_kz');
        
        if ("on" == $showWelcome) {
            $url                = plugins_url();
            $siteUrl            = get_site_url();
            $pluginFolder       = cRS_Indiebooking::RS_IB_PLUGIN_FOLDER;
            $settingsUrl        = menu_page_url('rs_einstellungen', false);
            $settingsUrl        = $settingsUrl."&ibui_settingstab=indiebooking";
            $settingsUrlTour    = $settingsUrl."&ibui_settingstab=indiebooking&startTour=true";
            $allowStatistics    = get_option('rs_indiebooking_settings_allow_statistics_kz');
            ?>
            <div id="rs_ib_admin_hello_view_box" class="ibui_welcome">
                <div id="rs_ib_admin_dashboard_welcome" class="rsib_container rsib_container-full rs_ib_admin_dashboard_margin-top">
                    <div id="rs_indiebooking_dont_show_welcome_again" class="ibui_welcome_btnclose"><span class="glyphicon glyphicon-remove"></span></div>
                    <div class="rsib_row">
                        <div class="rsib_col-sm-4 rsib_hiddexn-xs">
                        	<?php
                        	   $welcomeImage = $url.'/'.$pluginFolder."/assets/images/ib_welcome.png";
                        	?>
                            <img src="<?php echo esc_url($welcomeImage); ?>" class="img-responsive" />
                        </div>
                        <div class="rsib_col-sm-6 rsib_col-xs-12">
                            <h1><?php _e('Get startet', 'indiebooking');?></h1>
                            <h2><?php _e('Use indiebooking for Wordpress to start your bookings! Easy to understand, intuitive to use and quickly set up: set up indiebooking now!', 'indiebooking');?></h2>
                            <!--<a href="" class="ibui_btn ibui_btn_white"><?php _e('Functions', 'indiebooking');?></a>-->
                            <!--<a id="rs_indiebooking_btn_tour" href="" class="ibui_btn ibui_btn_white"><?php _e('Tour', 'indiebooking');?></a>-->
                            <!--<a href="" class="ibui_btn ibui_btn_white"><?php _e('Videos', 'indiebooking');?></a>-->
                            <?php //echo $settingsUrl; ?>
                            <a href="" id="ibfc_btn_start_tour" class="ibui_btn ibui_btn_white">
                            	<?php _e('Start Tour', 'indiebooking');?>
                        	</a>
                            <a id="ibfc_btn_first_steps" href="<?php echo $settingsUrl; ?>" class="ibui_btn ibui_btn_white">
                            	<?php _e('First Steps', 'indiebooking');?>
                        	</a>
                            <!-- <a href="<?php //echo $settingsUrl; ?>" class="ibui_btn ibui_btn_white"><?php //_e('Help', 'indiebooking');?></a> -->
                           	<div class="rsib_form-group" style="margin-top: 50px;">
                                <div style="float:left;padding-left:50px;">
                                	<label style="font-weight:200;" class="ibui_label">
                                		<?php //_e('Allow anonymous usage statistics', 'indiebooking'); ?>
                                		<?php _e("Allow send diagnostic and usage data to indiebooking", 'indiebooking')?>
                                	</label>
                            	</div>
                                <div class="ibui_switchbtn">
                                    <input  id="allowUsingStatisticsKz_welcome" class="ibui_switchbtn_input ibfc_switchbtn_input"
                                       	    name="rs_indiebooking_settings_allow_statistics_kz"
                                       		value="<?php echo $allowStatistics; ?>"
                                       	    type="checkbox" <?php echo ($allowStatistics == "on") ? "checked='checked'" : "";?> />
                                    <label for="allowUsingStatisticsKz_welcome"></label>
                                </div>
                                
                           	</div>
                        </div>
                        <div class="rsib_col-sm-2 rsib_hiddexn-xs"></div>
                    </div>
                </div>
            </div>
    <?php
        }
    }
}
// endif;
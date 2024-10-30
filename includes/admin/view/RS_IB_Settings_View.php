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
/* @var $mwst RS_IB_Model_Mwst */
/* @var $storno RS_IB_Model_Storno */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// if ( ! class_exists( 'RS_Indiebooking_SettingsView' ) ) :
class RS_Indiebooking_SettingsView {
    
    public function __construct() {
        add_action("rs_indiebooking_createSettingsView",array($this, "createSettingsView"), 10, 3);
        add_action("rs_indiebooking_payment_settings_tab", array($this, "showPaymentContent"), 9);
    }
    
    public function showPaymentContent() {
    	include 'settings_tabs/RS_IB_Settings_View_Payment_Tab.php';
    }
    
    private function add_settings_submit_button() {
    	?>
    	<div class="indiebooking_settings_submit_button_box rsib_row">
    		<div class="rsib_container-fluid">
    			<div class="rsib_col-xs-12">
			    	<?php
			    	submit_button();
			    	?>
		    	</div>
    		</div>
    	</div>
    	<?php
    }
    
    public function createSettingsView($mwsts, $stornos, $numberOfBookings = 0) {
        ?>
        <style>
            @import 'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i';
        </style>
    
        <form method="post" action="options.php" method="post">
    		<div id="rs_ib_admin_settings_view">
<!--             <div class="ibui_h2wrap"> -->
            	<h1><?php _e("Settings", "indiebooking");?></h1>
<!--             </div> -->
    		<?php
        		settings_fields( 'rs_indiebooking_option_group' );
        		do_settings_sections( 'rs_indiebooking_option_group' );
        		$ibtabechecked = "";
        		$generalChecked  = "checked='checked'";
        		wp_enqueue_media();
        		if (!is_array($mwsts) && $mwsts instanceof RS_IB_Model_Mwst) {
        			$mwst = $mwsts;
        			$mwsts = array();
        			array_push($mwsts, $mwst);
        		}
        		if (isset($_GET['ibui_settingstab'])) {
        		    $settingstab        = rsbp_getGetValue('ibui_settingstab', '', RS_IB_Data_Validation::DATATYPE_TEXT);
        		    if ($settingstab == "indiebooking") {
                        $ibtabechecked  = "checked='checked'";
                        $generalChecked = "";
        		    }
        		}
            ?>
            	<!--Contenedor-->
            	<div class="ibui_tab_container">
    
            	    <input id="tab-1" class="ibui_tab_radio rs_ib_tab_1" type="radio" name="tab-group" <?php echo $generalChecked; ?>/>
            	    <label id="rs_indiebooking_settings_tab1" class="ibui_tab_label" for="tab-1">
            	    	<?php _e('General', 'indiebooking'); ?>
            	    </label>
    
            	    <input id="tab-2" class="ibui_tab_radio rs_ib_tab_2" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab2" class="ibui_tab_label" for="tab-2">
            	    	<?php _e('Filter', 'indiebooking'); ?>
            	    </label>
    
            	    <input id="tab-3" class="ibui_tab_radio rs_ib_tab_3" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab3" class="ibui_tab_label" for="tab-3">
            	    	<?php _e('Payment', 'indiebooking'); ?>
            	    </label>
            	    
            	    <input id="tab-4" class="ibui_tab_radio rs_ib_tab_4" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab4" class="ibui_tab_label" for="tab-4">
            	    	<?php _e('Cancellation', 'indiebooking'); ?>
            	    </label>
            	    
            	    <input id="tab-5" class="ibui_tab_radio rs_ib_tab_5" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab5" class="ibui_tab_label" for="tab-5">
            	    	<?php _e('Taxes', 'indiebooking'); ?>
            	    </label>
            	    
            	    <input id="tab-6" class="ibui_tab_radio rs_ib_tab_6" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab6" class="ibui_tab_label" for="tab-6">
            	    	<?php _e('Printings', 'indiebooking'); ?>
            	    </label>
            	    
            	    <input id="tab-7" class="ibui_tab_radio rs_ib_tab_7" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab7" class="ibui_tab_label" for="tab-7">
            	    	<?php _e('Mail', 'indiebooking'); ?>
            	    </label>
            	    
            	    <input id="tab-8" class="ibui_tab_radio rs_ib_tab_8" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab8" class="ibui_tab_label" for="tab-8">
            	    	<?php _e('Terms and conditions', 'indiebooking'); ?>
        	    	</label>
        	    	
            	    <input id="tab-10" class="ibui_tab_radio rs_ib_tab_10" type="radio" name="tab-group" />
            	    <label id="rs_indiebooking_settings_tab10" class="ibui_tab_label" for="tab-10">
            	    	<?php _e('Licenses', 'indiebooking'); ?>
        	    	</label>
					
					<?php
						do_action("rs_indiebooking_add_new_settings_tabs");
					?>
					
            	    <input id="tab-9" class="ibui_tab_radio ibui_tab_item_indiebooking" type="radio" name="tab-group" <?php echo $ibtabechecked; ?>/>
            	    <label id="rs_indiebooking_settings_tab9" class="ibui_tab_label" for="tab-9">
            	    	Indiebooking
            	    </label>
            	    
            	    <?php
            	    if (current_user_can('administrator')) {
            	        ?>
                    	    <input id="tab-99" class="ibui_tab_radio ibui_tab_item_indiebooking_log" type="radio" name="tab-group"/>
                    	    <label id="rs_indiebooking_settings_tab99" class="ibui_tab_label" for="tab-99">
                    	    	Log
                    	    </label>
            	        <?php
            	    }
            	    ?>
            	    
            	    <div class="ibui_tab_content_container">
            	        <div class="ibui_tab_content ibui_tab_content-1">
                    			<?php include 'settings_tabs/RS_IB_Settings_View_Company_Tab.php';?>
                    			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-2">
                				<?php include 'settings_tabs/RS_IB_Settings_View_Filter_Tab.php';?>
                				<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-3">
                      			<?php //include 'settings_tabs/RS_IB_Settings_View_Payment_Tab.php';?>
                      			<?php do_action("rs_indiebooking_payment_settings_tab"); ?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-4">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Cancellation_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-5">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Taxes_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-6">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Print_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-7">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Mail_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-8">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Conditions_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <div class="ibui_tab_content ibui_tab_content-10">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Licence_Tab.php';?>
                      			<?php $this->add_settings_submit_button(); ?>
            	        </div>
            	        <?php
            	        	do_action("rs_indiebooking_add_new_settings_tabs_content");
            	        ?>
            	        <div class="ibui_tab_content ibui_tab_item_indiebooking_content">
                      			<?php include 'settings_tabs/RS_IB_Settings_View_Indiebooking_Tab.php';?>
                      			<?php //submit_button(); ?>
            	        </div>
						<?php
            	           if (current_user_can('administrator')) {
            	        ?>
						<div class="ibui_tab_content ibui_tab_item_indiebooking_log">
                      		<?php include 'settings_tabs/RS_IB_Settings_View_Indiebooking_Log_Tab.php';?>
            	        </div>
            	        <?php
            	           }
            	        ?>
            	    </div>
            	</div>
            	<div class="clear"></div>
        	</div>
        <div class="clear"></div>
    	</form>
    	<div class="clear"></div>
    <?php }
}
// endif;
new RS_Indiebooking_SettingsView();
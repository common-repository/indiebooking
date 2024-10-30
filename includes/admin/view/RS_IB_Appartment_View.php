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
?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php include 'apartment_parts/RS_IB_Stammdaten2_tab.php';?>
<?php include 'apartment_parts/RS_IB_zahlungsinformationen.php';?>
<?php

add_action("rs_indiebooking_add_apartment_admin_tab", "rs_indiebooking_add_apartment_admin_tab_master_data", 10);
add_action("rs_indiebooking_add_apartment_admin_tab", "rs_indiebooking_add_apartment_admin_tab_master_data2", 15);
//Gutschein & Aktionen ausgelagert
add_action("rs_indiebooking_add_apartment_admin_tab", "rs_indiebooking_add_apartment_admin_tab_payment_info", 25);
add_action("rs_indiebooking_add_apartment_admin_tab", "rs_indiebooking_add_apartment_admin_tab_booking_info", 30);
add_action("rs_indiebooking_add_apartment_admin_tab", "rs_indiebooking_add_apartment_admin_tab_apartment_gallery", 35);

add_action("rs_indiebooking_add_apartment_admin_tab_content", "rs_indiebooking_add_apartment_admin_tab_master_data_content", 10, 4);
add_action("rs_indiebooking_add_apartment_admin_tab_content", "rs_indiebooking_add_apartment_admin_tab_master_data2_content", 10, 4);
//Gutschein & Aktionen ausgelagert
add_action("rs_indiebooking_add_apartment_admin_tab_content", "rs_indiebooking_add_apartment_admin_tab_payment_info_content", 10, 4);
add_action("rs_indiebooking_add_apartment_admin_tab_content", "rs_indiebooking_add_apartment_admin_tab_booking_info_content", 10, 4);
add_action("rs_indiebooking_add_apartment_admin_tab_content", "rs_indiebooking_add_apartment_admin_tab_apartment_gallery_content", 10, 4);

add_action( 'rs_indiebooking_show_selected_payment_method', array('RS_IndiebookingApartmentView', 'show_selected_payment_method'));

add_filter("rs_indiebooking_add_javascript_plugin_kz", "rs_indiebooking_add_javascript_plugin_kz", 10, 1);

// if ( ! class_exists( 'RS_IndiebookingApartmentView' ) ) :
class RS_IndiebookingApartmentView {
    public static function indiebooking_add_header_before_title() {
        echo '<div class="ibui_h2wrap"><h2 class="ibui_h2" style="margin-top:20px;">' .
                __('Insert appartment name:', 'indiebooking') . '</h2></div>';
        apply_filters("rs_indiebooking_add_javascript_plugin_kz", true);
        check_add_new_apartment_head();
    }
    
    public static function indiebooking_add_header_after_title() {
        echo '<div class="ibui_h2wrap" style="margin-bottom:0px;"><h2 class="ibui_h2" style="padding:40px 1px 6px 0px !important;margin:0px;">' .
                __('Insert appartment description:', 'indiebooking') . '</h2></div>';
    }
    
    public static function show_selected_payment_method() {
        echo "<li>";
        _e("Pay by invoice", 'indiebooking');
        echo "</li>";
    }
}
// endif;

if ( ! function_exists( 'rs_indiebooking_add_javascript_plugin_kz' ) ) {
function rs_indiebooking_add_javascript_plugin_kz($freePlugin) {
    if ($freePlugin) {
        $apartmentCount = wp_count_posts(RS_IB_Model_Appartment::RS_POSTTYPE);
        echo '<span id="rs_indiebooking_all_apartmewnt_count" data-anzahlAllApartments="'.$apartmentCount->publish.'"></span>';
    }
}
}

if ( ! function_exists( 'check_add_new_apartment_head' ) ) {
function check_add_new_apartment_head() {
    global $post_type;
    if (rs_indiebooking_is_edit_page('new')) {
        if (RS_IB_Model_Appartment::RS_POSTTYPE == $post_type) {
            $show = true;
            $show = apply_filters("rs_indiebooking_check_add_new_apartment_head", $show);
            if (!$show) {
            ?>
			<div id="rs_indiebooking_no_more_apartment_dlg"></div>
            <?php
            }
        }
    }
}
}
// function showAppartmentDefaultInfos($appartment) {
//     $showOnStartPage = $appartment->getShowOnStartPage();
// }

/* *********************************************************************************************************
 * Apartment Tab Funktionen zum erstellen der verschiedenen Tabs. die Reihenfolge der Tabs wird durch die
 * Priorität bei hinzufügen zu der Action rs_indiebooking_add_apartment_admin_tab bestimmt.
 *********************************************************************************************************/
if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_master_data' ) ) {
function rs_indiebooking_add_apartment_admin_tab_master_data() {
    rs_indiebooking_add_apartment_admin_tab("tab-1", "rs_ib_tab_1", __('Master data', 'indiebooking'), true);
}
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_master_data2' ) ) {
function rs_indiebooking_add_apartment_admin_tab_master_data2() {
    rs_indiebooking_add_apartment_admin_tab("tab-2", "rs_ib_tab_2", __('Master data 2', 'indiebooking'));
}
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_payment_info' ) ) {
function rs_indiebooking_add_apartment_admin_tab_payment_info() {
    rs_indiebooking_add_apartment_admin_tab("tab-4", "rs_ib_tab_4", __('Payment info', 'indiebooking'));
}
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_booking_info' ) ) {
function rs_indiebooking_add_apartment_admin_tab_booking_info() {
    rs_indiebooking_add_apartment_admin_tab("tab-5", "rs_ib_tab_5", __('Booking info', 'indiebooking'));
}
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_apartment_gallery' ) ) {
function rs_indiebooking_add_apartment_admin_tab_apartment_gallery() {
    rs_indiebooking_add_apartment_admin_tab("tab-6", "rs_ib_tab_6", __('Appartment Gallery', 'indiebooking'));
}
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab' ) ) {
function rs_indiebooking_add_apartment_admin_tab($id, $class, $label, $checked=false) {
    $show = true;
    $checkstr = "";
    if ($checked) {
        $checkstr = 'checked="checked"';
    }
    if (rs_indiebooking_is_edit_page('new')) {
       $show = apply_filters("rs_indiebooking_check_add_new_apartment_head", $show);
    }
    if ($show) {
    ?>
    <input id="<?php echo $id; ?>" class="ibui_tab_radio <?php echo $class; ?>" type="radio" name="tab-group" <?php echo $checkstr; ?>/>
    <label id="ibui_aptab_<?php echo $id; ?>" class="ibui_tab_label" for="<?php echo $id; ?>"><?php echo $label; ?></label>
<?php }
}
}
/* *********************************************************************************************************
 *********************************************************************************************************/

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_master_data_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_master_data_content($post, $appartment, $mwsts, $bookedDates) { ?>
    <div class="ibui_tab_content ibui_tab_content-1">
	    <?php include 'apartment_parts/RS_IB_Stammdaten_tab.php';?>
    </div>
<?php }
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_master_data2_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_master_data2_content($post, $appartment, $mwsts, $bookedDates) { ?>
    <div class="ibui_tab_content ibui_tab_content-2">
        <!--<div class="rsib_container-fluid">
        	<div class="rsib_row">-->
    			<?php do_action("rs_indiebooking_show_stammdaten2_tab", $post); ?>
        <!--    </div>
        </div>-->
    </div>
<?php }
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_campaign_coupon_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_campaign_coupon_content($post, $appartment, $mwsts, $bookedDates) { ?>
	<div class="ibui_tab_content ibui_tab_content-3">
    	<?php include 'apartment_parts/RS_IB_Campaign_and_Coupons_tab.php';?>
    </div>
<?php }
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_payment_info_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_payment_info_content($post, $appartment, $mwsts, $bookedDates) { ?>
    <div class="ibui_tab_content ibui_tab_content-4">
    	<div id="appartment_payment_info_container">
            <?php do_action("rs_indiebooking_show_zahlungsinformation_tab", $post, $appartment, $mwsts, $bookedDates); ?>
            <?php //include 'apartment_parts/RS_IB_zahlungsinformationen.php';?>
        </div>
        <br class="clear" />
    </div>
<?php }
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_booking_info_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_booking_info_content($post, $appartment, $mwsts, $bookedDates) { ?>
    <div class="ibui_tab_content ibui_tab_content-5">
    	<?php include 'apartment_parts/RS_IB_buchungsinformationen.php';?>
    </div>
<?php }
}

if ( ! function_exists( 'rs_indiebooking_add_apartment_admin_tab_apartment_gallery_content' ) ) {
function rs_indiebooking_add_apartment_admin_tab_apartment_gallery_content($post, $appartment, $mwsts, $bookedDates) { ?>
    <div class="ibui_tab_content ibui_tab_content-6">
    		<?php RS_IB_Admin_Apartment_images::output($post);?>
    </div>
<?php }
}

if ( ! function_exists( 'showAppartmentStammdaten' ) ) {
// function showAppartmentStammdaten($post, $appartment, $mwsts, $dates, $yearlessDates, $bookedDates) {
function showAppartmentStammdaten($post, $appartment, $mwsts, $bookedDates) {
	?>
    <div class="modal"></div>
    <?php
    $show = true;
    if (rs_indiebooking_is_edit_page('new')) {
        $show = apply_filters("rs_indiebooking_check_add_new_apartment_head", $show);
    }
    if ($show) {
    ?>
    <div id="ibfc_apartment_tab_container" class="ibui_tab_container" style="min-height: 300px;">
		<?php do_action("rs_indiebooking_add_apartment_admin_tab"); ?>
		<br class="clear" />
        <div class="ibui_tab_content_container">
			<?php do_action("rs_indiebooking_add_apartment_admin_tab_content", $post, $appartment, $mwsts, $bookedDates); ?>
            <!--<?php submit_button(); ?>-->
        </div>
        <br class="clear" />
        <?php //submit_button(); ?>
        <?php
        	$rs_indiebooking_customAttr = array( 'id' => 'rs-indiebooking-custom-publish-btn' );
        	submit_button( NULL, 'primary', 'submit', true,  $rs_indiebooking_customAttr );
        ?>
    </div>
    <br />
    <?php } ?>
    <br class="clear" />
<?php }
}?>
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


// add_action( 'wp_head', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'include_global_javascript_translation_variables'), 5);
// add_action( 'admin_head', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'include_global_javascript_translation_variables'), 5);

add_action( 'rs_indiebooking_rsappartment_show_first_page_apartments', 'rs_indiebooking_rsappartment_show_first_page_apartments', 5 );
add_action( 'rs_indiebooking_rsappartment_search_box', 'rs_indiebooking_template_search_appartment_box', 5 );
add_action( 'rs_indiebooking_rsappartment_search_box_2', 'rs_indiebooking_template_search_appartment_box_2', 5 );

add_action( 'rs_indiebooking_rsappartment_search_box_small', 'rs_indiebooking_template_search_appartment_box_small', 5 );
add_action( 'rs_indiebooking_show_navbar', 'rs_indiebooking_show_navbar', 5, 1 );
add_action( 'rs_indiebooking_list_rsappartment_from_to_dates', 'rs_indiebooking_list_rsappartment_from_to_dates', 5);

add_action( 'rs_indiebooking_rsappartment_show_first_page_welcome', 'rs_indiebooking_rsappartment_show_first_page_welcome', 5);
// add_action('load-edit-tags.php','myprefix_redirect_to_custompage');

// function myprefix_redirect_to_custompage(){
//     $screen = get_current_screen();
    
//     if($screen->id == 'edit-rsappartmentcategories'){
//         $url = admin_url('edit.php?page=mypage');
//         wp_redirect($url);
//         exit;
//     }
// }
add_action( 'rs_indiebooking_show_booking_control_buttons', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'show_booking_control_button'), 10, 1);
add_action( 'rs_indiebooking_single_rsappartment_buchung_appartment_header', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'rs_indiebooking_single_rsappartment_buchung_appartment_header'), 10, 1);
add_action( 'rs_indiebooking_show_payment_boxes', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'show_payment_boxes'), 15, 3);
add_action( 'rs_indiebooking_show_default_booking_header_data', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'show_default_booking_header_data'), 10, 1);
/**
 * Appartment Hooks
 */
add_action( 'rs_indiebooking_single_rsappartment_header', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'rs_indiebooking_template_single_appartment_header'), 5, 1 );

//Verschoben in indiebooking_apartment_frontend_helper.php
// add_action( 'rs_indiebooking_single_rsappartment_gallery', 'rs_indiebooking_template_single_appartment_gallery', 10, 2 );
// add_action( 'rs_indiebooking_single_rsappartment_dates', 'rs_indiebooking_template_single_appartment_dates', 15 );
// add_action( 'rs_indiebooking_single_rsappartment_options', 'rs_indiebooking_template_single_appartment_options', 15 );
// add_action( 'rs_indiebooking_single_rsappartment_description', 'rs_indiebooking_template_single_appartment_description', 15 , 1);

add_action( 'rs_indiebooking_single_rsappartment_from_to_dates', 'rs_indiebooking_single_rsappartment_from_to_dates',15, 2 );
add_action( 'rs_indiebooking_single_rsappartment_smallesprice', 'rs_indiebooking_single_rsappartment_smallesprice', 15 );

add_action( 'rs_indiebooking_single_rsappartment_prices', 'rs_indiebooking_template_single_appartment_prices', 15 , 1);


add_action( 'post_updated', 'rs_indiebooking_changePostBookingStatusAction', 10, 3);

/**
 * Appartment Buchung Hooks
 */
add_action( 'rs_indiebooking_single_rsappartment_buchung_contact', 'rs_indiebooking_template_single_appartment_buchung_contact', 10, 2 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_contact_data', 'rs_indiebooking_template_single_appartment_buchung_contact_data', 10, 2 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_zahlungsart', 'rs_indiebooking_template_single_appartment_buchung_zahlungsart', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_payment_button', 'rs_indiebooking_template_single_appartment_buchung_payment_button', 10, 1 );

// add_action( 'rs_indiebooking_single_rsappartment_buchung_contact_data', 'rs_indiebooking_template_single_appartment_buchung_contact_data', 10, 2 );

add_action( 'rs_indiebooking_single_rsappartment_buchung_appartment_list', 'rs_indiebooking_template_single_appartment_buchung_appartment_list', 10, 2 );
// add_action( 'rs_indiebooking_single_rsappartment_buchung_summary', 'rs_indiebooking_template_single_appartment_buchung_contact', 10 );

add_action( 'rs_indiebooking_single_rsappartment_buchung_header', 'rs_indiebooking_single_rsappartment_buchung_header', 10, 2 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_start', 'rs_indiebooking_single_rsappartment_buchung_start', 10, 2 );

// add_action( 'rs_indiebooking_single_rsappartment_buchung_contact', 'rs_indiebooking_single_rsappartment_buchung_contact', 10 );

add_action( 'rs_indiebooking_single_rsappartment_buchung_almost_booked', 'rs_indiebooking_single_rsappartment_buchung_almost_booked', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_final', 'rs_indiebooking_single_rsappartment_buchung_final', 10 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_countdown', 'rs_indiebooking_template_single_appartment_buchung_countdown', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_controll_buttons', array("RS_INDIEBOOKING_TEMPLATE_FKT", 'show_buchung_controll_buttons'), 10, 2 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_not_found', 'rs_indiebooking_single_rsappartment_buchung_not_found', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_not_found2', 'rs_indiebooking_single_rsappartment_buchung_not_found2', 10, 1 );


add_action( 'rs_indiebooking_single_rsappartment_buchung_time_range', 'rs_indiebooking_single_rsappartment_buchung_time_range', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_detail_payment', 'rs_indiebooking_single_rsappartment_buchung_detail_payment', 10, 1 );
add_action( 'rs_indiebooking_single_rsappartment_buchung_full_prices', 'rs_indiebooking_single_rsappartment_buchung_full_prices', 10, 1 );

// add_action( 'rs_indiebooking_single_rsappartment_summary', 'rs_indiebooking_template_single_appartment_gallery', 10 );

/**
 * Appartment Buchung Print Hooks
 */
// add_filter( 'rs_indiebooking_print_rsappartment_buchung_header', 'rs_indiebooking_print_rsappartment_buchung_header', 10, 1 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_confirmation', 'rs_indiebooking_print_rsappartment_buchung_confirmation', 10, 3 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_confirmation_by_invoicenr', 'rs_indiebooking_print_rsappartment_buchung_confirmation_by_invoicenr', 10, 5 );

add_filter( 'rs_indiebooking_print_rsappartment_test_buchung_confirmation', 'rs_indiebooking_print_rsappartment_test_buchung_confirmation', 10 );
add_filter( 'rs_indiebooking_print_rsappartment_inquiry_confirmation', 'rs_indiebooking_print_rsappartment_inquiry_confirmation', 10, 2 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_payment_confirmation', 'rs_indiebooking_print_rsappartment_buchung_payment_confirmation', 10, 1 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_invoice', 'rs_indiebooking_print_rsappartment_buchung_invoice', 10, 1 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_storno', 'rs_indiebooking_print_rsappartment_buchung_storno', 10, 1 );
add_filter( 'rs_indiebooking_print_rsappartment_cancel_inquiry', 'rs_indiebooking_print_rsappartment_cancel_inquiry', 10, 1 );

add_filter( 'rs_indiebooking_print_by_order_type', 'rs_indiebooking_print_by_order_type', 10, 3);

// add_filter( 'rs_indiebooking_print_rsappartment_buchung_contact', 'rs_indiebooking_print_rsappartment_buchung_contact', 10, 1 );
// add_filter( 'rs_indiebooking_print_rsappartment_buchung_detailed_payment_info', 'rs_indiebooking_print_rsappartment_buchung_detailed_payment_info', 10, 2 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_options', 'rs_indiebooking_print_rsappartment_buchung_options', 10, 1 );
add_filter( 'rs_indiebooking_print_rsappartment_buchung_time_range', 'rs_indiebooking_print_rsappartment_buchung_time_range', 10, 1 );

add_action( 'rs_indiebooking_print_rsappartment_buchung_contact', 'rs_indiebooking_print_rsappartment_buchung_contact', 10, 1 );
add_action( 'rs_indiebooking_print_rsappartment_buchung_detailed_payment_info', 'rs_indiebooking_print_rsappartment_buchung_detailed_payment_info', 10, 1 );
// add_action( 'rs_indiebooking_print_rsappartment_buchung_options', 'rs_indiebooking_print_rsappartment_buchung_options', 10, 1 );
// add_action( 'rs_indiebooking_print_rsappartment_buchung_time_range', 'rs_indiebooking_print_rsappartment_buchung_time_range', 10, 1 );



add_filter( 'rs_indiebooking_is_apartment_bookable', 'rs_indiebooking_is_apartment_bookable', 10, 1);

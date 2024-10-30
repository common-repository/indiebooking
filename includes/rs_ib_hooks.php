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
 
add_action('plugins_loaded', 'rs_indiebooking_addAsyncMailAction');

// add_filter( 'heartbeat_received', 'rs_ib_indiebooking_receive_heartbeat', 10, 2 );
// add_filter( 'heartbeat_nopriv_received', 'rs_ib_indiebooking_receive_heartbeat', 10, 2 );

if ( ! function_exists( 'rs_indiebooking_addAsyncMailAction' ) ) {
function rs_indiebooking_addAsyncMailAction() {
    add_action( 'rs_ib_create_file_and_send_mail', 'rs_ib_create_file_and_send_mail', 10, 2 );
//     add_action( 'wp_async_rs_ib_create_file_and_send_mail', 'rs_ib_create_file_and_send_mail', 10, 2 );
    add_action( 'rs_indiebooking_buchung_reset_gutscheine', 'rs_ib_reset_buchung_gutscheine_dummy',10,2);
//     add_action( 'wp_async_nopriv_rs_ib_create_file_and_send_mail', 'rs_ib_create_file_and_send_mail', 10, 2 );

// 	Add filter to receive hook, and specify we need 2 parameters.
    
}
}



if (! function_exists('rs_ib_indiebooking_receive_heartbeat')) {
	function rs_ib_indiebooking_receive_heartbeat($response, $data) {
		if (empty($data['indiebooking_bookingPostId'])) {
			RS_Indiebooking_Log_Controller::write_log('no data for heartbeat ', __LINE__, __CLASS__);
			return $response;
		}
		
		$bookingPostId = $data['indiebooking_bookingPostId'];
		RS_Indiebooking_Log_Controller::write_log('update heartbeat '.$bookingPostId, __LINE__, __CLASS__);
		update_post_meta($bookingPostId, RS_IB_Model_Appartment_Buchung::BUCHUNG_LAST_HEARTBEAT, time());
		
		return $response;
	}
}

if ( ! function_exists( 'rs_ib_reset_buchung_gutscheine_dummy' ) ) {
/**
 * @param array $coupons
 */
	function rs_ib_reset_buchung_gutscheine_dummy($coupons = array()) {
	    //do nothing
// 	    $coupons = $coupons;
	}
}
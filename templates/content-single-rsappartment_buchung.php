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
/**
 * Diese Datei ist das default Template für die Anzeige der aktuellen Buchung.
 * Je nach status wird eine andere action ausgeführt
 *
 * Kopieren Sie es bei sich in das Theme in den Pfad:
 * "indiebooking/" um die Datei für Ihr Template anzupassen.
 *
 */
?>
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

while ( have_posts() ) : the_post();
    $postId       = get_the_ID();
    $postStatus   = get_post_status($postId);
    if (!session_id()) {
    	session_start();
    }
    $currentSessionPostId	= 0;
    if (key_exists('indiebooking_currentBookingPostId', $_SESSION)) {
	    $currentSessionPostId = $_SESSION['indiebooking_currentBookingPostId'];
    }
    if ($postId === $currentSessionPostId) {
	    if ($postStatus == "rs_ib-blocked") {
	        do_action("rs_indiebooking_single_rsappartment_buchung_start");
	    }
	    else if ($postStatus == "rs_ib-booking_info") {
	        do_action("rs_indiebooking_single_rsappartment_buchung_contact", $postId);
	    }
	    else if ($postStatus == "rs_ib-almost_booked") {
	    	global $RSBP_DATABASE;
	    	 
	    	$buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	    	$buchung                    = $buchungTable->getAppartmentBuchung($postId);
	    	$buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
	    	$zahlart					= strtoupper($buchungKopf->getHauptZahlungsart());
	    	
	//     	if (strtoupper($buchungKopf->getHauptZahlungsart()) == "STRIPECREDITCARD") {
	//     		self::show_creditcard_payment_button($buchungKopf);
	//     	} elseif (strtoupper($buchungKopf->getHauptZahlungsart()) == "STRIPESOFORT") {
	//     		self::show_sofort_payment_button($buchungKopf);
	//     	}
	    	if ($zahlart == "PAYPAL" || $zahlart == "PAYPALEXPRESS") {
	    		$paypalsuccess = rsbp_getGetValue('paypalsuccess', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    		if ('true' == $paypalsuccess) {
	    			$buchungController = new RS_IB_Appartment_Buchung_Controller();
	    			$buchungController->finalizeBooking($postId, "BASICPAYPAL_SUCCESS");
	    			do_action("rs_indiebooking_single_rsappartment_buchung_final", $postId);
	    		} else {
	    			$lastSavedPaged 		= get_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_CURRENT_PAGEKZ, true);
	    			$paypalexpresssuccess 	= rsbp_getGetValue('paypalexpresssuccess', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    			if ($paypalexpresssuccess == 'false') {
	    				$status			= "";
	    				$nextPage		= 1;
	    				if ($lastSavedPaged == "1") {
	    					//es wurde auf der ersten Buchungsseite Paypal Express ausgewaehlt
	    					$status 	= 'rs_ib-blocked';
	    				} else if ($lastSavedPaged == "2" || $lastSavedPaged == "3") {
	    					$status		= 'rs_ib-booking_info';
	    					$nextPage		= 2;
	    				} else {
	    					//aktuell einfach zur absicherung
	    					$status 	= 'rs_ib-blocked';
	    				}
	    				$myPost         = array(
	    					"ID"        => $postId,
	    					"post_status" => $status,
	    				);
	    				if (!is_null($myPost)) {
	    					wp_update_post($myPost);
	    				}
	    				update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, 1);
	    				if ($nextPage == 1) {
	    					do_action("rs_indiebooking_single_rsappartment_buchung_start", $postId);
	    				} else if ($nextPage == 2) {
	    					do_action("rs_indiebooking_single_rsappartment_buchung_contact", $postId);
	    				}
	    			} else {
	    				do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	    			}
	    		}
	    	} elseif (strtoupper($zahlart) == "STRIPESOFORT") {
	    		do_action("rs_indiebooking_stripe_sofort_payment", $postId);
	    	} elseif (strtoupper($zahlart) == "STRIPEGIROPAY") {
	    		do_action("rs_indiebooking_stripe_giropay_payment", $postId);
	    	} elseif (strtoupper($zahlart) == "AMAZONPAYMENTS" || strtoupper($zahlart) == "AMAZONPAYMENTSEXPRESS") {
	    		$amznAccesToken = rsbp_getGetValue('access_token', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    		$tokenType		= rsbp_getGetValue('token_type', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    		$expires		= rsbp_getGetValue('expires_in', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    		$scope			= rsbp_getGetValue('scope', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    		if (!is_null($amznAccesToken) && $amznAccesToken != "") {
	    			$args = array(
	    				'access_token' 	=> $amznAccesToken,
	    				'token_type' 	=> $tokenType,
	    				'expires_in' 	=> $expires,
	    				'scope' 		=> $scope
	    			);
	//     			do_action("rs_indiebooking_do_amazon_payment", $args);
	    			do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	    		} else {
	    			do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	    		}
	    	} else {
	    		do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	    	}
	//         $paypalsuccess = rsbp_getGetValue('paypalsuccess', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	//         if ('true' == $paypalsuccess) {
	//             $buchungController = new RS_IB_Appartment_Buchung_Controller();
	//             $buchungController->finalizeBooking($postId, "BASICPAYPAL_SUCCESS");
	//             do_action("rs_indiebooking_single_rsappartment_buchung_final", $postId);
	//         } else {
	//         	$lastSavedPaged 		= get_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_CURRENT_PAGEKZ, true);
	//             $paypalexpresssuccess 	= rsbp_getGetValue('paypalexpresssuccess', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	//             if ($paypalexpresssuccess == 'false') {
	//             	$status			= "";
	//             	$nextPage		= 1;
	//             	if ($lastSavedPaged == "1") {
	//             		//es wurde auf der ersten Buchungsseite Paypal Express ausgewaehlt
	//             		$status 	= 'rs_ib-blocked';
	//             	} else if ($lastSavedPaged == "2" || $lastSavedPaged == "3") {
	//             		$status		= 'rs_ib-booking_info';
	//             		$nextPage		= 2;
	//             	} else {
	//             		//aktuell einfach zur absicherung
	//             		$status 	= 'rs_ib-blocked';
	//             	}
	            	
	//             	$myPost         = array(
	//             		"ID"        => $postId,
	//             		"post_status" => $status,
	//             	);
	//             	if (!is_null($myPost)) {
	//             		wp_update_post($myPost);
	//             	}
	//             	update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, 1);
	//             	if ($nextPage == 1) {
	//             		do_action("rs_indiebooking_single_rsappartment_buchung_start", $postId);
	//             	} else if ($nextPage == 2) {
	//             		do_action("rs_indiebooking_single_rsappartment_buchung_contact", $postId);
	//             	}
	//             } else {
	//                 do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	//             }
	//         }
	    }
	    else if ($postStatus == "rs_ib-booked" || $postStatus == "rs_ib-pay_confirmed") {
	        do_action("rs_indiebooking_single_rsappartment_buchung_final", $postId);
	    } else if ($postStatus == "rs_ib-canceled" || $postStatus == "rs_ib-out_of_time") {
	        do_action("rs_indiebooking_single_rsappartment_buchung_not_found");
	    }
    } else {
    	do_action("rs_indiebooking_single_rsappartment_buchung_not_found2");
    }
endwhile; // end of the loop.

/*
while ( have_posts() ) : the_post();
	$postId                     = get_the_ID();
	$postStatus                 = get_post_status($postId);
	if ($postStatus == "rs_ib-blocked") {
	    do_action("rs_indiebooking_single_rsappartment_buchung_start");
	}
	elseif ($postStatus == "rs_ib-booking_info") {
	    do_action("rs_indiebooking_single_rsappartment_buchung_contact", $postId);
	}
	elseif ($postStatus == "rs_ib-almost_booked") {
	    $paypalsuccess = rsbp_getGetValue('paypalsuccess', '', RS_IB_Data_Validation::DATATYPE_TEXT);
	    if ('true' == $paypalsuccess) {
	        $buchungController = new RS_IB_Appartment_Buchung_Controller();
	        $buchungController->finalizeBooking($postId, "BASICPAYPAL_SUCCESS");
	        do_action("rs_indiebooking_single_rsappartment_buchung_final", $postId);
	    } else {
            do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $postId);
	    }
	}
	elseif ($postStatus == "rs_ib-booked" || $postStatus == "rs_ib-pay_confirmed") {
		    do_action("rs_indiebooking_single_rsappartment_buchung_final", $postId);
	} elseif ($postStatus == "rs_ib-canceled" || $postStatus == "rs_ib-out_of_time") {
        do_action("rs_indiebooking_single_rsappartment_buchung_not_found");
	}
endwhile; // end of the loop.
*/
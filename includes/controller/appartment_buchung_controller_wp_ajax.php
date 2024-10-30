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
<?php if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}
include_once 'parent_controller/appartment_buchung_controller.php';



// if ( ! class_exists( 'RS_IB_Appartment_Buchung_Controller_WP_AJAX' ) ) :
/**
 * @author schmitt
 * @date 07.02.2018
 * @brief Dieser Controller nimmt die AJAX Anfragen zur Buchung entgegen und verarbeitet diese.
 *
 * @details Folgende Actions bietet der Controller an, diese Actions sind &uuml;ber Javascript via. AJAX ansprechbar.
 * @attention Alle Actions sind sowohl f&uuml;r eingeloggte als auch f&uuml;r nicht eingeloggte
 *
 * @li createDummyBooking
 * @li updateBooking
 * @li updateBookingStatus
 * @li finalizeBooking
 * @li finalizeInquiry
 * @li sendBookingMail
 * @li bookingOutOfTime
 * @li updateBookingOptions
 * @li returnCurrentPrice
 * @li cancelBooking
 *
 * @image html Buchungsprozess.jpg
 *
 *
 */
class RS_IB_Appartment_Buchung_Controller_WP_AJAX extends RS_IB_Appartment_Buchung_Controller
{
    
	/**
	 * Aktuell wird das Objekt am Ende dieser Datei erzeugt.
	 * Der Konstruktor definiert die in der Detailbeschreibung angegebenen Actions.
	 * Alle Methodenaufrufe dieser Klasse erfolgen &uuml;ber Actions
	 */
    public function __construct() {
//         add_action( 'wp_ajax_my_action', array($this, 'addBuchung') );
        parent::__construct();
        
        //Fuer eingeloggte Nutzer
        add_action('wp_ajax_createDummyBooking', array($this, 'createDummyBooking_Ajax'));
        add_action('wp_ajax_updateBooking', array($this, 'updateBooking_Ajax'));
        add_action('wp_ajax_fetchRemainingTime', array($this, 'fetchRemainingTime_Ajax'));
        add_action('wp_ajax_updateBookingStatus', array($this, 'updateBookingStatus_Ajax'));
        add_action('wp_ajax_finalizeBooking', array($this, 'finalizeBooking_Ajax'));
        add_action('wp_ajax_finalizeInquiry', array($this, 'finalizeInquiry_Ajax'));
        add_action('wp_ajax_sendBookingMail', array($this, 'sendBookingMail_Ajax'));
//         add_action('wp_ajax_checkApartmentCouponCode', array($this, 'checkApartmentCouponCode_Ajax'));
        add_action('wp_ajax_bookingOutOfTime', array($this, 'bookingOutOfTime_Ajax'));
        add_action('wp_ajax_updateBookingOptions', array($this, 'updateOptions_Ajax'));
        
        add_action('wp_ajax_returnCurrentPrice', array($this, 'returnCurrentPrice_Ajax'));
        
        add_action('wp_ajax_cancelBooking', array($this, 'cancelBooking'));
        
        add_action('wp_ajax_indiebookingHeartbeat', array($this, 'receive_booking_heartbeat'));
        add_action('wp_ajax_nopriv_indiebookingHeartbeat', array($this, 'receive_booking_heartbeat'));
        
        //Fuer nicht eingeloggte Nutzer
        add_action('wp_ajax_nopriv_createDummyBooking', array($this, 'createDummyBooking_Ajax'));
        add_action('wp_ajax_nopriv_updateBooking', array($this, 'updateBooking_Ajax'));
        add_action('wp_ajax_nopriv_fetchRemainingTime', array($this, 'fetchRemainingTime_Ajax'));
        
        add_action('wp_ajax_nopriv_updateBookingStatus', array($this, 'updateBookingStatus_Ajax'));
        add_action('wp_ajax_nopriv_finalizeBooking', array($this, 'finalizeBooking_Ajax'));
        add_action('wp_ajax_nopriv_finalizeInquiry', array($this, 'finalizeInquiry_Ajax'));
        add_action('wp_ajax_nopriv_sendBookingMail', array($this, 'sendBookingMail_Ajax'));
//         add_action('wp_ajax_nopriv_checkApartmentCouponCode', array($this, 'checkApartmentCouponCode_Ajax'));
        add_action('wp_ajax_nopriv_bookingOutOfTime', array($this, 'bookingOutOfTime_Ajax'));
        add_action('wp_ajax_nopriv_updateBookingOptions', array($this, 'updateOptions_Ajax'));
        add_action('wp_ajax_nopriv_returnCurrentPrice', array($this, 'returnCurrentPrice_Ajax'));
        add_action('wp_ajax_nopriv_cancelBooking', array($this, 'cancelBooking'));
        
    }
    
    
    public function receive_booking_heartbeat() {
    	$bookingId     	= rsbp_getPostValue('bookingId', false, RS_IB_Data_Validation::DATATYPE_ALL);
    	RS_Indiebooking_Log_Controller::write_log('update heartbeat '.$bookingId, __LINE__, __CLASS__);
    	update_post_meta($bookingId, RS_IB_Model_Appartment_Buchung::BUCHUNG_LAST_HEARTBEAT, time());
    }
    
    /**
     * @brief Gibt den aktuellen Preis als HTML f&uuml;r die Buchung zur&uuml;ck
     * @detail Also Post-Parameter werden folgende Daten erwartet:
     * @param $_POST
     * @parblock
     * bookingId
     * security
     * isAdmin (optional)
     * @endparblock
     */
    public function returnCurrentPrice_Ajax() {
    	$isAdminRequest     	= rsbp_getPostValue('isAdmin', false, RS_IB_Data_Validation::DATATYPE_ALL);
    	$isAdminRequest			= boolval($isAdminRequest);
    	if ($isAdminRequest) {
    		$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	} else {
	    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
    	}
    	if ($nonceOk) {
	        $bookingPostId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $isAdminRequest     = rsbp_getPostValue('isAdmin', false, RS_IB_Data_Validation::DATATYPE_ALL);
	        $isAdminRequest		= boolval($isAdminRequest);
	        $this->loadCurrentBookingPrices($bookingPostId, $isAdminRequest);
	        wp_reset_postdata();
	//         wp_send_json_success();
	        wp_die();
    	}
    }
    
    /* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
    public function cancelBooking() {
        global $RSBP_DATABASE;
        
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        if ($nonceOk) {
	        $storno                     = false;
	        $cancel                     = true;
	        $answer                     = array();
	        $bookingId                  = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $bookingNr                  = rsbp_getPostValue('bookingNr', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $email                      = rsbp_getPostValue('emailadr', 0, RS_IB_Data_Validation::DATATYPE_EMAIL);
	        if ($email != "") {
	            $storno                 = true;
	        }
	        if ($bookingId == 0 && $bookingNr != 0) {
	            global $RSBP_DATABASE;
	        
	            $cancel                 = false;
	            $appartmentBuchungsTbl  = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	            $modelBuchung           = $appartmentBuchungsTbl->getAppartmentBuchungByBuchungsKopfNr($bookingNr);
	            if (!is_null($modelBuchung)) {
	                $bookingId              = $modelBuchung->getPostId();
	                if (isset($bookingId) && !is_null($bookingId)) {
	                    $buchungsKopfTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
	                    $buchungsKopf       = $buchungsKopfTbl->loadBooking($bookingNr, false);
	                    $kopfEmail          = $buchungsKopf->getKunde_email();
	                    if (strtoupper(trim($email)) == strtoupper(trim($kopfEmail))) {
	                        $cancel         = true;
	                    } else {
	                        $answer['MSG'] = __("Wrong E-Mail adress", 'indiebooking');
	                    }
	                } else {
	                    $answer['MSG'] = __("Bookingnumber not found", 'indiebooking');
	                }
	            } else {
	                $answer['MSG'] = __("Bookingnumber not found", 'indiebooking');
	            }
	        }
	        if ($cancel) {
	            $answer                 = $this->cancelBuchung($bookingId, $storno);
	        } else {
	            $answer['CODE'] = 0;
	        }
	        if ($answer['CODE'] == 1) {
	        	if (!session_id()) {
	        		session_start();
	        	}
	        	if (session_id()) {
	        		$_SESSION['indiebooking_currentBookingNr'] 		= '0';
	        		$_SESSION['indiebooking_currentBookingPostId'] 	= '0';
	        	}
	            if ($storno == true) {
	                $stornoPostId       = $answer['STORNOPOSTID'];
	                if (get_post_status($stornoPostId) == "rs_ib-storno" || get_post_status($stornoPostId) == "rs_ib-storno_paid") {
	                    do_action("rs_ib_create_file_and_send_mail", $stornoPostId, 5);
	                }
	            }
	            wp_send_json_success($answer);
	        } else {
	            wp_send_json_error($answer);
	        }
	        wp_die();
        }
    }
    
    public function bookingOutOfTime_Ajax() {
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        if ($nonceOk) {
	        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $answer         = $this->bookingOutOfTime($bookingId);
	        if ($answer['CODE'] == 1) {
	            wp_send_json_success($answer);
	        } else {
	            wp_send_json_error($answer);
	        }
        }
    }
    
    public function sendBookingMail_Ajax() {
        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $this->sendBookingMail($bookingId);
    }

    public function finalizeInquiry_Ajax() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
    	if ($nonceOk) {
	    	$bookingPostId  = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	    	
	    	$answer         = $this->finalizeInquiry($bookingPostId);
	    	if ($answer['CODE'] == 1) {
// 	    		global $post;
// 	    		$post       = get_post($bookingPostId);
	    		if ($answer['SUCCESSPAGE'] == "") {
		    		do_action("rs_indiebooking_single_rsappartment_buchung_final", $bookingPostId);
		    		wp_reset_postdata();
		    		die();
	    		} else {
	    			wp_send_json_success($answer);
	    		}
	    	} else {
	    		wp_send_json_error($answer);
	    	}
    	}
    }
    
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    public function finalizeBooking_Ajax() {
        global $RSBP_DATABASE;
        
        $bookingContact		= array();
        $adminKz			= rsbp_getPostValue('adminKz', '');
        if ($adminKz == "1") {
        	$nonceOk		= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
        } else {
        	$nonceOk		= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        }
//         $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        if ($nonceOk) {
	        $bookingPostId  = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        //TODO: paypal angaben validieren!
	        $paymentId      = rsbp_getPostValue('paymentId', 0);
	        $paypalToken    = rsbp_getPostValue('paypalToken', 0);
	        $payPalPpayerId = rsbp_getPostValue('payPalPayerId', 0);
	        $amznOrderRefId = rsbp_getPostValue('amznOrderRefId', '');
	        $customMessage	= rsbp_getPostValue('customMessage', '');
	        $adminKz		= rsbp_getPostValue('adminKz', '');
	        $isAdminKz      = rsbp_getPostValue('isAdminKz', false);
	        $specialAdminParams 		= array();
	        if ($adminKz == "1") {
	        	$bookingContact 		= rsbp_getPostValue('bookContact', array(), RS_IB_Data_Validation::DATATYPE_CONTACT_ARRAY);
	        	
	        	$ignoremimimumperiod 	= rsbp_getPostValue('ignoremimimumperiod', false);
	        	$allowPastBooking    	= rsbp_getPostValue('allowPastBooking', false);
	        	$changeBillDate 		= rsbp_getPostValue('changeBillDate', false);
	        	$billDate 				= rsbp_getPostValue('billDate', $today);
	        	$bookOptionOnly			= rsbp_getPostValue('bookOptionOnly', "off");
	        	
	        	if ($allowPastBooking == "off") {
	        		$allowPastBooking	= false;
	        	} else if ($allowPastBooking == "on") {
	        		$allowPastBooking	= true;
	        	}
	        	$specialAdminParams		= array(
	        		'allowPastBooking'	=> $allowPastBooking,
	        		'changeBillDate'	=> $changeBillDate,
	        		'billDate'			=> $billDate,
	        		'bookOptionOnly'	=> $bookOptionOnly,
	        	);
	        	
	        }
            if ($adminKz != '') {
            	$buchungNr          = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
            	$buchungKopfTable	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            	$buchungKopfTable->updateBuchungsAdminKz($buchungNr, $adminKz);
            }
            $answer         = $this->finalizeBooking($bookingPostId, $paymentId, $paypalToken, $payPalPpayerId, $customMessage, $bookingContact, $isAdminKz, $specialAdminParams, $amznOrderRefId);
	        if ($answer['CODE'] == 1) {
	            global $post;
	            
	            if (!session_id()) {
	            	session_start();
	            }
	            if (session_id()) {
	            	$_SESSION['indiebooking_currentBookingNr'] 		= '0';
	            	$_SESSION['indiebooking_currentBookingPostId'] 	= '0';
	            }
	            
	            $post       = get_post($bookingPostId);
	            if ($answer['SUCCESSPAGE'] == "") {
	            	do_action("rs_indiebooking_single_rsappartment_buchung_final", $bookingPostId);
	            	wp_reset_postdata();
		            die();
	            } else {
	            	wp_send_json_success($answer);
	            }
	        } else {
	            wp_send_json_error($answer);
	        }
        }
    }
    
    public function updateOptions_Ajax() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
    	if ($nonceOk) {
	        $bookingObj     = rsbp_getPostValue('bookingObj', array(), RS_IB_Data_Validation::DATATYPE_BOOKINGOBJ);
	        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $options        = (isset($bookingObj['options']) ? $bookingObj['options'] : array());
	        $answer         = $this->updateOptions($bookingId, $options);
	        
	        if ($answer['CODE'] == 1) {
	            global $post;
	            $post = get_post($bookingId);
	            do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $bookingId);
	            wp_reset_postdata();
	            die();
	        } else {
	            wp_send_json_error($answer);
	        }
    	}
    }
    
    public function updateBookingStatus_Ajax() {
    	$answer				= array();
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        if ($nonceOk) {
	        $bookingPostId  = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $pageKz         = rsbp_getPostValue('pageKz', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $toPageKz       = rsbp_getPostValue('toPageKz', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $myPost         = null; //get_post($bookingPostId);
	        if ($pageKz < 4) {
	            switch ( $toPageKz ) {
	                case 1:
	                    $myPost         = array(
	                        "ID"        => $bookingPostId,
	                        "post_status" => 'rs_ib-blocked',
	                    );
	                    break;
	                case 2:
	                    $myPost         = array(
	                        "ID"        => $bookingPostId,
	                        "post_status" => 'rs_ib-booking_info',
	                    );
	                    break;
	                case 3:
	                    $myPost         = array(
	                        "ID"        => $bookingPostId,
	                        "post_status" => 'rs_ib-almost_booked',
	                    );
	                    break;
	                default:
	                    wp_send_json_success($answer);
	            }
	            if (!is_null($myPost)) {
	            	$currentStatus 	= get_post_status($bookingPostId);
	            	$allowedStatus	= array(
	            		"rs_ib-blocked",
	            		"rs_ib-booking_info",
	            		"rs_ib-almost_booked",
	            	);
	            	if (in_array($currentStatus , $allowedStatus)) {
// 	                if (get_post_status($bookingPostId) !== 'rs_ib-booked' &&
// 	                	get_post_status($bookingPostId) !== 'rs_ib-pay_confirmed') {
	                    wp_update_post($myPost);
	                    update_post_meta($bookingPostId, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, time());
	                }
	            }
	        }
	        wp_reset_postdata();
	        die();
        }
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $modelBuchung RS_IB_Model_Appartment_Buchung */
    public function fetchRemainingTime_Ajax() {
    	global $RSBP_DATABASE;
    	
    	$postId  	= rsbp_getPostValue('bookingPostId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
    	$reset  	= rsbp_getPostValue('resettime', 0, RS_IB_Data_Validation::DATATYPE_ALL);
    	
    	if ($reset == "true") {
    		update_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, time());
    	}
    	
    	$buchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    	$modelBuchung		= $buchungTable->checkBookingTime($postId);
    	
    	$remainingTime 		= $modelBuchung->getRemainingtTime();
    	
    	$answer = array(
    		"success" => true,
    		"remainingTime" => $remainingTime
    	);
    	
    	wp_send_json_success($answer);
    }
    
    
    public function updateBooking_Ajax() {
    	if ( function_exists('icl_object_id') ) {
    		global $sitepress;
    	}
        $answer             	= array();
        $isAdminKz      		= rsbp_getPostValue('isAdminKz', false);
        
//         if ($isAdminKz) {
//         	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
//         } else {
// 	        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
//         }
        $nonceOk = true;
//         do_action( 'wpml_switch_language',  'en' );
//         $sitepress->switch_lang('en');
        
        if ($nonceOk) {
	        try {
	            $paymentMethod  = rsbp_getPostValue('paymentMethod', 0, RS_IB_Data_Validation::DATATYPE_TEXT);
	            $bookingObj     = rsbp_getPostValue('bookingObj', array(), RS_IB_Data_Validation::DATATYPE_BOOKINGOBJ);
	            $bookingContact = rsbp_getPostValue('bookContact', array(), RS_IB_Data_Validation::DATATYPE_CONTACT_ARRAY);
	            $pageKz         = rsbp_getPostValue('pageKz', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	            $toPageKz       = rsbp_getPostValue('toPageKz', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	            $isAdminKz      = rsbp_getPostValue('isAdminKz', false);
	            $processError	= rsbp_getPostValue('processError', false);
	            $isAdminKz		= boolval($isAdminKz);
	            
// 	            $today					= new DateTime("now");
// 	            $today					= $today->format("d.m.Y");
	            
// 	            $allowPastBooking    	= rsbp_getPostValue('allowPastBooking', false);
// 	            $changeBillDate 		= rsbp_getPostValue('changeBillDate', false);
// 	            $billDate 				= rsbp_getPostValue('billDate', $today);
// 	            $bookOptionOnly			= rsbp_getPostValue('bookOptionOnly', "off");
	            
// 	            $specialAdminParams		= array(
// 	            	'allowPastBooking'	=> $allowPastBooking,
// 	            	'changeBillDate'	=> $changeBillDate,
// 	            	'billDate'			=> $billDate,
// 	            	'bookOptionOnly'	=> $bookOptionOnly,
// 	            );
	            
	            /*
	             * pageKz = 1 --> Buchungsappartmentuebersicht
	             * pageKz = 2 --> Kontakteingabe
	             * pageKz = 3 --> Buchungsuebersicht
	             * pageKz = 4 --> Success-meldung
	             */
	            $bookingPostId  = $bookingObj['bookingPostId'];
	            if (!$processError) {
		            $answer         = $this->updateBooking($bookingObj, $bookingContact, $pageKz, $paymentMethod, $toPageKz, $isAdminKz);
		            if ($answer['CODE'] == 1 && $pageKz == 99 && $toPageKz == 99) {
		            	//wurde aus der administration ausgeloest.
		            	//deshalb muessen die Kontaktdaten ebenfalls sofort gespeichert werden.
		            	$answer     = $this->updateBooking($bookingObj, $bookingContact, 2, $paymentMethod, $toPageKz, $isAdminKz);
		            }
		            if ($toPageKz == 1 && $answer['AMZNPAY'] == 1) {
		            	$pageKz		= 2;
		            } else if (($toPageKz > 0 && $toPageKz < 3 ) || ($toPageKz == -1)) {
		                $pageKz     = $toPageKz;
		            }
		            if ($answer['CODE'] == 1 && $isAdminKz == false) {
		                global $post;
		    //             $post = get_post($bookingId);
		                switch ( $pageKz ) {
		                    case 99:
		                        wp_send_json_success($answer);
		                        break;
		                    case 1:
		                        do_action("rs_indiebooking_single_rsappartment_buchung_contact", $bookingPostId);
		                        break;
		                    case 2:
		                    	/*
		                    	 * Update Carsten Schmitt 14.09.2018
		                    	 * Da PAYPAL, SOFORT und GIROPAY Zahlungsarten sind, die fuer die Zahlungsabwicklung auf eine
		                    	 * andere Seite verlinken, darf die Finale Buchungsanzeige bei diesen Zahlarten nicht hierueber
		                    	 * gesteuert werden, sondern wird ueber den mitgegebenen Link von den Diensten selbst
		                    	 * gesteuert.
		                    	 */
		                    	if (strtoupper($paymentMethod) != "PAYPAL" && strtoupper($paymentMethod) != "STRIPESOFORT" && strtoupper($paymentMethod) != "STRIPEGIROPAY") {
		                        	do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $bookingPostId);
		                    	}
		                        break;
		                    default:
		                        wp_send_json_success($answer);
		                }
		                wp_reset_postdata();
		                die();
		            } else if ($isAdminKz) {
		            	wp_send_json_success($answer);
		            } else {
		                wp_send_json_error($answer);
		            }
	            } else {
	            	$answer = $this->handleProcessError($bookingObj, $processError, $pageKz);
	            }
	        } catch (IndiebookingException $ibe) {
	            wp_send_json_error($ibe->convertToArray());
	        } catch (Exception $e) {
	            $answer['CODE'] = 0;
	            $answer['MSG']  = $e->getMessage();
	            RS_Indiebooking_Log_Controller::write_log(
	                $e->getMessage(),
	                __LINE__,
	                __CLASS__,
	                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
	            );
	            wp_send_json_error($answer);
	        }
        }
    }
    
    public function createDummyBooking_Ajax() {
    	$nonceOk			= false;
    	$adminKz			= rsbp_getPostValue('adminKz', 0, RS_IB_Data_Validation::DATATYPE_INTEGER);
    	if ($adminKz == 1) {
    		$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	} else {
	        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
    	}
        if ($nonceOk) {
	        $answer                 	= array();
	        try {
	        	$today					= new DateTime("now");
	        	$today					= $today->format("d.m.Y");
	        	
	            $bookingInfosObj    	= rsbp_getPostValue('bookingInfosObj', array());
	            $ignoremimimumperiod 	= rsbp_getPostValue('ignoremimimumperiod', false);
	            $allowPastBooking    	= rsbp_getPostValue('allowPastBooking', false);
	            $changeBillDate 		= rsbp_getPostValue('changeBillDate', false);
	            $billDate 				= rsbp_getPostValue('billDate', $today);
	            $bookOptionOnly			= rsbp_getPostValue('bookOptionOnly', "off");
	            
	        	if ($allowPastBooking == "off") {
	        		$allowPastBooking	= false;
	        	} else if ($allowPastBooking == "on") {
	        		$allowPastBooking	= true;
	        	}
	            $specialAdminParams		= array(
	            	'allowPastBooking'	=> $allowPastBooking,
	            	'changeBillDate'	=> $changeBillDate,
	            	'billDate'			=> $billDate,
	            	'bookOptionOnly'	=> $bookOptionOnly,
	            );
	            
	            $adminKz				= rsbp_getPostValue('adminKz', 0, RS_IB_Data_Validation::DATATYPE_INTEGER);
	            $answer             	= $this->createDummyBooking($bookingInfosObj, $ignoremimimumperiod, $adminKz, $specialAdminParams);

	            if ($answer['CODE'] == 1) {
	            	if (!session_id()) {
	            		session_start();
	            	}
	            	if ($adminKz != 1 && session_id()) {
	            		$_SESSION['indiebooking_currentBookingNr'] 		= $answer['BUCHUNGSID'];
	            		$_SESSION['indiebooking_currentBookingPostId'] 	= $answer['BUCHUNGPOSTID'];
	            	}
	                wp_send_json_success($answer);
	            } else {
	                wp_send_json_error($answer);
	            }
	        } catch (IndiebookingException $ibe) {
	            wp_send_json_error($ibe->convertToArray());
	        } catch (Exception $e) {
	            $answer['CODE']     = 0;
	            $answer['MSG']      = $e->getMessage();
	            wp_send_json_error($answer);
	        }
        }
    }
    
    /* @var $modelGutschein RS_IB_Model_Gutschein */
    public function checkApartmentCouponCode_Ajax() {
        $couponCode                 = rsbp_getPostValue('couponCode', "");
        $buchungId                  = rsbp_getPostValue('buchungId', null, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $smallCouponCheck           = rsbp_getPostValue('smallCouponCheck', false);
        $answer                     = $this->checkApartmentCouponCode($couponCode, $buchungId);
        if ($answer['CODE'] == 1) {
//             return wp_send_json_success($answer);
            if ($smallCouponCheck) {
                wp_send_json_success($answer);
            } else {
                global $post;
                $post = get_post($buchungId);
                do_action("rs_indiebooking_single_rsappartment_buchung_almost_booked", $buchungId);
                wp_reset_postdata();
                die();
            }
        } else {
            return wp_send_json_error($answer);
        }
    }
}
// endif;
new RS_IB_Appartment_Buchung_Controller_WP_AJAX();
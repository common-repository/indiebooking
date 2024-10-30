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
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

// if ( ! class_exists( 'RS_IB_Appartment_Buchung_Controller' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Appartment_Buchung_Controller
{
    private $mailController;
    
    public function __construct($fullConstruct = true) {
        add_action( 'rs_indiebooking_buchung_loadOptionsAndCreatePosition', array($this, 'loadOptionsAndCreatePosition'),10,4);
        add_action( 'wp_mail_failed', array($this, 'logMailFailedError'),10,1);
        if ($fullConstruct) {
            $this->mailController       = RS_IB_Mail_Controller::instance(); //new RS_IB_Mail_Controller();
        }
    }
    
    /* @var $wp_error WP_Error */
    public function logMailFailedError($wp_error) {
        $errorCode  = $wp_error->get_error_code();
        $errorMsg   = $wp_error->get_error_message($errorCode);
        
        RS_Indiebooking_Log_Controller::write_log(
            $errorCode." ".$errorMsg,
            __LINE__, __CLASS__,
            RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
    }
    
    /* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
    public function cancelBuchung($bookingId, $storno = false) {
        global $RSBP_DATABASE;
        $answer = array();
        $cancel = true;
        if ($bookingId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $answer                     = $appartmentBuchungsTable->cancelBooking($bookingId, $cancel, $storno);
        }
        return $answer;
    }
    
    public static function returnBuchungByPostId($bookingPostId) {
        global $RSBP_DATABASE;
        
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        $buchungNr                  = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
        $buchung                    = $modelAppBuchungTable->loadBuchungskopf($buchungNr); //Laed die komplette Buchung
        
        return $buchung;
    }
    
    private function getBuchungByPostId($bookingPostId) {
        return self::returnBuchungByPostId($bookingPostId);
    }
     
    /* @var $appartmentTable RS_IB_Table_Appartment */
    /* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
    /* @var $appBuchungZeitraumTable RS_IB_Table_Appartment_Zeitraeume */
    /**
     * Update 21.04.2017 - Carsten Schmitt
     * $notSignificantBookingNr hinzugefuegt um wahrend einer Buchung den Zeitraum auch pruefen zu koennen
     * damit keine doppelbuchung zwischen booking.com und indiebooking ausgeloest werden koennen.
     *
     * @param unknown $appartmentId
     * @param unknown $dateFrom
     * @param unknown $dateTo
     * @param number $notSignificantBookingNr
     * @return boolean
     */
    private function checkDateRange($appartmentId, $dateFrom, $dateTo, $notSignificantBookingNr = 0,
    									$ignoreminimumperiod = false, $allowPastBooking = false) {
        global $RSBP_DATABASE;
        
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appBuchungZeitraumTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
        $notBookableDatesTable      = $RSBP_DATABASE->getTable(RS_IB_Model_Apartment_Gesperrter_Zeitraum::RS_TABLE);
        
        $valid              		= true;
        $dateFrom           		= rs_ib_date_util::convertDateValueToTimestamp($dateFrom);
        $dateTo             		= rs_ib_date_util::convertDateValueToTimestamp($dateTo);
        
        $dtFrom             		= new DateTime("@".$dateFrom);
        $dtTo               		= new DateTime("@".$dateTo);
        
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $curMaxDate					= new DateTime("now");
        $addYears					= "P".$futureAvailabilityYear."Y";
        $curMaxDate->add(new DateInterval($addYears));
        
        if ($dtFrom->getTimestamp() > $curMaxDate->getTimestamp() || $dtTo->getTimestamp() > $curMaxDate->getTimestamp()) {
        	$valid					= false;
        }
        
        if ($valid) {
	        $apartment                  = $appartmentTable->getAppartment($appartmentId);
	        $minnaechte                 = $apartment->getMinDateRange();
	//         $buchungen                  = $appartmentBuchungsTable->getBuchungenByAppartmentid($appartmentId);
	//         $bookedDates                = array();
	//         for ($i = 0; $i < sizeof($buchungen); $i++) {
	//             $bdates = unserialize($buchungen[$i]->meta_value);
	//             $bookedDates[$i]["from"]    = $bdates[0];
	//             $bookedDates[$i]["to"]      = $bdates[1];
	//         }
	        $bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($appartmentId, false, $notSignificantBookingNr);
	        //Schleife genau so noch in RS_IB_Admin_Appartment vorhanden! Methode auslagern!
	        
	        $daysBetween        = $dtFrom->diff($dtTo);
	        $daysBetween        = intval($daysBetween->format('%a'));
	//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."minnaechte: ".$minnaechte." daysBetween: ".$daysBetween);
	        if ($daysBetween >= $minnaechte || $ignoreminimumperiod == 1) {
	            $greater            = rs_ib_date_util::isDateGreaterToday($dateFrom);
	            if ($greater || $allowPastBooking) {
	                $greater        = rs_ib_date_util::isDateGreaterToday($dateTo);
	                if ($greater || $allowPastBooking) {
	                    $overlap    = rs_ib_date_util::isDateOverlap($bookedDates, $dateFrom, $dateTo, 0, true);
	                    if ($overlap) {
	//                         RS_Indiebooking_Log_Controller::wr ite_log("[".__LINE__." ".__CLASS__."]"."uberschneidung!");
	                        $valid          = false;
	                    } else {
	                        //alte Methode: pruefe ob Buchung mit verfuegbaren Zeitrueumen uebereinstimmt
	                        //pruefen, ob der Zeitraum ueberhaupt Buchbar ist.
	//                         $zeitraeume     = $appBuchungZeitraumTable->loadApartmentZeitraume($appartmentId, true);
	//                         $i              = 0;
	//                         $dates          = array();
	//                         foreach ($zeitraeume as $zeitraum) {
	//                             $dates[$i]["from"]  = new DateTime($zeitraum->date_from);
	//                             $dates[$i]["to"]    = new DateTime($zeitraum->date_to);
	//                             $i++;
	//                         }
	//                         $overlap        = rs_ib_date_util::isDateOverlap($dates, $dateFrom, $dateTo);
	//                         if (!$overlap) {
	//                             $valid      = false;
	//                         }
	                        
	                        //neue Methode: prueft ob Buchung nicht mit gesperrten Zeitrueumen uebereinstimmt
	                        $zeitraeume     = $notBookableDatesTable->loadApartmentGesperrteZeitraume($appartmentId);
	                        $i              = 0;
	                        $dates          = array();
	                        foreach ($zeitraeume as $zeitraum) {
	                            $dates[$i]["from"]  = new DateTime($zeitraum->date_from);
	                            $dates[$i]["to"]    = new DateTime($zeitraum->date_to);
	                            $i++;
	                        }
	                        $overlap        = rs_ib_date_util::isDateOverlap($dates, $dateFrom, $dateTo);
	                        if ($overlap) {
	                            $valid      = false;
	                        }
	                    }
	                }  else {
	                    $valid = false;
	                }
	            } else {
	                $valid = false;
	            }
	        } else {
	            $valid  = false;
	        }
        }
//         $valid  = true;
//         for ($i = 0; $i < sizeof($bookedDates); $i++) {
//             $date   = $bookedDates[$i];
//             $from   = $date["from"];
//             $to     = $date["to"];
//             if (($to - $from) > ($dateTo - $dateFrom)) {
//                 if ($dateFrom < $to && $dateTo > $from) {
//                     $valid  = false;
//                     break;
//                 }
//             } else {
//                 if ($from < $dateTo && $to > $dateFrom) {
//                     $valid  = false;
//                     break;
//                 }
//             }
//         }
        return $valid;
    }
    
    
    public function bookingOutOfTime($bookingPostId) {
        global $RSBP_DATABASE;
        $answer                         = array();
        $answer["CODE"]                 = 1;
        if ($bookingPostId > 0) {
        	$bookingStatus				= get_post_status($bookingPostId);
        	if ($bookingStatus != "rs_ib-canceled" && $bookingStatus != "rs_ib-out_of_time") {
	            $myerror                    = "";
	            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	            $wp_error                   = $appartmentBuchungsTable->cancelBooking($bookingPostId, false);
	            if (is_wp_error($wp_error)) {
	                $errors                 = $wp_error->get_error_messages();
	                $errorcode              = $wp_error->get_error_code();
	                foreach ($errors as $error) {
	                    $myerror            = $myerror ." | " . $error;
	                }
	                $answer["CODE"]         = 0;
	            }
	            $answer["MSG"]              = $myerror;
        	} else {
        		$answer["CODE"]         	= 1;
        	}
        }
        return $answer;
    }
    
    /* @var $buchungsKopfTable RS_IB_Table_Buchungskopf */
    public function sendBookingMail($bookingPostId, $mailArt = 1) {
//         $this->mailController->sendBuchungsbestaetigung($bookingPostId);
//         do_action("rs_ib_create_file_and_send_mail", $bookingPostId, $mailArt);
        
//         global $RSBP_DATABASE;
        
//         if ($bookingPostId > 0) {
//             $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
//             $buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
//             $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
            
//             $buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
//             $contact                    = $buchungKopf->getContactArray();
// //             $contact                    = $buchung->getContact();
// //             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
// //             $file                       = printBooking($buchung);
//             $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_confirmation", $bookingPostId);
//             $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_confirm_subject');
//             $to                         = $contact['email'];
//             $subject                    = $mail_confirm_subject; //"test indiebooking";
//             $message                    = get_option('rs_indiebooking_settings_mail_booking_confirmation_txt');
//             if ($message === false) {
//                 $message                = __("Thanks for Booking", 'indiebooking');
//             } else {
//                 $message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
//                 $message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
//                 $message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
//                 $message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
//                 $message                = str_replace('&nbsp;', "<br />", $message);
//             }
//             $header                     = "Content-type: text/html";
//             $attachments                = $file;
//             wp_mail($to, $subject, $message, $header, $attachments);
//         }
    }
//     public function updateOptions($bookingId, $options) {
//     public function updateOptions($bookingObj) {
//         $allAppartments                 = array();
//         if (key_exists('appartments', $bookingObj)) {
//             $allAppartments             = $bookingObj['appartments'];
//         }
//         try {
//             foreach ($allAppartments as $appartmentArray) {
//                 $appId              = $appartmentArray['id'];
//                 $appBuchungVon      = $appartmentArray['buchungVon'];
//                 $appBuchungBis      = $appartmentArray['buchungBis'];
//                 if (key_exists('options', $appartmentArray)) {
//                     $appOptions     = $appartmentArray['options'];
//                 }
//             }
//             update_post_meta($bookingId, RS_IB_Model_Appartment_Buchung::BUCHUNG_OPTIONEN, $bookOptions);
//             $answer['CODE']             = 1;
//         } catch (Exception $e) {
//             $answer['CODE']             = 0;
//             $answer['MSG']              = $e->getMessage();
//         }
//         return $answer;
//     }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    private function confirmPayment($bookingPostId, $totalPayment, $zahlungsstatus = "") {
        global $RSBP_DATABASE;
        $buchungZahlungTable        = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungKopfTable       	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungNr                  = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
        $buchungKopf                = $modelAppBuchungTable->loadBuchungskopf($buchungNr); //Lued die komplette Buchung
        $zahlungsbezeichnung        = "";
        $zahlart                    = 0;
        if ($buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS") {
            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_PAYPALEXPRESS;
            $zahlungsbezeichnung    = __("paypal express payment", 'indiebooking');
        } elseif ($buchungKopf->getHauptZahlungsart() == "PAYPAL") {
            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_PAYPAL;
            $zahlungsbezeichnung    = __("paypal payment", 'indiebooking');
        } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPECREDITCARD") {
            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_STRIPE_CREDIT;
            $zahlungsbezeichnung    = __("creditcard payment", 'indiebooking');
        } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPESOFORT") {
            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_STRIPE_SOFORT;
            $zahlungsbezeichnung    = __("sofort payment", 'indiebooking');
        } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY") {
        	$zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_STRIPE_GIROPAY;
        	$zahlungsbezeichnung    = __("griopay payment", 'indiebooking');
        } elseif ($buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTS") {
        	$zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_AMAZONPAYMENTS;
        	$zahlungsbezeichnung    = __("amazon payments", 'indiebooking');
        }
        $zahlung                    = new RS_IB_Model_BuchungZahlung();
        $zahlung->setBuchung_nr($buchungKopf->getBuchung_nr());
        $zahlung->setBezeichnung($zahlungsbezeichnung);
        $zahlung->setZahlung_nr(0);
        $zahlung->setZahlungbetrag($totalPayment);
        $zahlung->setZahlungzeitpunkt(new DateTime());
        $zahlung->setZahlungart($zahlart);
        $zahlung->setChargeId($buchungKopf->getChargeId());
        $zahlung->setStatus($zahlungsstatus);
        $buchungZahlungTable->saveOrUpdateBuchungZahlung($zahlung);
		/*
		 * Update Carsten Schmitt 10.09.2018
		 * Sofern die Anzahlung aktiv ist, darf die Buchung nicht als bezahlt markiert werden.
		 *
		 * Update Carsten 18.09.2018
		 * Sofern es sich um eine Anzahlung handelt, muss das Anzahlungskennzeichen gesetzt werden.
		 * Ist Anzahlung nicht aktiv, ist die Zahlung vollstaendig. Deshalb setzen wir das AnzahlungMailKz
		 * auf 1. Damit nicht versehentlich bei spaeterer aktivierung der Anzahlung eine Mail an den Kunden heraus geht
		 * obwohl dies garnicht passieren soll.
		 */
        $paymentlData 	= get_option( 'rs_indiebooking_settings_payment');
        $depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
        if ($depositKz == "on") {
        	//alle Zahlungen pruefen?
        	$buchungKopf->setBuchung_status('rs_ib-booked');
        	$buchungKopfTable->updateBuchungsAnzahlungBezahltKz($buchungKopf->getBuchung_nr(), 1);
        } else {
	        $buchungKopf->setBuchung_status('rs_ib-pay_confirmed');
	        $buchungKopf->setAnzahlungmailkz(1);
	        $buchungKopfTable->updateBuchungsAnzahlungBezahltKz($buchungKopf->getBuchung_nr(), 2);
	        $buchungKopfTable->updateAnzahlungMailKz($buchungKopf);
        }
        
        $dateNow			= date('d.m.Y - H:i:s', time());
        $postContent		= sprintf(__('Completed at %s', 'indiebooking'), $dateNow);
        $bookingPostType            = array(
            'ID'            => $bookingPostId,
            'post_title'    => __('Booking from', 'indiebooking')." ".$buchungKopf->getKunde_vorname()." ".$buchungKopf->getKunde_name(),
            'post_type'     => 'rsappartment_buchung',
        	'post_content'  => $postContent, //__('Booking from', 'indiebooking')." ".$buchungKopf->getKunde_vorname()." ".$buchungKopf->getKunde_name(),
            'post_status'   => $buchungKopf->getBuchung_status(),
        );
        $post_id                = wp_update_post($bookingPostType);
    }
    
    
    public function finalizeInquiry($bookingPostId) {
    	global $RSBP_DATABASE;
//     	RS_Indiebooking_Log_Controller::write_log("finalizeInquiry ".$bookingPostId, __LINE__, __CLASS__);
    	$answer								= array();
    	try {
    		if ($bookingPostId > 0) {
    			$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    			$modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    			$buchungNr                  = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
    			$buchungKopf                = $modelAppBuchungTable->loadBuchungskopf($buchungNr); //Laed die komplette Buchung
    			$totalPayment               = 0.0;
    			$zahlungsbezeichnung        = "";
    			$payment                    = false;
    	
    			if (!$payment) {
    				$dateNow			= date('d.m.Y - H:i:s', time());
    				$postContent		= sprintf(__('Completed at %s', 'indiebooking'), $dateNow);
    				$buchungKopf->setBuchung_status('rs_ib-requested');
    				$postTitle			= sprintf(__("Inquiry from %s", 'indiebooking'), $buchungKopf->getKunde_vorname());
    				$bookingPostType         = array(
    						'ID'            => $bookingPostId,
    						'post_title'    => $postTitle,
    						$buchungKopf->getKunde_name(),
    						'post_type'     => 'rsappartment_buchung',
    						'post_content'  => $postContent,
    						'post_status'   => $buchungKopf->getBuchung_status(),
    				);
    				$post_id                = wp_update_post($bookingPostType);
    				$this->mailController->sendAnfragebestaetigung($bookingPostId);
    			}
    			
    			$pages = get_pages(array(
    				'meta_key' => '_wp_page_template',
    				'meta_value' => 'ib_page_success.php'
    			));
    			$successPage 	= null;
    			foreach($pages as $page){
    				$successPage = get_permalink($page->ID);
    				break;
    			}
    			if (is_null($successPage)) {
    				$successPage = "";
    			} else {
    				if (strpos($successPage, '?')) {
    					$successPage = $successPage."&bookingid=".$bookingPostId;
    				} else {
    					$successPage = $successPage."?bookingid=".$bookingPostId;
    				}
    			}
    			$answer['SUCCESSPAGE']	= $successPage;

//     			$this->mailController->sendInfoMailToWordpressAdmin($bookingPostId, 2);
    			$answer['CODE']         = 1;
    			$answer['MSG']          = __("Thanks for your Inquiry.", 'indiebooking');
    			$answer['PERMALINK']    = get_permalink($bookingPostId);
    		} else {
    			$answer['CODE'] = 0;
    			$answer['MSG']  = __("No booking ID found", 'indiebooking');
    		}
    	} catch (Exception $e) {
    		$answer['CODE'] = 0;
    		$answer['MSG']  = $e->getMessage();
    		RS_Indiebooking_Log_Controller::write_log(
    			$e->getMessage(),
    			__LINE__,
    			__CLASS__,
    			RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
    		);
    	}
    	return $answer;
    }
    
    /* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
    /* @var $modelAppBuchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $teilbuchungskopf RS_IB_Model_Teilbuchungskopf */
    /* @var $buchungZahlungTable RS_IB_Table_BuchungZahlung */
    public function finalizeBooking($bookingPostId, $paymentId = "", $paypalToken = "", $payPalPayerId = 0, $customMessage = '', $bookingContact = array(), $isAdminKz = false, $specialAdminParams = array(), $amznOrderRefId = '') {
        global $RSBP_DATABASE;
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."finalizeBooking: ".$bookingPostId);
        
        try {
            if ($bookingPostId > 0) {
                $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
                $modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
                $buchungNr                  = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
                $buchungKopf                = $modelAppBuchungTable->loadBuchungskopf($buchungNr); //Laed die komplette Buchung
                $totalPayment               = 0.0;
                $zahlungsbezeichnung        = "";
                $zahlungsstatus        		= "";
                $payment                    = false;
                $dateRangeOk				= true;
                $bookingOk					= true;
				$contactOk					= true;
				$allowPastBooking			= $buchungKopf->getAllowPastBooking();
//                 $customMessage				= nl2br($customMessage);
//                 $customMessage				= htmlspecialchars($customMessage);
                $buchungKopf->setCustomText($customMessage);
                if (isset($bookingContact) && is_array($bookingContact) && sizeof($bookingContact) > 0) {
                	$wrongFields             = $this->checkContactData($bookingContact, $isAdminKz); //$buchungsKopf->getAdminKz()
                	if (sizeof($wrongFields) <= 0) {
                		$buchungKopf->setKunde_firma($bookingContact['firma']);
                		$buchungKopf->setKunde_anrede($bookingContact['anrede']);
                		$buchungKopf->setKunde_title($bookingContact['titel']);
                		$buchungKopf->setKunde_name($bookingContact['name']);
                		$buchungKopf->setKunde_vorname($bookingContact['firstName']);
                		$buchungKopf->setKunde_plz($bookingContact['plz']);
                		$buchungKopf->setKunde_strasse($bookingContact['strasse']);
                		$buchungKopf->setKunde_ort($bookingContact['ort']);
                		$buchungKopf->setKunde_strasse_nr($bookingContact['strasseNr']);
                		$buchungKopf->setKunde_land($bookingContact['country']);
                		$buchungKopf->setKunde_email($bookingContact['email']);
                		$buchungKopf->setKunde_telefon($bookingContact['telefon']);
                		$buchungKopf->setUseAdress2($bookingContact['altAdress']);
                		$buchungKopf->setKunde_firma2($bookingContact['firma2']);
                		$buchungKopf->setKunde_anrede2($bookingContact['anrede2']);
                		$buchungKopf->setKunde_title2($bookingContact['titel2']);
                		$buchungKopf->setKunde_name2($bookingContact['name2']);
                		$buchungKopf->setKunde_vorname2($bookingContact['firstName2']);
                		$buchungKopf->setKunde_plz2($bookingContact['plz2']);
                		$buchungKopf->setKunde_strasse2($bookingContact['strasse2']);
                		$buchungKopf->setKunde_ort2($bookingContact['ort2']);
                		$buchungKopf->setKunde_strasse_nr2($bookingContact['strasseNr2']);
                		$buchungKopf->setKunde_land2($bookingContact['country2']);
                		$buchungKopf->setKunde_email2($bookingContact['email2']);
                		$buchungKopf->setKunde_telefon2($bookingContact['telefon2']);
                	} else {
                		$contactOk = false;
                	}
                }
                if ($contactOk) {
                	if ($isAdminKz && sizeof($specialAdminParams) > 0) {
                		$allowPastBooking	= $specialAdminParams['allowPastBooking'];
                		$changeBillDate		= $specialAdminParams['changeBillDate'];
                		$billDate			= $specialAdminParams['billDate'];
                		$bookOptionOnly		= $specialAdminParams['bookOptionOnly'];
                		
                		if (($changeBillDate == 1 || $changeBillDate === 'on') && !is_null($billDate) && $billDate != '') {
                			$rechnungsdatum   	= DateTime::createFromFormat("d.m.Y", $billDate);
                			$buchungKopf->setRechnungsdatum($rechnungsdatum);
                			$changeBillDate		= 1;
                		} else {
                			$changeBillDate		= 0;
                		}
                		$buchungKopf->setChangeBillDate($changeBillDate);
                		$buchungKopf->setAllowPastBooking($allowPastBooking);
                	}
                	$paymentlData 	= get_option( 'rs_indiebooking_settings_payment');
                	$depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
                	if ($depositKz != "on") {
                		$buchungKopf->setAnzahlungBezahlt(2);
                	}
	                $modelAppBuchungTable->saveOrUpdateBuchungskopf($buchungKopf);
	                
// 	                if (has_action('rs_indiebooking_synchronize_one_to_booking')) {
// 	                	RS_Indiebooking_Log_Controller::write_log("sync one");
// 	                	do_action('rs_indiebooking_synchronize_one_to_booking', $buchungKopf->getBuchung_nr());
// 	                }
// 	                else
					
					/*
					 * synchronisiert alle Booking.com verfuegbarkeiten, um wirklich sicher zu gehen,
					 * dass waehrend der Buchung nicht eine Buchung bei booking.com eingegangen ist.
					 */
					if (has_action('rs_indiebooking_synchronize_bookingcom')) {
	                	do_action('rs_indiebooking_synchronize_bookingcom');
	                }
	                
	                /*
	                 * Prueft anschliessend noch einmal ob der Datumsbereich auch wirklich frei ist.
	                 */
	                $ignoreminimumperiod		= $buchungKopf->getIgnoreMinimumPeriod();
	                $teilbuchungen				= $buchungKopf->getTeilkoepfe();
	                foreach ($teilbuchungen as $teilbuchungskopf) {
	                	$appartmentId			= $teilbuchungskopf->getAppartment_id();
	                	$apdateFrom				= $teilbuchungskopf->getTeilbuchung_von();
	                	$apdateTo				= $teilbuchungskopf->getTeilbuchung_bis();
	                	if ($buchungKopf->getBookingType() == 1) {
		                	$dateRangeOk 		= $this->checkDateRange($teilbuchungskopf->getAppartment_id(), $apdateFrom,
		                												$apdateTo, $teilbuchungskopf->getBuchung_nr(),
		                												$ignoreminimumperiod, $allowPastBooking);
	                	} else {
	                		$dateRangeOk		= true;
	                	}

	                	if (!$dateRangeOk) {
	                		break;
	                	}
	                }
	                if ($dateRangeOk) {
		                if ($paymentId != "" && $buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS") {
		                    $paypalExpressAnswer    = apply_filters(
		                        "rs_indiebooking_accept_paypal_express_payment",
		                        $paymentId, $paypalToken, $payPalPayerId, $totalPayment
		                    );
		                    $payment                = $paypalExpressAnswer['payment'];
		                    $totalPayment           = $paypalExpressAnswer['totalpayment'];
		                    
		                } elseif ($buchungKopf->getHauptZahlungsart() == "PAYPAL") { //&& $paymentId == "BASICPAYPAL_SUCCESS"
		                    $zahlungsbezeichnung    = __("paypal payment", 'indiebooking');
		                    $totalPayment           = $buchungKopf->getZahlungsbetrag();
		//                     $payment                = true;
		                    $paypalExpressAnswer    = apply_filters(
		                    	"rs_indiebooking_accept_paypal_plus_payment",
		                    	$paymentId, $paypalToken, $payPalPayerId, $totalPayment
		                    );
		                    $payment                = $paypalExpressAnswer['payment'];
		                    $totalPayment           = $paypalExpressAnswer['totalpayment'];
		                } elseif ($buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTS" || $buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTSEXPRESS") {
		                	try {
		                		$amznAnswer 			= apply_filters('rs_indiebooking_do_amazon_order_payment', $buchungKopf);
		                	} catch (Exception $e) {
		                		$s = $e->getMessage();
		                	}
		                	if ($amznAnswer['CODE'] == 1) {
		                		$bookingOk			= true;
		                		$payment			= true;
		                		$totalPayment		= $amznAnswer['totalpayment'];
		                	} else {
		                		$bookingOk 			= false;
		                	}
		                } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPECREDITCARD"
		                	|| $buchungKopf->getHauptZahlungsart() == "STRIPESOFORT"
		                	|| $buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY"
		                	|| $buchungKopf->getHauptZahlungsart() == "STRIPESEPADIRECT") {
		                		if ($buchungKopf->getHauptZahlungsart() == "STRIPECREDITCARD") {
		                			$stripeAnswer			= apply_filters("rs_indiebooking_accept_stripe_payment", $buchungKopf->getBuchung_nr());
		                		} elseif ($buchungKopf->getHauptZahlungsart() == "STRIPESOFORT") {
		                			$stripeAnswer			= apply_filters("rs_indiebooking_accept_stripe_sofort_payment", $buchungKopf->getBuchung_nr());
		                		} elseif ($buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY") {
		                			$stripeAnswer			= apply_filters("rs_indiebooking_accept_stripe_giropay_payment", $buchungKopf->getBuchung_nr());
		                		} elseif ($buchungKopf->getHauptZahlungsart() == "STRIPESEPADIRECT") {
		                			$stripeAnswer			= apply_filters("rs_indiebooking_accept_stripe_sepadirect_payment", $buchungKopf->getBuchung_nr());
		                		}
			                	if ($stripeAnswer['CODE'] == 1) {
			                		if (key_exists('CHARGEID', $stripeAnswer)) {
			                			$buchungKopf->setChargeId($stripeAnswer['CHARGEID']);
			                		}
			                		if (key_exists('STATUS', $stripeAnswer)) {
			                			$zahlungsstatus = $stripeAnswer['STATUS'];
			                		}
			                		$totalPayment 		= $stripeAnswer['AMOUNTINCENT'];
			                		$totalPayment 		= floatval($totalPayment / 100);
			                		$payment			= true;
			                	} else {
			                		$bookingOk 			= false;
		                		}
		                }
		                if ($bookingOk) {
		                	$modelAppBuchungTable->createAndSaveBillNumber($buchungKopf);
		                	$dateNow				= date('d.m.Y - H:i:s', time());
		                	$postContent			= sprintf(__('Completed at %s', 'indiebooking'),
		                								$dateNow);
			                if (!$payment) {
			                	$bookingInquiriesKz		= get_option('rs_indiebooking_settings_booking_inquiries_kz');
			                    $buchungKopf->setBuchung_status('rs_ib-booked');
			                    $bookingPostType         = array(
			                        'ID'            => $bookingPostId,
			                        'post_title'    => __('Booking from', 'indiebooking')." ".$buchungKopf->getKunde_vorname()." ".
			                                                $buchungKopf->getKunde_name(),
			                        'post_type'     => 'rsappartment_buchung',
			                    	'post_content'  => $postContent,
			                        'post_status'   => $buchungKopf->getBuchung_status(),
			                    );
			                    $post_id                = wp_update_post($bookingPostType);
			//                     $this->mailController->sendBuchungsbestaetigung($bookingPostId);
			//                     $this->mailController->sendBuchungRechnung($bookingPostId);
			                    $this->mailController->sendBuchungsbestaetigungUndRechnung($bookingPostId);
			                } else {
			//                     $this->mailController->sendBuchungsbestaetigung($bookingPostId);
			                	$this->confirmPayment($bookingPostId, $totalPayment, $zahlungsstatus);
			                    if ($buchungKopf->getHauptZahlungsart() == "PAYPAL" || $buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS"
			                    		|| $buchungKopf->getHauptZahlungsart() == "STRIPECREDITCARD"
			                    		|| $buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY"
			                    		|| $buchungKopf->getHauptZahlungsart() == "STRIPESOFORT"
			                    		|| $buchungKopf->getHauptZahlungsart() == "AMAZONPAYMENTS") {
			//                         $this->mailController->sendBuchungRechnung($bookingPostId);
			                        $this->mailController->sendBuchungsbestaetigungUndRechnung($bookingPostId);
			                    } else {
			                        $this->mailController->sendZahlungsbestateigung($bookingPostId);
			                    }
			                }
	// 	                }
	// 	                if ($bookingOk) {
		//                 $modelAppBuchungTable->saveOrUpdateBuchungskopf($buchung);
			                $answer['CODE']         = 1;
			                $answer['MSG']          = __("Thanks for Booking.", 'indiebooking');
			                $answer['PERMALINK']    = get_permalink($bookingPostId);
			                

	// 		                $pageargs = [
	// 		                	'post_type' => 'page',
	// 		                	'fields' => 'ids',
	// 		                	'nopaging' => true,
	// 		                	'meta_key' => 'success page',
	// 		                ];
			               	$pages = get_pages(array(
			               		'meta_key' => '_wp_page_template',
			               		'meta_value' => 'ib_page_success.php'
			               	));
			               	$successPage 	= null;
			               	foreach($pages as $page){
			               		$successPage = get_permalink($page->ID);
			               		break;
			               	}
			               	if (is_null($successPage)) {
			               		$successPage = "";
			               	} else {
			               		if (strpos($successPage, '?')) {
			               			$successPage = $successPage."&bookingid=".$bookingPostId;
			               		} else {
			               			$successPage = $successPage."?bookingid=".$bookingPostId;
			               		}
			               	}
			               	
			               	$answer['SUCCESSPAGE']	= $successPage;
	// 		                $this->mailController->sendInfoMailToWordpressAdmin($bookingPostId, 1);
			                
			               	/*
			               	 * Synchronisiert anschliessend nur die gerade gaetigte Buchung zu booking.com
			               	 * oder, sofern das booking.com plugin noch nicht aktualisiert wurde, alle.
			               	 */
			               	if (has_action('rs_indiebooking_synchronize_one_to_booking')) {
			               		do_action('rs_indiebooking_synchronize_one_to_booking', $buchungKopf->getBuchung_nr());
							}
			                else if (has_action('rs_indiebooking_synchronize_bookingdata')) {
			                	do_action('rs_indiebooking_synchronize_bookingdata');
			                }
		                } else {
		                	$answer['CODE'] = 0;
		                	$answer['MSG']  = __("Sorry, there went something wrong.
	                							Please go back to step 1 and try again.", 'indiebooking');
		                }
	                } else {
	                	$answer['CODE'] = 0;
	                	$answer['MSG']  = __("Sorry, the date range you are trying to book doesn't seem to be available anymore.
	                							Please go back to step 1 and check the availabilities.", 'indiebooking');
	                }
                } else {
                	$answer['CODE'] = 0;
                	$answer['MSG']  = __("Invalid Contactdata", 'indiebooking');
                }
            } else {
                $answer['CODE'] = 0;
                $answer['MSG']  = __("No booking ID found", 'indiebooking');
            }
        } catch (Exception $e) {
            $answer['CODE'] = 0;
            $answer['MSG']  = $e->getMessage();
            RS_Indiebooking_Log_Controller::write_log(
                $e->getMessage(),
                __LINE__,
                __CLASS__,
                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
            );
        }
        return $answer;
    }
    
    private function checkContactData($contactData, $adminKz = false) {
        $wrongField = array();
        
        $checkFirma		= true;
        $checkAbteilung	= true;
        $checkAnrede	= true;
        $checkVorname	= true;
        $checkNachname	= true;
        $checkMail		= true;
        $checkAdress	= true;
        $checkTelefon	= true;
        
        $requiredFilterData 					= get_option( 'rs_indiebooking_settings_contact_required');
        if ($requiredFilterData && !$adminKz) {
        	$settingsContactRequiredFirmaKz		= (key_exists('firma', $requiredFilterData))     ?  esc_attr__( $requiredFilterData['firma'] )      : "";
        	$settingsContactRequiredAbteilungKz	= (key_exists('abteilung', $requiredFilterData)) ?  esc_attr__( $requiredFilterData['abteilung'] )  : "";
        	$settingsContactRequiredAnredeKz	= (key_exists('anrede', $requiredFilterData)) 	 ?  esc_attr__( $requiredFilterData['anrede'] ) 	: "";
        	$settingsContactRequiredVornameKz	= (key_exists('vorname', $requiredFilterData))   ?  esc_attr__( $requiredFilterData['vorname'] )    : "";
        	$settingsContactRequiredNachnameKz	= (key_exists('nachname', $requiredFilterData))  ?  esc_attr__( $requiredFilterData['nachname'] )   : "";
        	$settingsContactRequiredMailKz		= (key_exists('mail', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['mail'] )       : "";
        	$settingsContactRequiredAdressKz	= (key_exists('address', $requiredFilterData))   ?  esc_attr__( $requiredFilterData['address'] )	: "";
        	$settingsContactRequiredTelefonKz	= (key_exists('telefon', $requiredFilterData))   ?  esc_attr__( $requiredFilterData['telefon'] )	: "";
        	
        	if ($settingsContactRequiredFirmaKz		== "off") {
        		$checkFirma 	= false;
        	}
        	if ($settingsContactRequiredAbteilungKz	== "off") {
        		$checkAbteilung = false;
        	}
        	if ($settingsContactRequiredAnredeKz	== "off") {
        		$checkAnrede 	= false;
        	}
        	if ($settingsContactRequiredVornameKz	== "off") {
        		$checkVorname	= false;
        	}
        	if ($settingsContactRequiredNachnameKz	== "off") {
        		$checkNachname	= false;
        	}
        	if ($settingsContactRequiredMailKz		== "off") {
        		$checkMail		= false;
        	}
        	if ($settingsContactRequiredAdressKz	== "off") {
        		$checkAdress	= false;
        	}
        	if ($settingsContactRequiredTelefonKz	== "off") {
        		$checkTelefon	= false;
        	}
        } else if ($adminKz) {
        	$checkFirma		= false;
        	$checkAbteilung	= false;
        	$checkAnrede	= false;
        	$checkVorname	= false;
        	$checkNachname	= false;
        	$checkMail		= false;
        	$checkAdress	= false;
        	$checkTelefon	= false;
        }
        
        $altAdr     = filter_var($contactData["altAdress"], FILTER_VALIDATE_BOOLEAN);
        foreach ($contactData as $key => $value) {
            switch ($key) {
                //nachfolgend die Felder, die leer sein duerfen
                case "firma":
                	if ($checkFirma && strlen(trim($value)) <= 0) {
                		$wrongField[] = $key;
                	}
                	break;
                case "department":
                case "abteilung":
                	if ($checkAbteilung && strlen(trim($value)) <= 0) {
                		$wrongField[] = $key;
                	}
                	break;
                case "titel":
                	break;
                case "firstName":
                	if ($checkVorname && strlen(trim($value)) <= 0) {
                		$wrongField[] = $key;
                	}
                	break;
                case "name":
                	if ($checkNachname && strlen(trim($value)) <= 0) {
                		$wrongField[] = $key;
                	}
                	break;
                case "email":
                	if ($checkMail && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                		$wrongField[] = $key;
                	}
                	break;
                case "strasse":
                case "plz":
                case "ort":
                case "country":
                	if ($checkAdress && strlen(trim($value)) <= 0) {
                		$wrongField[] = $key;
                	}
                	break;
                case "strasseNr":
                	if ($checkAdress && (strlen($value) > 10 || strlen(trim($value)) <= 0)) {
                		$wrongField[] = $key;
                	}
                	break;
                case "telefon":
                	if ($adminKz || !$checkTelefon) {
                		/*
                		 * wird die Buchung aus der Administration heraus erstellt,
                		 * oder es ist angegeben, dass die Telefonnummer nicht verpflichtend ist,
                		 * darf die Telefonnummer fehlen.
                		 */
                		break;
                	}
                case "firma2":
                case "abteilung2":
                case "titel2":
                case "altAdress":
                case "email2":
                case "firstName2":
                case "name2":
                case "telefon2":
                case "anrede":
                case "anrede2":
                    break;
                case "strasseNr2":
            		if (strlen($value) > 10) {
            			$wrongField[] = $key;
            		}
        			break;
                default:
                    if (substr($key, -1) !== "2" || $altAdr == true) {
                        if ($value == "") {
                            $wrongField[] = $key;
                        }
                    }
            }
        }
        return $wrongField;
    }
    
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    /* @var $teilKopfTable RS_IB_Table_Teilbuchungskopf */
    /* @var $buchungPositionTable RS_IB_Table_Buchungposition */
    /* @var $buchungMwstTable RS_IB_Table_BuchungMwSt */
    
    /* @var $teilkopf RS_IB_Model_Teilbuchungskopf */
    /* @var $position RS_IB_Model_Buchungposition */
    private function updateCalculatedValues($buchungNr, $maxAnzahlNaechte = null, $biggestFrom = null, $biggestTo = null, $post_id = null) {
        global $RSBP_DATABASE;
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."updateCalculatedValues - $buchungNr");
        
        $buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $teilKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        $buchungPositionTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        $buchungMwstTable       = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungMwSt::RS_TABLE);
        
        $completeBuchung        = $buchungKopfTable->loadBooking($buchungNr); //beim laden werden alle Werte neu berechnet
        if (!is_null($maxAnzahlNaechte)) {
            $completeBuchung->setAnzahl_naechte($maxAnzahlNaechte);
        }
        if (!is_null($biggestFrom) && !is_null($biggestTo)) {
        	/*
        	 * Ohne das ich hier zuerst einmal den Buchungskopf mit den neusten Buchung_von und Buchung_bis
        	 * speichere, wuerde die Couponpruefung nicht funktionieren.
        	 */
        	$completeBuchung->setBuchung_von($biggestFrom);
        	$completeBuchung->setBuchung_bis($biggestTo);
        	$buchungKopfTable->saveOrUpdateBuchungskopf($completeBuchung);
        }
        $this->checkAndSaveCampaigns($completeBuchung);
        $completeBuchung        = $buchungKopfTable->loadBooking($buchungNr); //beim laden werden alle Werte neu berechnet
        foreach ($completeBuchung->getTeilkoepfe() as $teilkopf) {
            foreach ($teilkopf->getPositionen() as $position) {
                $position->setQuadratmeter($teilkopf->getAppartment_qm());
                $buchungPositionTable->saveOrUpdateBuchungsposition($position);
            }
            $teilKopfTable->saveOrUpdateTeilbuchungskopf($teilkopf);
        }
        if (!is_null($maxAnzahlNaechte)) {
            $completeBuchung->setAnzahl_naechte($maxAnzahlNaechte);
        }
        if (!is_null($biggestFrom) && !is_null($biggestTo)) {
        	$completeBuchung->setBuchung_von($biggestFrom);
        	$completeBuchung->setBuchung_bis($biggestTo);
        }
        
        $buchungKopfTable->saveOrUpdateBuchungskopf($completeBuchung);
        foreach ($completeBuchung->getFullMwstArray() as $mwst) {
            $buchungMwstTable->saveOrUpdateBuchungMwSt($mwst);
        }
    }
    
    
    
    public function checkApartmentBookable($apartmentId) {
        global $RSBP_DATABASE;
        
        $appartmentTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment             = $appartmentTable->getAppartment($apartmentId);
        
        return $appartment->isApartmentBookable();
    }
    
    public function checkApartmentArrivalDays($apartmentId, $buchungVon, $buchungBis) {
        global $RSBP_DATABASE;
    
        $appartmentTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment             = $appartmentTable->getAppartment($apartmentId);
        $arrivalDays            = $appartment->getArrivalDays();

        $tbVon                  = rs_ib_date_util::convertDateValueToDateTime($buchungVon);
        $tbBis                  = rs_ib_date_util::convertDateValueToDateTime($buchungBis);
        
        $dayOfWeek      = date("w", $tbVon->getTimestamp());
        if ($dayOfWeek == 0) {
            $dayOfWeek  = 7;
        }
        if (sizeof($arrivalDays) > 0 && !in_array($dayOfWeek, $arrivalDays)) {
            return false;
        }
        return true;
    }
    
    public function checkArrivalDays($buchungKopf) {
        global $RSBP_DATABASE;
    
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        foreach ($buchungKopf->getTeilkoepfe() as $teilkopf) {
            $appartment     = $appartmentTable->getAppartment($teilkopf->getAppartment_id());
            $arrivalDays    = $appartment->getArrivalDays();
    
            if (!$teilkopf->getTeilbuchung_von() instanceof DateTime) {
                $tbVon      = new DateTime($teilkopf->getTeilbuchung_von());
            } else {
                $tbVon      = $teilkopf->getTeilbuchung_von();
            }
            $dayOfWeek      = date("w", $tbVon->getTimestamp());
            if ($dayOfWeek == 0) {
                $dayOfWeek  = 7;
            }
            if (sizeof($arrivalDays) > 0 && !in_array($dayOfWeek, $arrivalDays)) {
                return false;
            }
        }
        return true;
    }
    
    private function getAnzahlTage($von, $bis) {
        $dtFrom         = rs_ib_date_util::convertDateValueToDateTime($von);//date_create_from_format('d.m.Y', $teilHeader->getTeilbuchung_von());
        $dtTo           = rs_ib_date_util::convertDateValueToDateTime($bis);//date_create_from_format('d.m.Y', $teilHeader->getTeilbuchung_bis());
//         $dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
//         $dtTo           = date('Y-m-d', $dtTo->getTimestamp());
        
        $differenz = date_diff($dtFrom, $dtTo);
        return $differenz->format('%a');
    }
    
    public function handleProcessError($bookingObj, $processError, $pageKz = false) {
    	$answer = array();
    	switch ($processError) {
    		case "stripeSofortCreateSourceError":
    			$bookingPostId = $bookingObj['bookingPostId'];
    			$bookingPostType        = array(
    				'ID'            => $bookingPostId,
    				'post_type'     => 'rsappartment_buchung',
    				'post_status'   => 'rs_ib-booking_info',
    			);
    			$post_id   = wp_update_post($bookingPostType);
    			
    			if ($pageKz) {
    				update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, $pageKz);
    			}
    			
    			$answer['CODE'] 	= 1;
    			$answer['POSTID'] 	= $post_id;
    			break;
    		default:
    			$answer['CODE'] 	= 0;
    			break;
    	}
    	return $answer;
    }
    
    
    private function resetRabattValues($buchungNr) {
    	global $RSBP_DATABASE;
    	
    	$rabattTable            = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
    	$rabattTable->resetDegressionRabatt($buchungNr);
    	$rabattTable->resetAufschlagsRabatt($buchungNr);
    	$rabattTable->resetAktionsRabatt($buchungNr);
    }
    
    /* @var $modelAppBuchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $teilbuchungskopf RS_IB_Model_Teilbuchungskopf */
//     public function updateBooking($bookingId, $appartmentId, $dateFrom, $dateTo, $options, $bookingContact) {
    /*
     * pageKz = 1 --> Buchungsappartmentuebersicht
     * pageKz = 2 --> Kontakteingabe
     * pageKz = 3 --> Buchungsuebersicht
     * pageKz = 4 --> Success-meldung
     */
    public function updateBooking($bookingObj, $bookingContact, $pageKz, $paymentMethod, $toPageKz = 0, $isAdminKz = false) {
        global $RSBP_DATABASE;
        
        $answer							= array();
        $doBooking						= true; //ein einfaches KZ das festhaelt ob die Buchung durchgefuert werden kann
        $dateRangeOk					= true;
        $optionDateRangeOk				= true;
        $appartmentTable                = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $modelAppBuchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $allAppartments                 = array();
        $optionDateAnswer				= array();
        $bookingPostId                  = $bookingObj['bookingPostId'];
        $post_id                        = $bookingPostId;
        $clickedOption					= array();
        $buchungNr                      = get_post_meta($bookingPostId, "rs_ib_buchung_kopf_id", true);
        if (key_exists('appartments', $bookingObj)) {
            $allAppartments             = $bookingObj['appartments'];
        }
        
        $answer['AMZNPAY']				= 0;
        $postStatus						= get_post_status($post_id);
        /*
         * Update Carsten Schmitt 28.09.2018
         * Auch beim updaten einer Buchung muss geprueft werden, ob die Buchung ueberhautp noch aktiv ist.
         * Ansonsten kann es passieren, dass eine Buchung, die auf Administrationsseite oder auch wegen eines Timeouts
         * abgebrochen wurde, durch die Javascript-Funktion erneut aktiviert wird.
         * das kann im schlimmsten Fall zu doppelbuchungen fuehren.
         *
         * TODO pruefen ob diese Loesung so reicht.
         */
        if ($postStatus != "rs_ib-canceled" && $postStatus != "rs_ib-out_of_time") {
	        /*
	         * Update Carsten Schmitt - 12.09.2018
	         * Bei jedem Update der Buchung soll automatisch der Heartbeat aktualisiert werden um zu vermeiden,
	         * dass es wegen einer Weiterleitung o.ä. beim nächsten Heartbeatcheck zu Problemen kommt.
	         */
	        update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_LAST_HEARTBEAT, time());
	        if ($toPageKz == 99) {
	        	update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, 0);
	        	RS_Indiebooking_Log_Controller::write_log("stop heartbeat ".$post_id);
	        } else {
	        	update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, 1);
	        	RS_Indiebooking_Log_Controller::write_log("activate heartbeat ".$post_id);
	        }
	        
		        /*
		         * Update Carsten Schmitt - 05.09.2018
		         * Ist gefuellt, wenn Updatebooking ueber den Klick auf eine Option durchgefuehrt wurde.
		         */
		        if (key_exists('clickedOption', $bookingObj)) {
		        	$clickedOption				= $bookingObj['clickedOption'];
		        }
		        
		//         $modelAppBuchungTable->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, time());

		    $buchungsKopf                   = $modelAppBuchungTable->loadBuchungskopf($buchungNr);
		    $amaznPayment					= ($buchungsKopf->getBuchung_status() == "rs_ib-blocked" && ($paymentMethod == "AMAZONPAYMENTSEXPRESS" || $buchungsKopf->getHauptZahlungsart() == "AMAZONPAYMENTSEXPRESS"));
		    $biggestPageKz					= get_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, true);
		    if (!$amaznPayment || ($biggestPageKz !== "" && $biggestPageKz != false)) {
		        $allowPastBooking				= $buchungsKopf->getAllowPastBooking();
		        $maxAnzalNaechte                = 0;
		        $current_user                   = wp_get_current_user();
		        $ignoreminimumperiod			= $buchungsKopf->getIgnoreMinimumPeriod();
		        $biggestFrom					= null;
		        $biggestTo						= null;
		        $biggestKz 						= get_post_meta($bookingPostId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, true);
		        
		        switch ($pageKz) {
		            case ($pageKz == 1 || $pageKz == 99): //Buchungsappartmentuebersicht -> Kontakteingabe
		            	$this->resetRabattValues($buchungNr);
		            	$optionErrorMsg			= "";
		            	$invalidOptionIds 		= array();
		                foreach ($allAppartments as $appartmentArray) {
		                    $appOptions         = array();
		                    
		                    $appId              = $appartmentArray['id'];
		                    $appBuchungVon      = $appartmentArray['buchungVon'];
		                    $appBuchungBis      = $appartmentArray['buchungBis'];
		                    $anzahlPersonen     = $appartmentArray['anzPers'];
		                    if (key_exists('options', $appartmentArray)) {
		                        $appOptions     = $appartmentArray['options'];
		                    }
		                    
		                    $teilbuchungskopfA  = $modelAppBuchungTable->loadTeilbuchungskopf($buchungNr, $appId);
		                    if (sizeof($teilbuchungskopfA) > 0) {
		                        foreach ($teilbuchungskopfA as $teilbuchungskopf) {
		                        	$dtFrom         = rs_ib_date_util::convertDateValueToDateTime($appBuchungVon);
		                        	$dtTo           = rs_ib_date_util::convertDateValueToDateTime($appBuchungBis);
		                        	if (is_null($biggestFrom) || ($dtFrom < $biggestFrom)) {
		                        		$biggestFrom = $dtFrom;
		                        	}
		                        	if (is_null($biggestTo) || ($dtTo < $biggestTo)) {
		                        		$biggestTo = $dtTo;
		                        	}
		                        	
		                            $teilbuchungskopf->setTeilbuchung_von($appBuchungVon);
		                            $teilbuchungskopf->setTeilbuchung_Bis($appBuchungBis);
		                            $teilbuchungskopf->setAnzahlPersonen($anzahlPersonen);
		                            
		                            $anzTage        = $this->getAnzahlTage($appBuchungVon, $appBuchungBis);
		                            if (intval($anzTage) > intval($maxAnzalNaechte)) {
		                                $maxAnzalNaechte = $anzTage;
		                            }
		                            
		                            if ($buchungsKopf->getBookingType() == 1) {
			                            $dateRangeOk = $this->checkDateRange($teilbuchungskopf->getAppartment_id(), $appBuchungVon,
			                            										$appBuchungBis, $teilbuchungskopf->getBuchung_nr(),
			                            										$ignoreminimumperiod, $allowPastBooking);
		                            } else {
		                            	$dateRangeOk = true;
		                            }
		                            if (sizeof($appOptions) > 0 && sizeof($clickedOption) > 0) {
		                            	$clickedOptionId 	= $clickedOption['optionId'];
		                            	$clickedApartmentId = $clickedOption['apartmentid'];
		                            	$wasChecked			= $clickedOption['checked'];
		                            	if ($wasChecked == "true" && $clickedApartmentId == $teilbuchungskopf->getAppartment_id()) {
		                            		$optionDateAnswer 	= apply_filters('rs_indiebooking_buchung_check_option_availability', $appOptions, $appBuchungVon, $appBuchungBis, $teilbuchungskopf, $ignoreminimumperiod, $allowPastBooking);
		                            	}
		//                             	$optionAnswer = array(
		//                             		'optionName' => $option->getName(),
		//                             		'optionId'	=> $option->getTermId(),
		//                             		'valid'		=> $valid,
		//                             	);
		                            	foreach ($optionDateAnswer as $optionAnswer) {
		                            		if ($optionAnswer['valid'] == false) {
		                            			$optionDateRangeOk = false;
		                            			break;
		                            		}
		                            	}
		                            }
		                            if ($dateRangeOk && $optionDateRangeOk) {
			                            $teilbuchungsNr = $modelAppBuchungTable->saveOrUpdateTeilbuchungskopf($teilbuchungskopf);
			                            $modelAppBuchungTable->deleteBuchungspositionen($teilbuchungskopf);
			                            
			                            if ($buchungsKopf->getBookingType() == 1) {
			                            	$this->loadAppartmentPricesAndCreatePosition($teilbuchungskopf->getAppartment_id(), $teilbuchungskopf, $buchungsKopf);
			                            }
			                            do_action("rs_indiebooking_buchung_loadOptionsAndCreatePosition",
			                            	$teilbuchungskopf->getAppartment_id(), $teilbuchungskopf, $appOptions, $isAdminKz);
		                            } else {
		                            	$doBooking = false;
		                            }
		                        }
		                    }
		                    if ($doBooking) {
			                    if ($toPageKz != -1) {
			                    	$postStatus				= 'rs_ib-booking_info';
			                    	$postDescription		= __('Stays on contact input page', 'indiebooking');
			                    	if ($toPageKz == 2 && $biggestKz >= 2) {
			                    		$postStatus			= 'rs_ib-almost_booked';
			                    		$postDescription	= __('Stays on overview / finalization page');
			                    	}
			                        $bookingPostType        = array(
			                            'ID'            => $bookingPostId,
			                        	'post_title'    => __('Booking', 'indiebooking').' '.$buchungsKopf->getBuchung_nr(),
			                            'post_type'     => 'rsappartment_buchung',
			                        	'post_content'  => $postDescription,
			                        	'post_status'   => $postStatus,
			                        );
			                        $post_id                = wp_update_post($bookingPostType);
			                    }
			                    $buchungsKopf               = $modelAppBuchungTable->loadBuchungskopf($buchungNr);
			                    
			                    if ($this->checkArrivalDays($buchungsKopf)) {
			                    	$this->updateCalculatedValues($buchungNr, $maxAnzalNaechte, $biggestFrom, $biggestTo, $post_id);
			                        
			                        $answer['CODE']         = 1;
			    //                     $answer['BUCHUNGSID']   = $post_id; //!= buchungNr
			                        $answer['PERMALINK']    = get_permalink($bookingPostId);
			                    } else {
			                        $answer['CODE']         = 0;
			                        $answer['MSG']          = __("Invalid Arrival Days", 'indiebooking');
			                    }
		                    } else {
		                    	$answer['CODE']         = 0;
		                    	if (!$dateRangeOk) {
		                    		$answer['MSG']          = __("Invalid Daterange", 'indiebooking');
		                    	} else if (!$optionDateRangeOk) {
		                    		if ($optionErrorMsg == "") {
		                    			$optionErrorMsg = __("The following options are not available in this time period", 'indiebooking');
		                    		}
		                    		foreach ($optionDateAnswer as $optionAnswer) {
		                    			if ($optionAnswer['valid'] == false) {
		                    				$optionErrorMsg .= "<br /> -".$optionAnswer['optionName'];
		                    				if (!key_exists($optionAnswer['apartmentId'], $invalidOptionIds)) {
		                    					$invalidOptionIds[$optionAnswer['apartmentId']] = array();
		                    				}
		                    				array_push($invalidOptionIds[$optionAnswer['apartmentId']], $optionAnswer['optionId']);
		                    			}
		                    		}
		                    		$optionDateAnswer			= array();
		                    		$answer['notValidOptions'] 	= $invalidOptionIds;
		                    		$answer['MSG']				= $optionErrorMsg;
		                    	} else {
		                    		$answer['MSG']          = __("An unexpected error occurred", 'indiebooking');
		                    	}
		                    }
		                }
		                break;
		            case ($pageKz == 2): //Kontakteingabe -> Buchungsuebersicht
		                $wrongFields             = $this->checkContactData($bookingContact, $isAdminKz); //$buchungsKopf->getAdminKz()
		                if (sizeof($wrongFields) <= 0) {
		                    $buchung            = $modelAppBuchungTable->loadBuchungskopf($buchungNr);
		                    $rechnungsdatum		= $buchung->getBuchungsdatum();
		                    if (!isset($rechnungsdatum) || is_null($rechnungsdatum)) {
		                    	$rechnungsdatum = new DateTime("now");
		                    }
		                    
		//                     echo $buchung->getBuchung_von();
		                    $buchung->setKunde_firma($bookingContact['firma']);
		                    $buchung->setKunde_anrede($bookingContact['anrede']);
		                    $buchung->setKunde_title($bookingContact['titel']);
		                    $buchung->setKunde_name($bookingContact['name']);
		                    $buchung->setKunde_vorname($bookingContact['firstName']);
		                    $buchung->setKunde_plz($bookingContact['plz']);
		                    $buchung->setKunde_strasse($bookingContact['strasse']);
		                    $buchung->setKunde_ort($bookingContact['ort']);
		                    $buchung->setKunde_strasse_nr($bookingContact['strasseNr']);
		                    $buchung->setKunde_land($bookingContact['country']);
		                    $buchung->setKunde_email($bookingContact['email']);
		                    $buchung->setKunde_telefon($bookingContact['telefon']);
		                    if (key_exists('abteilung', $bookingContact)) {
		                    	$buchung->setKunde_abteilung($bookingContact['abteilung']);
		                    } else if (key_exists('department', $bookingContact)) {
		                    	$buchung->setKunde_abteilung($bookingContact['department']);
		                    }
		                    
		                    $buchung->setUseAdress2($bookingContact['altAdress']);
		                    $buchung->setKunde_firma2($bookingContact['firma2']);
		                    $buchung->setKunde_anrede2($bookingContact['anrede2']);
		                    $buchung->setKunde_title2($bookingContact['titel2']);
		                    $buchung->setKunde_name2($bookingContact['name2']);
		                    $buchung->setKunde_vorname2($bookingContact['firstName2']);
		                    $buchung->setKunde_plz2($bookingContact['plz2']);
		                    $buchung->setKunde_strasse2($bookingContact['strasse2']);
		                    $buchung->setKunde_ort2($bookingContact['ort2']);
		                    $buchung->setKunde_strasse_nr2($bookingContact['strasseNr2']);
		                    $buchung->setKunde_land2($bookingContact['country2']);
		                    $buchung->setKunde_email2($bookingContact['email2']);
		                    $buchung->setKunde_telefon2($bookingContact['telefon2']);
		                    if (key_exists('abteilung2', $bookingContact)) {
			                    $buchung->setKunde_abteilung2($bookingContact['abteilung2']);
		                    }
		                    
		                    $buchung->setBuchung_status("rs_ib-almost_booked");
		                    $buchung->setHauptZahlungsart($paymentMethod);
		                    $buchung->setBuchungsdatum($rechnungsdatum);
		                    $modelAppBuchungTable->saveOrUpdateBuchungskopf($buchung);
		                    
		//                     RS_Indiebooking_Log_Controller::write_log("testestestest");
		//                     write_log($bookingContact['firstName2']);
		                    
		                    $bookingPostType        = array(
		                        'ID'            => $bookingPostId,
		                        'post_title'    => __('Booking from', 'indiebooking')." ".$buchung->getKunde_vorname()." ".$buchung->getKunde_name(),
		                        'post_type'     => 'rsappartment_buchung',
		                    	'post_content'  => __('Stays on overview / finalization page'), //__('Booking from', 'indiebooking')." ".$buchung->getKunde_vorname()." ".$buchung->getKunde_name(),
		                        'post_status'   => 'rs_ib-almost_booked',
		                    );
		                    $post_id                = wp_update_post($bookingPostType);
		                    
		                    $answer['CODE']         = 1;
		                    $answer['PERMALINK']    = get_permalink($bookingPostId);
		                } else {
		//                 	$test = "";
		//                 	foreach ($wrongFields as $field) {
		//                 		$test = $test." ".$field;
		//                 	}
		                    $exception              = new IndiebookingException(__("Invalid Contactdata", 'indiebooking'), 2);
		                    $exception->pushExtendedInformation(array('FIELDS' => $wrongFields));
		                    throw $exception;
		//                     $answer['CODE']         = 2;
		//                     $answer['FIELDS']       = $wrongFields;
		//                     $answer['MSG']          = __("Invalid Contactdata", 'indiebooking');
		                }
		                break;
		            case ($pageKz == 3): //Buchungsuebersicht -> Success-meldung
		                
		                break;
		            case ($pageKz == 4): //Success-meldung
		            
		                break;
		        }
		        $biggestKz = get_post_meta($bookingPostId, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, true);
		//         var_dump($pageKz);
		//         var_dump($biggestKz);
		        if (is_null($biggestKz)) {
		            $biggestKz  = 0;
		        }
		        if (intval($pageKz) > intval($biggestKz) && $toPageKz > -1) {
		            update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, $pageKz);
		        }
	        } else {
	        	$amznAnswer 				= apply_filters('rs_indiebooking_handle_amazon_payment', $bookingPostId);
	        	if ($amznAnswer['CODE'] == 1) {
		        	$answer['CODE']         = 1;
		        	$answer['AMZNPAY']		= 1;
		        	$answer['PERMALINK']    = get_permalink($bookingPostId);
		        	update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIGGEST_PAGEKZ, 3);
	        	} else {
	        		$answer['CODE']         = 0;
	        	}
	        }
	        update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, time());
	        
	//             $appartment                 = $appartmentTable->getAppartment($appartmentId);
	    
	            /**
	             * Ermittle die Aktionen, die bei dieser Buchung betrachtet werden muessen
	             */
	//             $aktionen                   = $appartment->getAktionen();
	//             $aktionenToCalc             = array();
	//             foreach ($aktionen as $aktion) {
	//                 $aktionOk       = true;
	//                 if ($aktion->getConditionType() == "1") { //min. nights
	//                     //                     $aktionOk   = false;
	//                     //TODO genauer Pruefen ob die Aktion gueltig ist.
	//                 }
	//                 if ($aktionOk) {
	//                     $validDates         = $aktion->getValidDates();
	//                     if (rs_ib_date_util::isDateOverlap($validDates, $dateFrom, $dateTo)) {
	//                         if ($aktion->getCombinable() == "off" && sizeof($aktionenToCalc) == 0) {
	//                             array_push($aktionenToCalc, $aktion);
	//                             break;
	//                         } elseif ($aktion->getCombinable() == "on") {
	//                             array_push($aktionenToCalc, $aktion);
	//                         }
	//                     }
	//                 }
	//             }
	//             $dates                      = array();
	//             $dates[0]                   = $dateFrom;
	//             $dates[1]                   = $dateTo;
	//             $bookingPost                = get_post($bookingId);
	//             $answer                     = array();
	//             if (!is_null($bookingPost)) {
	//                 $bookingPostType        = array(
	//                     'ID'            => $bookingId,
	//                     'post_title'    => 'Booking_for_appartment_'.$appartmentId,
	//                     'post_type'     => 'rsappartment_buchung',
	//                     'post_content'  => 'Booking_for_appartment_'.$appartmentId.' from '.$bookingContact['name'],
	//                     'post_status'   => 'rs_ib-almost_booked',
	//                 );
	//                 $post_id                = wp_update_post($bookingPostType);
	//                 $appartmentBuchung      = $modelAppBuchungTable->getAppartmentBuchung($post_id);
	//                 $appartmentBuchung->setStartDate($dateFrom);
	//                 $appartmentBuchung->setEndDate($dateTo);
	//                 $appartmentBuchung->setOptions($bookOptions);
	//                 $appartmentBuchung->setContact($bookingContact);
	//                 $appartmentBuchung->setAktionen($aktionenToCalc);
	//                 $post_id                = $modelAppBuchungTable->saveOrUpdateAppartmentBuchung($appartmentBuchung);
	                
	//                 $answer['CODE']         = 1;
	//                 $answer['BUCHUNGSID']   = $post_id;
	//                 $answer['PERMALINK']    = get_permalink($post_id);
	//             } else {
	//                 $answer['CODE'] = 0;
	//                 $answer['MSG']  = __("No blocked booking found. Maybe your run out of time", 'indiebooking');
	//             }
	//         }
        } else { //if ($postStatus != "rs_ib-canceled" && $postStatus != "rs_ib-out_of_time")
        	$answer['CODE']         = 0;
        	$answer['MSG']          = __("Your booking was canceled", 'indiebooking');
        	$answer['STOPBOOKING']	= 1;
        }
        return $answer;
    }
    
    /**
     * Erstellt einen Buchungssatz um die nuetigsten Informationen in der Post-Tabelle zu haben
     * und somit die Zuordnung von Post-Id zu Buchungskopf-Id zu knuepfen
     * @param unknown $appartmentId
     * @param unknown $dateFrom
     * @param unknown $dateTo
     * @param unknown $options
     */
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    public function createWPBookingPost(RS_IB_Model_Buchungskopf $buchungKopf) {
        global $RSBP_DATABASE;
        
        $bookOptions            = array();
        $answer                 = array();
        $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        $postTitle              = sprintf(
            /* TRANSLATORS: %s: Booking Number */
            __('Booking %s', 'indiebooking'),
            $buchungKopf->getBuchung_nr()
        );
        
//             /* TRANSLATORS: %s: Booking Number */
//         $postDescription        = sprintf(
//             __('Dummy for Booking Number %s', 'indiebooking'),
//             $buchungKopf->getBuchung_nr()
//         );
        
        $postDescription        = __('Booking was created');
        
        $post_id                = 0;
        $modelAppartmentBuchung = new RS_IB_Model_Appartment_Buchung();
        $modelAppartmentBuchung->setPost_title($postTitle);
        $modelAppartmentBuchung->setPost_type(RS_IB_Model_Appartment_Buchung::RS_POSTTYPE);
        $modelAppartmentBuchung->setPost_content($postDescription);
        /*
         * Update 13.02.2017 Carsten Schmitt
         * Es soll der Status aus dem Buchungskopf genommen werden, anstelle eines fest vorbelegtem.
         * Das hat den Grund, dass eine Buchung die aus Booking.com synchronisiert wird, direkt
         * zu beginn einen anderen Status hat.
         * Alter Zeile: $modelAppartmentBuchung->setPost_status('rs_ib-blocked');
         */
        if (!is_null($buchungKopf->getBuchung_status()) && $buchungKopf->getBuchung_status() != '') {
        	$modelAppartmentBuchung->setPost_status($buchungKopf->getBuchung_status());
        } else {
        	$modelAppartmentBuchung->setPost_status('rs_ib-blocked');
        }
        $modelAppartmentBuchung->setPostId($post_id);
        if ($buchungKopf->getBuchung_status() == "rs_ib-bookingcom") {
        	if ($buchungKopf->getBuchungsdatum() instanceof DateTime) {
        		$startTime 		= $buchungKopf->getBuchungsdatum();
        	} else {
        		$startTime		=  new DateTime($buchungKopf->getBuchungsdatum());
        	}
        	$startTime			= $startTime->getTimestamp();
        	$modelAppartmentBuchung->setStart_time($startTime);
        }
        $modelAppartmentBuchung->setStartDate($buchungKopf->getBuchung_von()); //add_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_ZEITRAUM, $dates);
        $modelAppartmentBuchung->setEndDate($buchungKopf->getBuchung_bis());
        $modelAppartmentBuchung->setBuchungKopfId($buchungKopf->getBuchung_nr());
        $post_id                = $buchungTable->saveOrUpdateAppartmentBuchung($modelAppartmentBuchung);
        
        /*
         * Update 13.02.2017 Carsten Schmitt
         * Wenn die Buchung aus bookingcom erstellt wurde, soll kein schedule-event angestossen werden,
         * da die Buchung darueber nicht geschlossen werden soll.
         *
         * Update 21.08.2018 Carsten Schmitt
         * Wird die Buchung aus der Administration gestartet, soll diese nicht nach dem Timeout abgebrochen werden.
         * Ferner wird diese Buchung aber abgebrochen, wenn der Heartbeat länger als 150 Sekunden her ist.
         *
        */
        if ($modelAppartmentBuchung->getPost_status() != 'rs_ib-bookingcom' && $buchungKopf->getAdminKz() == 0) {
	        $optionName             = "rs_indiebooking_settings_time_to_book";
	        if( !get_option( $optionName ) ) {
	            $time = 15;
	        } else {
	            $time = get_option( $optionName );
	        }
	        $time = $time * 60;
	        wp_schedule_single_event( time() + $time, 'rs_ib_check_booking_event', array( $post_id ) );
        }
        return $post_id;
    }
    
    /**
     * Erstellt den Buchungskopf fuer eine neue Buchung
     */
    /* @var $kunde RS_IB_Customer */
    /* @var $modelAppBuchungTable RS_IB_Table_Appartment_Buchung */
    public function createBookingHeader($bookingFrom, $bookingTo, $bookingStatus, $kunde, $bookingNr = 0, $ignoreminimumperiod = false, $specialAdminParams = array()) {
        global $RSBP_DATABASE;
        
        $bookingType		= 1; //1 = normal | 2 = nur option
        $allowPastBooking 	= false;
        $changeBillDate		= false;
        if (isset($specialAdminParams)) {
        	if (key_exists('allowPastBooking', $specialAdminParams)) {
        		$allowPastBooking	= $specialAdminParams['allowPastBooking'];
        	}
        	if (key_exists('changeBillDate', $specialAdminParams)) {
        		$changeBillDate		= $specialAdminParams['changeBillDate'];
        	}
        	if (key_exists('billDate', $specialAdminParams)) {
        		$billDate			= $specialAdminParams['billDate'];
        	}
        	if (key_exists('bookOptionOnly', $specialAdminParams)) {
        		$bookOptionOnly		= $specialAdminParams['bookOptionOnly'];
        		if ($bookOptionOnly == 'off') {
        			$bookOptionOnly	= false;
        		} else {
        			$bookingType	= 2;
        		}
        	}
        }
        
        $rechnungsdatum			= new DateTime("now");
        if (($changeBillDate == 1 || $changeBillDate === 'on') && !is_null($billDate) && $billDate != '') {
        	$rechnungsdatum   	= DateTime::createFromFormat("d.m.Y", $billDate);
        	$changeBillDate		= 1;
        } else {
        	$changeBillDate		= 0;
        }
        $modelAppBuchungTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungKopf            = new RS_IB_Model_Buchungskopf();
        
        $bookingFrom			= rs_ib_date_util::convertDateValueToDateTime($bookingFrom);
        $bookingTo				= rs_ib_date_util::convertDateValueToDateTime($bookingTo);
        
//         $numberOfNights         = date_diff(DateTime::createFromFormat("d.m.Y", $bookingFrom),
//                                                 DateTime::createFromFormat("d.m.Y", $bookingTo), false);
        
        $numberOfNights         = date_diff($bookingFrom, $bookingTo, false);
        
        $numberOfNights         = intval($numberOfNights->format('%a')); //%R
        $buchungKopf->setBuchung_nr($bookingNr);
        $buchungKopf->setBuchung_von($bookingFrom);
        $buchungKopf->setBuchung_bis($bookingTo);
        $buchungKopf->setBuchung_status($bookingStatus);
        $buchungKopf->setAnzahl_naechte($numberOfNights);
        $buchungKopf->setBuchungsdatum($rechnungsdatum);
        $buchungKopf->setRechnungsdatum($rechnungsdatum);
        $buchungKopf->setIgnoreMinimumPeriod($ignoreminimumperiod);
        $buchungKopf->setAllowPastBooking($allowPastBooking);
        $buchungKopf->setBookingType($bookingType);
        $buchungKopf->setChangeBillDate($changeBillDate);
        if (!is_null($kunde)) {
            $buchungKopf->setKunde_firma($kunde->getFirma());
            $buchungKopf->setKunde_anrede($kunde->getAnrede());
            $buchungKopf->setKunde_title($kunde->getTitel());
            $buchungKopf->setKunde_name($kunde->getLastName());
            $buchungKopf->setKunde_vorname($kunde->getFirstName());
            $buchungKopf->setKunde_strasse($kunde->getStreet());
            $buchungKopf->setKunde_plz($kunde->getZipCode());
            $buchungKopf->setKunde_ort($kunde->getLocation());
            $buchungKopf->setKunde_email($kunde->getEmail());
            $buchungKopf->setKunde_telefon($kunde->getTelefon());
            
            $buchungKopf->setKunde_firma2($kunde->getFirma2());
            $buchungKopf->setKunde_anrede2($kunde->getAnrede2());
            $buchungKopf->setKunde_title2($kunde->getTitel2());
            $buchungKopf->setKunde_name2($kunde->getLastName2());
            $buchungKopf->setKunde_vorname2($kunde->getFirstName2());
            $buchungKopf->setKunde_strasse2($kunde->getStreet2());
            $buchungKopf->setKunde_plz2($kunde->getZipCode2());
            $buchungKopf->setKunde_ort2($kunde->getLocation2());
            $buchungKopf->setKunde_email2($kunde->getEmail2());
            $buchungKopf->setKunde_telefon2($kunde->getTelefon2());
        } else {
            $buchungKopf->setKunde_firma("dummy");
            $buchungKopf->setKunde_anrede("");
            $buchungKopf->setKunde_title("");
            $buchungKopf->setKunde_name("dummy");
            $buchungKopf->setKunde_vorname("dummy");
            $buchungKopf->setKunde_strasse("dummy");
            $buchungKopf->setKunde_plz("dummy");
            $buchungKopf->setKunde_ort("dummy");
            $buchungKopf->setKunde_email("dummy");
            $buchungKopf->setKunde_telefon("dummy");
            
            $buchungKopf->setKunde_firma2("dummy");
            $buchungKopf->setKunde_anrede2("dummy");
            $buchungKopf->setKunde_title2("");
            $buchungKopf->setKunde_name2("dummy");
            $buchungKopf->setKunde_vorname2("dummy");
            $buchungKopf->setKunde_strasse2("dummy");
            $buchungKopf->setKunde_plz2("dummy");
            $buchungKopf->setKunde_ort2("dummy");
            $buchungKopf->setKunde_email2("dummy");
            $buchungKopf->setKunde_telefon2("dummy");
        }
        
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."createBookingHeader - ".$bookingNr);
        
        $buchungsNr             = $modelAppBuchungTable->saveOrUpdateBuchungskopf($buchungKopf);
        $buchungKopf->setBuchung_nr($buchungsNr);
        return $buchungKopf;
    }
    
    /* @var RS_IB_Model_Appartment $appartment */
    public function createBookingPartHeader($bookingNr, $appartment, $bookingFrom, $bookingTo, $anzPersonen = 1) {
        global $RSBP_DATABASE;
        
        $modelAppBuchungTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $teilHeader             = new RS_IB_Model_Teilbuchungskopf();
        $teilHeader->setBuchung_nr($bookingNr);
        $teilHeader->setAppartment_id($appartment->getPostId());
        $teilHeader->setAppartment_name($appartment->getPost_title());
        $teilHeader->setAppartment_qm($appartment->getQuadratmeter());
        $teilHeader->setTeilbuchung_von($bookingFrom);
        $teilHeader->setTeilbuchung_bis($bookingTo);
        $teilHeader->setAnzahlPersonen($anzPersonen);
        
        $teilBuchungsNr = $modelAppBuchungTable->saveOrUpdateTeilbuchungskopf($teilHeader);
        
        $teilHeader->setTeilbuchung_id($teilBuchungsNr);
        return $teilHeader;
    }
    
    public function createBookingPosition($buchungNr, $teilHeadId, $typ, $name, $preis_von, $preis_bis,
            $ep_brutto, $calcTyp, $mwstProz, $mwstId, $rabattKz = 0, $optionId = 0, $stornoKz = "true", $basispreis = 0) {
        
        global $RSBP_DATABASE;
        if ($stornoKz == "true") {
            $stornoKz = 1;
        } else {
            $stornoKz = 0;
        }
        $ep_brutto              = str_replace(",", ".", $ep_brutto);
        $modelAppBuchungTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $preis_von              = rs_ib_date_util::convertDateValueToDateTime($preis_von);
        $preis_bis              = rs_ib_date_util::convertDateValueToDateTime($preis_bis);
//$anzahl_naechte         = date_diff(DateTime::createFromFormat("d.m.Y", $preis_von), DateTime::createFromFormat("d.m.Y", $preis_bis), false);
        $anzahl_naechte         = date_diff($preis_von, $preis_bis, false);
        $anzahl_naechte         = intval($anzahl_naechte->format('%R%a'));
        if ($anzahl_naechte > 0) {
            $buchungsposition   = new RS_IB_Model_Buchungposition();
            
            $buchungsposition->setBuchung_nr($buchungNr);
            $buchungsposition->setTeilbuchung_id($teilHeadId);
            $buchungsposition->setBezeichnung($name);
            $buchungsposition->setPosition_typ($typ);
            $buchungsposition->setPreis_von($preis_von);
            $buchungsposition->setPreis_bis($preis_bis);
            $buchungsposition->setAnzahl_naechte($anzahl_naechte);
            $buchungsposition->setEinzelpreis($ep_brutto);
            $buchungsposition->setBasispreis($basispreis);
            $buchungsposition->setBerechnung_type($calcTyp);
            $buchungsposition->setMwst_prozent($mwstProz);
            $buchungsposition->setMwstTermId($mwstId);
            $buchungsposition->setData_id($optionId);
            $buchungsposition->setFullStorno($stornoKz);
            
            $positionNr         = $modelAppBuchungTable->saveOrUpdateBuchungsposition($buchungsposition);
            $buchungsposition->setPosition_id($positionNr);
            return $buchungsposition;
        }
    }
    
    
    /* @var $position RS_IB_Model_Buchungposition */
    /* @var $teilHeader RS_IB_Model_Teilbuchungskopf */
    /* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    /* @var $aktion RS_IB_Model_Appartmentaktion */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    /* @var $appartmentTable RS_IB_Table_Appartment */
    /* @var $appartment RS_IB_Model_Appartment */
    private function checkAndSaveCampaigns($buchungsKopf) {
        global $RSBP_DATABASE;
    
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."checkAndSaveCampaigns");
        
        $rabattArt                  = 1; //1 = Aktion || 2 = Coupon
        
        $rabattTable                = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $buchungNights              = $buchungsKopf->getAnzahl_naechte();
        foreach ($buchungsKopf->getTeilkoepfe() as $teilHeader) {
            $appartment             = $appartmentTable->getAppartment($teilHeader->getAppartment_id());
            
            $numberOfNights         = $teilHeader->getNumberOfNights();
            $teilHeadVon            = $teilHeader->getTeilbuchung_von();
            $teilHeadBis            = $teilHeader->getTeilbuchung_bis();
            
            if (!$teilHeadVon instanceOf DateTime) {
                $teilHeadVon        = new DateTime($teilHeadVon);
            }
            if (!$teilHeadBis instanceOf DateTime) {
                $teilHeadBis        = new DateTime($teilHeadBis);
            }
            
            $appartmentAktionen     = $appartment->getAktionen();
            foreach ($appartmentAktionen as $aktion) {
                $conditionOk        = false;
                $validAktionDates   = $aktion->getValidDates();
                $calcType           = $aktion->getCalcType(); //1 = apartment | 2 = option | 3 = total
                $conditionType      = $aktion->getConditionType(); // 0 = keine | 1 = min. Tage | 2 = min. Summe
                $conditionValue     = $aktion->getConditionValue();
                $aktionId           = $aktion->getTermId();
                $ausschreibenKz     = $aktion->getExpelPriceKz();
                $plusminusKz        = $aktion->getAktionType();
                
                if ($ausschreibenKz == "on") {
                    $ausschreibenKz = 1;
                } else {
                    $ausschreibenKz = 0;
                }
                
                $conditionValue     = str_replace(",", ".", $conditionValue);
                $conditionValue     = floatval($conditionValue);
                
                if ($conditionType == 0) {
                    $conditionOk                    = true;
                } elseif ($conditionType == 1) {
                    if ($calcType == 1 && ($numberOfNights >= $conditionValue)) {
                        $conditionOk                = true;
                    } elseif ($calcType == 3 && (!is_null($buchungNights))) {
                        if ($buchungNights >= $conditionValue) {
                            $conditionOk            = true;
                        }
                    }
                } elseif ($conditionType == 2) {
                    if ($buchungsKopf->getCalculatedPrice() >= $conditionValue) {
                        $conditionOk                = true;
                    }
                }
//                 $logPreis = $buchungsKopf->getCalculatedPrice();
//                 RS_Indiebooking_Log_Controller::write_log("BedigungsTyp: $conditionType - Bedingungswert: $conditionValue - Preis: $logPreis - Nacht: $numberOfNights");
                if ($validAktionDates && $conditionOk) {
                    foreach ($validAktionDates as $validDate) {
                        if ($validDate["active"] == "on") {
                            $aktionPosition         = array();
                            $completeAktion         = false;
                            $calcAktionFrom         = 0;
                            $calcAktionTo           = 0;
                            $aktionFrom             = DateTime::createFromFormat("d.m.Y", $validDate["from"]);
                            $aktionTo               = DateTime::createFromFormat("d.m.Y", $validDate["to"]);
                            $now                    = new DateTime("now");
                            /*
                             * Update 13.06.2016
                             * Bei der Pruefung der Aktion spielt das aktuelle Datum keine Rolle,
                             * sondern der gebuchte Zeitraum
                             */
//                             if (($aktionFrom >= $teilHeadVon && $aktionFrom <= $teilHeadBis)
//                                 || ($aktionTo >= $teilHeadVon && $aktionTo <= $teilHeadBis)) {
                            if (($teilHeadVon >= $aktionFrom && $teilHeadVon <= $aktionTo) ||
                                ($teilHeadBis >= $aktionFrom && $teilHeadBis <= $aktionTo)) {
//                             if ($aktionFrom <= $now && $aktionTo >= $now) {
                                if ($aktionFrom <= $teilHeadVon && $aktionTo >= $teilHeadBis) {
                                    //Gebuchter Zeitraum komplett in der Aktion
                                    $calcAktionFrom     = $teilHeadVon;
                                    $calcAktionTo       = $teilHeadBis;
                                    $completeAktion     = true;
                                }
                                elseif ($aktionFrom <= $teilHeadVon) {
                                    //Aktionszeitraum von in einem Preiszeitraum
                                    $aktionTo->add(new DateInterval('P1D'));
                                    $calcAktionFrom     = $teilHeadVon;
                                    $calcAktionTo       = $aktionTo;
                                }
                                elseif ($aktionTo >= $teilHeadBis) {
                                    //Aktionszeitraum bis in einem Preiszeitraum
                                    $calcAktionFrom     = $aktionFrom;
                                    $calcAktionTo       = $teilHeadBis;
                                }
                                elseif ($aktionFrom > $teilHeadVon && $aktionTo < $teilHeadBis) {
                                    //Aktionszeitraum komplett in Buchungszeitraum
                                    $aktionTo->add(new DateInterval('P1D'));
                                    $calcAktionFrom     = $aktionFrom;
                                    $calcAktionTo       = $aktionTo;
                                }
                                $actionNumberOfNights   = date_diff($calcAktionTo, $calcAktionFrom);
                                $actionNumberOfNights   = intval($actionNumberOfNights->format('%a'));
                                if ($actionNumberOfNights > 0) {
                                    foreach ($teilHeader->getPositionen() as $position) {
                                        if ($position->getPosition_typ() == "appartment_option" ||
                                            $position->getPosition_typ() == "appartment_price") {
                                            
                                            $saveAktion         = false;
                                            $buchungRabatt      = new RS_IB_Model_BuchungRabatt();
                                            $buchungRabatt->setBuchung_nr($teilHeader->getBuchung_nr());
                                            $buchungRabatt->setRabatt_art($rabattArt);
                                            $buchungRabatt->setRabatt_typ($aktion->getValueType()); //1 = total | 2 = %
                                            $buchungRabatt->setGueltig_von($calcAktionFrom);
                                            $buchungRabatt->setGueltig_bis($calcAktionTo);
                                            $buchungRabatt->setBezeichnung($aktion->getName());
                                            $buchungRabatt->setRabatt_wert($aktion->getPreis());
                                            $buchungRabatt->setBerechnung_art($calcType);
                                            $buchungRabatt->setRabatt_term_id($aktion->getTermId());
                                            $buchungRabatt->setPlus_minus_kz($plusminusKz);
                                            $buchungRabatt->setRabatt_ausschreiben_kz($ausschreibenKz);
                                            switch ($calcType) {
                                                case 1: //apartment
                                                    if ($position->getPosition_typ() != "appartment_option") {
                                                        $buchungRabatt->setTeilbuchung_nr($teilHeader->getTeilbuchung_id());
                                                        $buchungRabatt->setPosition_nr($position->getPosition_id());
                                                        $saveAktion     = true;
                                                    }
                                                    break;
                                                case 2: //option
                                                    if ($position->getPosition_typ() == "appartment_option") {
                                                        $buchungRabatt->setTeilbuchung_nr($teilHeader->getTeilbuchung_id());
                                                        $buchungRabatt->setPosition_nr($position->getPosition_id());
                                                        $saveAktion     = true;
                                                    }
                                                    break;
                                                case 3: //total
                                                    if ($position->getPosition_typ() != "appartment_option") {
                                                        $buchungRabatt->setTeilbuchung_nr(null);
                                                        $buchungRabatt->setPosition_nr(null);
                                                        $saveAktion     = true;
                                                    }
                                                    break;
                                                case 4: //ppN
                                                	if ($position->getPosition_typ() != "appartment_option") {
                                                		$buchungRabatt->setTeilbuchung_nr($teilHeader->getTeilbuchung_id());
                                                		$buchungRabatt->setPosition_nr($position->getPosition_id());
                                                		$saveAktion     = true;
                                                	}
                                                	break;
                                            }
                                            if ($saveAktion) {
                                                $rabattTable->saveOrUpdateBuchungRabatt($buchungRabatt);
                                            }
                                       }
                                    }
                                }
                            }
                            if ($completeAktion) {
                                break;
                            }
                        }
                    }
                } else {
                    /*
                     * Die Aktion wird aus der aktuellen Buchung geloescht, weil die Bedingungen
                     * nicht (mehr) passen.
                     * Das Loeschen wird auch ausgefuehrt, wenn die Aktion zuvor garnicht der Buchung
                     * zugeordnet wurde, somit werden dann also 0 Zeilen geloescht
                    */
                    $buchungRabatt      = new RS_IB_Model_BuchungRabatt();
                    $buchungRabatt->setBuchung_nr($teilHeader->getBuchung_nr());
                    $buchungRabatt->setRabatt_term_id($aktion->getTermId());
                    $buchungRabatt->setBezeichnung($aktion->getName());
                    $rabattTable->deleteBuchungRabattByTermId($buchungRabatt);
                }
            }
        }
    }
    
    
    public function loadOptionsAndCreatePosition($appartmentId, $teilHeader, $appOptions) {
       //do Nothing
    }
    
    /* @var RS_IB_Table_Appartment_Buchung $buchungTable */
    public function loadCurrentBookingPrices($bookingPostId, $isAdminRequest = false) {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $bookingTbl                 = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungRabattTbl           = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        
        $buchung                    = $buchungTable->getAppartmentBuchung($bookingPostId);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        if ($buchungKopf->getBuchung_status() != "rs_ib-almost_booked" || $isAdminRequest) {
        	$bookingByCategorieKz			= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
        	$bookingByCategorieKz			= ($bookingByCategorieKz == "on");
        //         $teilKoepfe                 = $buchungKopf->getTeilkoepfe();
        //         $rabatte                    = $buchungRabattTbl->loadBuchungRabatt($buchungKopf->getBuchung_nr());
            $args = array (
                'buchungKopf'      => $buchungKopf,
            	'showCategoryAsName' => $bookingByCategorieKz,
                //             'teilKoepfe'    => $teilKoepfe,
            //             'rabatte'       => $rabatte,
            //             'postId'        => $postId,
            );
            cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_detail_payment_info_small.php', $args);
        }
    }
    
    
    
    public function getAppartmentPrices($appartmentId) {
        $teilHeader     = new RS_IB_Model_Teilbuchungskopf();
        $today          = new DateTime("now");
        $year           = $today->format("Y");
//         $from           = new DateTime($year."-01-01");
//         $to             = new DateTime(($year+3)."-12-30");
        
        /*
         * Update Carsten Schmitt 13.06.17
         * from wird auf today gesetzt und
         * to wird nun anhand der max-Datumsangabe ermittelt.
         */
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $from           = new DateTime("now");
        $curDay			= $from->format("d");
        $curMonth		= $from->format("m");
        $to             = new DateTime(($year+$futureAvailabilityYear)."-".$curMonth."-".$curDay);
        
        $teilHeader->setBuchung_nr(0);
        $teilHeader->setTeilbuchung_id(0);
        $teilHeader->setTeilbuchung_von($from);
        $teilHeader->setTeilbuchung_bis($to);
        $datePrices     = $this->loadAppartmentPricesAndCreatePosition($appartmentId, $teilHeader, null, true, false);
        
//         $appartmentTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//         $apartment          = $appartmentTable->getAppartment($appartmentId);
        return $datePrices;
    }
    
    /* @var $teilHeader RS_IB_Model_Teilbuchungskopf */
    /* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    /* @var $appartmentAktionTable RS_IB_Table_Appartmentaktion */
    /* @var $saisonPriceTable RS_IB_Table_Appartment_Saison */
    /* @var $degressionTable RS_IB_Table_ApartmentDegression */
    private function loadAppartmentPricesAndCreatePosition($appartmentId, $teilHeader, $buchungsKopf = null,
    														$returnPrices = false, $calcDegression = true) {
        global $RSBP_DATABASE;
    
        $appartmentTable                    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $mwstTable                          = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $rabattTable            			= $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        $allMwst                            = $mwstTable->getAllMwsts();
        
        $returnPriceDates					= array();
        $saisonPriceArr                     = array();
        $buchungNr                          = $teilHeader->getBuchung_nr();
        $teilHeadId                         = $teilHeader->getTeilbuchung_id();
        $dateFrom                           = $teilHeader->getTeilbuchung_von();
        $dateTo                             = $teilHeader->getTeilbuchung_bis();
        $appartment                         = $appartmentTable->getAppartment($appartmentId);
        $appartmentName                     = $appartment->getPost_title();
        $defaultPrice                       = $appartment->getPreis();
        //**Erstelle Teilbuchungspositionen**
        $positionType                       = "appartment_price ";
        $mwstId                             = $appartment->getMwstId();
        $aufschlagArray 					= array();
        $tax								= 0;
        foreach ($allMwst as $mwst) {
            if ($mwst->getMwstId() == $mwstId) {
                $tax                    	= ($mwst->getMwstValue() / 100);
                break;
            }
        }
        $bookedFrom                         = rs_ib_date_util::convertDateValueToDateTime($dateFrom);//DateTime::createFromFormat("d.m.Y", $dateFrom);
        $bookedTo                           = rs_ib_date_util::convertDateValueToDateTime($dateTo);//DateTime::createFromFormat("d.m.Y", $dateTo);
        $numberOfNights         			= date_diff($bookedFrom, $bookedTo, false);
        $numberOfNights         			= intval($numberOfNights->format('%a'));
        $teilBuchungAnzNaechte				= $numberOfNights;
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            $saisonPriceTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Saison::RS_TABLE);
            $saisonPriceArr                 = $saisonPriceTable->loadApartmentSaison($appartmentId, false);
        }
        
        $apartmentPrices                    = rs_ib_date_util::getSaisonPrices($bookedFrom, $bookedTo, $saisonPriceArr, $defaultPrice);
        $returnIndex                        = 0;
        $sumOfApartmentPrices				= 0;
        foreach ($apartmentPrices as $apartmentPrice) {
            $returnPriceDates[$returnIndex]["from"]             = $apartmentPrice['from']->format("d.m.Y");
            $returnPriceDates[$returnIndex]["to"]               = $apartmentPrice['to']->format("d.m.Y");
            $returnPriceDates[$returnIndex]["price"]            = $apartmentPrice['price'];
    
            $returnPriceDates[$returnIndex]["buchungNr"]        = $buchungNr;
            $returnPriceDates[$returnIndex]["teilHeadId"]       = $teilHeadId;
            $returnPriceDates[$returnIndex]["positionType"]     = $positionType;
            $returnPriceDates[$returnIndex]["appartmentName"]   = $appartmentName;
            $returnPriceDates[$returnIndex]["berechnung"]       = RS_IB_Model_Buchungposition::RS_BERECHNUNG_TYP_PREISPRONACHT;
            $returnPriceDates[$returnIndex]["tax"]              = $tax;
            $returnPriceDates[$returnIndex]["mwstid"]           = $mwstId;
            
            $sumOfApartmentPrices = $sumOfApartmentPrices + $apartmentPrice['price'];
            
            $returnIndex++;
        }
        
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php') && $calcDegression) {
        	$extra_chargeArray					= $appartment->getExtraCharge();
        	if (!is_null($extra_chargeArray) && sizeof($extra_chargeArray) > 0) {
        		foreach ($extra_chargeArray as $extra_charge) {
        			$extraCharge 				= unserialize($extra_charge);
        			$aufschlagArray				= $extraCharge[0];
        			break;
        		}
        	}
        	$degressionTable		= $RSBP_DATABASE->getTable(RS_IB_Model_ApartmentDegression::RS_TABLE);
        	$degressions			= $degressionTable->loadApartmentDegression($appartment->getPostId());
        	$anzahl_naechte         = date_diff($bookedFrom, $bookedTo, false);
        	$anzahl_naechte         = intval($anzahl_naechte->format('%R%a'));
        	
        	$conditionFound			= false;
        	$reduzierung			= 0;
        	$reduzierungTyp			= 0;
        	foreach ($degressions as $degression) {
        		$typ 				= $degression->getConditionTyp(); //1 = days | 2 = weeks | 3 = months
        		$count 				= $degression->getConditionValue();
        		$multipli			= 1;
        		if ($typ == 3) {
        			$multipli 		= 28;
        		} else if ($typ == 2) {
        			$multipli		= 7;
        		}
        		$condition			= $count * $multipli;
        		if ($anzahl_naechte >= $condition) {
        			$conditionFound	= true;
        			$reduzierung	= $degression->getDegressionswert();
        			$reduzierungTyp	= $degression->getCalcTyp(); //1 = absolut | 2 = %
        		}
        		
        		if ($conditionFound) {
        			break;
        		}
        	}
        	if ($conditionFound) {
        		$percent = 0;
        		if ($reduzierungTyp == 1) {
// 					$percent	= (100 / $sumOfApartmentPrices) * $reduzierung;
					foreach ($returnPriceDates as $key => $priceDates) {
						$returnPriceDates[$key]["degression_wert"] 	= $reduzierung;
						$returnPriceDates[$key]["degression_typ"] 	= $reduzierungTyp;
// 						$returnPriceDates[$key]["price"] 		= $priceDates["price"] - $reduzierung;
						$returnPriceDates[$key]["price"] = $priceDates["price"];
					}
        		} else {
        			$percent	= $reduzierung;
	        		$percent	= (( 100 - $percent ) / 100);
	        		foreach ($returnPriceDates as $key => $priceDates) {
	        			$returnPriceDates[$key]["degression_wert"] 	= $reduzierung;
	        			$returnPriceDates[$key]["degression_typ"] 	= $reduzierungTyp;
// 	        			$returnPriceDates[$key]["price"] = $priceDates["price"] * $percent;
	        			$returnPriceDates[$key]["price"] = $priceDates["price"];
	        		}
        		}
        	}
        }
        $returnPriceDates           = array_reverse($returnPriceDates);
        if (!$returnPrices) {
            foreach ($returnPriceDates as $priceDates) {
                $position           = $this->createBookingPosition(
                    $priceDates["buchungNr"],
                    $priceDates["teilHeadId"],
                    $priceDates["positionType"],
                    $priceDates["appartmentName"],
                    $priceDates["from"],
                    $priceDates["to"],
                    $priceDates["price"],
                    $priceDates["berechnung"],
                    $priceDates["tax"],
                    $priceDates["mwstid"]
                );
                if (!is_null($position)) {
	                if (key_exists('degression_wert', $priceDates)) {
	                	$buchungRabatt      = new RS_IB_Model_BuchungRabatt();
	                	$buchungRabatt->setBuchung_nr($priceDates["buchungNr"]);
	                	$buchungRabatt->setRabatt_art(3);//1 = Aktion / 2 = Coupon / 3 = degression
	                	$buchungRabatt->setRabatt_typ($priceDates["degression_typ"]); //1 = total | 2 = %
	                	$buchungRabatt->setGueltig_von($priceDates["from"]);
	                	$buchungRabatt->setGueltig_bis($priceDates["to"]);
	                	$buchungRabatt->setBezeichnung("degression");
	                	$wert = $priceDates["degression_wert"];
	//                 	$wert = $wert * $position->getAnzahl_naechte();
	                	$buchungRabatt->setRabatt_wert($wert);
	                	$buchungRabatt->setBerechnung_art(4); //1 = apartment | 2 = option | 3 = total | 4 = preis position
	                	$buchungRabatt->setRabatt_term_id(0);
	                	$buchungRabatt->setPlus_minus_kz(1); // 1 = rabatt
	                	$buchungRabatt->setRabatt_ausschreiben_kz(1);
	                	$buchungRabatt->setTeilbuchung_nr($priceDates["teilHeadId"]);
	                	$buchungRabatt->setPosition_nr($position->getPosition_id());
	                	
	                	$rabattTable->saveOrUpdateBuchungRabatt($buchungRabatt);
	                }
	                
	                if (!is_null($aufschlagArray) && sizeof($aufschlagArray) > 0) {
	                	/*
	                	 * Carsten 27.12.2017
	                	 * Der Aufschlag bezieht sich nicht auf die Pruefung der Anzahl Naechte einer Position.
	                	 * Sonst kann es dazu kommen, dass eine Saisonposition einen Aufschlag ausloest, der
	                	 * garnicht benoetigt wird, da die Teilbuchung des Apartments ueber der Bedingung ist.
	                	 * Demnach wird anzNaechte nun mit teilBuchungAnzNaechte gefuellt.
	                	 */
	                	$anzNaechte 			= $teilBuchungAnzNaechte; //$position->getAnzahl_naechte();
	                	$extra_chargeCount		= $aufschlagArray['count'];
	                	$extra_chargeCondition	= $aufschlagArray['range']; //1 = Tage | 2 = Wochen | 3 = Monate
	                	if ($extra_chargeCondition == 2) {
	                		$extra_chargeCount	= $extra_chargeCount * 7;
	                	}
	                	if ($extra_chargeCondition == 3) {
	                		//TODO muss man noch iwie abaendern, dass nicht allgemein 30 genommen werden.
	                		$extra_chargeCount	= $extra_chargeCount * 30;
	                	}
	                	if (intval($extra_chargeCount) >= $anzNaechte) {
		                	$buchungRabatt      = new RS_IB_Model_BuchungRabatt();
		                	$buchungRabatt->setBuchung_nr($priceDates["buchungNr"]);
// 		                	$buchungRabatt->setRabatt_art(1);//1 = Aktion / 2 = Coupon / 3 = degression
		                	$buchungRabatt->setRabatt_art(4);//1 = Aktion / 2 = Coupon / 3 = degression / 4 = Aufschlag??
		                	$buchungRabatt->setRabatt_typ($aufschlagArray['extraChargeTyp']); //1 = total | 2 = %
		                	$buchungRabatt->setGueltig_von($priceDates["from"]);
		                	$buchungRabatt->setGueltig_bis($priceDates["to"]);
		                	$buchungRabatt->setBezeichnung("Position Extra Charge");
		                	$wert = $aufschlagArray["extraCharge"];
		                	//                 	$wert = $wert * $position->getAnzahl_naechte();
		                	$buchungRabatt->setRabatt_wert($wert);
		                	$buchungRabatt->setBerechnung_art(4); //1 = apartment | 2 = option | 3 = total | 4 = preis position
		                	$buchungRabatt->setRabatt_term_id(0);
		                	$buchungRabatt->setPlus_minus_kz(2); // 1 = rabatt || 2 = aufschlag
		                	$buchungRabatt->setRabatt_ausschreiben_kz(0);
		                	$buchungRabatt->setTeilbuchung_nr($priceDates["teilHeadId"]);
		                	$buchungRabatt->setPosition_nr($position->getPosition_id());
	
		                	$rabattTable->saveOrUpdateBuchungRabatt($buchungRabatt);
	                	}
	                }
                }
            }
        } else {
            return $returnPriceDates;
        }
    }
    
    
    /* @var $modelAppBuchungTable RS_IB_Table_Appartment_Buchung */
    /* @var $bookingTbl RS_IB_Table_Buchungskopf */
//     public function getAllBookingCustomers() {
//         global $RSBP_DATABASE;
//         $bookingTbl         = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
//         $buchungKopfTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
//         $bookingTbl->getAllBookingCustomers();
//     }
    /*
	            $specialAdminParams		= array(
	            	'allowPastBooking'	=> $allowPastBooking,
	            	'changeBillDate'	=> $changeBillDate,
	            	'billDate'			=> $billDate,
	            );$allowPastBooking
     */
    
    /* @var $teilbuchungsKopfTbl RS_IB_Table_Teilbuchungskopf */
    public function getBestApartmentIdByCategorie($apId, $apdateFrom, $apdateTo, $bookedApartmentIds = array(), $ignoreminimumperiod = false, $allowPastBooking = false) {
    	global $RSBP_DATABASE;
    	
    	$possibleApartmentIds 	= array();
    	$categoryIDs 			= array();
    	$bestApartmentId		= -1;
    	$categoryTbl 			= $RSBP_DATABASE->getTable(RS_IB_Model_Appartmentkategorie::RS_TABLE);
    	$buchungsKopfTbl 		= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    	$teilbuchungsKopfTbl 	= $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    	$all_post_terms 		= get_the_terms( $apId, RS_IB_Model_Appartmentkategorie::RS_TAXONOMY );
    	if ($all_post_terms) {
    		foreach ($all_post_terms as $post_term) {
    			$post_term_id           = $post_term->term_id;
    			array_push($categoryIDs, $post_term_id);
    		}
    	}
    	foreach ($categoryIDs as $categoryID) {
    		$posts 			= $categoryTbl->getCategoryApartments(array($categoryID));
    		foreach ($posts as $categoryApartmentId) {
    			/*
    			 * Update Carsten Schmitt 08.10.2018
    			 * Wurde die ApartmentId bereits beruecksichtigt, darf diese nicht erneut
    			 * geprueft werden.
    			 */
    			if (!in_array($categoryApartmentId, $bookedApartmentIds)) {
    				$isBookable				= false;
    				$allDatesOk				= false;
    				$arrivalDaysOk			= false;
    				$isBookable        		= $this->checkApartmentBookable($categoryApartmentId);
    				if ($isBookable) {
    					$allDatesOk         = $this->checkDateRange($categoryApartmentId, $apdateFrom, $apdateTo, 0, $ignoreminimumperiod, $allowPastBooking);
    					if ($allDatesOk) {
    						$arrivalDaysOk  = $this->checkApartmentArrivalDays($categoryApartmentId, $apdateFrom, $apdateTo);
    					}
    				}
    				if ($arrivalDaysOk) {
    					array_push($possibleApartmentIds, $categoryApartmentId);
    				}
    			}
    		}
    	}
    	if (sizeof($possibleApartmentIds) > 1) {
    		/*
    		 * an dieser Stelle muessen nun die letzten Buchungen (gemessen am Zeitraum)
    		 * betrachtet werden und es muss geprueft werden, bei welchem Apartment die kleinste
    		 * Differenz zu dem Buchungsstart dieser Buchung ist.
    		 */
    		$smalestDifferenz = array(
    			'apId' => 0,
    			'diff' => -1,
    		);
    		foreach ($possibleApartmentIds as $apaId) {
    			$lastBooking = $teilbuchungsKopfTbl->loadLastApartmentBooking($apaId, $apdateFrom);
    			/* @var $teilbuchungskopf RS_IB_Model_Teilbuchungskopf */
    			foreach ($lastBooking as $teilbuchungskopf) {
    				//kann eigentlich nur eine sein
    				$buchungBis = $teilbuchungskopf->getTeilbuchung_bis();
    				$vglDatum	= new DateTime($apdateFrom);
    				$interval 	= $buchungBis->diff($vglDatum);
    				$differenz	= intval($interval->format('%a'));
    				if ($smalestDifferenz['diff'] == -1 || $smalestDifferenz['diff'] > $differenz) {
    					$smalestDifferenz['diff'] = $differenz;
    					$smalestDifferenz['apId'] = $apaId;
    				}
    			}
    		}
    		if ($smalestDifferenz['apId'] != 0) {
    			$bestApartmentId	= $smalestDifferenz['apId'];
//     			$apartmentBuchungen[$abkey]['apartmentId'] = $smalestDifferenz['apId'];
//     			array_push($bookedApartmentIds, $smalestDifferenz['apId']);
    		}
    	} else if (sizeof($possibleApartmentIds) > 0) {
//     		$apartmentBuchungen[$abkey]['apartmentId'] = $possibleApartmentIds[0];
    		$bestApartmentId 		= $possibleApartmentIds[0];
    	} else {
//     		$isBookable				= false;
//     		$allDatesOk				= false;
//     		$arrivalDaysOk			= false;
    		$bestApartmentId		= false;
    	}
    	
    	return $bestApartmentId;
    }
    
    
    /* @var RS_IB_Table_Appartment_Buchung $buchungTable */
    /* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    /**
     *
     * @param unknown $bookingInfosObj
     * @param boolean $ignoreminimumperiod
     * @param number $adminKz
     * @param array $specialAdminParams
     * @throws IndiebookingException
     * @return array[]
     * $answer['CODE']
	 * $answer['BUCHUNGSID']
	 * $answer['BUCHUNGPOSTID']
	 * $answer['PERMALINK']
     */
    public function createDummyBooking($bookingInfosObj, $ignoreminimumperiod = false, $adminKz = 0, $specialAdminParams = array()) {
        global $RSBP_DATABASE;
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."createDummyBooking");
        
        try {
	        $allowPastBooking			= false;
	        $changeBillDate				= false;
	        $billDate					= null;
	        $bookOptionOnly				= false;
	        $dateFrom                   = "";
	        $dateTo                     = "";
	        $options                    = array();
	        $appartmentIds              = array();
	        $apartmentBuchungen         = array();
	        $bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
	        $bookingByCategorieKz		= ($bookingByCategorieKz == "on");
	        if (is_string($ignoreminimumperiod) &&  $ignoreminimumperiod == "on") {
	        	$ignoreminimumperiod = 1;
	        }
	        
	        if (isset($specialAdminParams)) {
	        	if (key_exists('allowPastBooking', $specialAdminParams)) {
	        		$allowPastBooking	= $specialAdminParams['allowPastBooking'];
	        	}
	        	if (key_exists('changeBillDate', $specialAdminParams)) {
	        		$changeBillDate		= $specialAdminParams['changeBillDate'];
	        	}
	        	if (key_exists('billDate', $specialAdminParams)) {
	        		$billDate			= $specialAdminParams['billDate'];
	        	}
	        	if (key_exists('bookOptionOnly', $specialAdminParams)) {
	        		$bookOptionOnly		= $specialAdminParams['bookOptionOnly'];
	        		if ($bookOptionOnly == 'off') {
	        			$bookOptionOnly	= false;
	        		}
	        	}
	        }
	        
	        if (isset($bookingInfosObj)) {
	            if (key_exists('dateFrom', $bookingInfosObj)) {
	                $dateFrom           = $bookingInfosObj['dateFrom'];
	            }
	            if (key_exists('dateTo', $bookingInfosObj)) {
	                $dateTo             = $bookingInfosObj['dateTo'];
	            }
	            if (key_exists('allOptionIds', $bookingInfosObj)) {
	                $options            = $bookingInfosObj['allOptionIds'];
	            }
	            if (key_exists('appartmentIds', $bookingInfosObj)) {
	                $appartmentIds      = $bookingInfosObj['appartmentIds'];
	            }
	            if (key_exists('apartmentBuchungen', $bookingInfosObj)) {
	                $apartmentBuchungen = $bookingInfosObj['apartmentBuchungen'];
	            }
	        }
	        $bookOptions                = array();
	        $answer                     = array();
	        $allDatesOk                 = true;
	        $isBookable                 = true;
	        $arrivalDaysOk              = true;
	//         foreach ($appartmentIds as $appartmentId) {
	//             $allDatesOk             = $this->checkDateRange($appartmentId, $dateFrom, $dateTo);
	//             if (!$allDatesOk) break;
	//         }
	        if (!$bookOptionOnly) {
	        	$bookedApartmentIds 	= array();
		        foreach ($apartmentBuchungen as $abkey => $apartmentBuchung) {
		            $appartmentId       = $apartmentBuchung['apartmentId'];
		            $apdateFrom         = $apartmentBuchung['fromDate'];
		            $apdateTo           = $apartmentBuchung['toDate'];
		            
		            if ($apdateFrom == "" || $apdateTo == "") {
		                throw new IndiebookingException(__("Please insert valid dates", 'indiebooking'), 0);
		            }

		            if ($bookingByCategorieKz) {
		            	$bestApartmentId		= $this->getBestApartmentIdByCategorie($appartmentId, $apdateFrom, $apdateTo, $bookedApartmentIds, $ignoreminimumperiod, $allowPastBooking);
		            	if ($bestApartmentId != false) {
		            		$apartmentBuchungen[$abkey]['apartmentId'] = $bestApartmentId;
		            		array_push($bookedApartmentIds, $bestApartmentId);
		            	} else {
		            		$isBookable			= false;
		            		$allDatesOk			= false;
		            		$arrivalDaysOk		= false;
		            	}
		            } else {
			            $isBookable         = $this->checkApartmentBookable($appartmentId);
			            if (!$isBookable) break;
			            $allDatesOk         = $this->checkDateRange($appartmentId, $apdateFrom, $apdateTo, 0, $ignoreminimumperiod, $allowPastBooking);
			            if ($allDatesOk) {
			                $arrivalDaysOk  = $this->checkApartmentArrivalDays($appartmentId, $apdateFrom, $apdateTo);
			            }
			            
			            if (!$allDatesOk) break;
			            if (!$arrivalDaysOk) break;
		            }
		        } //foreach ($apartmentBuchungen as $abkey => $apartmentBuchung)
		        
	        } else {
	        	$isBookable 	= true;
	        	$allDatesOk 	= true;
	        	$arrivalDaysOk 	= true;
	        }
	        /*
	         * Check Options
	         */
	        $optionsOk			= true;
	        if (sizeof($options) <= 0) {
		        $optionCheck		= array();
	        	foreach ($apartmentBuchungen as $apartmentBuchung) {
	        		if (key_exists('options', $apartmentBuchung)) {
	        			$apdateFrom         	= $apartmentBuchung['fromDate'];
	        			$apdateTo           	= $apartmentBuchung['toDate'];
	        			$optionsA				= $apartmentBuchung['options'];
	        			$optionDateAnswer		= apply_filters('rs_indiebooking_buchung_check_option_availability', $optionsA, $apdateFrom, $apdateTo, null, $ignoreminimumperiod, $allowPastBooking);
	        			foreach ($optionDateAnswer as $key => $optionAnswer) {
	        				$curOptId 	= $optionAnswer['optionId'];
	        				$optionMax	= $optionAnswer['optionMaxAnzahl'];
	        				$overlaps	= $optionAnswer['overlaps'];
	        				if ($optionMax > 0) { //0 = unendlich
	        					if (key_exists('count', $optionCheck[$curOptId])) {
	        						/*
	        						 * Existiert der count index, wurde die anzahl an gefundenen ueberlappungen bereits
	        						 * gezaehlt. Deshalb darf an dieser Stelle nur +1 gerechnet werden, da die Option
	        						 * mehrfach in einer Buchung angeklickt wurde.
	        						 */
	        						$optionCheck[$curOptId]['count']++;
	        						/*
	        						if (!isset($overlaps) || $overlaps <= 0) {
	        							$optionCheck[$curOptId]['count']++;
	        						} else {
	        							$optionCheck[$curOptId]['count'] +=  $overlaps;
	        						}
	        						*/
	        					} else {
	        						if (!isset($overlaps) || $overlaps <= 0) {
	        							$optionCheck[$curOptId]['count'] = 1;
	        						} else {
	        							$optionCheck[$curOptId]['count'] = $overlaps + 1;
	        						}
	        					}
	        					if ($optionCheck[$curOptId]['count'] > $optionMax) {
	        						$optionsOk 							= false;
	        						$optionDateAnswer[$key]['valid'] 	= false;
	        					}
	        				}
	        				if ($optionAnswer['valid'] == false) {
// 	        					if ($optionAnswer['apartmentId'] == 0) {
// 	        						$optionAnswer['apartmentId'] = $apartmentBuchung['appartmentid'];
// 	        					}
	        					$optionsOk = false;
	        					break;
	        				}
	        			}
	        		}
	        	}
	        } else if (sizeof($options) > 0) {
	        	$optionDateAnswer		= apply_filters('rs_indiebooking_buchung_check_option_availability', $options, $apdateFrom, $apdateTo, null, $ignoreminimumperiod, $allowPastBooking);
	        	foreach ($optionDateAnswer as $key => $optionAnswer) {
	        		if ($optionAnswer['valid'] == false) {
	        			if ($optionAnswer['apartmentId'] == 0) {
	        				$optionDateAnswer[$key]['apartmentId'] = $apartmentBuchung['apartmentId'];
	        			}
	        			$optionsOk = false;
	        			break;
	        		}
	        	}
	        } else {
	        	$optionsOk		= true;
	        }
			
	        if (!$isBookable) {
	            throw new IndiebookingException(__("Apartment is not bookable", 'indiebooking'), 0);
	        } elseif (!$allDatesOk) {
	            throw new IndiebookingException(__("Invalid Date Range", 'indiebooking'), 0);
	        } elseif (!$arrivalDaysOk) {
	            throw new IndiebookingException(__("Invalid Arrival Days", 'indiebooking'), 0);
	        } elseif (!$optionsOk) {
	        	$optionErrorMsg 	= __("The following options are not available in this time period", 'indiebooking');
	        	$invalidOptionIds 	= array();
	        	foreach ($optionDateAnswer as $optionAnswer) {
	        		if ($optionAnswer['valid'] == false) {
	        			$optionErrorMsg .= "<br /> -".$optionAnswer['optionName'];
	        			if ($adminKz == 1) {
	        				$optionErrorMsg .= " (".$optionCheck[$optionAnswer['optionId']]['count']." / ".$optionAnswer['optionMaxAnzahl'].")";
	        			}
        				if (!key_exists($optionAnswer['apartmentId'], $invalidOptionIds)) {
        					$invalidOptionIds[$optionAnswer['apartmentId']] = array();
        				}
        				array_push($invalidOptionIds[$optionAnswer['apartmentId']], $optionAnswer['optionId']);
// 	        			array_push($invalidOptionIds, $optionAnswer['optionId']);
	        		}
	        	}
	        	$answer['notValidOptions'] 	= $invalidOptionIds;
// 	        	$answer['MSG']				= $optionErrorMsg;
	        	$optionException = new IndiebookingException($optionErrorMsg, 0);
	        	$optionException->pushInvisibleExtendedInformation($answer);
	        	throw $optionException;
	        } else {
	            //**Erstelle Dummy Buchungskopf**
// 	            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."create Booking Header");
	            $buchungsKopf           = $this->createBookingHeader($dateFrom, $dateTo, 'rs_ib-blocked', null, 0, $ignoreminimumperiod, $specialAdminParams);
	            $appartmentTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
	            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	            $mwstTable              = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
	//             $allMwst                = $mwstTable->getAllMwsts();
	            
	            $teilkoepfe             = $buchungsKopf->getTeilkoepfe();
// 	            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."create Booking Teilkoepfe Buchungsnr: ".$buchungsKopf->getBuchung_nr());
	            foreach ($apartmentBuchungen as $apartmentBuchung) {
	                //**Erstelle (Dummy) Teilbuchungskopf**
	                if (!isset($options) || is_null($options)) {
	            		$options		= array();
	                }
	                $appartmentId       = $apartmentBuchung['apartmentId'];
	                $apdateFrom         = $apartmentBuchung['fromDate'];
	                $apdateTo           = $apartmentBuchung['toDate'];
	                if (key_exists('options', $apartmentBuchung)) {
	                	$options		= $apartmentBuchung['options'];
	                } else if (key_exists('optionIds', $apartmentBuchung)) {
	                	$options		= $apartmentBuchung['optionIds'];
	                } else {
	                	$options		= array();
	                }
	                if (key_exists('anzPers', $apartmentBuchung)) {
	                    $anzahlPersonen = $apartmentBuchung['anzPers'];
	                } else {
	                    $anzahlPersonen = 1;
	                }
// 	                RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] ApId: ".$appartmentId." Von: ".$apdateFrom." Bis: ".$apdateTo);
	                $appartment         = $appartmentTable->getAppartment($appartmentId);
	                
	                $teilHeader         = $this->createBookingPartHeader($buchungsKopf->getBuchung_nr(), $appartment,
	                                        $apdateFrom, $apdateTo, $anzahlPersonen);
	                $teilHeadId         = $teilHeader->getTeilbuchung_id();
	                array_push($teilkoepfe, $teilHeader);
	                
	                if (!$bookOptionOnly) {
	                	$this->loadAppartmentPricesAndCreatePosition($appartmentId, $teilHeader);
	                }
	                do_action("rs_indiebooking_buchung_loadOptionsAndCreatePosition", $teilHeader->getAppartment_id(), $teilHeader, $options, $adminKz);
	            }
	            
	//             foreach ($appartmentIds as $appartmentId) {
	//                 //**Erstelle (Dummy) Teilbuchungskopf**
	//                 $appartment         = $appartmentTable->getAppartment($appartmentId);
	//                 $teilHeader         = $this->createBookingPartHeader($buchungsKopf->getBuchung_nr(), $appartment,
	//                                         $dateFrom, $dateTo);
	//                 $teilHeadId         = $teilHeader->getTeilbuchung_id();
	//                 array_push($teilkoepfe, $teilHeader);
	//                 $this->loadAppartmentPricesAndCreatePosition($appartmentId, $teilHeader);
	//             }
	            $buchungKopfTable	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
	            if ($adminKz == 1) {
	            	$buchungsKopf->setAdminKz($adminKz);
	            }
	            $post_id              = $this->createWPBookingPost($buchungsKopf);
	            $buchungsKopf->setPostId($post_id);
	            $buchungKopfTable->saveOrUpdateBuchungskopf($buchungsKopf);
	            
	            update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, 1);
	            RS_Indiebooking_Log_Controller::write_log("activate heartbeat ".$post_id);
	            $this->updateCalculatedValues($buchungsKopf->getBuchung_nr());
	            
	            if ($adminKz == 1) {
	            	$buchungKopfTable->updateBuchungsAdminKz($buchungsKopf->getBuchung_nr(), $adminKz);
	//             	$buchungKopfTable->loadAllBookingCustomers();
	            }
	            
	            $answer['CODE']       = 1;
	            $answer['BUCHUNGSID'] = $buchungsKopf->getBuchung_nr();
	            $answer['BUCHUNGPOSTID'] = $post_id;
	//             $answer['REMAINTIME'] = $buchungObj->getRemainingtTime();
	            $answer['PERMALINK']  = get_permalink($post_id);
	        }
	        return $answer;
        } catch (Exception $e) {
        	if ($e instanceof IndiebookingException) {
        		throw  $e;
        	} else {
        		throw new IndiebookingException(__("Unknown Error ".$e->getMessage(), 'indiebooking'), 0);
        	}
        	RS_Indiebooking_Log_Controller::write_log("Unknown Error ".$e->getMessage(), $e->getLine(), $e->getCode(), RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
        	//TODO Rollback
        }
    }
    
    /* @var $modelGutschein RS_IB_Model_Gutschein */
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    /* @var $buchungRabattTable RS_IB_Table_BuchungRabatt */
    /* @var $buchungZahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
    
    public function recheckApartmentCoupon($gutschein, $buchungsKopf) {
        
    }

    public function checkApartmentCouponCode($couponCode, $buchungId, $isPostId = true, $checkExisting = false, $buchungsKopf = null) {
        $answer                 = array();
        $answer['MSG']          = "";
        $answer['MSG_AID']      = "";
        $answer['CODE']         = 1;
        if (class_exists('RS_IB_Indiebooking_Coupon_Controller')) {
            $couponcontroller   = new RS_IB_Indiebooking_Coupon_Controller();
            $answer             = $couponcontroller->checkApartmentCouponCode($couponCode, $buchungId, $isPostId, $checkExisting, $buchungsKopf);
        }
        return $answer;
    }
}
// endif;
// new RS_IB_Appartment_Buchung_Controller();

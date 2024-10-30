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
// if ( ! class_exists( 'RS_IB_Backend_Controller' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Backend_Controller
{
    public function __construct() {

    }
    
    /* @var $modelAppBuchungTable RS_IB_Table_Appartment_Buchung */
    public function updateBookingContactData($bookingNumber, $bookingContact, $bookingPostId = 0) {
    	global $RSBP_DATABASE;
    	
    	$answer = array(
	    	'CODE' => 1,
	    	'MSG'  => 'OK',
    	);
    	$reNumberArray = array();
    	try {
    		$reNumberArray	= get_post_meta($bookingPostId, 'rs_indiebooking_all_renr');
    		if (sizeof($reNumberArray) > 0) {
    			$reNumberArray 			= $reNumberArray[0];
    		} else {
    			$reNumberArray 			= array();
    		}
    		$this->createBookingPdf($bookingPostId, "", false); //um sicher zu stellen, dass die Originalrechnung erstellt wurde.
    		
	    	$modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	    	
	    	$buchungKopf                = $modelAppBuchungTable->loadBuchungskopf($bookingNumber); //Laed die komplette Buchung
	    	$buchungKopf->setKunde_firma($bookingContact['firma']);
	    	$buchungKopf->setKunde_abteilung($bookingContact['abteilung']);
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
	    	$buchungKopf->setKunde_abteilung2($bookingContact['abteilung2']);
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
	    	
	    	if ($buchungKopf->getRechnung_nr() > 0) {
	    		/*
	    		 * Da bereits eine Rechnung mit den vorherigen Daten erstellt wurde,
	    		 * muss an dieser Stelle eine neue Rechnungsnummer erzeugt werden.
	    		 */
	    		if (!in_array($buchungKopf->getRechnung_nr(), $reNumberArray)) {
	    			array_push($reNumberArray, $buchungKopf->getRechnung_nr());
	    		}
	    		$answer = $modelAppBuchungTable->cancelBooking($bookingPostId, false, true, 1, true);
	    		if ($answer['CODE'] == 1) {
	    			array_push($reNumberArray, $answer['INVOICENUMBER']);
	    			$this->createBookingPdfByInvoiceNumber($answer['INVOICENUMBER'], "", false, true, $bookingPostId);
	    		}
	    		
	    		/*
	    		 * Update Carsten Schmitt 16.08.2018
	    		 * Damit der Zeitraum auch definitiv zu Booking.com uebertragen wird bzw. dort vorhanden bleibt,
	    		 * setzen wir das BcomSynchronizedKZ an dieser Stelle wieder auf 0.
	    		 * Um sicher zu gehen, dass diese Uebertragung auch ausgefuert wird, rufen wir nach der
	    		 * erstellung der Buchung die synchronisation haendisch auf.
	    		 */
	    		$buchungKopf->setBcomSynchronizedKZ(0);
	    		$newReNr = $modelAppBuchungTable->createAndSaveBillNumber($buchungKopf);
	    		array_push($reNumberArray, $newReNr);
	    		$this->createBookingPdf($bookingPostId);
	    		
	    		update_post_meta($bookingPostId, 'rs_indiebooking_all_renr', $reNumberArray);
	    	}
	    	
	    	$modelAppBuchungTable->saveOrUpdateBuchungskopf($buchungKopf);
	    	if (has_action('rs_indiebooking_synchronize_bookingdata')) {
	    		if ($buchungKopf->getBcomSynchronizedKZ() == 0) {
	    			do_action('rs_indiebooking_synchronize_bookingdata');
	    		}
	    	}
    	} catch (\Exception $e) {
    		$answer = array(
    			'CODE' => 0,
    			'MSG'  => $e->getMessage(),
    		);
    	}
    	return $answer;
    }
    
    /* @var $buchungZahlungTable RS_IB_Table_BuchungZahlung */
    private function saveZahlung($postId, $zahlungsbetrag = null, $confirmByAdmin = false, $confirmDeposit = false) {
        global $RSBP_DATABASE;
        
        $anzahlung		= false;
        $paymentlData 	= get_option( 'rs_indiebooking_settings_payment');
        $depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
        if ($depositKz == "on") {
        	$anzahlung	= true;
        }
        $buchungZahlungTable        = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $modelAppBuchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungNr                  = get_post_meta($postId, "rs_ib_buchung_kopf_id", true);
        $buchungKopf                = $modelAppBuchungTable->loadBuchungskopf($buchungNr); //Lued die komplette Buchung
        $zahlungsbezeichnung        = "";
        $zahlart                    = 0;
        if (is_null($zahlungsbetrag)) {
        	$totalPayment           = $buchungKopf->getZahlungsbetrag();
        } else {
        	$totalPayment           = $zahlungsbetrag;
        }
        if ($confirmByAdmin && $anzahlung) {
        	$zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_INVOICE;
        	$zahlungsbezeichnung    = __("pay by invoice", 'indiebooking');
        } else {
	        if ($buchungKopf->getHauptZahlungsart() == "INVOICE") {
	            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_INVOICE;
	            $zahlungsbezeichnung    = __("pay by invoice", 'indiebooking');
	        } elseif ($buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS") {
	            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_PAYPALEXPRESS;
	            $zahlungsbezeichnung    = __("paypal express payment", 'indiebooking');
	        } elseif ($buchungKopf->getHauptZahlungsart() == "PAYPAL") {
	            $zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_PAYPAL;
	            $zahlungsbezeichnung    = __("paypal payment", 'indiebooking');
	        } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPESOFORT") {
	        	$zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_STRIPE_SOFORT;
	        	$zahlungsbezeichnung    = __("sofort payment", 'indiebooking');
	        } elseif ($buchungKopf->getHauptZahlungsart() == "STRIPEGIROPAY") {
	        	$zahlart                = RS_IB_Model_BuchungZahlung::ZAHLART_STRIPE_GIROPAY;
	        	$zahlungsbezeichnung    = __("giropay payment", 'indiebooking');
	        }
        }
        if ($buchungKopf->getBuchung_status() == 'rs_ib-storno' || $buchungKopf->getBuchung_status() == 'rs_ib-storno_paid') {
        	$buchungKopf->setBuchung_status('rs_ib-storno_paid');
        } else if (!$confirmDeposit){
        	$buchungKopf->setBuchung_status('rs_ib-pay_confirmed');
        }
        $zahlung                    = new RS_IB_Model_BuchungZahlung();
        $zahlung->setBuchung_nr($buchungKopf->getBuchung_nr());
        $zahlung->setBezeichnung($zahlungsbezeichnung);
        $zahlung->setZahlung_nr(0);
        $zahlung->setZahlungbetrag($totalPayment);
        $zahlung->setZahlungzeitpunkt(new DateTime());
        $zahlung->setZahlungart($zahlart);
        
        $buchungZahlungTable->saveOrUpdateBuchungZahlung($zahlung);
    }
    
    /* @var RS_IB_Table_Appartment_Buchung $appartmentBuchungsTable */
    public function anfrageBestaetigen($bookingId) {
    	global $RSBP_DATABASE;
    	$answer							= array();
    	if ($bookingId > 0) {
    		RS_Indiebooking_Log_Controller::write_log(
    				"Anfrage bestaetigt",
    				__LINE__,
    				__CLASS__,
    				RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
    		$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    		$postId                     = $appartmentBuchungsTable->confirmInquiry($bookingId);
    
    		$myerror                    = "";
    		$errorcode                  = null;
    		if (is_wp_error($postId)) {
    			$errors                 = $postId->get_error_messages();
    			$errorcode              = $postId->get_error_code();
    			foreach ($errors as $error) {
    				$myerror = $myerror ." | " . $error;
    			}
    		} else {
//     			do_action("rs_indiebooking_print_rsappartment_buchung_payment_confirmation", $bookingId);
    			if (has_action('rs_indiebooking_synchronize_bookingdata')) {
    				do_action('rs_indiebooking_synchronize_bookingdata');
    			}
    			do_action("rs_ib_create_file_and_send_mail", $bookingId, 1);
    		}
    		$answer['CODE']             = 1;
    		$answer['BookingId']        = $bookingId;
    		$answer['ERR']              = $myerror;
    		$answer['ERRCode']          = $errorcode;
    		$answer['MSG']              = __("Inquiry ".$bookingId." was confirmed.", 'indiebooking');
    	} else {
    		$answer['CODE']             = 0;
    		$answer['MSG']              = __("No booking ID found", 'indiebooking');
    	}
    	return $answer;
    }
    
    /* @var $buchungZahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $buchungZahlung RS_IB_Model_BuchungZahlung */
    public function updateZahlung($chargeId, $status) {
    	global $RSBP_DATABASE;
    	
    	$buchungZahlungTable    = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
    	$buchungZahlungen		= $buchungZahlungTable->loadBuchungZahlungenByChargeId($chargeId);
    	$found					= false;
    	if (!is_null($buchungZahlungen) && $buchungZahlungen != false) {
    		foreach ($buchungZahlungen as $buchungZahlung) {
	    		RS_Indiebooking_Log_Controller::write_log('zahlung gefunden - status '.$status, __LINE__, __CLASS__);
	    		$buchungZahlung->setStatus($status);
	    		$buchungZahlungTable->saveOrUpdateBuchungZahlung($buchungZahlung);
	    		$found 				= true;
    		}
    	}
    	return $found;
    }
    
    /* @var $buchungsKopfTbl RS_IB_Table_Buchungskopf */
    /* @var $buchungZahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $zahlung RS_IB_Model_BuchungZahlung */
    public function zahlungBestaetigen($bookingId, $zahlungsbetrag = null, $confirmByAdmin = false, $confirmDeposit = false) {
        global $RSBP_DATABASE;
        if ($bookingId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchung                	= $appartmentBuchungsTable->getAppartmentBuchung($bookingId);
            $buchungsKopfId         	= $buchung->getBuchungKopfId();
            $buchungsKopfTbl        	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchungsKopfModel      	= $buchungsKopfTbl->loadBooking($buchungsKopfId, true);
            
            if ($confirmDeposit) {
            	$zahlungsbetrag			= $buchungsKopfModel->getAnzahlungsbetrag();
            	$buchungsKopfTbl->updateBuchungsAnzahlungBezahltKz($buchungsKopfModel->getBuchung_nr(), 1);
            }
            
            if (is_null($zahlungsbetrag)) {
            	/*
            	 * Es handelt sich nicht um eine Anzahlung, sondern um die 'normale' bestaetigung der Zahlung
            	 */
            	$postId                 = $appartmentBuchungsTable->confirmPayment($bookingId, $buchungsKopfModel);
            } else {
            	$postId					= $buchungsKopfModel->getPostId();
            }
            $this->saveZahlung($postId, $zahlungsbetrag, $confirmByAdmin, $confirmDeposit);
            
            if (!is_null($zahlungsbetrag)) {
            	$buchungZahlungTable    = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
            	$zahlungen 				= $buchungZahlungTable->loadBuchungZahlungen($buchungsKopfModel->getBuchung_nr());
            	$bezahlterBetrag		= 0;
            	foreach ($zahlungen as $zahlung) {
            		$bezahlterBetrag	+= $zahlung->getZahlungbetrag();
            	}
            	if ($bezahlterBetrag == $buchungsKopfModel->getZahlungsbetrag()) {
            		$postId             = $appartmentBuchungsTable->confirmPayment($bookingId, $buchungsKopfModel);
            	}
            }
            
            $myerror                    = "";
            $errorcode                  = null;
            $coupon                     = null;
            if (is_wp_error($postId)) {
                $errors                 = $postId->get_error_messages();
                $errorcode              = $postId->get_error_code();
                foreach ($errors as $error) {
                    $myerror = $myerror ." | " . $error;
                }
                RS_Indiebooking_Log_Controller::write_log(
                    "Fehler bei Zahlung bestaetigen - ".$myerror.'('.$bookingId.')',
                    __LINE__,
                    __CLASS__,
                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
            } else {
                RS_Indiebooking_Log_Controller::write_log(
                    "Zahlung bestaetigt - ".$postId,
                    __LINE__,
                    __CLASS__,
                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
//                 do_action("rs_indiebooking_print_rsappartment_buchung_payment_confirmation", $bookingId);
	            $buchung                = $appartmentBuchungsTable->getAppartmentBuchung($bookingId);
	            $buchungsKopfId         = $buchung->getBuchungKopfId();
	            $buchungsKopfTbl        = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
	            $buchungsKopfModel      = $buchungsKopfTbl->loadBooking($buchungsKopfId, false);
	            $buchungsKopfTbl->updateAdminUserStatus($buchungsKopfModel->getBuchung_nr(), 1);
	            
	            if ($buchungsKopfModel->getBuchung_status() !== "rs_ib-storno_paid") {
	                $this->createPaymentConfirmationMail($bookingId);
	            }
            }
            $answer['CODE']             = 1;
            $answer['BookingId']        = $bookingId;
            $answer['ERR']              = $myerror;
            $answer['ERRCode']          = $errorcode;
            $answer['MSG']              = __("Booking ".$bookingId." was paid.", 'indiebooking');
        } else {
            $answer['CODE']             = 0;
            $answer['MSG']              = __("No booking ID found", 'indiebooking');
        }
        return $answer;
    }
    
    /* @var $buchung RS_IB_Model_Appartment_Buchung */
    /* @var $coupon RS_IB_Model_Gutschein */
    public function storniereBuchung($bookingId) {
        global $RSBP_DATABASE;
        $answer = array();
        if ($bookingId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $answer                     = $appartmentBuchungsTable->cancelBooking($bookingId, true, true, 1);
            if ($answer['CODE'] == 1) {
                $this->createStornoConirmationMail($answer['STORNOPOSTID']);
            }
        } else {
            $answer['CODE']             = 0;
            $answer['MSG']              = __("No booking ID found", 'indiebooking');
        }
        return $answer;
    }
    
    public function createStornoConirmationMail($bookingId) {
        if ($bookingId > 0) {
        	if (get_post_status($bookingId) == "rs_ib-storno" || get_post_status($bookingId) == "rs_ib-storno_paid") {
                do_action("rs_ib_create_file_and_send_mail", $bookingId, 5);
            } elseif (get_post_status($bookingId) == "rs_ib-canc_request") {
            	do_action("rs_ib_create_file_and_send_mail", $bookingId, 6);
            }
        }
    }
    
    public function createPaymentConfirmationMail($bookingId) {
        global $RSBP_DATABASE;
    
        if ($bookingId > 0) {
//             $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
//             $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingId);
            do_action("rs_ib_create_file_and_send_mail", $bookingId, 3);
//             $contact                    = $buchung->getContact();
    
//             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
//             //             $file                       = printBooking($buchung);
//             $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_payment_confirmation", $bookingId);
//             $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_invoice_subject');
//             $to                         = $contact['email'];
//             $subject                    = $mail_confirm_subject;
//             $message                    = get_option('rs_indiebooking_settings_mail_booking_invoice_txt');
//             if ($message === false) {
//                 $message                = __("Payment confirmation", 'indiebooking');
//             } else {
//                 $message                = str_replace('$$__BOOKINGNR__$$', $bookingId, $message);
//                 $message                = str_replace('$$__CUSTOMER__$$', $contact['name'], $message);
//                 $message                = str_replace('&nbsp;', "<br />", $message);
//             }
//             $header                     = "Content-type: text/html";
//             $attachments                = $file;
//             wp_mail($to, $subject, $message, $header, $attachments);
        }
    }
    
    public function createTestBookingPdf() {
    	$file 						= apply_filters("rs_indiebooking_print_rsappartment_test_buchung_confirmation", "");
    	$answer['CODE'] = 1;
    	$answer['MSG']              = __("Booking was printed.", 'indiebooking');
    	$answer['PERMALINK']        = "";//get_permalink($bookingId);
    	$answer['FILE']             = $file;
    	return $answer;
    }
    
    public function createBookingPdfByInvoiceNumber($invoiceNumber, $dest="", $createNewVersion = true, $storno = false, $buchungsNrToCopy = 0) {
    	if ($invoiceNumber > 0) {
    		if ($storno) {
    			$dest                   = "BOOKING_STORNO";
    		}
    		$file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_confirmation_by_invoicenr", $invoiceNumber, $dest, $createNewVersion, $storno, $buchungsNrToCopy);
    		
    		$answer['CODE'] = 1;
    		$answer['MSG']              = __("Booking was printed.", 'indiebooking');
//     		$answer['PERMALINK']        = get_permalink($bookingId);
    		$answer['FILE']             = $file;
    	} else {
    		$answer['CODE']             = 0;
    		$answer['MSG']              = __("No booking ID found", 'indiebooking');
    	}
    	return $answer;
    }
    
    public function createBookingPdf($bookingId, $dest="", $createNewVersion = true) {
        global $RSBP_DATABASE;
        
        if ($bookingId > 0) {
//             $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
//             $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingId);
//             $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
//             $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());

//             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
//             $file                       = printBooking($buchung);
            $postStatus                 = get_post_status($bookingId);
            if ($postStatus == "rs_ib-storno" || $postStatus == "rs_ib-storno_paid") {
                $dest                   = "BOOKING_STORNO";
            }
            $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_confirmation", $bookingId, $dest, $createNewVersion);
            
            //TODO "do_action("rs_indiebooking_print_rsappartment_buchung_booking_confirmation");"
//             $to             = "cschmitt@rewasoft.de";
//             $subject        = "test indiebooking";
//             $message        = "ein test in ehren kann niemand verwehren";
//             $header         = "";
// //             $attachments    = $file;
//             wp_mail($to, $subject, $message, $header);
            $answer['CODE'] = 1;
            $answer['MSG']              = __("Booking was printed.", 'indiebooking');
            $answer['PERMALINK']        = get_permalink($bookingId);
            $answer['FILE']             = $file;
        } else {
            $answer['CODE']             = 0;
            $answer['MSG']              = __("No booking ID found", 'indiebooking');
        }
        return $answer;
    }
}
// endif;
// new RS_IB_Appartment_Buchung_Controller();
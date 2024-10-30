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

if ( !function_exists( 'rs_ib_indiebooking_send_mail') ) {
    function rs_ib_indiebooking_send_mail($to, $subject, $message, $header = '', $attachments = array()) {
        $mailmsg                    = "";
        $mailReturn                 = wp_mail($to, $subject, $message, $header, $attachments);
        if ($mailReturn) {
        	$mailmsg    = "mail wurde versandt - ".$to; //.$bookingPostId
        } else {
        	$mailmsg    = "Fehler beim mail versandt - ".$to; //.$bookingPostId
        }
        RS_Indiebooking_Log_Controller::write_log(
            $mailmsg,
            __LINE__, __CLASS__,
            RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
        
        return $mailReturn;
    }
}

if ( !function_exists( 'rs_ib_indiebooking_send_mail_with_cc') ) {
	/**
	 * Der erste Eintrag in $to wird als to-Adresse genutzt. Alle weiteren Einträge werden
	 * in das cc angehangen.
	 * Fuer den Fall, dass extra cc und / oder bcc hinzugefuegt werden sollen, gibt es die Parameter $cc und $bcc
	 * der Paramter $to muss dennoch gefuellt sein (dann als string)!
	 * @param array $to
	 * @param unknown $subject
	 * @param unknown $message
	 * @param string $header
	 * @param array $attachments
	 * @param array $cc
	 * @param array $bcc
	 * @return boolean
	 */
	function rs_ib_indiebooking_send_mail_with_cc($to, $subject, $message, $header = '', $attachments = array(), $cc = array(), $bcc = array()) {
		$mailmsg                    = "";
		$toAdress					= "";
		$logAdr						= "";
		if ($header == '') {
			$header					= array();
		} else if (is_string($header)) {
			$hdr					= $header;
			$header					= array();
			array_push($header, $hdr);
		}
		if (is_array($to)) {
			foreach ($to as $key => $adr) {
				if ($key > 0) {
					$header[]		= 'Cc: '.$adr;
				} else {
					$toAdress		= $adr;
				}
				$logAdr = $logAdr.", ".$adr;
			}
		}
		if (is_string($cc) && $cc != '') {
			$header[]		= 'Cc: '.$cc;
			$logAdr 		= $logAdr.", cc: ".$cc;
		} else if (sizeof($cc) > 0) {
			foreach ($cc as $key => $adr) {
				$header[]		= 'Cc: '.$adr;
				$logAdr 		= $logAdr.", cc: ".$adr;
			}
		}
		if (is_string($bcc) && $bcc != '') {
			$header[]		= 'Cc: '.$bcc;
			$logAdr 		= $logAdr.", bcc: ".$bcc;
		} else if (sizeof($bcc) > 0) {
			foreach ($cc as $key => $adr) {
				$header[]		= 'Bcc: '.$adr;
				$logAdr 		= $logAdr.", bcc: ".$adr;
			}
		}
		if ($toAdress != "") {
			$mailReturn                 = wp_mail($toAdress, $subject, $message, $header, $attachments);
			if ($mailReturn) {
				$mailmsg    = "mail wurde versandt - ".$to; //.$bookingPostId
			} else {
				$mailmsg    = "Fehler beim mail versandt - ".$to; //.$bookingPostId
			}
			RS_Indiebooking_Log_Controller::write_log(
				$mailmsg,
				__LINE__, __CLASS__,
				RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
		} else {
			RS_Indiebooking_Log_Controller::write_log(
				'No Mail Adress',
				__LINE__, __CLASS__,
				RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
		}
		return $mailReturn;
	}
}


/**
 * ACTIONS
 */
if ( ! function_exists( 'rs_ib_create_mail_log_entry' ) ) {
	/* @var $maillogTbl RS_IB_Table_MailPrintJob */
	function rs_ib_create_mail_log_entry($bookingPostId, $mailArt) {
		global $RSBP_DATABASE;
		try {
			$maillogTbl = $RSBP_DATABASE->getTable(RS_IB_Model_MailPrintJob::RS_TABLE);
			$mailJobLog = new RS_IB_Model_MailPrintJob();
			$mailJobLog->setBookingPostId($bookingPostId);
			$mailJobLog->setPrintType($mailArt);
			
			$maillogTbl->saveOrUpdateMailPrintJob($mailJobLog);
		} catch (Exception $e) {
			RS_Indiebooking_Log_Controller::write_log(
				"Fehler: ".$e->getMessage()." Code: ".$e->getCode()."rs_ib_create_mail_log_entry: ".$bookingPostId . "mailart: ".$mailArt,
				__LINE__, __CLASS__,
				RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
		}
	}
}

if ( ! function_exists( 'rs_ib_create_file_and_send_mail' ) ) {
    function rs_ib_create_file_and_send_mail($bookingPostId, $mailArt) {
        global $RSBP_DATABASE;
        try {
            RS_Indiebooking_Log_Controller::write_log(
                "create pdf and send mail : ".$bookingPostId . "mailart: ".$mailArt,
                __LINE__, __CLASS__,
                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
            $mailArtDescription     = "";
            switch ($mailArt) {
                case 1:
                    $mailArtDescription = __("bookingconfirmation", "indiebooking");
                    rs_ib_sendBuchungsbestaetigung($bookingPostId);
                    break;
                case 2:
                    RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."send mail kz 2 - eigentlich nie aufgerufen?!");
                    rs_ib_sendBuchungRechnung($bookingPostId);
                    break;
                case 3:
                    $mailArtDescription = __("payment confirmation", "indiebooking");
                    rs_ib_sendZahlungsbestaetigung($bookingPostId);
                    break;
                case 4:
                    $mailArtDescription = __("inquiry confirmation", "indiebooking");
                	rs_ib_sendAnfragebestaetigung($bookingPostId);
                	break;
                case 5:
                    $mailArtDescription = __("storno confirmation", "indiebooking");
                    rs_ib_sendStornobestaetigung($bookingPostId);
                    break;
                case 6:
                    $mailArtDescription = __("inquiry rejection", "indiebooking");
                	rs_ib_sendAnfrageablehnung($bookingPostId);
                	break;
                case 7:
                	$mailArtDescription = __("deposit reminder", "indiebooking");
                	rs_ib_sendDepositReminder($bookingPostId);
                case 50:
                	$subject = __("SOFORT payment failed", "indiebooking");
                	$mailArtDescription = $subject;
                	rs_ib_sendInfoMailToAdmin($bookingPostId, $subject);
                	break;
                case 51:
                	$subject = __("SOFORT payment - booking number not found", "indiebooking");
                	$mailArtDescription = $subject;
                	rs_ib_sendInfoMailToAdmin($bookingPostId, $subject);
                	break;
                case 52:
                	$subject = __("SOFORT payment - event could not be verified by stripe", "indiebooking");
                	$mailArtDescription = $subject;
                	rs_ib_sendInfoMailToAdmin($bookingPostId, $subject);
                	break;
                case 98:
                	$bookingNumber = $bookingPostId;
                	$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
                	$buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    				$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
                	$buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
                	$file						= null;
                	if (!is_null($buchungKopf)) {
                		$fileName				= __('Booking confirmation', 'indiebooking') . '#'.$buchungKopf->getBuchung_nr();
                		$file					= rs_ib_print_util::getPrintedPDFFromFilesystem($buchungKopf->getBuchung_nr(), $fileName);
                	}
                	$subject = __("You have a new Booking", "indiebooking");
                	$mailArtDescription = $subject;
                	rs_ib_sendInfoMailToAdmin($bookingNumber, $subject, '', $file);
                	break;
                case 99:
                	$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
                	$buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
                	$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
                	$buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
                	$file						= null;
                	if (!is_null($buchungKopf)) {
    	            	$fileName				= __('Inquiry_Confirmation', 'indiebooking') . '#'.$buchungKopf->getBuchung_nr();
    	            	$file					= rs_ib_print_util::getPrintedPDFFromFilesystem($buchungKopf->getBuchung_nr(), $fileName);
                	}
                	$subject 	= __("You have a new inquiry", "indiebooking");
                	$mailArtDescription = $subject;
                	rs_ib_sendInfoMailToAdmin($bookingPostId, $subject, '', $file);
                	break;
            }
            RS_Indiebooking_Log_Controller::write_log(
                "pdf was created and mail was send: ".$bookingPostId . " - mailart: ".$mailArt. " - Desc: ".$mailArtDescription,
                __LINE__, __CLASS__,
                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
        } catch (Exception $e) {
            RS_Indiebooking_Log_Controller::write_log(
                "Fehler: ".$e->getMessage()." Code: ".$e->getCode()."rs_ib_create_file_and_send_mail: ".$bookingPostId . "mailart: ".$mailArt,
                __LINE__, __CLASS__,
                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
        }
    }
}

if ( !function_exists( 'rs_ib_sendAnfragebestaetigung' )) {
	function rs_ib_sendAnfragebestaetigung($bookingPostId) {
		global $RSBP_DATABASE;
	
		if ($bookingPostId > 0) {
			$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			$buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
			$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
	
			$buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
			$contact                    = $buchungKopf->getContactArray();

			$file                       = apply_filters("rs_indiebooking_print_rsappartment_inquiry_confirmation", $bookingPostId);
			
			$mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_inquiry_subject');
			$to                         = $contact['email'];
			$subject                    = $mail_confirm_subject; //"test indiebooking";
			$message                    = get_option('rs_indiebooking_settings_mail_inquiry_confirmation_txt');
			if ($message === false || is_null($message) || strlen($message) <= 0) {
				$message                = __("Thanks for your inquiry", 'indiebooking');
			} else {
				$message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
				$message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
				$message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
				$message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
				$message                = str_replace('&nbsp;', "<br />", $message);
			}
			$header                     = "Content-type: text/html";
			$attachments				= rs_indiebooking_getAllMailAttachments();
			if (sizeof($attachments) > 0) {
				array_push($attachments, $file);
			} else {
				$attachments            = $file;
			}
			
// 			wp_mail($to, $subject, $message, $header, $attachments);
			rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
			
// 			$attachments                = $file;
// 			wp_mail($to, $subject, $message, $header, $attachments);
			
			//Senden dem / den admins die gleiche bestaetigungsmail
			$subject2 = __("You have a new Inquiry", "indiebooking");
			rs_ib_sendInfoMailToAdmin($bookingPostId, $subject2, '', $file);
		}
	}
}

if ( ! function_exists('rs_ib_sendInfoMailToAdmin')) {
	function rs_ib_sendInfoMailToAdmin($bookingPostId, $subject, $customMessage = '', $filename = null, $sendBookingLink = true) {
		$header     		= "Content-type: text/html";
		$wasSend			= false;
		$successSend		= false;
		$message			= '';
// 		$link		= get_edit_post_link($bookingPostId); //funktioniert nur wenn Benutzer eingeloggt ist und edit-post darf
		if ($sendBookingLink) {
			$link			= admin_url('post.php?post='.$bookingPostId.'&action=edit');
			$message		= "<a href='".$link."'>".__("Show booking", "indiebooking")."</a>";
		}
		$message			= $message.'<br />'.$customMessage;
		
		$adminEmails		= get_option('rs_indiebooking_settings_admin_email');
		if (is_null($adminEmails) || sizeof($adminEmails) <= 0) {
			$adminEmails 	= array();
			$to 			= get_option('admin_email');
			array_push($adminEmails, $to);
		}
		if (sizeof($adminEmails) > 0) {
			if (!is_null($filename)) {
				$successSend    = rs_ib_indiebooking_send_mail_with_cc($adminEmails, $subject, $message, $header, $filename);
			} else {
				$successSend    = rs_ib_indiebooking_send_mail_with_cc($adminEmails, $subject, $message, $header);
			}
			$wasSend = true;
			/*
			foreach ($adminEmails as $adminEmail) {
				if ("" != $adminEmail) {
					if (!is_null($filename)) {
// 						$successSend = wp_mail($adminEmail, $subject, $message, $header, $filename);
					    $successSend    = rs_ib_indiebooking_send_mail($adminEmail, $subject, $message, $header, $filename);
					} else {
// 						$successSend = wp_mail($adminEmail, $subject, $message, $header);
					    $successSend    = rs_ib_indiebooking_send_mail($adminEmail, $subject, $message, $header);
					}
					$wasSend = true;
				}
			}
			*/
		}
		if (!$wasSend) {
			$to 		= get_option('admin_email');
			if (!is_null($filename)) {
// 				$successSend = wp_mail($to, $subject, $message, $header, $filename);
			    $successSend    = rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $filename);
			} else {
			    $successSend    = rs_ib_indiebooking_send_mail($to, $subject, $message, $header);
// 				$successSend = wp_mail($to, $subject, $message, $header);
			}
		}
		return $successSend;
	}
}

if ( ! function_exists('rs_ib_sendInfoMailToReceiver')) {
	function rs_ib_sendInfoMailToReceiver($subject, $customMessage = '', $receiver = array(), $filename = null) {
		$header     		= "Content-type: text/html";
		$successSend		= false;
		$message			= '';
		$message			= $message.$customMessage;

		if (sizeof($receiver) > 0) {
			foreach ($receiver as $adminEmail) {
				if ("" != $adminEmail) {
					if (!is_null($filename)) {
						$successSend = wp_mail($adminEmail, $subject, $message, $header, $filename);
					} else {
						$successSend = wp_mail($adminEmail, $subject, $message, $header);
					}
				}
			}
		}
		return $successSend;
	}
}

if ( ! function_exists( 'rs_indiebooking_getAllMailAttachments')) {
	function rs_indiebooking_getAllMailAttachments() {
		$attachmentArray		= array();
		$mailAttachmentIds		= get_option( 'rs_indiebooking_settings_admin_email_attachment' );
		if (!$mailAttachmentIds) {
			$mailAttachmentIds = "";
		}
		
		$attachments = array_filter( explode( ',', $mailAttachmentIds ) );
		if ( ! empty( $attachments ) ) {
			foreach ( $attachments as $attachment_id ) {
				$attachment_id 	= intval($attachment_id);
				// 						$url			= wp_get_attachment_url($attachment_id);
// 				$attachment_title = get_the_title($attachment_id);
				$attachment = get_attached_file($attachment_id);
				if (isset($attachment) && !is_null($attachment) && !empty($attachment)) {
					array_push($attachmentArray, $attachment);
				}
			}
		}
		
		return $attachmentArray;
	}
}

if ( ! function_exists( 'rs_ib_sendBuchungsbestaetigung' ) ) {
	/* @var $buchungKopf RS_IB_Model_Buchungskopf */
    function rs_ib_sendBuchungsbestaetigung($bookingPostId) {
        global $RSBP_DATABASE;
    
        if ($bookingPostId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
    
            $buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
            $contact                    = $buchungKopf->getContactArray();
            //             $contact                    = $buchung->getContact();
            //             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
            //             $file                       = printBooking($buchung);
            $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_confirmation", $bookingPostId);
            $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_confirm_subject');
            $to                         = $contact['email'];
            $subject                    = $mail_confirm_subject; //"test indiebooking";
            $message                    = get_option('rs_indiebooking_settings_mail_booking_confirmation_txt');
            if ($message === false || is_null($message) || strlen($message) <= 0) {
                $message                = __("Thanks for Booking", 'indiebooking');
            } else {
                $message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
                $message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
                $message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
                $message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
                $message                = str_replace('&nbsp;', "<br />", $message);
            }
            $header                     = "Content-type: text/html";
            
            $attachments				= rs_indiebooking_getAllMailAttachments();
            if (sizeof($attachments) > 0) {
            	array_push($attachments, $file);
            } else {
            	$attachments            = $file;
            }
            
            rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
//             $mailmsg                    = "";
//             $mailReturn                 = wp_mail($to, $subject, $message, $header, $attachments);
//             if ($mailReturn) {
//                 $mailmsg    = "mail wurde versandt - ".$bookingPostId." ".$to;
//             } else {
//                 $mailmsg    = "Fehler beim mail versandt - ".$bookingPostId." ".$to;
//             }
//             RS_Indiebooking_Log_Controller::write_log(
//                 $mailmsg,
//                 __LINE__, __CLASS__,
//                 RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
            
            //Senden dem / den admins die gleiche bestaetigungsmail
            $subject2 = __("You have a new booking", "indiebooking");
            rs_ib_sendInfoMailToAdmin($bookingPostId, $subject2, '', $file);
        }
    }
}


if (! function_exists('rs_ib_sendDepositReminder')) {
	function rs_ib_sendDepositReminder($bookingPostId) {
		global $RSBP_DATABASE;
		
		if ($bookingPostId > 0) {
			$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			$buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
			$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
			
			$buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
			$contact                    = $buchungKopf->getContactArray();

			$file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_invoice", $bookingPostId);
			$mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_deposit_reminder_subject');
			$to                         = $contact['email'];
			$subject                    = $mail_confirm_subject; //"test indiebooking";
			$message                    = get_option('rs_indiebooking_settings_mail_deposit_reminder_txt');
			if ($message === false || is_null($message) || strlen($message) <= 0) {
				$message                = __("Thanks for Booking", 'indiebooking');
			} else {
				$message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
				$message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
				$message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
				$message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
				$message                = str_replace('&nbsp;', "<br />", $message);
			}
			$header                     = "Content-type: text/html";
			$attachments				= rs_indiebooking_getAllMailAttachments();
			if (sizeof($attachments) > 0) {
				array_push($attachments, $file);
			} else {
				$attachments            = $file;
			}
			
			rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
		}
	}
}

if ( ! function_exists( 'rs_ib_sendBuchungRechnung' ) ) {
    function rs_ib_sendBuchungRechnung($bookingPostId) {
        global $RSBP_DATABASE;
    
        if ($bookingPostId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
    
            $buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
            $contact                    = $buchungKopf->getContactArray();
            //             $contact                    = $buchung->getContact();
            //             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
            //             $file                       = printBooking($buchung);
            $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_invoice", $bookingPostId);
            $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_invoice_subject');
            $to                         = $contact['email'];
            $subject                    = $mail_confirm_subject; //"test indiebooking";
            $message                    = get_option('rs_indiebooking_settings_mail_booking_invoice_txt');
            if ($message === false || is_null($message) || strlen($message) <= 0) {
                $message                = __("Thanks for Booking", 'indiebooking');
            } else {
                $message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
                $message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
                $message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
                $message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
                $message                = str_replace('&nbsp;', "<br />", $message);
            }
            $header                     = "Content-type: text/html";
            $attachments				= rs_indiebooking_getAllMailAttachments();
            if (sizeof($attachments) > 0) {
            	array_push($attachments, $file);
            } else {
            	$attachments            = $file;
            }
            
            rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
//             $mailmsg                    = "";
//             $mailReturn                 = wp_mail($to, $subject, $message, $header, $attachments);
//             if ($mailReturn) {
//                 $mailmsg    = "mail wurde versandt - ".$bookingPostId." ".$to;
//             } else {
//                 $mailmsg    = "Fehler beim mail versandt - ".$bookingPostId." ".$to;
//             }
//             RS_Indiebooking_Log_Controller::write_log(
//                 $mailmsg,
//                 __LINE__, __CLASS__,
//                 RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
//             $attachments                = $file;
//             wp_mail($to, $subject, $message, $header, $attachments);
        }
    }
}


if ( ! function_exists( 'rs_ib_sendStornobestaetigung' ) ) {
    function rs_ib_sendStornobestaetigung($bookingPostId) {
        global $RSBP_DATABASE;

        if ($bookingPostId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);

            $buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
            $contact                    = $buchungKopf->getContactArray();
            //             $contact                    = $buchung->getContact();
            //             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
            //             $file                       = printBooking($buchung);
            $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_storno", $bookingPostId);
            $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_storno_subject');
            $to                         = $contact['email'];
            $subject                    = $mail_confirm_subject; //"test indiebooking";
            $message                    = get_option('rs_indiebooking_settings_mail_storno_confirmation_txt');
            if ($message === false || is_null($message) || strlen($message) <= 0) {
                $message                = __("In the appendix you will find your cancellation confirmation.", 'indiebooking');
            } else {
                $message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
                $message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
                $message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
                $message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
                $message                = str_replace('&nbsp;', "<br />", $message);
            }
            $header                     = "Content-type: text/html";
            $attachments				= rs_indiebooking_getAllMailAttachments();
            if (sizeof($attachments) > 0) {
            	array_push($attachments, $file);
            } else {
            	$attachments            = $file;
            }
            
            $mailReturn                 = rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
            
            if ($mailReturn) {
                rs_ib_sendInfoMailToAdmin($bookingPostId, $subject, $message, $file);
            }
//             $mailmsg                    = "";
//             $mailReturn                 = wp_mail($to, $subject, $message, $header, $attachments);
//             if ($mailReturn) {
//                 $mailmsg    = "mail wurde versandt - ".$bookingPostId." ".$to;
//             } else {
//                 $mailmsg    = "Fehler beim mail versandt - ".$bookingPostId." ".$to;
//             }
//             RS_Indiebooking_Log_Controller::write_log(
//                 $mailmsg,
//                 __LINE__, __CLASS__,
//                 RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
//             $attachments                = $file;
//             wp_mail($to, $subject, $message, $header, $attachments);
        }
    }
}


if ( ! function_exists( 'rs_ib_sendAnfrageablehnung' ) ) {
	function rs_ib_sendAnfrageablehnung($bookingPostId) {
		global $RSBP_DATABASE;

		if ($bookingPostId > 0) {
			$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			$buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
			$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);

			$buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
			$contact                    = $buchungKopf->getContactArray();
			
			$file                       = apply_filters("rs_indiebooking_print_rsappartment_cancel_inquiry", $bookingPostId);
			$mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_inquiry_deny_subject');
			$to                         = $contact['email'];
			$subject                    = $mail_confirm_subject; //"test indiebooking";
			$message                    = get_option('rs_indiebooking_settings_mail_inquiry_deny_txt');
			if ($message === false || is_null($message) || strlen($message) <= 0) {
				$message                = __("Your inquiry was denied", 'indiebooking');//__("Thanks for Booking", 'indiebooking');
			} else {
				$message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
				$message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
				$message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
				$message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
				$message                = str_replace('&nbsp;', "<br />", $message);
			}
			$header                     = "Content-type: text/html";
			$attachments				= rs_indiebooking_getAllMailAttachments();
			if (sizeof($attachments) > 0) {
				array_push($attachments, $file);
			} else {
				$attachments            = array();
			}
			rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
// 			wp_mail($to, $subject, $message, $header, $attachments);
// 			$attachments                = array();
// 			wp_mail($to, $subject, $message, $header, $attachments);
		}
	}
}


if ( ! function_exists( 'rs_ib_sendZahlungsbestaetigung' ) ) {
    function rs_ib_sendZahlungsbestaetigung($bookingPostId) {
        global $RSBP_DATABASE;
    
        if ($bookingPostId > 0) {
            $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungsKopfTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($bookingPostId);
    
            $buchungKopf                = $buchungsKopfTable->loadBooking($buchung->getBuchungKopfId(), false);
            $contact                    = $buchungKopf->getContactArray();
            //             $contact                    = $buchung->getContact();
            //             $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
            //             $file                       = printBooking($buchung);
            $file                       = apply_filters("rs_indiebooking_print_rsappartment_buchung_payment_confirmation", $bookingPostId);
            $mail_confirm_subject       = get_option('rs_indiebooking_settings_mail_invoice_subject');
            $to                         = $contact['email'];
            $subject                    = $mail_confirm_subject; //"test indiebooking";
            $message                    = get_option('rs_indiebooking_settings_mail_booking_invoice_txt');
            if ($message === false || is_null($message) || strlen($message) <= 0) {
                $message                = __("Thanks for Booking", 'indiebooking');
            } else {
                $message                = str_replace('$$__BOOKINGNR__$$', $buchungKopf->getBuchung_nr(), $message);
                $message                = str_replace('$$__SALUTATION__$$', $contact['anrede'], $message);
                $message                = str_replace('$$__TITLE__$$', $contact['title'], $message);
                $message                = str_replace('$$__CUSTOMER__$$', $contact['firstName']." ".$contact['name'], $message);
                $message                = str_replace('&nbsp;', "<br />", $message);
            }
            $header                     = "Content-type: text/html";
            $attachments				= rs_indiebooking_getAllMailAttachments();
            if (sizeof($attachments) > 0) {
            	array_push($attachments, $file);
            } else {
            	$attachments            = $file;
            }
            $mailReturn                 = rs_ib_indiebooking_send_mail($to, $subject, $message, $header, $attachments);
            if ($mailReturn) {
                rs_ib_sendInfoMailToAdmin($bookingPostId, $subject, $message, $file);
            }
//             wp_mail($to, $subject, $message, $header, $attachments);
//             $attachments                = $file;
//             wp_mail($to, $subject, $message, $header, $attachments);
        }
    }
}
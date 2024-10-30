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


function rs_indiebooking_doFileNameToAscii($fileName) {
	return strtr(utf8_decode($fileName),
			utf8_decode('Å Å’Å½Å¡Å“Å¾Å¸Â¥ÂµÃ€Ã�Ã‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃ�ÃŽÃ�Ã�Ã‘Ã’Ã“Ã”Ã•Ã–Ã˜Ã™ÃšÃ›ÃœÃ�ÃŸÃ Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã¸Ã¹ÃºÃ»Ã¼Ã½Ã¿'),
			'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy');
}

/**
 * ACTIONS
 */

if ( ! function_exists( 'rs_indiebooking_print_test_invoice' ) ) {
	/* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
	function rs_indiebooking_print_test_invoice() {
		global $RSBP_DATABASE;
		//$bookingId, $orderType
	
		$template						= "";
		$printData						= new rs_ib_print_util_data_object();
		$waehrung      					= rs_ib_currency_util::getCurrentCurrency();
	
		$options              			= get_option( 'rs_indiebooking_settings' );
		$companyName          			= esc_attr__( $options['company_name']);
		$companyStreet        			= esc_attr__( $options['company_street']);
		$company_zip_code    	 		= esc_attr__( $options['company_zip_code']);
		$companyLocation      			= esc_attr__( $options['company_location']);
	
		$printData->setCompanyName($companyName);
		$printData->setCompanyStreet($companyStreet);
		$printData->setCompanyZipCode($company_zip_code);
		$printData->setCompanyLocation($companyLocation);
	
		$oberbuchungObj             	= null;
		$buchungKopfTable           	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
		$buchungsKopf					= $buchungKopfTable->getTestDataBuchungskopf(); //new RS_IB_Model_Buchungskopf();
		
		$printData->setWaehrung($waehrung);
		$printData->setBuchungNr(4711);
		$printData->setBuchungsKopf($buchungsKopf);
		$printData->setOberBuchungskopf($oberbuchungObj);
		$printData->setContactDataFromArray($buchungsKopf->getContactArray());
	
		$return							= null;
		$ueberschrift       			= __('Booking confirmation', 'indiebooking');
		$fileName       				= __('Booking_Invoice', 'indiebooking') . '#' . $buchungsKopf->getBuchung_nr();
		$printData->setFileName($fileName);
		$printData->setUeberschrift($ueberschrift);
		$template						= 'rs_ib_print/print_booking_invoice.php';

		$args = array(
				'buchungKopf'   => $buchungsKopf,
				'oberbuchung'   => $oberbuchungObj,
				'printDataObj'	=> $printData,
		);
	
		rs_ib_print_util::startPrintHtmlPage();
		cRS_Template_Loader::rs_ib_get_template($template, $args);
		$return								=  rs_ib_print_util::endPrintHtmlPage($printData);
	
		return $return;
	}
}

if (!function_exists('rs_indiebooking_print_rsappartment_buchung_confirmation_by_invoicenr')) {
	function rs_indiebooking_print_rsappartment_buchung_confirmation_by_invoicenr($invoiceNr, $dest = "", $createNewVersion = true, $storno = false, $buchungsNrToCopy = 0) {
		rs_indiebooking_print_by_order_type($invoiceNr, $dest, $createNewVersion, true, $storno, $buchungsNrToCopy);
	}
}

/* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
if ( ! function_exists( 'rs_indiebooking_print_by_order_type' ) ) {
	function rs_indiebooking_print_by_order_type($bookingId, $orderType, $createNewVersion = true, $byInvoice = false, $storno = false, $buchungsNrToCopy = 0) {
		global $RSBP_DATABASE;
		
		if ( function_exists('icl_object_id') ) {
// 			global $sitepress;
// 			$my_current_lang = apply_filters( 'wpml_current_language', NULL );
// 			RS_Indiebooking_Log_Controller::write_log("currentLanguage ".$my_current_lang, __LINE__, __CLASS__);
// 			do_action( 'wpml_switch_language', $my_current_lang);
// 			$sitepress->switch_lang($my_current_lang);
// 			$ueberschrift       	= __('Invoice', 'indiebooking');
// 			RS_Indiebooking_Log_Controller::write_log("ueberschrift: ".$ueberschrift, __LINE__, __CLASS__);
		}
		
		$template						= "";
		$printData						= new rs_ib_print_util_data_object();
		$waehrung      					= rs_ib_currency_util::getCurrentCurrency();
		
		$options              			= get_option( 'rs_indiebooking_settings' );
		$companyLocation 				= "";
		$companyStreet       			= "";
		$companyName     				= "";
		$company_zip_code				= "";
		if ($options != false) {
			$companyLocation      		= (key_exists('company_location', $options)) 	? esc_attr__( $options['company_location'] ) 	: "";
			$companyStreet            	= (key_exists('company_street', $options)) 		? esc_attr__( $options['company_street'] ) 		: "";
			$companyName          		= (key_exists('company_name', $options)) 		? esc_attr__( $options['company_name'] ) 		: "";
			$company_zip_code			= (key_exists('company_zip_code', $options)) 	? esc_attr__( $options['company_zip_code'] ) 	: "";
		}
		
		$printData->setCompanyName($companyName);
		$printData->setCompanyStreet($companyStreet);
		$printData->setCompanyZipCode($company_zip_code);
		$printData->setCompanyLocation($companyLocation);
		
		$oberbuchungObj             	= null;
		$appartmentBuchungsTable    	= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
		$buchungKopfTable           	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
		$appartmentTable            	= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
		
		$buchung						= null;
		if (!$byInvoice) {
			$buchung                    	= $appartmentBuchungsTable->getAppartmentBuchung($bookingId);
			$appartment                 	= $appartmentTable->getAppartment($buchung->getAppartment_id());
			
			$buchung                    	= $appartmentBuchungsTable->getPositions($buchung);
			$buchungKopfId              	= $buchung->getBuchungKopfId();
			$buchungKopf                	= $buchungKopfTable->loadBooking($buchungKopfId);
		} else {
			$buchungKopf                	= $buchungKopfTable->loadBookingByInvoiceNumber($bookingId);
		}
		
		if ($buchungKopf->getBuchung_status() == "rs_ib-storno" || $buchungKopf->getBuchung_status() == "rs_ib-storno_paid" ||$storno) {
			$oberbuchungTable			= $RSBP_DATABASE->getTable(RS_IB_Model_Oberbuchungkopf::RS_TABLE);
			$oberbuchungObj     		= $oberbuchungTable->loadBookingByRechnungnr($buchungKopf->getRechnung_nr());
		}
		
		$printData->setWaehrung($waehrung);
		$printData->setBuchungNr($bookingId);
		$printData->setBuchungsKopf($buchungKopf);
		$printData->setOberBuchungskopf($oberbuchungObj);
		$printData->setContactDataFromArray($buchungKopf->getContactArray());
		$printData->setCreateNewVersion($createNewVersion);
		
		$return							= null;
		if ($orderType != "INQUIRY_CONFIRMATION" && $orderType != "INQUIRY_CANCELED" && $buchungKopf->getRechnung_nr() <= 0) {
			$appartmentBuchungsTable->createAndSaveBillNumber($buchungKopf);
			$printData->setFirstBillPrint(true);
		}
// 				$options                    = get_option( 'rs_indiebooking_settings' );
		switch ($orderType) {
			case "PAYMENT_CONFIRMATION":
				//Nur wenn die Zahlung durch die Administration bestaetigt wurde, soll das Dokument mit Zahlungsbestaetigung
				//ueberschrieben sein, ansonsten mit Rechnung
				if ($buchungKopf->getAdminKz() == 0) {
					$ueberschrift       	= __('Invoice', 'indiebooking');
					$fileName       		= __('Invoice', 'indiebooking') . '#' . $buchungKopf->getRechnung_nr();
				} else {
					$ueberschrift       	= __('Payment confirmation', 'indiebooking');
					$fileName       		= __('Payment_Confirmation', 'indiebooking') . '#' . $buchungKopf->getRechnung_nr();
				}
// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$template				= 'rs_ib_print/print_payment_confirmation.php';
				
				break;
			case "BOOKING_INVOICE":
// 				$ueberschrift       	= __('Booking confirmation', 'indiebooking');
// 				$fileName       		= __('Booking_Invoice', 'indiebooking') . '#' . $buchungKopf->getBuchung_nr(); //$buchung->getPostId();
				$ueberschrift       	= __('Invoice', 'indiebooking');
				$fileName       		= __('Invoice', 'indiebooking') . '#' . $buchungKopf->getRechnung_nr();
// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$template				= 'rs_ib_print/print_booking_invoice.php';
				
				break;
			case "BOOKING_STORNO":
// 				$ueberschrift       	= __('Storno confirmation', 'indiebooking');
				$ueberschrift			= __('invoice adjustment', 'indiebooking');
				if (!$byInvoice) {
					$fileName       		= __('Storno_Confirmation', 'indiebooking') . '#' . $buchung->getPostId();
				} else {
					$fileName       		= __('Storno_Confirmation', 'indiebooking') . '#' . $bookingId;
				}
// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$printData->setBuchungsNrToCopy($buchungsNrToCopy);
				$template				= 'rs_ib_print/print_booking_confirmation.php';
				break;
			case "INQUIRY_CONFIRMATION":
				$ueberschrift       	= __('Inquiry confirmation', 'indiebooking');
				$fileName       		= __('Inquiry_Confirmation', 'indiebooking') . '#' . $buchungKopf->getBuchung_nr(); //$buchung->getPostId();
// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$template				= 'rs_ib_print/print_inquiry_confirmation.php';
				
				break;
			case "INQUIRY_CANCELED":
				$ueberschrift       	= __('Inquiry rejected', 'indiebooking');
				$fileName       		= __('Inquiry_rejected', 'indiebooking') . '#' . $buchungKopf->getBuchung_nr(); //$buchung->getPostId();
				// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$template				= 'rs_ib_print/print_inquiry_confirmation.php';
				
				break;
			case "BOOKIONG_CONFIRMATION":
// 				if ($buchungKopf->getAdminKz() == 0) {
					$ueberschrift       	= __('Invoice', 'indiebooking');
					$fileName       		= __('Invoice', 'indiebooking') . '#' . $buchungKopf->getRechnung_nr();
// 				} else {
// 					$ueberschrift       	= __('Payment confirmation', 'indiebooking');
// 					$fileName       		= __('Payment_Confirmation', 'indiebooking') . '#' . $buchungKopf->getRechnung_nr();
// 				}
// 				$ueberschrift       	= __('Booking confirmation', 'indiebooking');
// 				$fileName       		= __('Booking_Confirmation', 'indiebooking') . '#' . $buchungKopf->getBuchung_nr(); //$buchung->getPostId();
// 				$fileName				= rs_indiebooking_doFileNameToAscii($fileName);
				
				$printData->setFileName($fileName);
				$printData->setUeberschrift($ueberschrift);
				$template				= 'rs_ib_print/print_booking_confirmation.php';
				
				break;
		}
		
		$args = array(
			'buchungKopf'   => $buchungKopf,
			'oberbuchung'   => $oberbuchungObj,
			'printDataObj'	=> $printData,
		);
		
		rs_ib_print_util::startPrintHtmlPage();
		cRS_Template_Loader::rs_ib_get_template($template, $args);
		$return					=  rs_ib_print_util::endPrintHtmlPage($printData);
		
		return $return;
	}
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_payment_confirmation' ) ) {
    function rs_indiebooking_print_rsappartment_buchung_payment_confirmation($bookingId) {
    	return rs_indiebooking_print_by_order_type($bookingId, "PAYMENT_CONFIRMATION");
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_invoice' ) ) {
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    function rs_indiebooking_print_rsappartment_buchung_invoice($bookingPostId) {
    	return rs_indiebooking_print_by_order_type($bookingPostId, "BOOKING_INVOICE");
    }
}

//aus welchen Grund auch immer, probleme mit irgend einem zeichen
if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_storno' ) ) {
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    function rs_indiebooking_print_rsappartment_buchung_storno($bookingId) {
    	return rs_indiebooking_print_by_order_type($bookingId, "BOOKING_STORNO");
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_cancel_inquiry' ) ) {
	/* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
	function rs_indiebooking_print_rsappartment_cancel_inquiry($bookingId) {
		return rs_indiebooking_print_by_order_type($bookingId, "INQUIRY_CANCELED");
	}
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_inquiry_confirmation' ) ) {
	/* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
	function rs_indiebooking_print_rsappartment_inquiry_confirmation($bookingPostId, $dest = "") {
		return rs_indiebooking_print_by_order_type($bookingPostId, "INQUIRY_CONFIRMATION");
	}
}



if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_confirmation' ) ) {
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    function rs_indiebooking_print_rsappartment_buchung_confirmation($bookingPostId, $dest = "", $createNewVersion = true) {
        if ($dest !== "BOOKING_STORNO") {
        	return rs_indiebooking_print_by_order_type($bookingPostId, "BOOKIONG_CONFIRMATION", $createNewVersion);
        } else {
        	return rs_indiebooking_print_by_order_type($bookingPostId, $dest, $createNewVersion);
        }
    }
    
}

if (! function_exists( 'rs_indiebooking_print_rsappartment_test_buchung_confirmation' ) ) {
	function rs_indiebooking_print_rsappartment_test_buchung_confirmation() {
		return rs_indiebooking_print_test_invoice();
	}
}


/*
 * PRINT PARTS
 */
// if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_header' ) ) {
//     function rs_indiebooking_print_rsappartment_buchung_header($buchungObj) {
//         $args = array(
//             'buchungObj'    => $buchungObj,
//         );
//         cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_header.php', $args);
//     }
// }


if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_time_range' ) ) {
    function rs_indiebooking_print_rsappartment_buchung_time_range($buchungObj) {
        $args = array(
            'buchungObj'    => $buchungObj,
        );
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_time_range.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_contact' ) ) {
	/* @var $printDataObj rs_ib_print_util_data_object */
    function rs_indiebooking_print_rsappartment_buchung_contact($printDataObj) {
//         global $RSBP_DATABASE;

//         $options              = get_option( 'rs_indiebooking_settings' );
//         $companyName          = esc_attr__( $options['company_name']);
//         $companyStreet        = esc_attr__( $options['company_street']);
//         $company_zip_code     = esc_attr__( $options['company_zip_code']);
//         $companyLocation      = esc_attr__( $options['company_location']);
        
//         $args = array(
//             'contact'           => $contact,
//             'companyName'       => $companyName,
//             'companyStreet'     => $companyStreet,
//             'company_zip_code'  => $company_zip_code,
//             'companyLocation'   => $companyLocation,
//         );
    	$args = array(
    		'printDataObj' => $printDataObj,
    	);
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_contact.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_options' ) ) {
    function rs_indiebooking_print_rsappartment_buchung_options($optionPositions) {
        $args = array(
            'optionPositions'    => $optionPositions,
        );
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_options.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_detailed_payment_info' ) ) {
	/* @var $printDataObj rs_ib_print_util_data_object */
    function rs_indiebooking_print_rsappartment_buchung_detailed_payment_info($printDataObj) {
        $options              	= get_option( 'rs_indiebooking_settings' );
        $companyLocation 		= "";
        $dankeText       		= "";
        $companyName     		= "";
        if ($options != false) {
        	$companyLocation      	= (key_exists('company_location', $options)) ? esc_attr__( $options['company_location'] ) : "";
        	$dankeText            	= (key_exists('thankstxt', $options)) 		? esc_attr__( $options['thankstxt'] ) 		: "";
	        $companyName          	= (key_exists('company_name', $options)) 	? esc_attr__( $options['company_name'] ) 	: "";
        }
        
        $buchungKopf			= $printDataObj->getBuchungsKopf();
        $oberbuchung			= $printDataObj->getOberBuchungskopf();
        
        $bankData               = get_option( 'rs_indiebooking_settings_bankdata' );
        /* *****************************************************************
         ******************BANK DATEN AUSLESEN*****************************
         *******************************************************************/
        $printBankData			= array();
        if ($bankData) {
        	$bankName           = (key_exists('bank_name', $bankData))      ?  esc_attr__( $bankData['bank_name'] )     : "";
        	$iban               = (key_exists('bank_iban', $bankData))      ?  esc_attr__( $bankData['bank_iban'] )     : "";
        	$bic                = (key_exists('bank_bic', $bankData))       ?  esc_attr__( $bankData['bank_bic'] )      : "";
        	$kontoInhaber       = (key_exists('bank_account', $bankData))   ?  esc_attr__( $bankData['bank_account'] )  : "";
        	$printBankData['companyname']	= $companyName;
        	$printBankData['bank_name'] 	= $bankName;
        	$printBankData['bank_iban'] 	= $iban;
        	$printBankData['bank_bic'] 		= $bic;
        	$printBankData['bank_account'] 	= $kontoInhaber;
        }
        
        
        $args = array(
            'buchungKopf'       => $buchungKopf,
            'companyLocation'   => $companyLocation,
            'oberbuchung'       => $oberbuchung,
            'dankeText'         => $dankeText,
        	'printBankData'		=> $printBankData,
        	'dataUeberschrift'	=> $printDataObj->getUeberschrift(),
        	'firstBillPrint'	=> $printDataObj->getFirstBillPrint(),
        );
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_detailed_payment_info.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_print_rsappartment_buchung_full_price_info' ) ) {
    function rs_indiebooking_print_rsappartment_buchung_full_price_info($buchungObj) {
        $args = array(
            'buchungObj'    => $buchungObj,
        );
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_full_price.php', $args);
    }
}

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
/**
 * ACTIONS
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

// if ( ! class_exists( 'RS_IB_Template_Buchungsanzeige' ) ) :
/**
 * In dieser Klasse sind alle Methoden gesammelt, die zur Anzeige einer Buchung (egal ob 1 oder mehrere Appartments)
 * benuetigt werden.
 *
 * @author schmitt
 *
 */
add_action("rs_indiebooking_show_apartment_map", array('RS_IB_Template_Buchungsanzeige', "show_appartment_map"),15, 2);
add_action("rs_indiebooking_show_ongoing_booking_popup", array('RS_IB_Template_Buchungsanzeige', "show_ongoing_booking_popup"));
class RS_IB_Template_Buchungsanzeige
{
    public static function show_default_appartment_header($appartment_id) {
        global $RSBP_DATABASE;
        
        $appartmentTable                = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartmentBuchungsTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $mwstTable                      = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);

        $appartment                     = $appartmentTable->getAppartment($appartment_id);
        $allMwst                        = $mwstTable->getAllMwsts();
        
        foreach ($allMwst as $appMwSt) {
            if ($appMwSt->getMwstId() == $appartment->getMwstId()) {
                $appartmentMwst         = $appMwSt->getMwstValue();
                break;
            }
        }
        ?>
        <!-- <input class='rs_zabuto-calendar_data' type="hidden" data-appartmentId='<?php //$appartment_id; ?>' data-bookable='<?php //echo $bookableDates ?>' data-booked='<?php //echo $bookedDates?>' /> -->
        
        <input class="price_per_night" data-isNet="<?php echo $appartment->getPriceIsNet();?>"
        		data-mwst="<?php echo $appartmentMwst;?>"
        		type="hidden" value="<?php echo $appartment->getPreis();?>" />
        <input class="apartemtn_square_meter" type="hidden" value="<?php echo $appartment->getQuadratmeter();?>" />
        <?php
    }

    public static function show_ongoing_booking_popup() {
    	$post = get_post();
    	if (!is_null($post) && $post->post_type != 'rsappartment_buchung') {
    		/*
    		 * Wir befinden uns nicht mehr in der Buchung
    		 */
    		if (key_exists('indiebooking_currentBookingNr', $_SESSION)) {
    			if ($_SESSION['indiebooking_currentBookingNr'] != '0') {
    				global $RSBP_DATABASE;
    				
    				$currentPostId 				= $_SESSION['indiebooking_currentBookingPostId'];
    				$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    				$buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($currentPostId);
    				$remainingTime              = $buchung->getRemainingtTime();
    				
    				if ($remainingTime > 0) {
	    				$postStatus					= get_post_status($currentPostId);
	    				if ($postStatus == 'rs_ib-blocked' || $postStatus == 'rs_ib-booking_info' || $postStatus == 'rs_ib-almost_booked') {
	    					$permalink 				= get_permalink($currentPostId);
	    					?>
							<div id="rs_indiebooking_ongoing_booking_info_wrp">
						        <div id="rs_indiebooking_ongoing_booking_info_overlay"></div>
						        <div id="rs_indiebooking_ongoing_booking_info_banner">
						        	<div class="background_lightgreen padding_all">
<!-- 							            <button class="rs_indiebooking_ongoing_booking_info_close"></button> -->
										<div class="countdownbox">
											<input id="bookingPostId" value="<?php echo $currentPostId; ?>" style="display: none;" ></input>
									        <div class="countdown">
									        	<?php //_e("Remaining time to complete the booking", 'indiebooking');?>
									        	<?php _e("You still have an active booking", 'indiebooking');?>
									        </div>
									        <br />
<!-- 									        <div id="countdownBooking_2" class="countdown"> -->
<!-- 									        	<script> -->
									        		<!-- window.indiebookingCountdownController.countdown(<?php //echo $remainingTime; ?>,'countdownBooking_2');jQuery(".countdown").show(); -->
<!-- 									        	</script> -->
<!-- 									        </div> -->
											<div class="countdown rs_indiebooking_popup_button_box">
												<a class="btn lightgreen btnCancelBooking btnCancelBookingFromOnGoingPopup" style="margin-right:15px;">abbrechen</a>
												<a class="btn green"href="<?php echo $permalink?>"><?php _e("back to booking", "indiebooking"); ?></a>
											</div>
									    </div>
									</div>
						        </div>
						    </div>
							<?php
						} else {
							$_SESSION['indiebooking_currentBookingNr'] 		= '0';
							$_SESSION['indiebooking_currentBookingPostId'] 	= '0';
						}
    				} else {
    					$_SESSION['indiebooking_currentBookingNr'] 			= '0';
    					$_SESSION['indiebooking_currentBookingPostId'] 		= '0';
    				}
				}
			}
		}
    }
    
    
    
    /* @var $appartment RS_IB_Model_Appartment */
    public static function show_appartment_smalles_price($appartment_id) {
        global $RSBP_DATABASE;
        
        $appartmentTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $appartment         = $appartmentTable->getAppartment($appartment_id, true);
        $buchbar            = true;
        
//         if ($appartment->getPreis() == "" || $appartment->getPreis() <= 0) {
//             //kein defaultpreis gepflegt
//             if (sizeof($appartment->getYearlessPriceDates()) <= 0) {
//                 $buchbar    = false;
//             }
//         }
//         if (sizeof($appartment->getOffenZeitraumeDB()) <= 0) {
//             $buchbar        = true;
//         }
        $bookPrice          = $appartment->getSmalestPrice();
        if ($bookPrice != "") {
            $bookPrice      = str_replace(".", ",", number_format($bookPrice, 2));
        } else {
            //wenn der kleinste ermittelte Preis ein Leerstring ist, sind keine Preise
            // fuer das Apartment gepflegt. Also kann das Apartment nicht gebucht werden.
            $buchbar        = false;
        }
        $args = array(
            'smallesprice'  => $bookPrice,
            'waehrung'      => rs_ib_currency_util::getCurrentCurrency(),
            'istBuchbar'    => $buchbar,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_smallestPrice.php', $args);
    }
    
    public static function show_single_appartment_from_to_dates($appartment_id, $buchungVon, $buchungBis) {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($appartment_id);
        
        $bookedDates                = $buchungTable->getBuchungszeitraeumeByAppartmentId($appartment_id);
        $bookableDates              = json_encode($appartment->getBookableDates());
        $bookableDatesEng           = json_encode($appartment->getZeitraumeDB());
        $bookedDates                = json_encode($bookedDates);
        $arrivalDays                = json_encode($appartment->getArrivalDays());
        $notBookableDates           = json_encode($appartment->getNotbookableDates());
        $minnaechte                 = json_encode($appartment->getMinDateRange());
        
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $curMaxDate					= new DateTime("now");
        $addYears					= "P".$futureAvailabilityYear."Y";
        $curMaxDate->add(new DateInterval($addYears));
        $curMaxDate					= $curMaxDate->format("Y-m-d");
        
        $args = array(
            'bookableDates'     => $bookableDates,
            'bookableDatesEng'  => $bookableDatesEng,
            'bookedDates'       => $bookedDates,
            "buchungVon"        => $buchungVon,
            "buchungBis"        => $buchungBis,
            'notBookableDates'  => $notBookableDates,
            'arrivalDays'       => $arrivalDays,
            'minnaechte'        => $minnaechte,
        	'curMaxDate'		=> $curMaxDate,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment/appartment_from_to_dates.php', $args);
    }
    
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    public static function show_appartment_from_to_dates($appartment_id) {
        global $RSBP_DATABASE;
        
        $buchungNr					= 0;
        $buchungVon                 = "";
        $buchungBis                 = "";
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $appartment                 = $appartmentTable->getAppartment($appartment_id);
        $arrivalDays                = $appartment->getArrivalDays();
        
        
        $bookableDates              = json_encode($appartment->getBookableDates());
        $bookableDatesEng           = json_encode($appartment->getZeitraumeDB());
        $minnaechte                 = json_encode($appartment->getMinDateRange());
        
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $curMaxDate					= new DateTime("now");
        $addYears					= "P".$futureAvailabilityYear."Y";
        $curMaxDate->add(new DateInterval($addYears));
        $curMaxDate					= $curMaxDate->format("Y-m-d");
        
        $postType                   = get_post_type( get_the_ID() );
        if ($postType == "rsappartment_buchung") {
            $buchungId              = get_the_ID();
            $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
            $buchungKopfTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $teilBuchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
            $buchung                = $buchungTable->getAppartmentBuchung($buchungId);

            $buchungTeilKopf        = $teilBuchungKopfTbl->loadBookingPartHeader($buchung->getBuchungKopfId(), $appartment_id);

            $buchungVon             = $buchungTeilKopf[0]->getTeilbuchung_von();
            $buchungBis             = $buchungTeilKopf[0]->getTeilbuchung_bis();
            $buchungNr				= $buchungTeilKopf[0]->getBuchung_nr();
            if (!$buchungVon instanceof DateTime) {
                $buchungVon         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_von()));
                $buchungBis         = (new Datetime($buchungTeilKopf[0]->getTeilbuchung_bis()));
            }
            $buchungVon             = $buchungVon->format("d.m.Y");
            $buchungBis             = $buchungBis->format("d.m.Y");
        }
        $bookedDates                = $buchungTable->getBuchungszeitraeumeByAppartmentId($appartment_id, false, $buchungNr);
        $bookedDates                = json_encode($bookedDates);
        $args = array(
            'appartment_id' 	=> $appartment_id,
            'buchungVon'    	=> $buchungVon,
            'buchungBis'    	=> $buchungBis,
            'arrivalDays'   	=> $arrivalDays,
            'bookableDatesEng'  => $bookableDatesEng,
            'bookedDates'       => $bookedDates,
        	'minnaechte'        => $minnaechte,
        	'curMaxDate'		=> $curMaxDate,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_from_to_dates.php', $args);
    }
    
    public static function show_appartment_map($appartment_id, $startseite) {
        // do nothing
    }
        
    public static function show_buchung_zahlungsarten($buchungPostId) {
        global $RSBP_DATABASE;

        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $buchungTable->getAppartmentBuchung($buchungPostId);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $zahlart                    = $buchungKopf->getHauptZahlungsart();
        
        $inquirie					= false;
        $bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
        if (isset($bookingInquiriesKz) && !is_null($bookingInquiriesKz) && $bookingInquiriesKz == "on") {
        	$inquirie				= true;
        }
        if (!$inquirie) {
        	$inquirie				= $buchungKopf->hasBookingInquiryApartments();
        }
        
        if (!isset($zahlart) || is_null($zahlart) || $zahlart == "") {
        	$defaultZahlart			= get_option( 'rs_indiebooking_settings_default_payment_method' );
        	if (!isset($defaultZahlart) || is_null($defaultZahlart) || $defaultZahlart == "") {
        		$defaultZahlart		= "INVOICE";
        	}
        	$zahlart				= $defaultZahlart;
        }
        $zahlart                    = strtoupper($zahlart);
        $paypalPlusKz		 		= "";
        $testpaypalKz 				= "";
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
        	$paypalData         = get_option( 'rs_indiebooking_settings_paypal');
        	if ($paypalData) {
        		$paypalPlusKz	= ""; //(key_exists('paypal_plus_kz', $paypalData)) ?  esc_attr__( $paypalData['paypal_plus_kz'] ) : "";
        		$testpaypalKz   = (key_exists('testpaypal_kz', $paypalData)) ? esc_attr__( $paypalData['testpaypal_kz'] ) : "";
        	}
        }
        
        $args = array(
            'buchungPostId' => $buchungPostId,
            'zahlart'       => $zahlart,
        	'testpaypalKz'	=> $testpaypalKz,
        	'paypalPlusKz' 	=> $paypalPlusKz,
        	'inquiry'		=> $inquirie,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_zahlungsarten.php', $args);
    }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    public static function show_buchung_zahlungsartbutton($buchungPostId) {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $buchungTable->getAppartmentBuchung($buchungPostId);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        
        $zahlart                    = $buchungKopf->getHauptZahlungsart();
        $inquirie					= false;
        $bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
        if (isset($bookingInquiriesKz) && !is_null($bookingInquiriesKz) && $bookingInquiriesKz == "on") {
        	$inquirie				= true;
        }
        if (!$inquirie) {
        	$inquirie				= $buchungKopf->hasBookingInquiryApartments();
        }
        $zahlart                    = strtoupper($zahlart);
        $agbText                    = get_option('rs_indiebooking_settings_booking_agb_txt');
        $agbAccepted                = $buchungKopf->getNutzungsbedinungKz();
        
        $paymentId					= "";
        $success					= "";
        $payPalToken				= "";
        $payPalPayerId				= "";
        if ("PAYPAL" == $zahlart || "PAYPALEXPRESS" == $zahlart) {
        	if (isset($_GET["paymentId"])) {
        		$paymentId = rsbp_getGetValue("paymentId", '', RS_IB_Data_Validation::DATATYPE_TEXT);
        	}
        	if ($paymentId != "") {
	        	if (isset($_GET["success"])) {
	        		$success = rsbp_getGetValue("success", '', RS_IB_Data_Validation::DATATYPE_TEXT);
	        	} elseif (isset($_GET["paypalexpresssuccess"])) {
	        		$success = rsbp_getGetValue("paypalexpresssuccess", '', RS_IB_Data_Validation::DATATYPE_TEXT);
	        	} elseif (isset($_GET["paypalsuccess"])) {
	        		$success = rsbp_getGetValue("paypalsuccess", '', RS_IB_Data_Validation::DATATYPE_TEXT);
	        	}
	        	if (isset($_GET["token"])) {
	        		$payPalToken = rsbp_getGetValue("token", '', RS_IB_Data_Validation::DATATYPE_TEXT);
	        	}
	        	if (isset($_GET["PayerID"])) {
	        		$payPalPayerId = rsbp_getGetValue("PayerID", '', RS_IB_Data_Validation::DATATYPE_TEXT);
	        	}
        	} else {
        		$ppmetadata 				= get_post_meta($buchungPostId, RS_IB_Model_Appartment_Buchung::BUCHUNG_PAYPALDATA);
        		if (sizeof($ppmetadata) > 0) {
	        		$paymentId				= $ppmetadata[0]["paymentid"];
	        		$success				= $ppmetadata[0]["success"];
	        		$payPalToken			= $ppmetadata[0]["paypaltoken"];
	        		$payPalPayerId			= $ppmetadata[0]["payerid"];
        		}
        	}
        }
        
        $args = array(
            'buchungPostId' => $buchungPostId,
            'zahlart'       => $zahlart,
            'agbText'       => $agbText,
            'agbAccepted'   => $agbAccepted,
        	'inquirie'		=> $inquirie,
        	'paymentId' 	=> $paymentId,
        	'success' 		=> $success,
        	'payPalToken' 	=> $payPalToken,
        	'payPalPayerId' => $payPalPayerId,
        );
        ?>
        <input type="hidden" id="rs_ib_buchung_zahlart" value="<?php echo strtoupper($buchungKopf->getHauptZahlungsart()); ?>">
        <?php
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_zahlungsart_button.php', $args);
    }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    /* @var $buchungKopfTable RS_IB_Table_Buchungskopf */
    public static function show_buchung_contact_data($postId, $disabled) {
        global $RSBP_DATABASE;
        
        if ( function_exists('icl_object_id') ) {
        	global $sitepress;
	        $my_current_lang = apply_filters( 'wpml_current_language', NULL );
	        do_action( 'wpml_switch_language',  $my_current_lang );
	        $sitepress->switch_lang($my_current_lang);
        }
        
        if (!$postId) {
            $postId                 = get_the_ID();
        }
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchungKopfTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        
        $buchung                    = $buchungTable->getAppartmentBuchung($postId);
//         $buchung                    = $buchungTable->getPositions($buchung);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        $contact                    = $buchungKopf->getContactArray();
        $zahlart					= $buchungKopf->getHauptZahlungsart();
        $paymentId                  = "";
        
        $success                    = "";
        if (isset($_GET["success"])) {
            $success = rsbp_getGetValue("success", '', RS_IB_Data_Validation::DATATYPE_TEXT);
        } elseif (isset($_GET['paypalexpresssuccess'])) {
            $success = rsbp_getGetValue("paypalexpresssuccess", '', RS_IB_Data_Validation::DATATYPE_TEXT);
        } elseif (isset($_GET['paypalsuccess'])) {
            $success = rsbp_getGetValue("paypalsuccess", '', RS_IB_Data_Validation::DATATYPE_TEXT);
        }
        if ("true" == $success && $zahlart == "PAYPALEXPRESS") {
            $contact                = apply_filters("rs_indiebooking_get_paypal_contact_data", "");
            $buchungKopfTable->updateBuchungsKontakt($buchungKopf, $contact);
        }
        
        
        $errFirma		= __("The company Data is invalid", 'indiebooking');
        $errAbteilung	= __("The department Data is invalid", 'indiebooking');
        $errTitle       = __("The title Data is invalid", 'indiebooking');
        $errAnrede      = __("The salutation Data is invalid", 'indiebooking');
        $errName        = __("The Name Data is invalid", 'indiebooking');
        $errFirstName   = __("The First Name Data is invalid", 'indiebooking');
        $errEmail       = __("The Email Data is invalid", 'indiebooking');
        $errStreet      = __("The Street Data is invalid", 'indiebooking');
        $errZip         = __("The Zip Data is invalid", 'indiebooking');
        $errLocation    = __("The Location Data is invalid", 'indiebooking');
        $errTelefon     = __("The Telefon Data is invalid", 'indiebooking');
        $errHausnr      = __("The Nr is invalid (max 10 character)", 'indiebooking');
        $errCountry     = __("The Country is invalid", 'indiebooking');
        $firma          = "";
        $title          = "";
        $anrede         = "";
        $lastName       = "";
        $firstName      = "";
        $email          = "";
        $street         = "";
        $zipCode        = "";
        $location       = "";
        $telefon        = "";
        $country        = "";
        $strasseNr      = "";
        $department		= "";
        
        $useAdress2     = 0;
        $firma2         = "";
        $title2         = "";
        $anrede2        = "";
        $lastName2      = "";
        $firstName2     = "";
        $email2         = "";
        $street2        = "";
        $zipCode2       = "";
        $location2      = "";
        $telefon2       = "";
        $country2       = "";
        $strasseNr2     = "";
        $department2	= "";
        
//         var_dump($contact);
        if (!isset($result)) {
            $result     = "";
        }
        if (isset($contact) && !is_null($contact)) {
            if (array_key_exists('name', $contact)) {
                $lastName = $contact['name'];
            }
            if (array_key_exists('firstName', $contact)) {
                $firstName = $contact['firstName'];
            }
            if (array_key_exists('strasse', $contact)) {
                $street = $contact['strasse'];
            }
            if (array_key_exists('ort', $contact)) {
                $location = $contact['ort'];
            }
            if (array_key_exists('plz', $contact)) {
                $zipCode = $contact['plz'];
            }
            if (array_key_exists('email', $contact)) {
                $email = $contact['email'];
            }
            if (array_key_exists('telefon', $contact)) {
                $telefon = $contact['telefon'];
            }
            if (array_key_exists('title', $contact)) {
                $title = $contact['title'];
            }
            if (array_key_exists('country', $contact)) {
                $country = $contact['country'];
            }
            if (array_key_exists('strasseNr', $contact)) {
                $strasseNr = $contact['strasseNr'];
            }
            if (array_key_exists('firma', $contact)) {
                $firma = $contact['firma'];
            }
            if (array_key_exists('abteilung', $contact)) {
            	$department = $contact['abteilung'];
            }
            
            if (array_key_exists('useAdress2', $contact)) {
                $useAdress2 = $contact['useAdress2'];
            }
            if (array_key_exists('name2', $contact)) {
                $lastName2 = $contact['name2'];
            }
            if (array_key_exists('firstName2', $contact)) {
                $firstName2 = $contact['firstName2'];
            }
            if (array_key_exists('strasse2', $contact)) {
                $street2 = $contact['strasse2'];
            }
            if (array_key_exists('ort2', $contact)) {
                $location2 = $contact['ort2'];
            }
            if (array_key_exists('plz2', $contact)) {
                $zipCode2 = $contact['plz2'];
            }
            if (array_key_exists('email2', $contact)) {
                $email2 = $contact['email2'];
            }
            if (array_key_exists('telefon2', $contact)) {
                $telefon2 = $contact['telefon2'];
            }
            if (array_key_exists('title2', $contact)) {
                $title2 = $contact['title2'];
//                 if ($title2 == "dummy") {
//                 	$title2 = "";
//                 }
            }
            if (array_key_exists('country2', $contact)) {
                $country2 = $contact['country2'];
            }
            if (array_key_exists('strasseNr2', $contact)) {
                $strasseNr2 = $contact['strasseNr2'];
            }
            if (array_key_exists('firma2', $contact)) {
                $firma2 = $contact['firma2'];
            }
            if (array_key_exists('abteilung2', $contact)) {
            	$department2 = $contact['abteilung2'];
            }
            
            $select1 = "";
            $select2 = "";
            if (array_key_exists('anrede', $contact)) {
                $anrede = $contact['anrede'];
                if ($disabled == "") {
                    switch (trim($anrede)) {
                        case __("Mr.", 'indiebooking') :
                            $select1 = "selected = 'selected'";
                            break;
                        case __("Mrs.", 'indiebooking') :
                            $select2 = "selected = 'selected'";
                            break;
                    }
                }
            }
            
            $select21 = "";
            $select22 = "";
            $select23 = "selected = 'selected'";
            $select24 = "";
            if (array_key_exists('anrede2', $contact)) {
                $anrede2 = $contact['anrede2'];
                if ($disabled == "") {
                    switch (trim($anrede2)) {
                        case __("Mr.", 'indiebooking') :
                            $select21 = "selected = 'selected'";
                            break;
                        case __("Mrs.", 'indiebooking') :
                            $select22 = "selected = 'selected'";
                            break;
                        case __("", 'indiebooking') :
                            $select23 = "selected = 'selected'";
                            break;
                        case __("Department", 'indiebooking') :
                            $select24 = "selected = 'selected'";
                            break;
                    }
                }
            }
            
        }
        
        $requiredFilterData = get_option( 'rs_indiebooking_settings_contact_required');
        $contactPflichtKzArray = array(
        	'companyRequiredKz' 	=> true,
        	'departmentRequiredKz' 	=> true,
        	'salutationRequiredKz' 	=> true,
        	'firstnameRequiredKz' 	=> true,
        	'nameRequiredKz' 		=> true,
        	'mailRequiredKz' 		=> true,
        	'addressRequiredKz' 	=> true,
        	'telefonRequiredKz'	 	=> true,
        );
        if ($requiredFilterData) {
        	$settingsContactRequiredFirmaKz		= (key_exists('firma', $requiredFilterData))        ?  esc_attr__( $requiredFilterData['firma'] )       : "";
        	$settingsContactRequiredAbteilungKz	= (key_exists('abteilung', $requiredFilterData))   	?  esc_attr__( $requiredFilterData['abteilung'] )   : "";
        	$settingsContactRequiredAnredeKz	= (key_exists('anrede', $requiredFilterData)) 		?  esc_attr__( $requiredFilterData['anrede'] ) 		: "";
        	$settingsContactRequiredVornameKz	= (key_exists('vorname', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['vorname'] )     : "";
        	$settingsContactRequiredNachnameKz	= (key_exists('nachname', $requiredFilterData))     ?  esc_attr__( $requiredFilterData['nachname'] )    : "";
        	$settingsContactRequiredMailKz		= (key_exists('mail', $requiredFilterData))         ?  esc_attr__( $requiredFilterData['mail'] )        : "";
        	$settingsContactRequiredAdressKz	= (key_exists('address', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['address'] )		: "";
        	$settingsContactRequiredTelefonKz	= (key_exists('telefon', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['telefon'] )		: "";
        	
        	if ($settingsContactRequiredFirmaKz		== "off") {
        		$contactPflichtKzArray['companyRequiredKz'] 	= false;
        	}
        	if ($settingsContactRequiredAbteilungKz	== "off") {
        		$contactPflichtKzArray['departmentRequiredKz'] 	= false;
        	}
        	if ($settingsContactRequiredAnredeKz	== "off") {
        		$contactPflichtKzArray['salutationRequiredKz'] 	= false;
        	}
        	if ($settingsContactRequiredVornameKz	== "off") {
        		$contactPflichtKzArray['firstnameRequiredKz'] 	= false;
        	}
        	if ($settingsContactRequiredNachnameKz	== "off") {
        		$contactPflichtKzArray['nameRequiredKz'] 		= false;
        	}
        	if ($settingsContactRequiredMailKz		== "off") {
        		$contactPflichtKzArray['mailRequiredKz'] 		= false;
        	}
        	if ($settingsContactRequiredAdressKz	== "off") {
        		$contactPflichtKzArray['addressRequiredKz'] 	= false;
        	}
        	if ($settingsContactRequiredTelefonKz	== "off") {
        		$contactPflichtKzArray['telefonRequiredKz'] 	= false;
        	}
        }
        
        $useGoogleFeatures = ($zahlart != "AMAZONPAYMENTSEXPRESS");
        
        $args = array(
            'appartment'    => $appartment,
            'buchung'       => $buchung,
            'buchungKopf'   => $buchungKopf,
            'postId'        => $postId,
            'contact'       => $contact,
            'disabled'      => $disabled,
            'errTitle'      => $errTitle,
            'errAnrede'     => $errAnrede,
            'errName'       => $errName,
            'errFirstName'  => $errFirstName,
            'errEmail'      => $errEmail,
            'errStreet'     => $errStreet,
            'errZip'        => $errZip,
            'errLocation'   => $errLocation,
            'errTelefon'    => $errTelefon,
        	'errFirma' 		=> $errFirma,
        	'errAbteilung'  => $errAbteilung,
            'firma'         => $firma,
            'title'         => $title,
            'anrede'        => $anrede,
            'lastName'      => $lastName,
            'firstName'     => $firstName,
            'email'         => $email,
            'street'        => $street,
            'zipCode'       => $zipCode,
            'location'      => $location,
            'country'       => $country,
            'strasseNr'     => $strasseNr,
            'errCountry'    => $errCountry,
            'errHausnr'     => $errHausnr,
            'telefon'       => $telefon,
        	'department'    => $department,
            
            'useAdress2'    => $useAdress2,
            'firma2'        => $firma2,
            'title2'        => $title2,
            'anrede2'       => $anrede2,
            'lastName2'     => $lastName2,
            'firstName2'    => $firstName2,
            'email2'        => $email2,
            'street2'       => $street2,
            'zipCode2'      => $zipCode2,
            'location2'     => $location2,
            'country2'      => $country2,
            'strasseNr2'    => $strasseNr2,
            'telefon2'      => $telefon2,
        	'department2'    => $department2,
            
            'select1'       => $select1,
            'select2'       => $select2,
            'select21'      => $select21,
            'select22'      => $select22,
            'select23'      => $select23,
            'select24'      => $select24,
        	
        	'contactRequired' => $contactPflichtKzArray,
        	
        	'useGoogleFeatures'		=> $useGoogleFeatures,
        	
            'result'        => $result,
        );
        
//         default_booking_header_data($buchung);
        do_action("rs_indiebooking_show_default_booking_header_data", $buchung);
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_contactdata.php', $args);
    }
    
    public static function show_buchung_contact($buchungPostId = null, $disabled = "") {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $buchung                    = $buchungTable->getAppartmentBuchung($buchungPostId);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        
//         $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
//         $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($buchungPostId);
        
//         $contact                    = $buchungKopf->getContactArray();
        
        $args = array(
            'disabled'  => $disabled,
            'postId'    => $buchungPostId,
//             'contact'   => $contact,
        );
//         cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_contactdata.php', $args);
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/content-single-rsappartment_buchung_contact.php', $args);
    }
    
    
    //TODO methode ausbauen / huebsch machen!
    public static function checkArrivalDays($buchungKopf) {
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
//                 echo $appartment->getPost_title();
            } else {
//                 echo "geht";
            }
        }
    }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    /* @var $teilkopf RS_IB_Model_Teilbuchungskopf */
    /* @var $buchungTable RS_IB_Table_Appartment_Buchung */
    public static function show_buchung_appartment_list($buchungId, $disabled = "") {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."show_buchung_appartment_list");
        $buchung                    = $buchungTable->getAppartmentBuchung($buchungId);
        
//         $buchung                    = $buchungTable->getPositions($buchung);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        self::checkArrivalDays($buchungKopf);
        echo '<div id="appartment_list_box">';
            foreach ($buchungKopf->getTeilkoepfe() as $teilkopf) {
//                 echo "Hier: ".$teilkopf->getAppartment_name();
                echo '<div class="appartment_box item_box" data-appartmentId="'.$teilkopf->getAppartment_id().'">';
//                 echo '<div class="toggle_button_item">';
//                     echo '<button class="btnToggleTest">zuklappen</button>';
//                 echo '</div>';
//                 echo '<div class="toggle_item">';
                    $appartment     = $appartmentTable->getAppartment($teilkopf->getAppartment_id());
//                     $arrivalDays    = $appartment->getArrivalDays();
                    
//                     if (!$teilkopf->getTeilbuchung_von() instanceof DateTime) {
//                         $tbVon      = new DateTime($teilkopf->getTeilbuchung_von());
//                     } else {
//                         $tbVon      = $teilkopf->getTeilbuchung_von();
//                     }
//                     $dayOfWeek      = date("w", $tbVon->getTimestamp());
//                     if ($dayOfWeek == 0) {
//                         $dayOfWeek  = 7;
//                     }
                    $args = array(
                        'buchungKopf'       => $buchungKopf,
                        'appartment'        => $appartment,
                        'appartmentId'      => $appartment->getPostId(),
                        'appartmentName'    => $appartment->getPost_title(),
                    );
                    cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_appartment_list.php', $args);
//                     echo '</div>';
                echo '</div>';
            }
        echo '</div>';
    }
    
    public static function show_buchung_appartment_zeitraeume($bookingPostId) {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $bookingTbl                 = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $teilbuchungTbl             = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        $buchung                    = $buchungTable->getAppartmentBuchung($bookingPostId);
        //         $buchung                    = $buchungTable->getPositions($buchung);
//         $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $teilKoepfe                 = $teilbuchungTbl->loadBookingPartHeader($buchung->getBuchungKopfId(), null, false);
        $bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
        $bookingByCategorieKz		= ($bookingByCategorieKz == "on");
        $args = array(
            'teilKoepfe'    => $teilKoepfe,
        	'showCategoryAsName' => $bookingByCategorieKz,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_time_range.php', $args);
    }
    
    /* @var $buchungRabattTbl RS_IB_Table_BuchungRabatt */
    public static function rs_indiebooking_single_rsappartment_buchung_detail_payment($bookingPostId) {
        global $RSBP_DATABASE;
        
        $buchungTable               = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $bookingTbl                 = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $buchungRabattTbl           = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        
        
        $buchung                    = $buchungTable->getAppartmentBuchung($bookingPostId);
        $buchungKopf                = $buchungTable->loadBuchungskopf($buchung->getBuchungKopfId());
        $showCouponBox              = true;
        if ($buchungKopf->getHauptZahlungsart() == "PAYPALEXPRESS" || $buchungKopf->getHauptZahlungsart() == "PAYPAL") {
            $showCouponBox          = false;
        }
        
        $bookingByCategorieKz		= get_option('rs_indiebooking_settings_booking_by_categorie_kz');
        $bookingByCategorieKz		= ($bookingByCategorieKz == "on");
        
//         $teilKoepfe                 = $buchungKopf->getTeilkoepfe();
//         $rabatte                    = $buchungRabattTbl->loadBuchungRabatt($buchungKopf->getBuchung_nr());
        
        //TODO hier muesste gerechnet werden / es sollte ein controller da sein der die Buchung berechnet so das in der
        // Anzeige nur noch die errechneten Positionen wiedergegeben werden muessen
        
        $args = array (
            'buchungKopf'      => $buchungKopf,
            'showCouponBox'    => $showCouponBox,
            'showCategoryAsName' => $bookingByCategorieKz,
//             'teilKoepfe'    => $teilKoepfe,
//             'rabatte'       => $rabatte,
//             'postId'        => $postId,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_detail_payment_info.php', $args);
    }
}
// endif;
/*
if ( ! function_exists( 'rs_indiebooking_template_single_appartment_buchung_countdown' ) ) {
    function rs_indiebooking_template_single_appartment_buchung_countdown() {
        global $RSBP_DATABASE;
        $postId                     = get_the_ID();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung(get_the_ID());
        $remainingTime              = $buchung->getRemainingtTime();
        
        $args = array(
            'remainingTime' => $remainingTime,
        );
        
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_countdown.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_time_range' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_time_range() {
        global $RSBP_DATABASE;
        $postId                     = get_the_ID();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung(get_the_ID());
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        
        $args = array(
            'appartment'    => $appartment,
            'buchungObj'    => $buchung,
        );
        ?>
        <input type="hidden" id="booking_date_from" class="booking_datepicker" name="booking_date_from"   value="<?php echo $buchung->getStartDate(); ?>">
        <input type="hidden" id="booking_date_to" class="booking_datepicker" name="booking_date_to"  value="<?php echo $buchung->getEndDate(); ?>">
        <?php
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_time_range.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_detail_payment' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_detail_payment() {
        global $RSBP_DATABASE;
        $postId                     = get_the_ID();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung(get_the_ID());
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
        $positionArray = $buchung->getBuchungsPositionen();
        $args = array(
            'positionArray' => $positionArray,
            
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_detail_payment_info.php', $args);
    }
}

if ( ! function_exists( 'rs_indiebooking_single_rsappartment_buchung_full_prices' ) ) {
    function rs_indiebooking_single_rsappartment_buchung_full_prices() {
        global $RSBP_DATABASE;
        $postId                     = get_the_ID();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $buchung                    = $appartmentBuchungsTable->getAppartmentBuchung($postId);
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($buchung->getAppartment_id());
        $buchung                    = $appartmentBuchungsTable->getPositions($buchung);
        $args = array(
            'appartment'    => $appartment,
            'buchungObj'    => $buchung,
        );
        cRS_Template_Loader::rs_ib_get_template('single-appartment-buchung/appartment_buchung_full_prices.php', $args);
    }
}
*/

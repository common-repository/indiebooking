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
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Table_Appartment_Buchung' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Table_Appartment_Buchung extends RS_IB_Table_Postmeta
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        
    }
    
    
//**********************************************************************************************
//**********************************NEUE TABELLEN***********************************************
//**********************************************************************************************

    /* @var $bookingTbl RS_IB_Table_Buchungskopf */
    public function loadContactData() {
    	global $RSBP_DATABASE;
    	$bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    	return $bookingTbl->loadAllBookingCustomers();
    }
    
    /* @var $buchungKopf RS_IB_Model_Buchungskopf */
    
    public function createAndSaveBillNumber($buchungKopf) {
    	global $RSBP_DATABASE;
    	$bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    	
    	$rechnungsNr    = $bookingTbl->getNextRechnungsbuchungsnrNumber();
    	
    	$buchungKopf->setRechnung_nr($rechnungsNr);
    	
    	if ($buchungKopf->getChangeBillDate() == 0) {
    		$rechnungsdatum = new DateTime("now");
    		$buchungKopf->setRechnungsdatum($rechnungsdatum);
    	}
    	
    	$bookingTbl->saveOrUpdateBuchungskopf($buchungKopf);
    	
    	return $rechnungsNr;
    }
    
    /* @var RS_IB_Model_Buchungskopf $buchungskopf */
    /* @var $bookingTbl RS_IB_Table_Buchungskopf */
    public function saveOrUpdateBuchungskopf($buchungskopf) {
        global $RSBP_DATABASE;
        $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        if ($buchungskopf->getBuchung_nr() == 0) {
            //neuer Buchungskopf
            
            if ($buchungskopf->getBuchung_status()  === "rs_ib-blocked") {
                $startTime  = time();
            }
            /*
             * Update Carsten Schmitt 14.03.2018
             * Die Rechnungsnummer darf erst bei Abschluss der Buchung / erstellen der Rechnung erzeugt werden.
             * Dadurch wird garantiert, dass die Rechnungsnummer L�ckenlos durchg�ngig ist.
             *
            	$rechnungsNr    = $bookingTbl->getNextRechnungsbuchungsnrNumber();
            	$buchungskopf->setRechnung_nr($rechnungsNr);
            	RS_Indiebooking_Log_Controller::write_log('Buchung gestartet - RE-Nr: '.$rechnungsNr,
                	__LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
            */
            
            $buchungsNrNew = $bookingTbl->saveOrUpdateBuchungskopf($buchungskopf);
            RS_Indiebooking_Log_Controller::write_log('Buchung gestartet - Buchung-Nr: '.$buchungsNrNew,
            	__LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
            
            return $buchungsNrNew;
        } else {
            //bestehender Kopf --> Update
            return $bookingTbl->saveOrUpdateBuchungskopf($buchungskopf);
        }
    }
    
    public function loadOutstandingPayments() {
        global $RSBP_DATABASE;
        $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        return $bookingTbl->loadOutstandingPayments();
    }
    
    public function loadLastBookings($numberOfBookings = 10) {
        global $RSBP_DATABASE;
        $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        return $bookingTbl->loadLastBookings($numberOfBookings);
    }
    
    /* @var $bookingTbl RS_IB_Table_Buchungskopf */
    public function loadBuchungskopf($buchungNr) {
        global $RSBP_DATABASE;
        $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        return $bookingTbl->loadBooking($buchungNr);
    }

    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    public function loadTeilbuchungskopf($buchungNr, $appartmentId) {
        global $RSBP_DATABASE;
//         $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $teilbuchungTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
//         return $bookingTbl->loadBooking($buchungNr);
        return $teilbuchungTbl->loadBookingPartHeader($buchungNr, $appartmentId);
    }

    /*@var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    public function saveOrUpdateTeilbuchungskopf($teilbuchungskopf) {
        global $RSBP_DATABASE;
//         $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $teilbuchungTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        return $teilbuchungTbl->saveOrUpdateTeilbuchungskopf($teilbuchungskopf);
    }
    
    /* @var $positionTbl RS_IB_Table_Buchungposition */
    public function saveOrUpdateBuchungsposition( RS_IB_Model_Buchungposition $position) {
        global $RSBP_DATABASE;
//         $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $positionTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        return $positionTbl->saveOrUpdateBuchungsposition($position);
    }
    
    /* @var $bookingPosTbl RS_IB_Table_Buchungposition */
    public function deleteBuchungspositionen($teilbuchungskopf) {
        global $RSBP_DATABASE;
//         $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $bookingPosTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        return $bookingPosTbl->deleteBuchungspositionen($teilbuchungskopf);
    }
//**********************************************************************************************
//**********************************************************************************************
    
    
    /* @var $modelAppartmentBuchung RS_IB_Model_Appartment_Buchung */
    public function saveOrUpdateAppartmentBuchung( $modelAppartmentBuchung ) {
        $post_id            = $modelAppartmentBuchung->getPostId();
        if ($post_id == 0) {
            $testPostType   = array(
                'post_title'    => $modelAppartmentBuchung->getPost_title(),
                'post_type'     => $modelAppartmentBuchung->getPost_type(),
                'post_content'  => $modelAppartmentBuchung->getPost_content(),
                'post_status'   => $modelAppartmentBuchung->getPost_status(),
            );
            $post_id        = wp_insert_post($testPostType);
            $modelAppartmentBuchung->setPostId($post_id);
            if (function_exists('icl_object_id') ) {
//             	try {
	            	global $sitepress;
	            	
	            	$defaultLanguage	= $sitepress->get_default_language();
	            	$set_language_args = array(
	            		'element_id'    => $post_id,
	            		'element_type'  => 'post_rsappartment_buchung',
	            		'trid'   		=> false,
	            		'language_code' => $defaultLanguage,
	            	);
	            	
	            	do_action( 'wpml_set_element_language_details', $set_language_args );
//             	} catch ()
            }
        }
        if ($post_id) {
            if ($modelAppartmentBuchung->getPost_status() === "rs_ib-blocked") {
                $modelAppartmentBuchung->setStart_time(time());
            }
            if (!is_null($modelAppartmentBuchung->getStartDate()) && !is_null($modelAppartmentBuchung->getEndDate())) {
                $dates      = array();
                $dates[0]   = $modelAppartmentBuchung->getStartDate();
                $dates[1]   = $modelAppartmentBuchung->getEndDate();
                $this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_ZEITRAUM, $dates);

                $dtFrom     = date_create_from_format('d.m.Y', $modelAppartmentBuchung->getStartDate());
                $dtTo       = date_create_from_format('d.m.Y', $modelAppartmentBuchung->getEndDate());
    
                $dtFrom     = date('Y-m-d', $dtFrom->getTimestamp());
                $dtTo       = date('Y-m-d', $dtTo->getTimestamp());
                $this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_VON, $dtFrom);
                $this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_BIS, $dtTo);
            }
            if (!is_null($modelAppartmentBuchung->getStart_time())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, $modelAppartmentBuchung->getStart_time());
            }
            if (!is_null($modelAppartmentBuchung->getBuchungKopfId())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_KOPF_ID, $modelAppartmentBuchung->getBuchungKopfId());
            }
        }
        return $post_id;
    }
     
    public function saveOrUpdateBuchungHeadPosition( RS_IB_Model_Appartment_Buchung $modelAppartmentBuchung ) {
        global $RSBP_DATABASE;
        $post_id                = $modelAppartmentBuchung->getPostId();
        if ($post_id) {
            try {
                $bookingHeader  = new RS_IB_Model_Booking_Header();
                $bookingHeader->setBooking_id($post_id);
                $bookingHeader->setBooking_status(get_post_status($post_id));
                $bookingHeader->setDate_from($modelAppartmentBuchung->getStartDate());
                $bookingHeader->setDate_to($modelAppartmentBuchung->getEndDate());
                $bookingHeader->setNumber_of_nights($modelAppartmentBuchung->getAnzahlNeachte());
                
                $contact        = $modelAppartmentBuchung->getContact();
                $bookingHeader->setCustomer_name($contact['name']);
                $bookingHeader->setCustomer_first_name($contact['first_name']);
                $bookingHeader->setCustomer_location($contact['ort']);
                $bookingHeader->setCustomer_email($contact['email']);
                $bookingHeader->setCustomer_telefon($contact['telefon']);
                $bookingHeader->setCustomer_strasse($contact['strasse']);
                
                /* @var $bookingTbl RS_IB_Table_Booking */
                $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Booking_Header::RS_TABLE);
                
                $dtBuchungVon   = DateTime::createFromFormat("d.m.Y", $modelAppartmentBuchung->getStartDate());
                $dtBuchungBis   = DateTime::createFromFormat("d.m.Y", $modelAppartmentBuchung->getEndDate());
                /*
                 * POSITIONEN
                 */
                $positionArray          = array();
                
                $bookingPrices          = $modelAppartmentBuchung->getBookingPrices();
                $yearPrices             = $bookingPrices['year'];
                $yearLessPrices         = $bookingPrices['yearless'];
                $defaultPrice           = $bookingPrices['default'];
                
                foreach ($yearPrices as $yearPrice) {
                    $dtPositionFrom     = 0;
                    $dtPositionTo       = 0;
                    $netto              = 0;
                    $brutto             = 0;
                    $tax                = 0;
                
                    $tax                = 0;
                    $datePriceFrom      = DateTime::createFromFormat("d.m.Y", $yearPrice['from']);
                    $datePriceTo        = DateTime::createFromFormat("d.m.Y", $yearPrice['to']);
                    $tax                = str_replace(",", ".", $yearPrice['tax']);
                    $datePrice          = str_replace(",", ".", $yearPrice['calcPrice']);
                    if (is_null($datePrice) || $datePrice == "" || $datePrice <= 0) {
                        //TODO yearless prices beachten!
                        $datePrice      = $default;
                    }
                    $datePrice          = str_replace(",", ".", $datePrice);
                
                    $calculatedDates    = $this->getDateRangeToCalc($dtBuchungVon, $dtBuchungBis, $datePriceFrom, $datePriceTo);
                    $dtPositionFrom     = $calculatedDates['von'];
                    $dtPositionTo       = $calculatedDates['bis'];
                    $allInOneTimeRange  = $calculatedDates['allInOne'];
                    if ($allInOneTimeRange) {
                        $allPrices      = array();
                    }
                    $numberOfNights                         = date_diff($dtPositionFrom, $dtPositionTo, false);
                    $numberOfNights                         = intval($numberOfNights->format('%R%a'));
                    if ($numberOfNights > 0) {
//                         $prices                             = rs_ib_price_calculation_util::calcPrice($datePrice, $tax, $priceIsNet, 1, $numberOfNights, 0); //$couponsToCalc
                    	$prices                             = $this->calcPrice($datePrice, $tax, $priceIsNet, 1, $numberOfNights, 0); //$couponsToCalc
                        $positionBrutto                     = $prices["brutto"];
                        $positionPricePerNight              = $prices["calcBrutto"];
                        
                        $bookingPos = new RS_IB_Model_Booking_Position();
                        $bookingPos->setBooking_id($post_id);
                        $bookingPos->setPosition_id(sizeof($positionArray)+1);
                        $bookingPos->setAppartment_id($modelAppartmentBuchung->getAppartment_id()); //TODO mehr wie eine ID
                        $bookingPos->setAppartment_qm($modelAppartmentBuchung->getAppartment_square()); //TODO fuer mehrere Appartments!
                        $bookingPos->setNumber_of_nights($numberOfNights);
                        $bookingPos->setPrice($positionPricePerNight);
                        $bookingPos->setMwst_percent($tax);
                        $bookingPos->setPosition_type("appartment_price");
                        $bookingPos->setCalc_type(1); //price / night
                        $bookingPos->getCalculation(1); //total
                        $bookingPos->setDate_from($dtPositionFrom);
                        $bookingPos->setDate_to($dtPositionTo);
                        array_push($positionArray, $bookingPos);
                    }
                    //         $coupons         = $buchungObj->getCoupons(); //Die Coupons werden bei Eingabe (und validierung) dem Buchungssatz hinzugefuegt.
                    if ($allInOneTimeRange) {
                        break;
                    }
                }
                
                $bookedOptions  = $modelAppartmentBuchung->getOptions();
                foreach ($bookedOptions as $myoption) {
                    $bookingPos = new RS_IB_Model_Booking_Position();
                    
                    $bookingPos->setBooking_id($post_id);
                    $bookingPos->setAppartment_id($modelAppartmentBuchung->getAppartment_id()); //TODO mehr wie eine ID
                    $bookingPos->setAppartment_qm($modelAppartmentBuchung->getAppartment_square()); //TODO fuer mehrere Appartments!
                    $bookingPos->setDate_from($modelAppartmentBuchung->getStartDate());
                    $bookingPos->setDate_to($modelAppartmentBuchung->getEndDate());
                    $bookingPos->setPosition_type("option");
                    $bookingPos->setMwst_percent($myoption["mwst"]);
                    $bookingPos->setMeta_value($myoption["name"]." ".$myoption["id"]);
                    $bookingPos->setPrice($myoption["price"]);
                    $bookingPos->setCalc_type($myoption["calc"]);
                    array_push($positionArray, $bookingPos);
                }
                $bookingTbl->saveOrUpdateBooking($bookingHeader, $positionArray);
            } catch (Exception $e) {
                RS_Indiebooking_Log_Controller::write_log(
                    $e->getMessage(),
                    __LINE__,
                    __CLASS__,
                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
                );
            }
//             $tbl_booking_header     = $wpdb->prefix .'rewabp_booking_header';
//             $tbl_booking_position   = $wpdb->prefix .'rewabp_booking_position';
        }
    }
    
    public function confirmInquiry($bookingId, $storno = true) {
    	$status = 'rs_ib-booked';
    	$bookingPostType = array(
    			'ID'            => $bookingId,
    			'post_title'    => 'Inquiry '.$bookingId.' was confirmed.',
    			'post_status'   => $status, //'rs_ib-canceled',
    	);
    	$wp_error        = wp_update_post($bookingPostType, true);
    	return $wp_error;
    }
    
    /* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    public function confirmPayment($bookingId, $buchungsKopf) {
        $status 	= 'rs_ib-pay_confirmed';
        if ($buchungsKopf->getBcomBookingKz() !== 0) {
        	/*
        	 * Update 28.05.2018 Carsten Schmitt
        	 * Wird eine BookingCom Zahlung bestaetigt, wird ein anderer Status verwendet.
        	 */
//         	$status 	= 'rs_ib-pay_confirmed_bcom';
        }
        if ($buchungsKopf->getBuchung_status() == 'rs_ib-storno') {
        	$status = 'rs_ib-storno_paid';
        }
//         $postTitle 	= sprintf(__('Booking %s was paid.', 'indiebooking'), $buchungsKopf->getBuchung_nr());
        $bookingPostType = array(
            'ID'            => $bookingId,
//         	'post_title'    => $postTitle,
            'post_status'   => $status, //'rs_ib-canceled',
        );
        $wp_error        = wp_update_post($bookingPostType, true);
        return $wp_error;
    }
    
    /* @var $teilbuchungKopfTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $positionTbl RS_IB_Table_Buchungposition */
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $tkopf RS_IB_Model_Teilbuchungskopf */
    /* @var $tposition RS_IB_Model_Buchungposition */
    /* @var $buchungRabatt RS_IB_Model_BuchungRabatt */
    /* @var $zahlung RS_IB_Model_BuchungZahlung */
    /* @var $buchungsKopfTbl RS_IB_Table_Buchungskopf */
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    private function cloneBookingData($bookingPostId, $forStorno = true, $rechnungsNr = 0) {
        global $RSBP_DATABASE;
        $buchungsKopfTbl        = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $teilbuchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        $positionTbl            = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        $rabattTable            = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        $zahlungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
        $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        
        $buchung                = $this->getAppartmentBuchung($bookingPostId);
        $buchungskopf           = $buchungsKopfTbl->loadBooking($buchung->getBuchungKopfId());
        $cloneKopf              = clone $buchungskopf;
        if ($rechnungsNr == 0) {
            $rechnungsNr        = $buchungsKopfTbl->getNextRechnungsbuchungsnrNumber();
        }
        $cloneKopf->setRechnung_nr($rechnungsNr);
        $cloneKopf->setBuchung_nr(0);

        $cloneKopf->setCalculatedPrice($cloneKopf->getCalculatedPrice() * -1);
        $buchungsNr             = $buchungsKopfTbl->saveOrUpdateBuchungskopf($cloneKopf);
        $cloneKopf->setBuchung_nr($buchungsNr);

//         var_dump($cloneKopf->getZahlungen());
        if ($cloneKopf->getZahlungen()) {
            foreach ($cloneKopf->getZahlungen() as $zahlung) {
            	if ($zahlung->getZahlungart() != 99) {
	                $zahlung->setBuchung_nr($buchungsNr);
	                $zahlung->setZahlung_nr(0);
	                $zahlung->setZahlungzeitpunkt(new DateTime($zahlung->getZahlungzeitpunkt()));
	                $zahlung->setZahlungbetrag($zahlung->getZahlungbetrag() * -1);
	                $zahlungTable->saveOrUpdateBuchungZahlung($zahlung);
            	}
            }
        }
        
        if ($cloneKopf->getRabatte()) {
            foreach ($cloneKopf->getRabatte() as $buchungRabatt) {
                //                                 var_dump($buchungRabatt);
                $buchungRabatt->setBuchung_nr($buchungsNr);
                $buchungRabatt->setRabatt_id(0);
                $buchungRabatt->setGueltig_von(new DateTime($buchungRabatt->getGueltig_von()));
                $buchungRabatt->setGueltig_bis(new DateTime($buchungRabatt->getGueltig_bis()));
                if ($forStorno) {
                    $buchungRabatt->setRabatt_wert($buchungRabatt->getRabatt_wert() * -1);
                }
                $rabattTable->saveOrUpdateBuchungRabatt($buchungRabatt);
            }
        }
        
        foreach ($cloneKopf->getTeilkoepfe() as $tkopf) {
            $tkopf->setBuchung_nr($buchungsNr);
            $tkopf->setTeilbuchung_id(0);
            $tkopf->setCalculatedPrice($tkopf->getCalculatedPrice() * -1);
            $tkopf->setTeilbuchung_id($teilbuchungKopfTbl->saveOrUpdateTeilbuchungskopf($tkopf));
            if ($tkopf->getRabatte()) {
                foreach ($tkopf->getRabatte() as $buchungRabatt) {
                    $buchungRabatt->setBuchung_nr($buchungsNr);
                    $buchungRabatt->setTeilbuchung_nr($tkopf->getTeilbuchung_id());
                    $buchungRabatt->setRabatt_id(0);
                    $buchungRabatt->setGueltig_von(new DateTime($buchungRabatt->getGueltig_von()));
                    $buchungRabatt->setGueltig_bis(new DateTime($buchungRabatt->getGueltig_bis()));
                    if ($forStorno) {
                        $buchungRabatt->setRabatt_wert($buchungRabatt->getRabatt_wert() * -1);
                    }
                    $rabattTable->saveOrUpdateBuchungRabatt($buchungRabatt);
                }
            }

            foreach ($tkopf->getPositionen() as $tposition) {
                $tposition->setBuchung_nr($buchungsNr);
                $tposition->setTeilbuchung_id($tkopf->getTeilbuchung_id());
                $tposition->setPosition_id(0);
                if ($forStorno) {
                    $tposition->setEinzelpreis($tposition->getEinzelpreis() * -1);
                    $tposition->setCalcPosPrice($tposition->getCalcPosPrice() * -1);
                    $tposition->setCalculatedPrice($tposition->getCalculatedPrice() * -1);
                } else {
                    $tposition->setEinzelpreis($tposition->getEinzelpreis());
                    $tposition->setCalcPosPrice($tposition->getCalcPosPrice());
                    $tposition->setCalculatedPrice($tposition->getCalculatedPrice());
                }
                $tposition->setPosition_id($positionTbl->saveOrUpdateBuchungsposition($tposition));

                if ($tposition->getRabatte()) {
                    foreach ($tposition->getRabatte() as $posBuchungRabatt) {
                        $posBuchungRabatt->setBuchung_nr($buchungsNr);
                        $posBuchungRabatt->setTeilbuchung_nr($tposition->getTeilbuchung_id());
                        $posBuchungRabatt->setPosition_nr($tposition->getPosition_id());
                        $posBuchungRabatt->setRabatt_id(0);
                        
                        $posBuchungRabatt->setGueltig_von(new DateTime($posBuchungRabatt->getGueltig_von()));
                        $posBuchungRabatt->setGueltig_bis(new DateTime($posBuchungRabatt->getGueltig_bis()));
                        if ($forStorno) {
                            $posBuchungRabatt->setRabatt_wert($posBuchungRabatt->getRabatt_wert() * -1);
                        }
                        $rabattTable->saveOrUpdateBuchungRabatt($posBuchungRabatt, true);
                    }
                }
            }
        }
        
        return $cloneKopf;
    }
    
    /* @var $buchungsKopfTbl RS_IB_Table_Buchungskopf */
    /* @var $stornoObj RS_IB_Model_Storno */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $buchungsKopfModel RS_IB_Model_Buchungskopf */
    public function cancelBooking($bookingId, $cancel = true, $storno = false, $adminKz = 0, $dataStorno = false) {
    	/*
    	 * wenn $dataStorno = true ist, bedeutet das, dass die Buchung nicht komplett storniert wurde, sondern
    	 * dass die urspruengliche Rechnung veraendert wurde und deshalb eine stornierung der aktuellen Rechnung noetig ist.
    	 */
    	global $RSBP_DATABASE;
        
        $myerror                    = "";
        $answer['CODE']             = 0;
        $post_id                    = 0;
        $buchungNrToCancel          = 0;
        $wp_error 					= null;
        $newCalcBuchungKopf			= null;
        if ($bookingId > 0) {
            $buchung                = $this->loadAppartmentBooking($bookingId);//$this->getAppartmentBuchung($bookingId);
            $buchungsKopfId         = $buchung->getBuchungKopfId();
            $buchungsKopfTbl        = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
            $buchungsKopfModel      = $buchungsKopfTbl->loadBooking($buchungsKopfId, false);
            
            $buchungsNummer         = $buchungsKopfModel->getBuchung_nr();
            
            $currentStatus  		= get_post_status($bookingId);
            $dateNow				= date('d.m.Y - H:i:s', time());
            if (!$dataStorno) {
	            $postContent			= sprintf(__('Canceled at %s with status %s', 'indiebooking'), $dateNow, $currentStatus);
	            if ($currentStatus == "rs_ib-requested") {
	            	$status         	= 'rs_ib-canc_request';
	            	$post_id			= $bookingId;
	            	$storno				= false; //Eine Abgebrochene Anfrage kann nie eine zu stornierende Rechnung haben.
	            } else {
		            $status         	= 'rs_ib-canceled';
		            if (!$cancel) {
		                $status     	= 'rs_ib-out_of_time';
		            }
	            }
	            
	            if ($storno == false) {
	                $msg                = 'Buchung abgebrochen - BuchungsNr: '.$buchungsNummer;
	                $msg                = $msg." bei Status: ".$currentStatus;
	                $msg                = $msg." zu Status: ".$status;
	            } else {
	                $msg                = 'Buchung storniert - BuchungsNr: '.$buchungsNummer;
	            }
	            
	            RS_Indiebooking_Log_Controller::write_log($msg,
	                __LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
	            
	            
            	$postTitle = $buchung->getPost_title();
            	$postTitle = $postTitle." ".__('was canceled.', 'indiebooking');
	            if ($status !== 'rs_ib-canc_request') {
		            $bookingPostType = array(
		                'ID'            => $bookingId,
		            	'post_title'	=> $postTitle,
// 		                'post_title'    => __('Booking', 'indiebooking')." ".$buchungsNummer." ".__('was canceled.', 'indiebooking'),
		            	'post_content' 	=> $postContent,
		                'post_status'   => $status,
		            );
	            } else {
	            	$bookingPostType = array(
	            		'ID'            => $bookingId,
	            		'post_title'	=> $postTitle,
// 	            		'post_title'    => __('Inquiry', 'indiebooking')." ".$buchungsNummer." ".__('was canceled.', 'indiebooking'),
	            		'post_content' 	=> $postContent,
	            		'post_status'   => $status,
	            	);
	            }
	            $wp_error = null;
	            try {
	                $wp_error                    = wp_update_post($bookingPostType, true);
	            } catch (Exception $e) {
	                $emsg = $e->getMessage();
	                RS_Indiebooking_Log_Controller::write_log(
	                    $e->getMessage(),
	                    __LINE__,
	                    __CLASS__,
	                    RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
	                );
	            }
            }
            if (is_null($wp_error) || !$wp_error instanceOf WP_Error) {
            	if (!$dataStorno) {
                	$buchungsKopfTbl->updateBuchungsStatus($buchung->getBuchungKopfId(), $status, $adminKz);
            	} else {
            		$status         	= 'rs_ib-canceled';
            		$buchungsKopfTbl->updateBuchungsStatus($buchung->getBuchungKopfId(), $status, $adminKz);
            	}
                if ($buchungsKopfModel->getBcomBookingKz() == 0) {
                	if (!$dataStorno) {
		                $buchungCoupon              = $buchung->getCoupons();
		                $buchungGutscheine			= $buchung->getGutscheine();
		                if (sizeof($buchungCoupon) > 0 || sizeof($buchungGutscheine) > 0) {
		                	if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
		                		//nur zur absicherung, dass auf jedenfall die Tabelle geladen und somit auch die
		                		//actions hinzugefuegt wurden.
		                		$coupontbl			= $RSBP_DATABASE->getTable(RS_IB_Model_Gutschein::RS_TABLE);
		                		$gutscheintbl		= $RSBP_DATABASE->getTable(RS_IB_Model_Real_Gutschein::RS_TABLE);
		                	}
		                }
		                $errorcode                  = null;
		                $number1                    = 0;
		                $number2                    = 0;
		                $coupon                     = null;
		                
		                do_action("rs_indiebooking_buchung_reset_real_gutscheine", $buchungGutscheine);
		//                 $buchungskopf               = $buchungsKopfTbl->loadBooking($buchung->getBuchungKopfId());
		                if (!$storno) {
		                	/*
		                	 * ein Coupon soll nur zurueckgesetzt werden, wenn es sich nicht um eine
		                	 * stornierung handelt.
		                	 * Bei einer Stornierung wird der Coupon mit storniert, demnach soll er danach
		                	 * weiterhin nicht mehr verfuegbar sein.
		                	 */
		                	do_action("rs_indiebooking_buchung_reset_gutscheine", $buchungCoupon);
		                }
                	}
                	
	                /*
	                 * Eine Stornobuchung soll nur dann erstellt werden, wenn die Buchung zuvor abgeschlossen wurde
	                 * oder die Zahlung bereits erfolgt ist. Wird die Buchung im Buchungsprozess abgebrochen,
	                 * muss kein Storno erfolgen
	                 */
	                if ($storno && ($currentStatus == 'rs_ib-pay_confirmed' || $currentStatus == 'rs_ib-booked')) {
	                    
	                    $teilbuchungKopfTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
	                    $positionTbl            = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
	                    //Buchung wurde storniert. Stornobedingungen muessen geprueft und ggf. angewendet werden
	                    $stornoTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Storno::RS_TABLE);
	                    $rabattTable            = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
	                    $zahlungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
	                    
	                    $stornos                = $stornoTable->getAllStorno();
	                    
	                    $startDate              = DateTime::createFromFormat("d.m.Y", $buchung->getStartDate());
	                    $today                  = new DateTime();
	                    $anzahlTageBisCheckId   = date_diff($today, $startDate, false);
	                    $anzahlTageBisCheckId   = intval($anzahlTageBisCheckId->format('%R%a'));
	                    
	                    $greatesDayStorno       = 0;
	                    $smallestDayStorno      = -1;
	                    $stornoProzent          = 0;
	                    if (!$dataStorno) {
	                    	/*
	                    	 * wird die Rechnung aufgrund von Datenaenderungen storniert,
	                    	 * fallen keine Stornogebuehrren an.
	                    	 */
		                    foreach ($stornos as $stornoObj) {
		//                         if ($anzahlTageBisCheckId >= intval($stornoObj->getStornodays()) && intval($stornoObj->getStornodays()) > $greatesDayStorno) {
		//                             $greatesDayStorno = intval($stornoObj->getStornodays());
		//                             $stornoProzent  = $stornoObj->getStornovalue();
		//                         }
		                        //Bpsw.: Tage bis CheckIn = 2 | Storno Tage = 3
		                        if ($anzahlTageBisCheckId <= intval($stornoObj->getStornodays())
		                            && ($smallestDayStorno == -1 || intval($stornoObj->getStornodays() < $smallestDayStorno))) {
		                                
		//                             $greatesDayStorno = intval($stornoObj->getStornodays());
		                            $smallestDayStorno 	= intval($stornoObj->getStornodays());
		                            $stornoProzent  	= $stornoObj->getStornovalue();
		                        }
		                    }
	                    }
	                    $stornoProzent          = floatval($stornoProzent);
	                    /*
	                     * Erstellt eine Kopie der Urspruenglichen Buchung, damit die Werte vollstaendig Storniert werden.
	                     */
	                    $stornoKopf             = $this->cloneBookingData($bookingId, true);
	                    $buchungsNr             = $stornoKopf->getBuchung_nr();
	                    
	                    $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	                    
	                    $buchung                = $this->getAppartmentBuchung($bookingId);
	                    $buchungskopf           = $buchungsKopfTbl->loadBooking($buchung->getBuchungKopfId());
	                    $buchungNrToCancel      = $buchungskopf->getBuchung_nr();
	                    
	                    /*
	                     * ANLAGE DER STORNOBUCHUNG
	                     */
	                    $cloneKopf              = clone $buchungskopf;
	                    $cloneKopf->setBuchung_nr(0);
	                    $cloneKopf->setRechnung_nr($stornoKopf->getRechnung_nr());
	                    $cloneKopf->setCalculatedPrice($cloneKopf->getCalculatedPrice() * -1); //kann nicht stimmen! Auueer bei 100%
	                    $cloneKopf->setBuchung_status('rs_ib-storno');
	                    
	                    $buchungsNr             = $buchungsKopfTbl->saveOrUpdateBuchungskopf($cloneKopf);
	                    $cloneKopf->setBuchung_nr($buchungsNr);
	                    $stornoStartTime		= time();

	                    /*
	                     * WARUM $multipli = -1?
	                     * Weil die Werte im Stornokopf negativ sind. Unsere Stornogebuehren ja aber wieder positiv
	                     * ausgewiesen werden sollen!
	                     */
	                    $multipli = -1;
	//                     $multipli = 1;
	                    if ($stornoProzent > 0) {
	                        $stornogebuehr          = $stornoKopf->getCalculatedPrice() * ($stornoProzent / 100);
	                        $stornogutschrift       = $stornoKopf->getCalculatedPrice() - $stornogebuehr;
	                        
	                        foreach ($stornoKopf->getTeilkoepfe() as $tkopf) {
	                            $tkopf->setBuchung_nr($buchungsNr);
	                            $tkopf->setTeilbuchung_id(0);
	                            $tkopf->setAppartment_name("Stornogebuehr - ".$tkopf->getAppartment_name());
	                            $tkopfId = $teilbuchungKopfTbl->saveOrUpdateTeilbuchungskopf($tkopf);
	                            $tkopf->setTeilbuchung_id($tkopfId);
	                            
	                            foreach ($tkopf->getPositionen() as $tposition) {
	                            	if (intval($tposition->getFullStorno()) == 1) {
		                                $tposition->setBuchung_nr($buchungsNr);
		                                $tposition->setTeilbuchung_id($tkopf->getTeilbuchung_id());
		                                $tposition->setPosition_id(0);
		                                $tposition->setCalcPosPrice(0);
		                                
		//                                 $tposition->setBezeic<hnung(strval($stornoProzent)."% - ".$tposition->getBezeichnung());
		                                $tposition->setKommentar(strval($stornoProzent)."%");
		                                
		                                $tposition->setCalculatedPrice($tposition->getCalculatedPrice() * ($stornoProzent / 100) * $multipli);
		//                                 RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."Posberechnung ".$tposition->getEinzelpreis()." * ".($stornoProzent / 100));
		                                $tposition->setEinzelpreis($tposition->getEinzelpreis() * ($stornoProzent / 100) * $multipli);
		                                
		                                $positionId = $positionTbl->saveOrUpdateBuchungsposition($tposition);
		                                $tposition->setPosition_id($positionId);
		                                /* @var $posRabatt RS_IB_Model_BuchungRabatt */
		                                foreach ($tposition->getRabatte() as $posRabatt) {
		                                    $posRabatt->setBuchung_nr($buchungsNr);
		                                    $posRabatt->setPosition_nr($tposition->getPosition_id());
		                                    $posRabatt->setTeilbuchung_nr($tposition->getTeilbuchung_id());
		                                    if ($posRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
		                                        $posRabatt->setRabatt_wert($posRabatt->getRabatt_wert() * ($stornoProzent / 100) * $multipli);
		                                    } elseif ($posRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
		                                        $posRabatt->setRabatt_wert($posRabatt->getRabatt_wert() * $multipli);
		                                    }
		                                    $rabattTable->saveOrUpdateBuchungRabatt($posRabatt, true);
		                                }
	                            	}
	                            }
	                            
	                            foreach ($tkopf->getRabatte() as $tkopfRabatt) {
	                                $tkopfRabatt->setBuchung_nr($buchungsNr);
	                                $tkopfRabatt->setTeilbuchung_nr($tkopf->getTeilbuchung_id());
	                                if ($tkopfRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                                    $tkopfRabatt->setRabatt_wert($tkopfRabatt->getRabatt_wert() * ($stornoProzent / 100) * $multipli);
	                                } elseif ($tkopfRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                                    $tkopfRabatt->setRabatt_wert($tkopfRabatt->getRabatt_wert() * $multipli);
	                                }
	                                $rabattTable->saveOrUpdateBuchungRabatt($tkopfRabatt);
	                            }
	                        }
	                        foreach ($stornoKopf->getRabatte() as $kopfRabatt) {
	                            $kopfRabatt->setBuchung_nr($buchungsNr);
	                            if ($kopfRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                                $kopfRabatt->setRabatt_wert($kopfRabatt->getRabatt_wert() * ($stornoProzent / 100) * $multipli);
	                            } elseif ($kopfRabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                                $kopfRabatt->setRabatt_wert($kopfRabatt->getRabatt_wert() * $multipli);
	                            }
	                            $rabattTable->saveOrUpdateBuchungRabatt($kopfRabatt);
	                        }
	                    }
	                    /* @var $buchungsZahlungen RS_IB_Model_BuchungZahlung */
	                    foreach ($stornoKopf->getZahlungen() as $buchungZahlung) {
	                    	if ($buchungZahlung->getZahlungart() != 99) {
	                        	$buchungZahlung->setBuchung_nr($buchungsNr);
	                        	$buchungZahlung->setZahlungbetrag($buchungZahlung->getZahlungbetrag() * $multipli);
	                        	$zahlungTable->saveOrUpdateBuchungZahlung($buchungZahlung);
	                    	}
	                    }
	                    /*
	                     * Laedt die Stornierungsgebuehren Buchung einmal neu, um alle Werte korrekt zu berechnen
	                     * und dann auch korrekt in die Datenbank zu schreiben.
	                     */
	                    $newCalcBuchungKopf = $buchungsKopfTbl->loadBooking($cloneKopf->getBuchung_nr());
	                    $newCalcBuchungKopf->setAnzahlungsbetrag(-1); //die Stornierung soll keine Anzahlung haben.
	                    $newCalcBuchungKopf->setAnzahlungBezahlt(0);
	                    foreach ($newCalcBuchungKopf->getTeilkoepfe() as $newTKoepfe) {
	                        $teilbuchungKopfTbl->saveOrUpdateTeilbuchungskopf($newTKoepfe);
	                    }
	                    $buchungsKopfTbl->saveOrUpdateBuchungskopf($newCalcBuchungKopf);
	                    
	                    if (!$dataStorno) {
		                    $buchungTable           = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
		                    
		                    $modelAppartmentBuchung = new RS_IB_Model_Appartment_Buchung();
// 		                    $modelAppartmentBuchung->setPost_title('Booking_Number_' . $stornoKopf->getBuchung_nr());
							$modelAppartmentBuchung->setPost_title(__("Storno from ", "indiebooking").$newCalcBuchungKopf->getKunde_vorname()." ".$newCalcBuchungKopf->getKunde_name());
		                    $modelAppartmentBuchung->setPost_type(RS_IB_Model_Appartment_Buchung::RS_POSTTYPE);
		                    
		                    /*
		                     * Update 19.04.2017 Carsten Schmitt
		                     * stornoKopf->Buchungsnummer durch $buchungNrToCancel ersetzt, damit man bei der Stornobuchung sieht,
		                     * welche Buchung storniert wurde.
		                     */
		                    $modelAppartmentBuchung->setPost_content(__('Storno for Booking Nr ', 'indiebooking').$buchungNrToCancel); //$stornoKopf->getBuchung_nr()
		                    $modelAppartmentBuchung->setPost_status('rs_ib-storno');
		                    if ($newCalcBuchungKopf->getZahlungsbetrag() <= 0 && $stornoProzent > 0) {
		                    	/*
		                    	 * Update Carsten 07.08.2018
		                    	 * Nur wenn Stornogebuehren vorliegen, sollen diese auch ggf. als bezahlt markiert werden.
		                    	 *
		                    	 */
		                    	//(sizeof($newCalcBuchungKopf->getZahlungen()) > 0)
		                    	//($newCalcBuchungKopf->getCalculatedPrice() > 0) &&
		                    	$modelAppartmentBuchung->setPost_status('rs_ib-storno_paid');
		                    	$newCalcBuchungKopf->setBuchung_status('rs_ib-storno_paid');
		                    }
		                    	//echo number_format($oberbuchung->getEndbetrag(), 2, ',', '.');
		                    $modelAppartmentBuchung->setPostId(0);
		                    $modelAppartmentBuchung->setStartDate($stornoKopf->getBuchung_von()); //add_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_ZEITRAUM, $dates);
		                    $modelAppartmentBuchung->setEndDate($stornoKopf->getBuchung_bis());
		                    $modelAppartmentBuchung->setBuchungKopfId($buchungsNr);
		                    $post_id                = $buchungTable->saveOrUpdateAppartmentBuchung($modelAppartmentBuchung);
		                    
		                    $newCalcBuchungKopf->setPostId($post_id);
		                    $buchungsKopfTbl->saveOrUpdateBuchungskopf($newCalcBuchungKopf);
		                    
		                    if (!is_null($stornoStartTime)) {
		                    	$this->update_post_meta($post_id, RS_IB_Model_Appartment_Buchung::BUCHUNG_START_BOOKING_TIME, $stornoStartTime);
		                    }
	                    }
	                }
	                $answer['CODE']             = 1;
	                $answer['BookingId']        = $bookingId;
	                $answer['ERR']              = $myerror;
	                $answer['ERRCode']          = $errorcode;
	                $answer['CNumberBevor']     = $number1;
	                $answer['CNumber']          = $coupon;
	                $answer['STORNOPOSTID']     = $post_id;
	                if (!is_null($newCalcBuchungKopf)) {
	                	$answer['INVOICENUMBER']    = $newCalcBuchungKopf->getRechnung_nr();
	                } else {
	                	$answer['INVOICENUMBER'] = 0;
	                }
	                $answer['MSG']              = sprintf(__("Booking %s was canceled.", 'indiebooking'), $buchungNrToCancel);
                } else {
                	//beim stornieren einer Booking.com Buchung muss nicht mehr gemacht werden.
                	$answer['CODE']             = 1;
                	
                }
            } else {
                if (is_wp_error($wp_error)) {
                    $errors                 = $wp_error->get_error_messages();
                    $errorcode              = $wp_error->get_error_code();
                    foreach ($errors as $error) {
                        $myerror = $myerror ." | " . $error;
                    }
                }
                $answer['CODE']             = 0;
                $answer['ERR']              = $myerror;
            }
            
            /*
             * Update Carsten Schmitt 20.03.2018
             * Damit eine Stornierte Buchung auch sofort an Booking.com uebertragen wird um die Verfuegbarkeiten
             * zu aktualisieren, muss an dieser Stelle eine Synchronisation von indiebooking zu booking.com
             * angestossen werden.
             */
            if (has_action('rs_indiebooking_synchronize_bookingdata')) {
            	/*
            	 * Update Carsten Schmitt 16.08.2018
            	 * Wird eine Buchung storniert, weil die Daten abgeändert wurden, muss die Stornierte Buchung nicht sofort
            	 * an Booking.com uebertragen werden, da ansonsten für einen kurzen Zeitraum dieser Zeitraum wieder frei ist.
            	 * Nur wenn es sich um ein vollständige Stornierung handelt, soll die synchronisation zu Booking.com
            	 * angestossen werden.
            	 */
            	if (!$dataStorno) {
	            	if ($currentStatus !== 'rs_ib-blocked' && $currentStatus !== 'rs_ib-booking_info' && $currentStatus !== 'rs_ib-almost_booked') {
	            		do_action('rs_indiebooking_synchronize_bookingdata');
	            	}
            	}
            }
        }
        return $answer;
    }
    
    public function updateBookingCoupons($buchungId, $coupons) {
        $this->update_post_meta($buchungId, RS_IB_Model_Appartment_Buchung::BUCHUNG_COUPONS, $coupons);
    }
    
    public function updateBookingGutscheine($buchungId, $gutscheine) {
    	$this->update_post_meta($buchungId, RS_IB_Model_Appartment_Buchung::BUCHUNG_GUTSCHEINE, $gutscheine);
    }

    /* @var $bookingTbl RS_IB_Table_Buchungskopf */
    /* @var $buchungsKopf RS_IB_Model_Buchungskopf */
    /* @var $buchungModel RS_IB_Model_Appartment_Buchung */
    public function checkBookingHeartbeats() {
    	global $RSBP_DATABASE;
    	$bookingTbl     		= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);

    	$now					= time();
    	$openBuchungsKoepfe 	= $bookingTbl->loadOpenBookings();
    	foreach ($openBuchungsKoepfe as $buchungsKopf) {
    		$postId				= $buchungsKopf->getPostId();
    		if ($postId == 0 || $postId == "0") {
    			$buchungModel 	= $this->getAppartmentBuchungByBuchungsKopfNr($buchungsKopf->getBuchung_nr());
    			$postId			= $buchungModel;
    		}
    		$postStatus			= get_post_status($postId);
    		if ($postStatus != 'rs_ib-canceled' && $postStatus != 'rs_ib-out_of_time') {
	    		$checkHeartbeat		= get_post_meta($postId, RS_IB_Model_Appartment_Buchung::BUCHUNG_DO_HEARTBEAT, true);
	    		if (!is_null($postId) && $postId > 0 && $checkHeartbeat == 1) {
		    		$lastHeartBeat 	= get_post_meta($buchungsKopf->getPostId(), RS_IB_Model_Appartment_Buchung::BUCHUNG_LAST_HEARTBEAT);
		    		if (!is_null($lastHeartBeat) && sizeof($lastHeartBeat) > 0) {
		    			$lastHeartBeat 	= $lastHeartBeat[0];
			    		
			    		$diff       	= ($lastHeartBeat + (2 * 60) + 30 ) - $now;
			    		if ($diff <= 0) {
			    			RS_Indiebooking_Log_Controller::write_log('cancel because of heartbeat '.$buchungsKopf->getPostId(), __LINE__, __CLASS__);
			    			$this->cancelBooking($buchungsKopf->getPostId(), false);
			    		}
		    		}
	    		} else if ($postId > 0) {
	    			RS_Indiebooking_Log_Controller::write_log('heartbeat ausgesetzt '.$checkHeartbeat." ".$buchungsKopf->getPostId(), __LINE__, __CLASS__);
	    		}
    		}
    	}
    }
    
    public function checkBookingTime($post_id) {
    	global $RSBP_DATABASE;
    	
        $optionName = "rs_indiebooking_settings_time_to_book";
        if( !get_option( $optionName ) ) {
            $timeToBook = 15;
        } else {
            $timeToBook = get_option( $optionName );
        }
//         echo "timeToBook: " . $timeToBook." <br />";
        $modelBuchung           = $this->loadAppartmentBooking($post_id);
        $startTime              = $modelBuchung->getStart_time();
//         echo "Status: " . $modelBuchung->getPost_status()." <br />";
        if (!is_null($startTime) && ($modelBuchung->getPost_status() === 'rs_ib-blocked'
            ||  $modelBuchung->getPost_status() === 'rs_ib-almost_booked'
            ||  $modelBuchung->getPost_status() === 'rs_ib-booking_info')) {
            $now        = time();
            $diff       = ($startTime + ($timeToBook * 60)) - $now;
            if ($diff <= 0) {
                $diff   = 0; //ZEIT ABGELAUFEN!!!
//                 $buchungsKopfId = $modelBuchung->getBuchungKopfId();

                $buchungsKopfId         = $modelBuchung->getBuchungKopfId();
                $buchungsKopfTbl        = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
                $buchungsKopfModel      = $buchungsKopfTbl->loadBooking($buchungsKopfId, false);
				
                if ($buchungsKopfModel->getAdminKz() == 0) {
                	$this->cancelBooking($post_id, false);
                }
            }
            $modelBuchung->setRemainingtTime($diff);
        } else {
            $modelBuchung->setRemainingtTime(null);
        }
        return $modelBuchung;
    }
    
    /* @var $appartmentOptionTable RS_IB_Table_Appartmentoption */
    private function loadAppartmentBooking($post_id) {
//         $custom                 = get_post_custom( $post_id );
        $meta                   = get_post_meta( $post_id );
        
        //HEADER INFORMATIONEN
        $modelBuchung           = new RS_IB_Model_Appartment_Buchung($meta, $post_id);
//         $modelBuchung->setPostId($post_id);
//         var_dump($modelBuchung);
        return $modelBuchung;
    }
    
    /**
     * @param unknown $post_id
     * @return RS_IB_Model_Appartment_Buchung
     */
    /* @var RS_IB_Model_Buchungskopf $buchungskopf */
    /* @var $bookingTbl RS_IB_Table_Booking */
    public function getAppartmentBuchung( $post_id ) {
//         $bookingTbl             = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        $modelBuchung           = $this->checkBookingTime($post_id);
//         $modelBuchung           = $bookingTbl->loadBooking($modelBuchung->getBuchungKopfId());
        
//         $modelBuchung           = $this->loadAppartmentBooking($post_id);
        //POSITIONEN
//         $this->getPositions($modelBuchung);
//         $allPrices       = rs_ib_price_calculation_util::calculateBookingPrices($dtFromdate, $dtTodate, $priceIsNet, $yearPrices, $yearlessPrices, $coupons, $options, $aktionen, $square);
        return $modelBuchung;
    }
    
    public function getBookingPostIdByBuchungNr($buchungNr) {
    	global $wpdb;
    	$results    = null;
    	
    	//         $bookingTbl             = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    	$postTbl = $wpdb->prefix.'postmeta';
    	//         $sql = 'SELECT post_id'.
    	//             ' FROM '.$postTbl.
    	//             ' WHERE meta_key ="'.RS_IB_Model_Appartment_Buchung::BUCHUNG_KOPF_ID.'"'.
    	//             ' AND meta_value = "'.$buchungsKopfNr.'"';
    	$sql = $wpdb->prepare('SELECT post_id
             FROM '.$postTbl.
             ' WHERE meta_key = %s
             AND meta_value = %s',
             array(
	             RS_IB_Model_Appartment_Buchung::BUCHUNG_KOPF_ID,
	             $buchungNr
        	)
    	);
    	
    	//         write_log($sql);
    	$results        = $wpdb->get_results( $sql , OBJECT );
    	$post_id		= null;
    	if (!is_null($results) && sizeof($results) > 0) {
    		$post_id    = $results[0]->post_id;
    	}
    	return $post_id;
    }
    
    /* @var RS_IB_Model_Buchungskopf $buchungskopf */
    /* @var $bookingTbl RS_IB_Table_Booking */
    public function getAppartmentBuchungByBuchungsKopfNr( $buchungsKopfNr ) {
        global $wpdb;
        $results    = null;
        
        /*
        $postTbl = $wpdb->prefix.'postmeta';
        $sql = $wpdb->prepare('SELECT post_id
             FROM '.$postTbl.
            ' WHERE meta_key = %s
             AND meta_value = %s',
            array(
                RS_IB_Model_Appartment_Buchung::BUCHUNG_KOPF_ID,
                $buchungsKopfNr
            ));
        
//         write_log($sql);
        $results        = $wpdb->get_results( $sql , OBJECT );
        if (!is_null($results) && sizeof($results) > 0) {
            $post_id        = $results[0]->post_id;
            $modelBuchung   = $this->getAppartmentBuchung($post_id);
        }
        else {
            $modelBuchung = null;
        }
        */
        $post_id 			= $this->getBookingPostIdByBuchungNr($buchungsKopfNr);
        if (!is_null($post_id)) {
        	$modelBuchung   = $this->getAppartmentBuchung($post_id);
        }
        else {
        	$modelBuchung 	= null;
        }
        return $modelBuchung;
    }
    
    /**
     * Gibt die Tage zurueck, die in dem Verfuegbaren Zeitraum voll gebucht sind.
     * Voll gebucht = Von allen in dem Zeitraum mueglichen Apartments
     */
    public function getFullBookedDays() {
        global $wpdb;
        global $RSBP_DATABASE;
        global $RSBP_TABLEPREFIX;
        
        $teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        $buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $gesperrtTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
        
        //ermittle den minimale bis maximalen Buchungszeitraum ueber alle Apartments
        $sql            = 'SELECT MIN(tb.teilbuchung_von) as von, MAX(tb.teilbuchung_bis) as bis'.
            ' FROM '.$teilbuchungTbl.' tb'.
            ' INNER JOIN '.$buchungTbl.' bk'.
            ' ON tb.buchung_nr = bk.buchung_nr'.
            ' AND tb.teilbuchung_bis >= CURDATE()'.
            ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )';
        
//         $sql            = 'SELECT CASE'.
// 	                      ' WHEN (MIN(tb.teilbuchung_von)) IS NULL'.
//                           ' THEN 0'.
//                           ' ELSE MIN(tb.teilbuchung_von)'.
//                           ' END as von,'.
//                           ' CASE'.
// 	                      ' WHEN (MAX(tb.teilbuchung_bis)) IS NULL'.
//                           ' THEN 0'.
//                           ' ELSE MAX(tb.teilbuchung_bis)'.
//                           ' END as bis'.
//             ' FROM '.$teilbuchungTbl.' tb'.
//             ' INNER JOIN '.$buchungTbl.' bk'.
//             ' ON tb.buchung_nr = bk.buchung_nr'.
//             ' AND tb.teilbuchung_bis >= CURDATE()'.
//             ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-out_of_time" )';

        $sqlNotBookable = 'SELECT MIN(gz.date_from) as von, MAX(gz.date_to) as bis'.
            ' FROM '.$gesperrtTbl.' gz'.
            ' WHERE gz.date_to >= CURDATE()';
        
        $sql2           = "SELECT MIN(von) as von, MAX(bis) as bis FROM ( ".$sql." UNION ".$sqlNotBookable." ) as t" ;
        
        $results        = $wpdb->get_results( $sql2 , ARRAY_A );
        
        //ermittle alle Tage zwischen dem oben ermittelten Zeitraum
        $bookedDates                = array();
        if (is_array($results) && sizeof($results) > 0) {
        	if (!is_null($results[0]['von'])) {
            	$dtTeilBFrom			= new DateTime($results[0]['von']);
        	} else {
        		$dtTeilBFrom			= null;
        	}
        	if (!is_null($results[0]['bis'])) {
	            $dtTeilBTo				= new DateTime($results[0]['bis']);
        	} else {
        		$dtTeilBTo				= null;
        	}
        	if (!is_null($dtTeilBFrom) && !is_null($dtTeilBTo)) {
	            $date = $dtTeilBFrom;
	            while($date <= $dtTeilBTo) {
	                $newDate = clone $date;
	                array_push($bookedDates, "('".$newDate->format("Y-m-d")."')");
	                $date->add(new DateInterval('P1D'));
	            }
        	}
        }
        
        $date       = new DateTime("now");
        $timestamp  = $date->getTimestamp();
        $random     = rand();
        $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . "tmp_dates_".$timestamp."_".$random;

        if (is_array($bookedDates) && sizeof($bookedDates) > 0) {
	        $values     = implode(",", $bookedDates);
	        $insert     = "INSERT INTO $table_name (datum) VALUES $values";
	        $dropTable  = "DROP TABLE $table_name";
	        $create     = "CREATE TABLE $table_name (
	                            datum datetime NOT NULL DEFAULT 0,
	                            PRIMARY KEY (datum)
	                        );";
	        
	        $buchungunskopftbl      = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	        $teilbuchungunskopftbl  = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
	        $buchungspositionbl  	= $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
	        $buchungunszeitraumtbl  = $wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
	        $postsTbl               = $wpdb->prefix . 'posts';
	        
	        $wpdb->query($create);
	        $wpdb->query($insert);
        
        //in der temporueren Tabelle stehen nun alle Tage (min bis max) die nicht Buchbar sind.
        //d.h. es sind alle gebuchten und alle gesperrten Tage in dieser Tabelle. (Aber auch Tage die einfach
        //dazwischen liegen.
        
        /*
        $fullBookedSql = "SELECT tbZ.datum
        FROM (
            SELECT tb1.datum, tb1.COUNT1 AS anzGebucht, COUNT(tb2.appid) AS anzZeitraum
            FROM (
                SELECT d.datum, COUNT(tb.appartment_id) AS count1
                FROM $teilbuchungunskopftbl tb
                INNER JOIN $buchungunskopftbl bk
                ON tb.buchung_nr = bk.buchung_nr AND tb.teilbuchung_bis >= CURDATE()
                AND bk.buchung_status NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-out_of_time' )
                JOIN $table_name d
                ON (d.datum BETWEEN tb.teilbuchung_von AND tb.teilbuchung_bis)
                GROUP BY d.datum
            ) AS tb1
            INNER JOIN (
                SELECT bz.post_id AS appid, bz.date_from, bz.date_to
                FROM `$buchungunszeitraumtbl` bz
                INNER JOIN $postsTbl p
                ON bz.post_id = p.ID AND p.post_type = 'rsappartment' AND p.post_status = 'publish'
                WHERE date_to >= CURDATE()
                ORDER BY post_id
            ) AS tb2
            ON tb1.datum BETWEEN tb2.date_from AND tb2.date_to
            WHERE tb1.datum >= CURDATE()
            GROUP BY tb1.datum, tb1.COUNT1
            ) AS tbZ
        WHERE tbZ.anzGebucht = tbZ.anzZeitraum";
//         $wpdb->query($dropTable);
        $result = $wpdb->get_results($fullBookedSql, OBJECT );
        return $result;
        */
        
	        //ON (d.datum BETWEEN tb.teilbuchung_von AND tb.teilbuchung_bis)
	        //ON (d.datum > tb.teilbuchung_von AND d.datum < tb.teilbuchung_bis)
	        
	        $anzApartSQL = "SELECT COUNT(*) as allApartments FROM $postsTbl po
	            WHERE po.post_type = 'rsappartment' AND po.post_status = 'publish'";
	        
	        if (function_exists('icl_object_id') ) {
	        	$wpml_options = get_option( 'icl_sitepress_settings' );
	        	$default_lang = $wpml_options['default_language'];
	        	
	        	$iclTbl = $wpdb->prefix . "icl_translations";
// 	        	$anzApartSQL = "SELECT COUNT(*) as allApartments FROM (Select icl.trid FROM $postsTbl po
// 	            INNER JOIN $iclTbl icl ON po.ID = icl.element_id
// 				WHERE po.post_type = 'rsappartment' AND po.post_status = 'publish' AND language_code = '$default_lang'
// 				GROUP BY icl.trid) as a";
	        	$anzApartSQL = "SELECT COUNT(*) as allApartments FROM (Select po.ID FROM $postsTbl po
	            INNER JOIN $iclTbl icl ON po.ID = icl.element_id
				WHERE po.post_type = 'rsappartment' AND po.post_status = 'publish' AND language_code = '$default_lang'
				GROUP BY po.ID) as a";
	        }
	        
	        $fullBookedSql = "SELECT tbZ.datum
	        FROM (
	            SELECT tb1.datum, COUNT(tb1.apartmentId) as anzGesperrt, sum(tb1.dauer) as dauer
	            FROM (
	                SELECT d.datum, tb.appartment_id as apartmentId,
							sum(case when d.datum = tb.teilbuchung_von or d.datum = tb.teilbuchung_bis then 0.5 else 1 end) as dauer
	                FROM $teilbuchungunskopftbl tb
	                INNER JOIN $buchungunskopftbl bk
	                ON tb.buchung_nr = bk.buchung_nr AND tb.teilbuchung_bis >= CURDATE()
	                AND bk.buchung_status NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-canc_request', 'rs_ib-out_of_time', 'rs_ib-storno', 'rs_ib-storno_paid' )
					INNER JOIN (
						SELECT bp.buchung_nr as buchung_nr FROM $buchungspositionbl bp
						WHERE bp.position_type = 'appartment_price'
						AND bp.preis_bis >= CURDATE()
						GROUP BY bp.buchung_nr
					) as bpbn
					ON tb.buchung_nr = bpbn.buchung_nr
	                INNER JOIN $postsTbl p
	                ON tb.appartment_id = p.ID AND p.post_type = 'rsappartment' AND p.post_status = 'publish'
	                JOIN $table_name d
	                ON (d.datum BETWEEN tb.teilbuchung_von AND tb.teilbuchung_bis)
					AND d.datum >= CURDATE()
	                GROUP BY d.datum, tb.appartment_id
	                UNION
	                SELECT d.datum, gz.post_id as apartmentId, sum(1) as dauer
	                FROM $gesperrtTbl gz
	                INNER JOIN $postsTbl p
	                ON gz.post_id = p.ID AND p.post_type = 'rsappartment' AND p.post_status = 'publish'
	                JOIN $table_name d
	                ON (d.datum BETWEEN gz.date_from AND gz.date_to)
					AND d.datum >= CURDATE()
	                GROUP BY d.datum, gz.post_id
	            ) AS tb1
	            GROUP BY tb1.datum
	        ) AS tbZ
	        INNER JOIN (
				$anzApartSQL
	        ) as anzPosts
	        ON tbZ.dauer = anzPosts.allApartments
	        ORDER BY tbZ.datum";
	        
// 			echo $fullBookedSql;
				
	        $result = $wpdb->get_results($fullBookedSql, OBJECT );
	        $wpdb->query($dropTable);
        } else {
        	$result = array();
        }
        return $result;
    }
    
    /**
     * Gibt alle aktuell gebuchten Zeitrueume inkl. ApartmentID zurueck
     */
    public function getAllBuchungszeitraume() {
        global $wpdb;
        global $RSBP_DATABASE;
        global $RSBP_TABLEPREFIX;
        
        $teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        $buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $sql            = 'SELECT tb.appartment_id, tb.teilbuchung_von, tb.teilbuchung_bis'.
            ' FROM '.$teilbuchungTbl.' tb'.
            ' INNER JOIN '.$buchungTbl.' bk'.
            ' ON tb.buchung_nr = bk.buchung_nr'.
            ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", rs_ib-out_of_time" )';
        
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        
        $bookedDates                = array();
        if (is_array($results) && sizeof($results) > 0) {
            for ($i = 0; $i < sizeof($results); $i++) {
                $dtTeilBFrom				= new DateTime($results[$i]['teilbuchung_von']);
                $dtTeilBTo					= new DateTime($results[$i]['teilbuchung_bis']);
                $bookedDates[$i]["from"]    = $dtTeilBFrom->format('d.m.Y');
                $bookedDates[$i]["to"]      = $dtTeilBTo->format('d.m.Y');
            }
        }
        
        return $bookedDates;
    }
    
    
    
    
    /**
     * Gibt die Tage zurueck, die in dem Verfuegbaren Zeitraum bei einem Apartment voll gebucht sind.
     * Voll gebucht = Von allen Apartments in dem Zeitraum (Beispiel fuer die anlage mehrere Apartments unter einer Post-ID)
     */
    public function getBuchungszeitraeumeByAppartmentId_new($all_appartment_ids) {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	global $RSBP_TABLEPREFIX;
    	
    	$teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    	$buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$gesperrtTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
    	
    	$allWpmlIds		= array();
    	foreach ($all_appartment_ids as $appartment_id) {
    		$appartment_ids	= apply_filters('rs_indiebooking_get_all_apartment_ids_from_wpml', $appartment_id);
    		foreach ($appartment_ids as $apid) {
    			array_push($allWpmlIds, $apid);
    		}
    	}
    	$appartment_ids = implode(",", $allWpmlIds);
    	
    	//ermittle den minimale bis maximalen Buchungszeitraum ueber alle Apartments
    	$sql            = 'SELECT MIN(tb.teilbuchung_von) as von, MAX(tb.teilbuchung_bis) as bis'.
     	' FROM '.$teilbuchungTbl.' tb'.
     	' INNER JOIN '.$buchungTbl.' bk'.
     	' ON tb.buchung_nr = bk.buchung_nr'.
     	' AND tb.teilbuchung_bis >= CURDATE()'.
     	' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )';
    	
    	
    	$sqlNotBookable = 'SELECT MIN(gz.date_from) as von, MAX(gz.date_to) as bis'.
     	' FROM '.$gesperrtTbl.' gz'.
     	' WHERE gz.date_to >= CURDATE()';
    	
    	$sql2           = "SELECT MIN(von) as von, MAX(bis) as bis FROM ( ".$sql." UNION ".$sqlNotBookable." ) as t" ;
    	
    	$results        = $wpdb->get_results( $sql2 , ARRAY_A );
    	
    	//ermittle alle Tage zwischen dem oben ermittelten Zeitraum
    	$bookedDates                = array();
    	if (is_array($results) && sizeof($results) > 0) {
    		if (!is_null($results[0]['von'])) {
    			$dtTeilBFrom			= new DateTime($results[0]['von']);
    		} else {
    			$dtTeilBFrom			= null;
    		}
    		if (!is_null($results[0]['bis'])) {
    			$dtTeilBTo				= new DateTime($results[0]['bis']);
    		} else {
    			$dtTeilBTo				= null;
    		}
    		if (!is_null($dtTeilBFrom) && !is_null($dtTeilBTo)) {
    			$date = $dtTeilBFrom;
    			while($date <= $dtTeilBTo) {
    				$newDate = clone $date;
    				array_push($bookedDates, "('".$newDate->format("Y-m-d")."')");
    				$date->add(new DateInterval('P1D'));
    			}
    		}
    	}
    	
    	$date       = new DateTime("now");
    	$timestamp  = $date->getTimestamp();
    	$random     = rand();
    	$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . "tmp_dates_new_".$timestamp."_".$random;
    	
    	if (is_array($bookedDates) && sizeof($bookedDates) > 0) {
    		$values     = implode(",", $bookedDates);
    		$insert     = "INSERT INTO $table_name (datum) VALUES $values";
    		$dropTable  = "DROP TABLE $table_name";
    		$create     = "CREATE TABLE $table_name (
	                            datum datetime NOT NULL DEFAULT 0,
	                            PRIMARY KEY (datum)
	                        );";
    		
    		$buchungunskopftbl      = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    		$teilbuchungunskopftbl  = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    		$buchungspositionbl  	= $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
    		$buchungunszeitraumtbl  = $wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
    		$postsTbl               = $wpdb->prefix . 'posts';
    		
    		$wpdb->query($create);
    		$wpdb->query($insert);
    		
    		//in der temporueren Tabelle stehen nun alle Tage (min bis max) die nicht Buchbar sind.
    		//d.h. es sind alle gebuchten und alle gesperrten Tage in dieser Tabelle. (Aber auch Tage die einfach
    		//dazwischen liegen.
    		
    		$anzApartSQL = "SELECT COUNT(*) as allApartments FROM $postsTbl po
	            WHERE po.post_type = 'rsappartment' AND po.post_status = 'publish'
				AND po.ID in ($appartment_ids)";
    		
    		if (function_exists('icl_object_id') ) {
    			$wpml_options = get_option( 'icl_sitepress_settings' );
    			$default_lang = $wpml_options['default_language'];
    			
    			$iclTbl = $wpdb->prefix . "icl_translations";
    			$anzApartSQL = "SELECT COUNT(*) as allApartments FROM (Select icl.trid FROM $postsTbl po
	            INNER JOIN $iclTbl icl ON po.ID = icl.element_id
				WHERE po.post_type = 'rsappartment' AND po.post_status = 'publish' AND language_code = '$default_lang'
				AND po.ID in ($appartment_ids)
				GROUP BY icl.trid) as a";
    		}
    		
    		
    		$fullBookedSql = "SELECT tbZ.datum
	        FROM (
	            SELECT tb1.datum, COUNT(tb1.apartmentId) as anzGesperrt, sum(tb1.dauer) as dauer
	            FROM (
	                SELECT d.datum, tb.appartment_id as apartmentId,
							sum(case when d.datum = tb.teilbuchung_von or d.datum = tb.teilbuchung_bis then 0.5 else 1 end) as dauer
	                FROM $teilbuchungunskopftbl tb
	                INNER JOIN $buchungunskopftbl bk
	                ON tb.buchung_nr = bk.buchung_nr AND tb.teilbuchung_bis >= CURDATE()
	                AND bk.buchung_status NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-canc_request', 'rs_ib-out_of_time', 'rs_ib-storno', 'rs_ib-storno_paid' )
					INNER JOIN (
						SELECT bp.buchung_nr as buchung_nr FROM $buchungspositionbl bp
						WHERE bp.position_type = 'appartment_price'
						AND bp.preis_bis >= CURDATE()
						GROUP BY bp.buchung_nr
					) as bpbn
					ON tb.buchung_nr = bpbn.buchung_nr
	                INNER JOIN $postsTbl p
	                ON tb.appartment_id = p.ID AND p.post_type = 'rsappartment' AND p.post_status = 'publish'
	                JOIN $table_name d
	                ON (d.datum BETWEEN tb.teilbuchung_von AND tb.teilbuchung_bis)
					WHERE tb.appartment_id IN ($appartment_ids)
	                GROUP BY d.datum, tb.appartment_id
	                UNION
	                SELECT d.datum, gz.post_id as apartmentId, sum(1) as dauer
	                FROM $gesperrtTbl gz
	                INNER JOIN $postsTbl p
	                ON gz.post_id = p.ID AND p.post_type = 'rsappartment' AND p.post_status = 'publish'
	                JOIN $table_name d
	                ON (d.datum BETWEEN gz.date_from AND gz.date_to)
	                GROUP BY d.datum, gz.post_id
	            ) AS tb1
	            GROUP BY tb1.datum
	        ) AS tbZ
	        INNER JOIN (
				$anzApartSQL
	        ) as anzPosts
	        ON tbZ.dauer = anzPosts.allApartments
	        ORDER BY tbZ.datum";
    		
//     		echo $fullBookedSql;
    		
    		$result = $wpdb->get_results($fullBookedSql, OBJECT );
    		$wpdb->query($dropTable);
    	} else {
    		$result = array();
    	}
    	return $result;
    }
    
    
    
    
    /**
     * Gibt die Gebuchten Zeitrueume eines Apartments zurueck.
     *
     * Update: 26.04.2016 - Carsten Schmitt
     * Es werden nur noch die Buchungen beruecksichtigt, bei denen das Bis-Datum grueueer
     * dem aktuellen Tag ist. Damit werden vergangene Buchungen nicht mehr eingeschlossen.
     *
     * Update: 20.06.2016 - Carsten Schmitt
     * Status "rs_ib-storno" hinzugefuegt, da dieser Status bei dem Buchungszeitraum nicht beachtet werden darf,
     * schlieuelich ist es ein stornierter Zeitraum
     *
     * Update : 21.04.2016 - Carsten Schmitt
     * $notSignificantBookingNr eingefuegt um auch wahrend einem Buchungsdurchlauf pruefen zu koennen ob der Zeitraum noch passt.
     * Passt er nicht mehr, kann es beispielsweise vorgekommen sein, dass ueber booking.com eine Buchung ausgefuehrt wurde.
     *
     * @param unknown $appartment_id
     */
    public function getBuchungszeitraeumeByAppartmentId($appartment_id, $onlyFuture = false,
                            $notSignificantBookingNr = 0, $maxDate = false, $minDate = false, $orderByDate = false) {
        global $wpdb;
        global $RSBP_DATABASE;
        global $RSBP_TABLEPREFIX;
        
//         $this->getBuchungszeitraeumeByAppartmentId_new($appartment_id);
        
//         $appartment_id	= apply_filters('rs_indiebooking_get_original_apartment_id_from_wpml', $appartment_id);
        $appartment_ids	= apply_filters('rs_indiebooking_get_all_apartment_ids_from_wpml', $appartment_id);
        $appartment_ids = implode(",", $appartment_ids);
//         $appartment_ids	= $appartment_id;
        $onlyFutureStr	= ' AND tb.teilbuchung_bis >= CURDATE()';
        $minDateStr     = ' AND tb.teilbuchung_bis >= "';
        $tillMaxDate	= ' AND tb.teilbuchung_von <= "';
        $orderBy        = ' ORDER BY tb.teilbuchung_von';
        $teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        $buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $sql            = 'SELECT tb.teilbuchung_von, tb.teilbuchung_bis, bk.kunde_name, bk.kunde_vorname, bk.buchung_nr, tb.anzahl_personen, bk.bcom_booking, bk.post_id'.
                            ' FROM '.$teilbuchungTbl.' tb'.
                            ' INNER JOIN '.$buchungTbl.' bk'.
                            ' ON tb.buchung_nr = bk.buchung_nr'.
//                             ' WHERE tb.appartment_id = '.$appartment_id;
        					' WHERE tb.appartment_id IN ('.$appartment_ids.')';
//                             ' AND tb.teilbuchung_bis >= CURDATE()'.
							if (!$onlyFuture && !$minDate) {
        						$sql = $sql.' AND tb.teilbuchung_bis >= DATE_SUB(CURDATE(), INTERVAL 185 DAY)';
							} elseif (!$minDate) {
								$sql = $sql.$onlyFutureStr;
							}
							if ($maxDate != false) {
								$tillMaxDate = $tillMaxDate.$maxDate.'"';
								$sql = $sql.$tillMaxDate;
							}
							if ($minDate != false) {
							    $minDateStr = $minDateStr.$minDate.'"';
							    $sql = $sql.$minDateStr;
							}
							
							if ($notSignificantBookingNr > 0) {
								$sql = $sql.' AND tb.buchung_nr <> '.$notSignificantBookingNr;
							}
                            $sql = $sql.' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )';
                            $sql = $sql.' AND bk.booking_type = 1';
                            if ($orderByDate) {
                                $sql = $sql.$orderBy;
                            }
                            
// 		echo $sql;
        $results        = $wpdb->get_results( $sql , ARRAY_A );

        $bookedDates                = array();
        if (is_array($results) && sizeof($results) > 0) {
            for ($i = 0; $i < sizeof($results); $i++) {
//                 $dates = unserialize($buchungen[$i]->meta_value);
				$postId	= 0;
				if (key_exists('post_id', $results[$i])) {
            		$postId	= $results[$i]['post_id'];
            	}
            	if (is_null($postId) || $postId == 0) {
            		$bookingPostId				= $this->getBookingPostIdByBuchungNr($results[$i]['buchung_nr']);
            	} else {
            		$bookingPostId				= $postId;
            	}
            	$bookingStatus					= get_post_status($bookingPostId);
            	$permaLink						= get_edit_post_link($bookingPostId);
            	
				$dtTeilBFrom				    = new DateTime($results[$i]['teilbuchung_von']);
				$dtTeilBTo					 	= new DateTime($results[$i]['teilbuchung_bis']);
				$bookedDates[$i]["bcomKz"]		= $results[$i]["bcom_booking"];
                $bookedDates[$i]["from"]        = $dtTeilBFrom->format('d.m.Y');
                $bookedDates[$i]["to"]          = $dtTeilBTo->format('d.m.Y');
                $bookedDates[$i]["date_from"]   = $dtTeilBFrom->format('Y-m-d');
                $bookedDates[$i]["date_to"]     = $dtTeilBTo->format('Y-m-d');
                $bookedDates[$i]["kdname"]      = $results[$i]['kunde_vorname']." ".$results[$i]['kunde_name'];
//                 $bookedDates[$i]["kdvname"]     = $results[$i]['kunde_vorname'];
                $bookedDates[$i]["buchungNr"]   = $results[$i]['buchung_nr'];
                $bookedDates[$i]["anzahl_personen"]   = $results[$i]['anzahl_personen'];
                $bookedDates[$i]["bookingPermLink"]   = $permaLink;
                $bookedDates[$i]["bookingStatus"]   = $bookingStatus;
            }
        }
//         var_dump($bookedDates);
        return $bookedDates;
    }
    
    
    /* @var $notSignificantTeilbuchung RS_IB_Model_Teilbuchungskopf */
    /**
     * Gibt die Gebuchten Zeitrueume eines Apartments zurueck.
     *
     * Update: 26.04.2016 - Carsten Schmitt
     * Es werden nur noch die Buchungen beruecksichtigt, bei denen das Bis-Datum grueueer
     * dem aktuellen Tag ist. Damit werden vergangene Buchungen nicht mehr eingeschlossen.
     *
     * Update: 20.06.2016 - Carsten Schmitt
     * Status "rs_ib-storno" hinzugefuegt, da dieser Status bei dem Buchungszeitraum nicht beachtet werden darf,
     * schlieuelich ist es ein stornierter Zeitraum
     *
     * Update : 21.04.2016 - Carsten Schmitt
     * $notSignificantBookingNr eingefuegt um auch wahrend einem Buchungsdurchlauf pruefen zu koennen ob der Zeitraum noch passt.
     * Passt er nicht mehr, kann es beispielsweise vorgekommen sein, dass ueber booking.com eine Buchung ausgefuehrt wurde.
     *
     * @param unknown $appartment_id
     */
    public function getBuchungszeitraeumeByOptionTermId($optionTermId, $onlyFuture = false,
    		$notSignificantTeilbuchung = null, $maxDate = false, $minDate = false, $orderByDate = false) {
    	
    		global $wpdb;
    		global $RSBP_DATABASE;
    		global $RSBP_TABLEPREFIX;
    		
    		/*
    		 * TODO alle ID's der Optionen ermitteln (wpml!)
    		 */
    		$elementType 	= "tax_".RS_IB_Model_Appartmentoption::RS_TAXONOMY;
    		$allOptionIds 	= RS_IB_Model_Appartmentoption::getAllIdsFromWPMLId($optionTermId, $elementType);
    		$optionTermIds	= array();
    		foreach ($allOptionIds as $optionId) {
    			array_push($optionTermIds, $optionId);
    		}
    		
    		$optionTermIds = implode(",", $optionTermIds);
    		//         $appartment_ids	= $appartment_id;
    		$onlyFutureStr	= ' AND tb.teilbuchung_bis >= CURDATE()';
    		$minDateStr     = ' AND tb.teilbuchung_bis >= "';
    		$tillMaxDate	= ' AND tb.teilbuchung_von <= "';
    		$orderBy        = ' ORDER BY tb.teilbuchung_von';
    		$teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    		$buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    		$buchungposTbl  = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
    		/*
    		$sql            = 'SELECT tb.teilbuchung_von, tb.teilbuchung_bis, bk.kunde_name, bk.kunde_vorname, bk.buchung_nr, tb.anzahl_personen, bk.bcom_booking, bk.post_id'.
     		' FROM '.$teilbuchungTbl.' tb'.
     		' INNER JOIN '.$buchungTbl.' bk'.
     		' ON tb.buchung_nr = bk.buchung_nr'.
     		' INNER JOIN '.$buchungposTbl.' bp'.
     		' ON tb.buchung_nr = bp.buchung_nr'.
     		' WHERE bp.position_type = "appartment_option" AND bp.data_id IN ('.$optionTermIds.')';
    		*/
    		$sql            = 'SELECT bp.preis_von, bp.preis_bis, bk.kunde_name, bk.kunde_vorname, bk.buchung_nr, bk.bcom_booking, bk.post_id'.
     			' FROM '.$buchungposTbl.' bp'.
	     		' INNER JOIN '.$buchungTbl.' bk'.
	     		' ON bp.buchung_nr = bk.buchung_nr'.
	     		' WHERE bp.position_type = "appartment_option" AND bp.data_id IN ('.$optionTermIds.')';
    		if (!$onlyFuture && !$minDate) {
    			$sql = $sql.' AND bp.preis_bis >= DATE_SUB(CURDATE(), INTERVAL 185 DAY)';
    		} elseif (!$minDate) {
    			$sql = $sql.$onlyFutureStr;
    		}
    		if ($maxDate != false) {
    			$tillMaxDate = $tillMaxDate.$maxDate.'"';
    			$sql = $sql.$tillMaxDate;
    		}
    		if ($minDate != false) {
    			$minDateStr = $minDateStr.$minDate.'"';
    			$sql = $sql.$minDateStr;
    		}
    		
    		if (!is_null($notSignificantTeilbuchung)) {
    			$sql = $sql.' AND NOT (bp.buchung_nr = '.$notSignificantTeilbuchung->getBuchung_nr().' AND bp.teilbuchung_id = '.$notSignificantTeilbuchung->getTeilbuchung_id().' )';
    		}
    		$sql = $sql.' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )';
    		$sql = $sql.' AND bk.booking_type = 1';
    		if ($orderByDate) {
    			$sql = $sql.$orderBy;
    		}
    		
    		// 		echo $sql;
    		$results        = $wpdb->get_results( $sql , ARRAY_A );
    		
    		$bookedDates                = array();
    		if (is_array($results) && sizeof($results) > 0) {
    			for ($i = 0; $i < sizeof($results); $i++) {
    				//                 $dates = unserialize($buchungen[$i]->meta_value);
    				$postId	= 0;
    				if (key_exists('post_id', $results[$i])) {
    					$postId	= $results[$i]['post_id'];
    				}
    				if (is_null($postId) || $postId == 0) {
    					$bookingPostId				= $this->getBookingPostIdByBuchungNr($results[$i]['buchung_nr']);
    				} else {
    					$bookingPostId				= $postId;
    				}
    				$bookingStatus					= get_post_status($bookingPostId);
    				$permaLink						= get_edit_post_link($bookingPostId);
    				
    				$dtTeilBFrom				    = new DateTime($results[$i]['preis_von']);
    				$dtTeilBTo					 	= new DateTime($results[$i]['preis_bis']);
    				$bookedDates[$i]["bcomKz"]		= $results[$i]["bcom_booking"];
    				$bookedDates[$i]["from"]        = $dtTeilBFrom->format('d.m.Y');
    				$bookedDates[$i]["to"]          = $dtTeilBTo->format('d.m.Y');
    				$bookedDates[$i]["date_from"]   = $dtTeilBFrom->format('Y-m-d');
    				$bookedDates[$i]["date_to"]     = $dtTeilBTo->format('Y-m-d');
    				$bookedDates[$i]["kdname"]      = $results[$i]['kunde_vorname']." ".$results[$i]['kunde_name'];
    				//                 $bookedDates[$i]["kdvname"]     = $results[$i]['kunde_vorname'];
    				$bookedDates[$i]["buchungNr"]   = $results[$i]['buchung_nr'];
    				$bookedDates[$i]["anzahl_personen"]   = 0;//$results[$i]['anzahl_personen'];
    				$bookedDates[$i]["bookingPermLink"]   = $permaLink;
    				$bookedDates[$i]["bookingStatus"]   = $bookingStatus;
    			}
    		}
    		//         var_dump($bookedDates);
    		return $bookedDates;
    }
    
    public function getCleainingDates($cleanStart = null) {
    	global $RSBP_DATABASE;
    	
    	if (is_null($cleanStart)) {
    		$cleanStart		= new DateTime("now");
    	}
    	$cleanStart->setTime(0, 0);
    	$today 	        	= date('Y-m-d', $cleanStart->getTimestamp());
    	$today2	        	= date('d.m.Y', $cleanStart->getTimestamp());
    	
    	$todayDt			= new DateTime($today);
    	$minDateDt			= clone $todayDt;
    	$maxDateDt			= clone $todayDt;
    	$maxDateDt->add(new DateInterval('P14D'));
    	$minDateDt->sub(new DateInterval('P21D'));
    	
//     	$maxdate			= date('Y-m-d', strtotime("+14 days"));
//     	$minDate            = date('Y-m-d', strtotime("-21 days"));
    	$maxdate			= date('Y-m-d', $maxDateDt->getTimestamp());
    	$minDate            = date('Y-m-d', $minDateDt->getTimestamp());
    	
    	
    	$appartmentTable            		= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
    	$zeitraumTable              		= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
    	
    	$apartmentArray             		= $appartmentTable->getAllApartments();
    	$availabilityInfo    	 			= array();

    	foreach ($apartmentArray as $apartment) {
    		$apartmentInfo          		= array();
    		$bookedDates            		= $this->getBuchungszeitraeumeByAppartmentId(
    			$apartment->getPostId(), false, 0, $maxdate, $minDate, true);
    		$freeDates              		= $zeitraumTable->loadApartmentZeitraume($apartment->getPostId(), true);
    		$apartmentInfo['apartmentid'] 	= $apartment->getPostId();
    		$apartmentInfo['apartmentname'] = $apartment->getPost_title();
    		$apartmentInfo['bookeddays'] 	= $bookedDates;
    		$apartmentInfo['freedays'] 		= $freeDates;
    		array_push($availabilityInfo, $apartmentInfo);
    	}
    	
    	$version        = 1;
    	
    	$start 	        = new DateTime($today);
    	$start->setISODate($start->format('o'), $start->format('W') + 1);
    	$calendarWeek   = $start->format('W');
    	$goal           = clone $start;
    	$goal           = $goal->add(new DateInterval('P06D'));
    	// $calWeek = date("W", $start->getTimestamp());
    	// $calWeek++;
    	$end 	      = $goal;
    	$end->add(new DateInterval('P7D'));
    	
    	$cleaningDays	= get_option('rs_indiebooking_cleaning_plan_day');
    	if (!isset($cleaningDays) || is_null($cleaningDays) || empty($cleaningDays) || sizeof($cleaningDays) <= 0) {
    		$cleaningDays	= array();
    	}
    	$year               = $start->format("Y");
    	$cleaningDayStr     = "";
    	/*
    	 * Ermittlung des Reinigungstags (aktuell nur einer)
    	 */
    	foreach ($cleaningDays as $day) {
    		$gendate        = new DateTime();
    		$gendate->setISODate($year, $calendarWeek, $day); //year , week num , day
    		$cleaningDay     = clone $gendate;
    		$cleaningDay->setTime(0, 0);
    		$lastCleaningDay = clone $cleaningDay;
    		
    		$lastCleaningDay->setISODate($lastCleaningDay->format('o'), $lastCleaningDay->format('W') - 2, $cleaningDay->format('w'));
    		$cleaningDayStr  = $cleaningDayStr . $gendate->format('d.m.Y')." ";
    		break; //was wenn mehrere?
    	}
	    /*
	     * Ermittle zunaechst den letzten Auszug pro Apartment,
	     * der vor dem zu Beobachteten Zeitraum liegt
	     */
	    $auszugArray                	= array();
	    $einzugArray                	= array();
	    foreach ($availabilityInfo as $info) {
	    	$i 							= 0;
	    	$apartmentname              = $info['apartmentname'];
	    	$letzterAuszug              = null;
	    	$naechsterEinzug            = null;
	    	foreach ($info['bookeddays'] as $bookedday) {
	    		$dateFrom               = new DateTime($bookedday['date_from']);
	    		$dateTo 			    = new DateTime($bookedday['date_to']);
	    		if ($dateTo < $start && (is_null($letzterAuszug) || $dateTo > $letzterAuszug)) {
	    			$letzterAuszug      = clone $dateTo;
	    		}
	    		$einzugArray[$apartmentname][$i] = clone $dateFrom;
	    		$i++;
	    	}
	    	$auszugArray[$apartmentname] = $letzterAuszug;
	    }
	    
	    $defaultCleaningDay     		= clone $cleaningDay;
	    $cleaningPlanArray				= array();
	    foreach ($availabilityInfo as $info) {
	    	$apartmentname      		= $info['apartmentname'];
	    	$letzterAuszug      		= $auszugArray[$apartmentname];
	    	$apartLastCleaining 		= clone $lastCleaningDay;
	    	$cleaningDay        		= clone $defaultCleaningDay;
	    	
			$loopDate                   = clone $lastCleaningDay;
			$loopDate->sub(new DateInterval('P1D'));
			$bleibeWhg                  = false;
			$auszugWhg                  = false;
			$auszugTag                  = array();
			$einzugTag                  = array();
			$index						= 0;
			while ($loopDate <= $end) {
			    $auszug                 = false;
			    $einzug                 = false;
			    $frei                   = false;
			    $extraClass = "";
			    if (rs_ib_date_util::getDayName(date( "w", $loopDate->getTimestamp())) == rs_ib_date_util::getDayName(date( "w", $cleaningDay->getTimestamp()))) {
			        $extraClass = "defaultCleaningDay";
			    }
			    if ($loopDate > $cleaningDay) {
			        $cleaningDay->setISODate($cleaningDay->format('o'), $cleaningDay->format('W') + 1, $cleaningDay->format('w'));
			    }
			    $found					= false;
			    foreach ($info['bookeddays'] as $bookedday) {
			        $dateFrom 			= new DateTime($bookedday['date_from']);
			        $dateTo 			= new DateTime($bookedday['date_to']);

			        if ($loopDate >= $dateFrom && $loopDate <= $dateTo) {
			            if ($loopDate == $dateFrom) {
                            $einzug     = true;
                            $anzDays2   = date_diff($apartLastCleaining, $loopDate);
                            $anzDays2   = intval($anzDays2->format('%a'));
                            if (($letzterAuszug > $apartLastCleaining || $anzDays2 > 7)
			                     && $dateFrom < $cleaningDay) {
                                if ($extraClass == "") {
                                    $extraClass = "extraordinaryCleaning";
                                    $apartLastCleaining = clone $loopDate;
                                }
			                }
			            } elseif ($loopDate == $dateTo) {
                            $auszug             = true;
                            $apartLastCleaining = clone $loopDate;
                            if ($extraClass == "") {
                                $extraClass         = "extraordinaryCleaning";
                            }
			            } elseif ($loopDate->format("d.m.Y") == $cleaningDay->format("d.m.Y")) {
			                $anzDays2   = date_diff($apartLastCleaining, $loopDate);
			                $anzDays2   = intval($anzDays2->format('%a'));
                            $bleibeWhg  = true;
			                if ($anzDays2 > 7) {
                                $apartLastCleaining = clone $loopDate;
			                }
			            }
			            if ($dateTo > $letzterAuszug) {
                            $letzterAuszug  = clone $dateTo;
			            }
			            $found	= true;
						break;
					}
			    }
			    $dateOne 	= $loopDate->format("d.m.Y");
			    $dateTwo 	= $cleaningDay->format("d.m.Y");
			    $dateThree 	= $lastCleaningDay->format("d.m.Y");;
			    if ($dateOne == $dateTwo || $dateOne == $dateThree) {
			    	$cleaningPlanArray[$apartmentname][$index]['isCleaningDay'] = true;
			    } else {
			    	$cleaningPlanArray[$apartmentname][$index]['isCleaningDay'] = false;
			    }
			    if ($bleibeWhg == true && ($dateOne == $dateTwo)) {
			    	$cleaningPlanArray[$apartmentname][$index]['date'] 	= $loopDate->format('Y-m-d');
			    	$cleaningPlanArray[$apartmentname][$index]['value'] = 'B';
				} elseif ($einzug) {
					$cleaningPlanArray[$apartmentname][$index]['date'] 	= $loopDate->format('Y-m-d');
					$cleaningPlanArray[$apartmentname][$index]['value'] = 'O';
				} elseif ($auszug) {
					$cleaningPlanArray[$apartmentname][$index]['date'] 	= $loopDate->format('Y-m-d');
					$cleaningPlanArray[$apartmentname][$index]['value'] = 'X';
				} else {
					if ($found) {
						$cleaningPlanArray[$apartmentname][$index]['date']	= $loopDate->format('Y-m-d');
						$cleaningPlanArray[$apartmentname][$index]['value'] = '--';
					} else {
						$cleaningPlanArray[$apartmentname][$index]['date'] 	= $loopDate->format('Y-m-d');
						$cleaningPlanArray[$apartmentname][$index]['value'] = '';
					}
					//<td class=" //echo $extraClass;">&nbsp;</td>
				}
				$index++;
				$loopDate->add(new DateInterval('P1D'));
			}
		}
		return $cleaningPlanArray;
    }
    
    
    /*
     * diese Methode wird aktuell nur fuer das booking.com plugin verwendet
     */
    public function getNotAvailableDatesByAppartmentId($appartment_id) {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	global $RSBP_TABLEPREFIX;

    	$teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    	$buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$gesperrtTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
    	
    	//$appartment_id	= $this->getOriginalApartmentIdFromWPMLId($appartment_id);
    	$appartment_id	= apply_filters('rs_indiebooking_get_original_apartment_id_from_wpml', $appartment_id);

    	$appartment_ids	= apply_filters('rs_indiebooking_get_all_apartment_ids_from_wpml', $appartment_id);
    	$appartment_ids = implode(",", $appartment_ids);
    	$appartment_ids	= "(".$appartment_ids.")";
    	$sql            = 'SELECT notAvailable.datumVon, notAvailable.datumBis, notAvailable.availDateType FROM ('.
      				' SELECT tb.teilbuchung_von as datumVon, tb.teilbuchung_bis as datumBis, 1 as availDateType'.
    				' FROM '.$teilbuchungTbl.' tb'.
    				' INNER JOIN '.$buchungTbl.' bk'.
    				' ON tb.buchung_nr = bk.buchung_nr'.
    				' WHERE tb.appartment_id in '.$appartment_ids.
    				' AND tb.teilbuchung_bis >= CURDATE()'.
    				' AND bk.buchung_status NOT IN ( '.
    					'"trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )'.
    				' AND bk.booking_type = 1'.
    				' UNION'.
    				' SELECT lo.date_from as datumVon, lo.date_to as datumBis, 2 as availDateType'.
    				' FROM '.$gesperrtTbl.' lo'.
    				' WHERE lo.post_id = '.$appartment_id.
    					' AND lo.date_to >= CURDATE()'.
    			' ) as notAvailable'.
    			' ORDER BY notAvailable.datumVon';
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    
    	$notAvailableDates                = array();
    	if (is_array($results) && sizeof($results) > 0) {
    		for ($i = 0; $i < sizeof($results); $i++) {
    			$dtTeilBFrom				    = new DateTime($results[$i]['datumVon']);
    			$dtTeilBTo					 	= new DateTime($results[$i]['datumBis']);
    			if ($results[$i]['availDateType'] == 1) {
    				/*
    				 * Bei einer Buchung soll der Abreisetag als verfuegbarer Tag uebertragen werden.
    				 */
    				$dtTeilBTo->sub(new DateInterval('P01D'));
    			}
    			/*
    			 * Update Carsten Schmitt 01.03.2018
    			 * $dtTeilBTo wird um 1 reduziert, damit der Abreisetag als Verfuegbar dargestellt wird.
    			 */
    			
    			$notAvailableDates[$i]["from"]  = $dtTeilBFrom->format('d.m.Y');
    			$notAvailableDates[$i]["to"]    = $dtTeilBTo->format('d.m.Y');
    		}
    	}
    	return $notAvailableDates;
    }
    
    
    
    /**
     * gibt den berechneten Zeitraum zurueck
     * Objekte (Datetime = objekte) werden immer call by reference aufgerufen!!!
     * @param unknown $dtBuchungVon
     * @param unknown $dtBuchungBis
     * @param unknown $dtVglVon
     * @param unknown $dtVglBis
     * @return boolean[]|unknown[]
     */
    private function getDateRangeToCalc($dtBuchungVon, $dtBuchungBis, $vglVon, $vglBis) {
        $return                 = array();
        
        /*
         * Objekte (Datetime = objekt) werden immer call by reference aufgerufen!!!
         * Daher clone ich alle uebergebenen Objekte, um das eigentliche Objekt nicht zu veruendern.
         */
        $dtBasisVon             = clone $dtBuchungVon;
        $dtBasisBis             = clone $dtBuchungBis;
        $dtVglVon               = clone $vglVon;
        $dtVglBis               = clone $vglBis;
        
        $allInOneTimeRange      = false;
        if ($dtVglVon <= $dtBasisVon && $dtVglBis >= $dtBasisBis) {
            //Buchungszeitraum in einem einzigen Preiszeitraum
            $allPrices          = array();
            $dtReturnVon        = $dtBuchungVon;
            $dtReturnBis        = $dtBasisBis;
            $allInOneTimeRange  = true;
        }
        elseif ($dtVglVon <= $dtBasisVon) {
            //Buchungszeitraum von in einem Preiszeitraum
            $dtReturnVon        = $dtBasisVon;
            $dtReturnBis        = $dtVglBis;
            $dtReturnBis->add(new DateInterval('P1D'));
        }
        elseif ($dtVglBis >= $dtBasisBis) {
            //Buchungszeitraum bis in einem Preiszeitraum
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtBasisBis;
        }
        elseif ($dtVglVon > $dtBasisVon && $dtVglBis < $dtBasisBis) {
            //Preiszeitraum komplett in Buchungszeitraum
            $dtReturnVon        = $dtVglVon;
            $dtReturnBis        = $dtVglBis;
            $dtReturnBis->add(new DateInterval('P1D'));
        }
//         else {
//             $dtReturnVon        = $dtBuchungVon;
//             $dtReturnBis        = $dtBuchungBis;
//         }
        
        $return['von']          = $dtReturnVon;
        $return['bis']          = $dtReturnBis;
        $return['allInOne']     = $allInOneTimeRange;
        
        return $return;
    }
    
//     public function getAvailableDates($appartmentID) {
//         return $this->getAvailableDates($appartmentID, null, null, 1);
//     }
    
//     public function getAvailableDates($suche_von, $suche_bis) {
//         return $this->getAvailableDates(0, $suche_von, $suche_bis, 2);
//     }
    
//     public function getAvailableDates($dtSuche_von, $dtSuche_bis) {
//         $dtFrom     = date('Y-m-d', $dtSuche_von->getTimestamp());
//         $dtTo       = date('Y-m-d', $dtSuche_bis->getTimestamp());
//         $sql = "SELECT a.* FROM `wp_rewabp_appartment_buchungszeitraum` as a
//                 LEFT OUTER JOIN
//                 (
//                 SELECT buchungen.* FROM (
//                 SELECT c.meta_value as 'appartment_id', c.post_id as 'buchung_id',
//                 	(
//                         SELECT a.meta_value as 'buchung_von' FROM `wp_postmeta` as a
//                 		WHERE a.meta_key = 'rs_buchungszeitraum_von' AND a.post_id = c.post_id
//                     ) as 'buchung_von',
//                 	(
//                         SELECT b.meta_value as 'buchung_bis' FROM `wp_postmeta` as b
//                 		WHERE b.meta_key = 'rs_buchungszeitraum_bis' AND b.post_id = c.post_id
//                     ) as 'buchung_bis'
//                 FROM `wp_postmeta` as c
//                 WHERE c.meta_key = 'rs_buchung_appartment_id'
//                 ) as buchungen
//                 where buchungen.buchung_von IS NOT NULL AND buchungen.buchung_bis IS NOT NULL
//                 ) b
//                 ON a.post_id = b.appartment_id AND '$dtFrom' <= b.buchung_bis AND '$dtTo' >= b.buchung_von
//                 INNER JOIN `wp_posts` as p
//                 ON p.ID = a.post_id AND p.post_status <> 'trash' AND p.post_status <> 'rs_ib-out_of_time' AND p.post_status <> 'rs_ib-canceled'
//                 where a.date_from >= '$dtFrom' AND a.date_to <= '$dtTo' AND b.appartment_id IS NULL
//                 AND a.date_to >= CURRENT_DATE()
//                 ORDER BY a.date_from asc";
        
//     }
    
    public function getAllAppartments() {
    	global $wpdb;
//     	global $RSBP_DATABASE;
//     	global $RSBP_TABLEPREFIX;
    
    	
    	$availableIds 	= array();
    		 
    	$query          = 'SELECT ID from '.$wpdb->prefix.'posts p'.
    						' WHERE p.post_type = "rsappartment" AND p.post_status <> "trash"';
    		 
//     	$sql            = $wpdb->prepare($query);
    	$results        = $wpdb->get_results( $query, ARRAY_N);
    	foreach ($results as $result) {
    		array_push($availableIds, $result[0]);
    	}
    	return $availableIds;
    }
    
    /**
     * Gibt die ID's der in dem angegebenen Zeitraum verfuegbaren Apartments zurueck.
     *
     *
     * Update Carsten Schmitt 29.03.2017
     * Abfrage so erweitert, dass nur Apartments angezeigt werden, bei denen auch der Anreisetag mit
     * dem gesuchten Von-Datumstag uebereinstimmen
     *
     * @param DateTime $dtSuche_von
     * @param DateTime $dtSuche_bis
     * @return array|object|NULL
     */
    public function getAvailableAppartments($dtSuche_von, $dtSuche_bis) {
        global $wpdb;
        global $RSBP_DATABASE;
        global $RSBP_TABLEPREFIX;
        
        $availableIds   			= array();
        $futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
        if (!$futureAvailabilityYear) {
        	$futureAvailabilityYear	= 2;
        }
        $curMaxDate					= new DateTime("now");
        $addYears					= "P".$futureAvailabilityYear."Y";
        $curMaxDate->add(new DateInterval($addYears));
        $checkDateVon		= rs_ib_date_util::convertDateValueToDateTime($dtSuche_von);
        $checkDateBis		= rs_ib_date_util::convertDateValueToDateTime($dtSuche_bis);
        if ($checkDateVon->getTimestamp() <= $curMaxDate->getTimestamp() && $checkDateBis->getTimestamp() <= $curMaxDate->getTimestamp()) {
        	$fromDayOfWeek	= date("N", $checkDateVon->getTimestamp());
	        $dtFrom         = date('Y-m-d', rs_ib_date_util::convertDateValueToTimestamp($dtSuche_von));
	        $dtTo           = date('Y-m-d', rs_ib_date_util::convertDateValueToTimestamp($dtSuche_bis));
	        $teilbuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
	        $buchungTbl     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	//         $zeitraumTbl    = $wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
	        $gesperrtTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
	        
	        $wpmlQuery		= "";
	        $gebuchteIdsSQL = 'SELECT tb.appartment_id'.
		        	' FROM '.$teilbuchungTbl.' tb'.
		        	' INNER JOIN '.$buchungTbl.' bk'.
		        	' ON tb.buchung_nr = bk.buchung_nr'.
		        	' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid" )'.
		        	' WHERE %s >= tb.teilbuchung_von AND %s < tb.teilbuchung_bis'.
		        	' OR  %s > tb.teilbuchung_von AND %s <= tb.teilbuchung_bis'.
		        	' OR tb.teilbuchung_von >= %s AND tb.teilbuchung_von < %s'.
		        	' OR tb.teilbuchung_bis > %s AND tb.teilbuchung_bis <= %s'.
		        	
		        	' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno", "rs_ib-storno_paid")'.
		        	' GROUP BY appartment_id ';
	        
	        if (function_exists('icl_object_id') ) {
		        $wpmlTable		= $wpdb->prefix.'icl_translations';
		        $wpmlQuery		= 'SELECT DISTINCT Id FROM (
										SELECT tb2.appartment_id as apId, tb2.appartment_id as Id
										FROM '.$teilbuchungTbl.' tb2
										UNION
										SELECT wpml.trid as apId,  wpml.element_id as Id FROM '.$wpmlTable.' wpml
										UNION
										SELECT wpml2.element_id as apId, wpml2.trid as Id FROM '.$wpmlTable.' wpml2
									) as apidtbl'.
									' WHERE apidtbl.apId IN ( '.
									$gebuchteIdsSQL.
									' )';
		        
				$translationTbl = $wpdb->prefix . 'icl_translations';
				$wpmlQuery		= 'SELECT DISTINCT wpml10.element_id FROM '.$translationTbl.' wpml10'.
			        				' WHERE wpml10.trid IN ('.$wpmlQuery.')';
		        
				$gebuchteIdsSQL	= $wpmlQuery;
		        
// 		        $wpmlQuery		= ' UNION'.
// 			        				' SELECT wpml.trid FROM '.$wpmlTable.' wpml'.
// 			        				' WHERE wpml.element_id IN ('.
// 				        				' SELECT tb.appartment_id'.
// 				        				' FROM '.$teilbuchungTbl.' tb'.
// 				        				' INNER JOIN '.$buchungTbl.' bk'.
// 				        				' ON tb.buchung_nr = bk.buchung_nr'.
// 				        				' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno" )'.
// 								        ' WHERE "%s" >= tb.teilbuchung_von AND "%s" < tb.teilbuchung_bis'.
// 								        ' OR  "%s" > tb.teilbuchung_von AND "%s" <= tb.teilbuchung_bis'.
// 								        ' OR tb.teilbuchung_von >= "%s" AND tb.teilbuchung_von < "%s"'.
// 								        ' OR tb.teilbuchung_bis > "%s" AND tb.teilbuchung_bis <= "%s"'.
								        
// 								        ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno")'.
// 								        ' GROUP BY appartment_id'.
// 								    ')';
// 		        $wpmlQuery		= sprintf($wpmlQuery,$dtFrom,$dtFrom,$dtTo,$dtTo,$dtFrom, $dtTo,$dtFrom, $dtTo);
	        }
	        
	        $query            = 'SELECT ID from '.$wpdb->prefix.'posts p'.
	                          ' WHERE p.post_type = "rsappartment" AND p.post_status <> "trash"'.
	                          ' AND p.ID NOT IN ( '.
	                          		$gebuchteIdsSQL .
// 	                            ' SELECT tb.appartment_id'.
// 	                                ' FROM '.$teilbuchungTbl.' tb'.
// 	                                ' INNER JOIN '.$buchungTbl.' bk'.
// 	                                ' ON tb.buchung_nr = bk.buchung_nr'.
// 	                                ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno" )'.
// 							        ' WHERE %s >= tb.teilbuchung_von AND %s < tb.teilbuchung_bis'.
// 							        ' OR  %s > tb.teilbuchung_von AND %s <= tb.teilbuchung_bis'.
// 	        						' OR tb.teilbuchung_von >= %s AND tb.teilbuchung_von < %s'.
// 	        						' OR tb.teilbuchung_bis > %s AND tb.teilbuchung_bis <= %s'.
	                                
// 	                                ' AND bk.buchung_status NOT IN ( "trash", "rs_ib-canceled", "rs_ib-canc_request", "rs_ib-out_of_time", "rs_ib-storno")'.
// 	                                ' GROUP BY appartment_id '.
	                            ' ) AND p.ID NOT IN ('.
	                                'SELECT bz.post_id FROM '.$gesperrtTbl.' bz'.
	                                ' WHERE %s between bz.date_from AND bz.date_to OR'.
	                                ' %s between bz.date_from AND bz.date_to'.
	                            ' ) AND p.ID IN ('.
	        						'SELECT meta.post_id FROM '.$wpdb->prefix.'postmeta meta'.
	        						' WHERE meta.meta_key = "rs_appartment_arrival_days" AND'.
	        						' (meta.meta_value LIKE "%s" OR meta.meta_value = "a:0:{}")'.
                               	' ) AND p.ID != "auto-draft"';
	        					/*
                                ' AND p.ID IN ('.
                                  		'SELECT wpml.element_id FROM '.$wpmlTable.' wpml'.
                                      ' WHERE wpml.element_id = wpml.trid'.
                                  ' )';
                                  */
	//                           ' ) AND p.ID IN ('.
	//                               'SELECT bz.post_id FROM '.$zeitraumTbl.' bz'.
	//                               ' WHERE %s between bz.date_from AND bz.date_to OR'.
	//                               ' %s between bz.date_from AND bz.date_to'.
	//                           ' )';
			$anreiseQuery	= '%:1:"'.$fromDayOfWeek.'"%';
			$sql            = $wpdb->prepare($query, array($dtFrom,$dtFrom,$dtTo,$dtTo,$dtFrom, $dtTo,$dtFrom, $dtTo,$dtFrom, $dtTo, $anreiseQuery));
	        $results        = $wpdb->get_results( $sql, ARRAY_N);
	//         var_dump($results);
	        foreach ($results as $result) {
	            array_push($availableIds, $result[0]);
	        }
        }
        return $availableIds;
    }
    
    
    
    /**
     * Berechnet den Brutto-, Netto & Steuerpreis
     * Diese Methode ist Analog der calcPrice-Methode in der appartment_buchung.js
     * Es werden beide Methoden benuetigt, um im Frontend den Preis schnell berechnen und anzeigen zu kuennen
     * im Backend jedoch die sicherheit vor manipulationen zu gewuehrleisten
     *
     * @param unknown $price
     * @param unknown $tax
     * @param unknown $priceIsNet
     * @param unknown $calcType
     * @param unknown $numberOfNights
     * @param unknown $couponsToCalc
     * @param unknown $square
     */
    private function calcPrice($price, $tax, $priceIsNetKz, $calcType, $numberOfNights, $square) { //$couponsToCalc
    	$priceObj           = array();
    	$brutto				= 0;
    	$calcBrutto			= 0;
    	$fullBrutto			= 0;
    	$fullRabatt         = 0;
    	if ($price == '') {
    		$price          = 0;
    	} else {
    		$price          = floatval(str_replace(",", ".", $price));
    	}
    	if ($price > 0) {
    		if ($priceIsNetKz == "on") {
    			//                 $netto		= $value;
    			$calcBrutto = ($price * (1 + $tax));
    		} else {
    			$calcBrutto	= $price;
    			//                 $netto		= ($brutto / (1 + $tax));
    		}
    		$calcBrutto		= round($calcBrutto, 2);
    		if ($calcType == 0) {//price total
    			$brutto     = $calcBrutto;
    		}
    		elseif ($calcType == 1) {//price / night
    			$brutto     = $calcBrutto * $numberOfNights;
    		}
    		elseif ($calcType == 2) {//price / qm
    			$brutto 	= $calcBrutto * $square;
    		}
    		elseif ($calcType == 3) {//price / qm / night
    			$brutto     = $calcBrutto * $square * $numberOfNights;
    		}
    	}
    	$fullBrutto         = $brutto;
    	//         foreach ($couponsToCalc as $coupon) {
    	//             $fullRabatt     = $fullRabatt + ($brutto * $coupon->getPercent()/100);
    	//             $brutto			= $brutto - ($brutto * $coupon->getPercent()/100);
    	//         }
    	
    	$fullBrutto             = round($fullBrutto, 2);
    	$priceObj['calcBrutto'] = $calcBrutto;
    	$priceObj['fullBrutto'] = $fullBrutto; // round($fullBrutto * 100); //(round($fullBrutto * 100) / 100);
    	$priceObj['fullNetto']  = round(($fullBrutto / ($tax+1)) ,2);
    	$priceObj['fullTax']    = $priceObj['fullBrutto'] - $priceObj['fullNetto'];//round(($priceObj['fullBrutto'] / ($tax+1)),2);
    	$priceObj['fullRabatt'] = $fullRabatt;
    	
    	$brutto                 = round($brutto, 2);
    	$priceObj['brutto']     = $brutto;
    	$priceObj['netto']      = round(($brutto / ($tax+1)) ,2);
    	$priceObj['tax']        = $priceObj['brutto'] - $priceObj['netto']; //round(($priceObj['brutto'] / ($tax+1)) ,2);
    	
    	return $priceObj;
    }
    
    
    /* @var $buchungObj RS_IB_Model_Appartment_Buchung */
    /* @var $aktionToCalc RS_IB_Model_Appartmentaktion */
    /* @var $actualTotalPosition RS_IB_Buchungsposition */
    /* @var $myCoupon RS_IB_Model_Gutschein */
    public function getPositions($buchungObj) {
        //$dtFromdate, $dtToDate, $yearPrices, $yearlessPrices, $priceIsNet, $couponsToCalc, $aktionenToCalc
        $positionArray          = array();
        $positionOptionArray    = array();
        $fullPricePositionArray = array();
        $allPrices              = array();
        $aktionenToCalc         = array();
        $allTaxes               = array();
        $allInOneTimeRange      = false;
        
        $dtBuchungVon           = DateTime::createFromFormat("d.m.Y", $buchungObj->getStartDate());
        $dtBuchungBis           = DateTime::createFromFormat("d.m.Y", $buchungObj->getEndDate());
        $priceIsNet             = $buchungObj->getPriceIsNetKz(); //TODO Muss noch im Buchungsheader hinterlegt werden!
        $aktionen               = $buchungObj->getAktionen();
        $square                 = $buchungObj->getAppartment_square();
        $anzahlNaechte          = date_diff($dtBuchungVon, $dtBuchungBis, false);
        $anzahlNaechte          = intval($anzahlNaechte->format('%R%a'));
        $coupons                = $buchungObj->getCoupons();
        
        $biggestBruttoType      = 0; //0 = nichts | 1 = Zeitraum | 2 = Option
        $biggestBrutto          = 0;
        $calcTimeRangeBrutto    = 0;
        $calcOptionBrutto       = 0;
        $biggestBruttoIndex     = 0;
/*
 ***************************************************************************************************
 *Zeitraum Positionen Anfang
 ***************************************************************************************************
 */
        /**
         * Ermittle Positionen der Zeitraumpreise
         */
        $calcTimeRangePosition  = new RS_IB_Buchungsposition(RS_IB_Buchungsposition::ART_SUMME_ZEITRAUM);
        $calcTimeRangePosition->setDatumVon($dtBuchungVon);
        $calcTimeRangePosition->setDatumBis($dtBuchungBis);
        $calcTimeRangePosition->setName("Summe: ");
        
        $totalAktionPositions   = array();
        $yearPrices             = array();
        $yearLessPrices         = array();
        $default                = "";
        
        $bookingPrices          = $buchungObj->getBookingPrices();
        if (isset($bookingPrices) && (sizeof($bookingPrices) > 0)) {
            if (key_exists('year', $bookingPrices)) {
                $yearPrices     = $bookingPrices['year'];
            }
            if (key_exists('yearless', $bookingPrices)) {
                $yearLessPrices = $bookingPrices['yearless'];
            }
            if (key_exists('default', $bookingPrices)) {
                $default        = $bookingPrices['default'];
            }
        }
        if (isset($yearPrices) && (sizeof($yearPrices) > 0)) {
            foreach ($yearPrices as $yearPrice) {
                $dtPositionFrom     = 0;
                $dtPositionTo       = 0;
                $netto              = 0;
                $brutto             = 0;
                $tax                = 0;
        
                $tax                = 0;
                $datePriceFrom      = DateTime::createFromFormat("d.m.Y", $yearPrice['from']);
                $datePriceTo        = DateTime::createFromFormat("d.m.Y", $yearPrice['to']);
                $tax                = str_replace(",", ".", $yearPrice['tax']);
                $datePrice          = str_replace(",", ".", $yearPrice['calcPrice']);
                if (is_null($datePrice) || $datePrice == "" || $datePrice <= 0) {
                    //TODO yearless prices beachten!
                    
                    $datePrice      = $default;
                }
                
                $datePrice          = str_replace(",", ".", $datePrice);
                
                
                $calculatedDates    = $this->getDateRangeToCalc($dtBuchungVon, $dtBuchungBis, $datePriceFrom, $datePriceTo);
                $dtPositionFrom     = $calculatedDates['von'];
                $dtPositionTo       = $calculatedDates['bis'];
                $allInOneTimeRange  = $calculatedDates['allInOne'];
                if ($allInOneTimeRange) {
                    $allPrices      = array();
                }
                $numberOfNights                         = date_diff($dtPositionFrom, $dtPositionTo, false);
                $numberOfNights                         = intval($numberOfNights->format('%R%a'));
                if ($numberOfNights > 0) {
                    $position                           = new RS_IB_Buchungsposition(RS_IB_Buchungsposition::ART_ZEITRAUM);
                    $position->setDatumVon($dtPositionFrom);
                    $position->setDatumBis($dtPositionTo);
                    $position->setTax($tax);
                    $calcTimeRangePosition->setTax($tax);
//                     $prices                             = rs_ib_price_calculation_util::calcPrice($datePrice, $tax, $priceIsNet, 1, $numberOfNights, 0); //$couponsToCalc
                    $prices                             = $this->calcPrice($datePrice, $tax, $priceIsNet, 1, $numberOfNights, 0); //$couponsToCalc
                    $positionBrutto                     = $prices["brutto"];
                    $positionPricePerNight              = $prices["calcBrutto"];
                    $position->setBrutto($positionBrutto);
                    $position->setNetto($prices["netto"]);
                    $position->setTaxValue($prices["tax"]);
                    $position->setDefaultPrice($positionPricePerNight);
                    array_push($positionArray, $position);
                    
                    $calcTimeRangeBrutto                = $calcTimeRangeBrutto + $positionBrutto;
                
                    /**
                     * Ermittle Aktionen der Buchungsposition
                     */
                    $aktionenToCalc                     = $buchungObj->getAktionen();
    //                 var_dump($aktionenToCalc);
                    foreach ($aktionenToCalc as $aktionToCalc) {
                        //Pruefe ob sich die Aktion nur auf einen Appartment Zeitraum beziehen soll
                        if ($aktionToCalc->getCalcType() == RS_IB_Model_Appartmentaktion::AKTION_CALC_APPARTMENT) {
                            //Pruefe ob die Aktion fuer die Aktuelle Position gueltig ist.
                            $validAktionDates                 = $aktionToCalc->getValidDates();
                            foreach ($validAktionDates as $validDate) {
                                $aktionPosition         = array();
                                $completeAktion         = false;
                                $calcAktionFrom         = 0;
                                $calcAktionTo           = 0;
                                $aktionPreis            = 0;
                                $aktionFrom             = DateTime::createFromFormat("d.m.Y", $validDate["from"]);
                                $aktionTo               = DateTime::createFromFormat("d.m.Y", $validDate["to"]);
                                
                                $aktionDatesToCalc      = $this->getDateRangeToCalc($dtPositionFrom, $dtPositionTo, $aktionFrom, $aktionTo);
                                $calcAktionFrom         = $aktionDatesToCalc['von'];
                                $calcAktionTo           = $aktionDatesToCalc['bis'];
                                $completeAktion         = $aktionDatesToCalc['allInOne'];
                                $actionNumberOfNights   = date_diff($calcAktionFrom, $calcAktionTo, false);
                                $actionNumberOfNights   = intval($actionNumberOfNights->format('%R%a'));
                                if ($actionNumberOfNights > 0) {
                                    if ($aktionToCalc->getValueType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_PERCENT) {
    //                                     $aktionsWert        = $positionBrutto * ($aktionToCalc->getPreis() / 100);$positionPricePerNight
                                        $aktionPreis        = $positionPricePerNight * ($aktionToCalc->getPreis() / 100);
                                        $aktionsWert        = $aktionPreis * $actionNumberOfNights;
                                        if ($aktionToCalc->getAktionType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_DISCOUNT) {
                                            $aktionsWert    = $aktionsWert * -1;
                                        }
                                        $calcTimeRangeBrutto  = $calcTimeRangeBrutto + $aktionsWert;
                                        $aktionPosition     = new RS_IB_Buchungsposition(RS_IB_Buchungsposition::ART_AKTION);
                                        $aktionPosition->setDatumVon($calcAktionFrom);
                                        $aktionPosition->setDatumBis($calcAktionTo);
                                        $aktionPosition->setBrutto($aktionsWert);
                                        $aktionPosition->setPositionObject($aktionToCalc);
                                        $aktionPosition->setDefaultPrice($aktionPreis);
                                        
                                        array_push($positionArray, $aktionPosition);
                                    }
                                    elseif ($aktionToCalc->getValueType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_TOTAL) {
                                        $aktionNotInArray = true;
                                        foreach ($totalAktionPositions as $actualTotalPosition) {
                                            if ($actualTotalPosition->getPositionObject()->getTermId() == $aktionToCalc->getTermId()) {
                                                //Aktion wurde bereits beruecksichtig, daher nicht mehr mit aufnehmen.
                                                $aktionNotInArray = false;
                                                break;
                                            }
                                        }
                                        if ($aktionNotInArray) {
                                            $aktionsWert        = $aktionToCalc->getPreis();
                                            if ($aktionToCalc->getAktionType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_DISCOUNT) {
                                                $aktionsWert    = $aktionsWert * -1;
                                            }
                                            $totalAktionPosition = new RS_IB_Buchungsposition(RS_IB_Buchungsposition::ART_AKTION);
                                            $totalAktionPosition->setDatumVon($calcAktionFrom);
                                            $totalAktionPosition->setDatumBis($calcAktionTo);
                                            $totalAktionPosition->setBrutto($aktionsWert);
                                            $totalAktionPosition->setPositionObject($aktionToCalc);
    //                                         $totalAktionPosition->setDefaultPrice($aktionsWert);
                                            
                                            array_push($totalAktionPositions, $totalAktionPosition);
                                        }
                                    }
                                }
                                if ($completeAktion) {
                                    break;
                                }
                            }
                        }
                    }
                }
    //         $coupons         = $buchungObj->getCoupons(); //Die Coupons werden bei Eingabe (und validierung) dem Buchungssatz hinzugefuegt.
                if ($allInOneTimeRange) {
                    break;
                }
            }
        }
        foreach ($totalAktionPositions as $totalAktion) {
            array_push($positionArray, $totalAktion);
            $calcTimeRangeBrutto        = $calcTimeRangeBrutto + $totalAktion->getBrutto();
        }
//         $totalPrices                    = rs_ib_price_calculation_util::calcPrice($calcTimeRangeBrutto, $calcTimeRangePosition->getTax(), "off", 0, $anzahlNaechte, 0);
        $totalPrices                    = $this->calcPrice($calcTimeRangeBrutto, $calcTimeRangePosition->getTax(), "off", 0, $anzahlNaechte, 0);
        $calcTimeRangePosition->setBrutto($calcTimeRangeBrutto);
        $calcTimeRangePosition->setDefaultPrice($calcTimeRangeBrutto / $anzahlNaechte);
        $calcTimeRangePosition->setTaxValue($totalPrices["tax"]);
        $calcTimeRangePosition->setNetto($totalPrices["netto"]);
        $timeTaxKey                     = $calcTimeRangePosition->getTaxKey();
        if (!(array_key_exists($timeTaxKey, $allTaxes))) {
            $allTaxes[$timeTaxKey]["taxValue"]    = $calcTimeRangePosition->getTaxValue();
            $allTaxes[$timeTaxKey]["brutto"] = $calcTimeRangePosition->getBrutto();
        } else {
            $allTaxes[$timeTaxKey]["taxValue"]    = $allTaxes[$timeTaxKey]["taxValue"] + $calcTimeRangePosition->getTaxValue();
            $allTaxes[$timeTaxKey]["brutto"] = $allTaxes[$timeTaxKey]["brutto"] + $calcTimeRangePosition->getBrutto();
        }
        $allTaxes[$timeTaxKey]["tax"]        = $calcTimeRangePosition->getTax();
        $biggestBrutto                  = $calcTimeRangeBrutto;
        $biggestBruttoType              = 1;
/*
 ***************************************************************************************************
 *Zeitraum Positionen Ende
 ***************************************************************************************************
 */
        
/*
 ***************************************************************************************************
 *Optionen Positionen Anfang
 ***************************************************************************************************
 */
        $buchungsOptions                = $buchungObj->getOptions();
        $optionIndex                    = 0;
        $allOptionPrices                = array();
        //TODO Aktionen fuer Optionen!
        foreach ($buchungsOptions as $option) {
            $name                       = "";
            $optionPosition             = new RS_IB_Buchungsposition(RS_IB_Buchungsposition::ART_OPTION);
            
            $priceObj                   = array();
            $optionObj                  = array();
            $optionFree                 = false;
            $id                         = $option['id'];
            foreach ($coupons as $myCoupon) {
                $optionIdArray          = array();
                $options                = $myCoupon->getApartmentOption();
                if (sizeof($options) > 0) {
                    foreach ($options as $testOption) {
                        if ($id == $testOption) {
                            $optionFree = true;
                            $name       = $myCoupon->getName();
                            break;
                        }
                    }
                    if ($optionFree) {
                        break;
                    }
                }
            }
            $mwstPercent                = $option['mwst'];
            $tax                        = str_replace(",", ".", $mwstPercent);
            $key                        = ((String)((float)$tax));
            $tax                        = ((float)$tax)/100;
        
            $price                      = str_replace(",", ".", $option['price']);
            $price                      = (float)$price;
            $calcType                   = $option['calc'];
            $bruttoOption               = 0;
            $taxesOption                = 0;
            $nettoOption                = 0;
            if (!$optionFree) {
//                 $priceObj               = rs_ib_price_calculation_util::calcPrice($price, $tax, $priceIsNet, $calcType, $anzahlNaechte, $square); //$couponsToCalc
            	$priceObj               = $this->calcPrice($price, $tax, $priceIsNet, $calcType, $anzahlNaechte, $square); //$couponsToCalc
                $bruttoOption           = $priceObj["brutto"];
                $taxesOption            = $priceObj["tax"];
                $nettoOption            = $priceObj["netto"];
            }
            $optionObj['id']            = $id;
            $optionObj['price']	        = $option['price'];
            $optionObj['name']	        = $option['name'];
            $optionObj['calculation']   = $calcType;
            $optionObj['mwstPercent']   = $key;
            $optionPosition->setPositionObject($optionObj);
            $optionPosition->setName($name);
            $optionPosition->setBrutto($bruttoOption);
            $optionPosition->setTax($tax);
            $optionPosition->setDefaultPrice($option['price']);
            $optionPosition->setTaxValue($taxesOption);
            $optionPosition->setNetto($nettoOption);
            
            $optionTaxKey                = $optionPosition->getTaxKey();
            if (!(array_key_exists($optionTaxKey, $allTaxes))) {
                $allTaxes[$optionTaxKey]["taxValue"]    = $optionPosition->getTaxValue();
                $allTaxes[$optionTaxKey]["brutto"]      = $optionPosition->getBrutto();
            } else {
                $allTaxes[$optionTaxKey]["taxValue"]    = $allTaxes[$optionTaxKey]["taxValue"] + $optionPosition->getTaxValue();
                $allTaxes[$optionTaxKey]["brutto"]      = $allTaxes[$optionTaxKey]["brutto"] + $optionPosition->getBrutto();
            }
            $allTaxes[$optionTaxKey]["tax"]             = $tax;
            $calcOptionBrutto           = $calcOptionBrutto + $bruttoOption;
//             $optionObj['optionFree']    = $optionFree;
//             $optionObj["brutto"]        = $priceObj["brutto"];
//             $optionObj["netto"]         = $priceObj["netto"];
//             $optionObj["tax"]           = $priceObj["tax"];
            if ($biggestBrutto < $bruttoOption) {
                $biggestBrutto          = $bruttoOption;
                $biggestBruttoType      = 2;
                $biggestBruttoIndex     = $optionIndex;
            }
            $optionIndex++;
            array_push($positionOptionArray, $optionPosition);
        }
/*
 ***************************************************************************************************
 *Optionen Positionen Ende
 ***************************************************************************************************
 */
        $fullBrutto                     = $calcTimeRangeBrutto + $calcOptionBrutto;
        $calculatedBrutto               = $fullBrutto;
        
/*An diesen Punkt sind folgende Dinge berechnet:
 * - Verschiedene Zeitraumpreise
 * - Aktionen die sich NUR auf Zeitraumpreise beziehen
 * - Gebuchte Optionen
 *
*/

/*
 * Aktionen - Auf alles - Anfang
 */
        foreach ($aktionenToCalc as $aktionToCalc) {
            $aktionsWert                    = $aktionToCalc->getPreis();
            //Pruefe ob sich die Aktion nur auf einen Appartment Zeitraum beziehen soll
            if ($aktionToCalc->getCalcType() == RS_IB_Model_Appartmentaktion::AKTION_CALC_TOTAL) {
                $percent = 0;
                if ($aktionToCalc->getValueType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_TOTAL) {
                    if ($calculatedBrutto > 0) {
                        $percent                = (100 / $calculatedBrutto) * $aktionsWert;
                    }
                }
                elseif ($aktionToCalc->getValueType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_PERCENT) {
                    $percent                = $aktionsWert;
                }
                $calculatedBrutto           = $calculatedBrutto - ($calculatedBrutto * ($percent / 100));
                foreach ($allTaxes as $taxKey => $taxValue) {
                    if ($aktionToCalc->getAktionType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_DISCOUNT) {
                        $taxValue["brutto"] = $taxValue["brutto"] - ($taxValue["brutto"] * ($percent / 100));
                    }
                    elseif ($aktionToCalc->getAktionType() == RS_IB_Model_Appartmentaktion::AKTION_VALUE_EXTRA_CHARGE) {
                        $taxValue["brutto"] = $taxValue["brutto"] + ($taxValue["brutto"] * ($percent / 100));
                    }
//                     $priceObj               = rs_ib_price_calculation_util::calcPrice($taxValue["brutto"], $taxValue["tax"], "off", 0, 0, 0);
                    $priceObj               = $this->calcPrice($taxValue["brutto"], $taxValue["tax"], "off", 0, 0, 0);
                    $taxValue["taxValue"]   = $priceObj["tax"];
                    $allTaxes[$taxKey]      = $taxValue;
                }
            }
        }
/*
 ***************************************************************************************************
 *Coupons - Auf alles - Anfang
 ***************************************************************************************************
 */
        $afterAllTimePosArray           = array();
//         $calculatedBrutto               = $fullBrutto;
        $summiertesBrutto               = 0;
        $couponsToCalc                  = array();
        $calcTimeRangePos               = clone $calcTimeRangePosition;
        if (sizeof($coupons) > 0 ) {
//             $newTimeRangeBrutto         = $calcTimeRangePos->getBrutto();
//             foreach ($coupons as $myCoupon) {
//                 if ($myCoupon->getGutscheinKz() == "off") { //Wertrabatt --> kein Gutschein
//                     if ($myCoupon->getType() == 1) { //VOLL
//                         $myCoupon->setPercent((100 / $calculatedBrutto) * $myCoupon->getValue());
//                     } elseif ($myCoupon->getType() == 2) { //PROZENT
//                         $myCoupon->setPercent($myCoupon->getValue());
//                     }
//                     array_push($couponsToCalc, $myCoupon);
//                     $calculatedBrutto   = $calculatedBrutto - ($calculatedBrutto * ($myCoupon->getPercent() / 100));
//                     $newTimeRangeBrutto = $newTimeRangeBrutto - ($newTimeRangeBrutto * $myCoupon->getPercent() / 100);
//                     foreach ($allTaxes as $taxKey => $taxValue) {
//                         $taxValue["brutto"] = $taxValue["brutto"] - ($taxValue["brutto"] * ($myCoupon->getPercent() / 100));
//                         $priceObj           = rs_ib_price_calculation_util::calcPrice($taxValue["brutto"], $taxValue["tax"], "off", 0, 0, 0);
//                         $taxValue["taxValue"] = $priceObj["tax"];
//                         $allTaxes[$taxKey] = $taxValue;
//                     }
//                 }
//             }
//             $calcTimeRangePos->setBrutto($newTimeRangeBrutto);
//             $summiertesBrutto = $summiertesBrutto + $newTimeRangeBrutto;
        }
        
        array_push($afterAllTimePosArray, $calcTimeRangePos);
//         $afterAllOptionsArray = array(); //clone $positionOptionArray; //<--geht nicht
//         if (sizeof($couponsToCalc) > 0) {
//             foreach ($positionOptionArray as $myOptionPosition) {
//                 $newOptionPosition = clone $myOptionPosition;
//                 $optionBrutto = $newOptionPosition->getBrutto();
//                 foreach ($couponsToCalc as $calcOptionCoupon) {
//                     $optionBrutto = $optionBrutto - ($optionBrutto * $calcOptionCoupon->getPercent()/100);
//                 }
//                 $newOptionPosition->setBrutto($optionBrutto);
//                 $summiertesBrutto = $summiertesBrutto + $optionBrutto;
//                 array_push($afterAllTimePosArray, $newOptionPosition);
//             }
//         }
        
        if (round($calculatedBrutto, 2) <> round($summiertesBrutto, 2)) {
//             //CENT DIFFERENZ!
//             $differenz = round($calculatedBrutto, 2) - round($summiertesBrutto, 2);//$calculatedBrutto - $bruttoVorGutschein;
//             $differenz = round($differenz*100, 2);
            
//             if ($biggestBruttoType == 1) { //Zeitraumbrutto
//                 $calcTimeRangePosition->setBrutto($calcTimeRangePosition->getBrutto() + ($differenz));
//             } elseif ($biggestBruttoType == 2) { //Optionsposition
//                 $optionPos  = $positionOptionArray[$biggestBruttoIndex];
//                 $optionPos->setBrutto($optionPos->getBrutto() + ($differenz));
//                 $positionOptionArray[$biggestBruttoIndex] = $optionsPos;
//             }
        }
                
/*
 ***************************************************************************************************
 *Coupons Ende
 ***************************************************************************************************
 */
        $calculatedNetto        = $calculatedBrutto;
        $calculatedEndBrutto    = $calculatedBrutto;
        foreach ($allTaxes as $taxKey => $taxValue) {
            $calculatedNetto    = $calculatedNetto - $taxValue["taxValue"];
        }
        
/*
 ***************************************************************************************************
 *Gutschein Anfang
 ***************************************************************************************************
 */
        if (sizeof($coupons) > 0 ) {
            $newTimeRangeBrutto         = $calcTimeRangePos->getBrutto();
            foreach ($coupons as $myCoupon) {
                if ($myCoupon->getGutscheinKz() == "on") { //Gutscheinrabatt --> kein Wertrabatt
                    //GUTSCHEIN NUR ALS VOLL!
                    $calculatedEndBrutto = $calculatedEndBrutto - $myCoupon->getValue();
//                     if ($myCoupon->getType() == 1) { //VOLL
//                         $myCoupon->setPercent((100 / $calculatedBrutto) * $myCoupon->getValue());
//                     }
//                     elseif ($myCoupon->getType() == 2) { //PROZENT
//                         $myCoupon->setPercent($myCoupon->getValue());
//                     }
                    
                }
            }
            $calcTimeRangePos->setBrutto($newTimeRangeBrutto);
            $summiertesBrutto = $summiertesBrutto + $newTimeRangeBrutto;
        }
/*
 ***************************************************************************************************
 *Gutschein Ende
 ***************************************************************************************************
 */
        
        //$fullPricePositionArray
        array_push($positionArray, $calcTimeRangePosition); //erst am ende Einfuegen, falls noch Coupons dazu kommen.
        $buchungObj->setBuchungsPositionen($positionArray);
        $buchungObj->setOptionenPositionen($positionOptionArray);
        $buchungObj->setAfterAllTimePos($afterAllTimePosArray);
        $buchungObj->setFullBrutto($fullBrutto);
        ksort($allTaxes);
        $buchungObj->setAllTaxes($allTaxes);
//         $buchungObj->setCalculatedEndBrutto($calculatedBrutto);
        $buchungObj->setCalculatedNetto($calculatedNetto);
        $buchungObj->setCalculatedBrutto($calculatedBrutto);
        $buchungObj->setCalculatedEndBrutto($calculatedEndBrutto);
        return $buchungObj;
    }
}
// endif;
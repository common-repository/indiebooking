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
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Table_Appartment' ) ) :
/**
 * RS_IB_Table_Appartment ist dafuer zustuendig, die Meta-Appartmentwerte in der entsprechenden Tabelle zu speichern
 * und bei Abruf als Objekt zurueck zu geben.
 * @author schmitt
 *
 */
class RS_IB_Table_Appartment extends RS_IB_Table_Postmeta
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
        if (is_admin()) {
            add_action("rs_indiebooking_admin_saveOrUpdateApartment", array($this, "saveOrUpdateAppartment"),1,1);
        }
    }
    
    /* @var $modelAppartment RS_IB_Model_Appartment */
    /* @var $zeitraumTable RS_IB_Table_Appartment_Zeitraeume */
    /* @var $saisonTable RS_IB_Table_Appartment_Saison */
    public function saveOrUpdateAppartment( $modelAppartment ) {
        global $RSBP_DATABASE;
//         $zeitraumTable      = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
        
//         //TODO initialisierung der Tabellen muss woanders hin verschoben werden, damit die
//         //speicher actions auch korrekt ausgefuehrt werden.
//         $gesperrtZeitTable  = $RSBP_DATABASE->getTable(RS_IB_Model_Apartment_Gesperrter_Zeitraum::RS_TABLE);
//         $saisonTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Saison::RS_TABLE);
        RS_Indiebooking_Log_Controller::write_log(
            "Apartment gespeichert / geandert",
            __LINE__,
            __CLASS__,
            RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
        $post_id            = $modelAppartment->getPostId();
        if ($post_id) {
            if (!is_null($modelAppartment->getJahr())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_JAHR, $modelAppartment->getJahr());
            }
            if (!is_null($modelAppartment->getPreis())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_PREIS, $modelAppartment->getPreis());
            }
            if (!is_null($modelAppartment->getBookableDates())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_DATES, $modelAppartment->getBookableDates());
            }
            if (!is_null($modelAppartment->getMwstId())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_MWST_ID, $modelAppartment->getMwstId());
            }
            if (!is_null($modelAppartment->getQuadratmeter())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_QM, $modelAppartment->getQuadratmeter());
            }
            if (!is_null($modelAppartment->getMinDateRange())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_MIN_RANGE, $modelAppartment->getMinDateRange());
            }
//             if (!is_null($modelAppartment->getPriceIsNet())) {
//                 $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_PRICE_NET_KZ, $modelAppartment->getPriceIsNet());
//             }
            if (!is_null($modelAppartment->getYearlessPriceDates())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_PRICE_DATES, $modelAppartment->getYearlessPriceDates());
//                 foreach ($modelAppartment->getYearlessPriceDates() as $yearless_prices) {
                    /*
                        $priceDates[$i]["from"]  = $priceFromDates[$i];
                        $priceDates[$i]["to"]    = $priceToDates[$i];
                        $priceDates[$i]["price"] = $pricePrices[$i];
                     */
//                     add_post_meta($post_id, $meta_key, $meta_value);
//                 }
            }
            if (!is_null($modelAppartment->getArrivalDays())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ARRIVAL_DAYS, $modelAppartment->getArrivalDays());
            } else {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ARRIVAL_DAYS, array());
            }
            if (!is_null($modelAppartment->getLocation())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_LOCATION, $modelAppartment->getLocation());
            }
            if (!is_null($modelAppartment->getStreet())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_STREET, $modelAppartment->getStreet());
            }
            if (!is_null($modelAppartment->getZipCode())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ZIPCODE, $modelAppartment->getZipCode());
            }
            if (!is_null($modelAppartment->getAnzahlBetten())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_BETTEN, $modelAppartment->getAnzahlBetten());
            }
            if (!is_null($modelAppartment->getAnzahlPersonen())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_PERSONEN, $modelAppartment->getAnzahlPersonen());
            }
            if (!is_null($modelAppartment->getAnzahlPersonenVorbelegung())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_PERSONEN_VORBELEGUNG, $modelAppartment->getAnzahlPersonenVorbelegung());
            }
            if (!is_null($modelAppartment->getAllowVaryingPrice())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ALLOW_VARYING_PRICE, $modelAppartment->getAllowVaryingPrice());
            }
            if (!is_null($modelAppartment->getShowOnStartPage())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_SHOWONSTART, $modelAppartment->getShowOnStartPage());
            }
            if (!is_null($modelAppartment->getLocationDescription())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_LOCATION_DESC, $modelAppartment->getLocationDescription());
            }
            if (!is_null($modelAppartment->getShortDescription())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_KURZBESCHREIBUNG, $modelAppartment->getShortDescription());
            }
            if (!is_null($modelAppartment->getLat())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_LAT, $modelAppartment->getLat());
            }
            if (!is_null($modelAppartment->getLng())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_LNG, $modelAppartment->getLng());
            }
            if (!is_null($modelAppartment->getAnzahlZimmer())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_ZIMMER, $modelAppartment->getAnzahlZimmer());
            }
            if (!is_null($modelAppartment->getAnzahlDoppelBetten())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_DOPPEL_BETTEN, $modelAppartment->getAnzahlDoppelBetten());
            }
            if (!is_null($modelAppartment->getAnzahlEinzelBetten())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ANZAHL_EINZEL_BETTEN, $modelAppartment->getAnzahlEinzelBetten());
            }
            if (!is_null($modelAppartment->getPflichtOptionen())) {
                $this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_PFLICHT_OPTIONEN, $modelAppartment->getPflichtOptionen());
            }
            if (!is_null($modelAppartment->getBookingComHotelId())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_BOOKING_HOTELID, $modelAppartment->getBookingComHotelId());
            }
            if (!is_null($modelAppartment->getBookingComRoomId())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_BOOKING_ROOMID, $modelAppartment->getBookingComRoomId());
            }
            if (!is_null($modelAppartment->getBookingComDefaultRateId())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_BOOKING_RATEID, $modelAppartment->getBookingComDefaultRateId());
            }
            if (!is_null($modelAppartment->getBookingComAufschlag())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_BOOKING_AUFSCHLAG, $modelAppartment->getBookingComAufschlag());
            }
            if (!is_null($modelAppartment->getBookingComAufschlagTyp())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_BOOKING_AUFSCHLAGTYP, $modelAppartment->getBookingComAufschlagTyp());
            }
            if (!is_null($modelAppartment->getSecurityDeposit())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_SECURITY_DEPOSIT, $modelAppartment->getSecurityDeposit());
            }
            if (!is_null($modelAppartment->getExtraCharge()) && !empty($modelAppartment->getExtraCharge())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_EXTRA_CHARGE, $modelAppartment->getExtraCharge());
            }
            if (!is_null($modelAppartment->getOnlyInquire()) && !empty($modelAppartment->getOnlyInquire())) {
            	$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_ONLYINQUIRE, $modelAppartment->getOnlyInquire());
            }
            if (!is_null($modelAppartment->getFeatures())) {
            	/*
            	 * Zuerst loesche ich alle Werte der Features des Apartments
            	 * Anschliessend schreibe ich fuer jedes feature einen neuen Satz in die Datenbank
            	 * das gewaehrleistet, dass ich per SQL auf die features gut zugreifen kann
            	 * und bei der Anzeige des Apartments dennoch die Daten als Array zurueck bekomme
            	 */
            	delete_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_FEATURES);
            	foreach ($modelAppartment->getFeatures() as $feature) {
//             		$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_FEATURES, $feature);
//             		$this->update_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_FEATURES, $feature);
            		add_post_meta($post_id, RS_IB_Model_Appartment::APPARTMENT_FEATURES, $feature);
            	}
            }
//             $gesperrtZeitTable->saveOrUpdateApartmentGesperrterZeitraum($modelAppartment);
            /*
             * Update Carsten Schmitt: 23.09.2016
             * wird nicht mehr gebraucht, da die Preise und Zeitrueume durch Saisons
             * und "nicht buchbare" Zeitrueume definiert werden.
             */
//             $zeitraumTable->saveOrUpdateAppartmentZeitraume($modelAppartment);
//             $saisonTable->saveOrUpdateAppartmentSaison($modelAppartment);
        }
    }
    
    
    public function getApartmentFirstCategoryName($apartmentId) {
    	$all_post_category_terms    = get_the_terms($apartmentId , 'rsappartmentcategories' );
    	$categories                 = "";
    	$firstCategoryName			= "";
    	if ($all_post_category_terms !== false) {
    		foreach ($all_post_category_terms as $category_term) {
    			if ($firstCategoryName == "") {
    				$firstCategoryName = $category_term->name;
//     				$apartment_title	= $firstCategoryName;
    				break;
    			}
    		}
    	}
    	return $firstCategoryName;
    }
    
    /*
     * Speichere die ApartmentIDs doch bei den Optionen anstatt umgekehrt
     */
    public function updatePflichtOption($apartmentId, $optionId, $activeKz) {
//         $apartment          = $this->getAppartment($apartmentId);
//         $pflichtOptionen    = $apartment->getPflichtOptionen();
//         if (!in_array($optionId, $pflichtOptionen) && $activeKz == 'on') {
//             array_push($pflichtOptionen, $optionId);
//         } elseif (in_array($optionId, $pflichtOptionen) && $activeKz == 'off') {
//             foreach ($pflichtOptionen as $key => $option) {
//                 if ($option == $optionId) {
//                     unset($pflichtOptionen[$key]);
//                     break;
//                 }
//             }
//         }
//         $this->update_post_meta($apartmentId, RS_IB_Model_Appartment::APPARTMENT_PFLICHT_OPTIONEN, $pflichtOptionen);
    }
    
    /**
     * Wird aufgerufen, wenn bspw. in der Kampagnenverwaltung eine Kampagne einem Apartment zugeordnet wird
     * @param unknown $apartmentId
     * @param unknown $taxonomy
     * @param unknown $taxonomyId
     * @param unknown $activeKz
     * @return string
     */
    public function updateApartmentTaxonomy( $apartmentId, $taxonomy, $taxonomyId, $activeKz) {
        if ($activeKz == "on") {
           $term_taxonomy_ids = wp_set_object_terms( intval($apartmentId), array($taxonomyId), $taxonomy, true );
        } else {
           $term_taxonomy_ids = wp_remove_object_terms(intval($apartmentId), $taxonomyId, $taxonomy);
        }
        if ( is_wp_error( $term_taxonomy_ids ) ) {
            return "error";
        } else {
            return "success";
        }
    }
    
    public function loadMaxAnzahlBetten() {
        $maxBetten          = $this->select_end_meta_value(RS_IB_Model_Appartment::APPARTMENT_ANZAHL_BETTEN);
        if (!is_null($maxBetten)) {
            $maxBetten      = intval($maxBetten);
        } else {
            $maxBetten      = 0;
        }
        return $maxBetten;
    }
    
    public function loadMaxAnzahlPersonen() {
        $maxPersonen        = $this->select_end_meta_value(RS_IB_Model_Appartment::APPARTMENT_ANZAHL_PERSONEN);
        if (!is_null($maxPersonen)) {
            $maxPersonen    = intval($maxPersonen);
        } else {
            $maxPersonen    = 0;
        }
        return $maxPersonen;
    }
    
    public function loadMaxAnzahlZimmer() {
        $maxZimmer      = $this->select_end_meta_value(RS_IB_Model_Appartment::APPARTMENT_ANZAHL_ZIMMER);
        if (!is_null($maxZimmer)) {
            $maxZimmer  = intval($maxZimmer);
        } else {
            $maxZimmer  = 0;
        }
        return $maxZimmer;
    }
    
    public function loadAllLocations() {
        $locations      = $this->select_list_of_meta_value(RS_IB_Model_Appartment::APPARTMENT_LOCATION_DESC);
        if (is_null($locations)) {
            $locations  = array();
        }
        return $locations;
    }
    
    public function loadAllFeatures() {
    	$allFeatures	= array();
    	$features      	= $this->select_list_of_meta_value(RS_IB_Model_Appartment::APPARTMENT_FEATURES);
    	if (!is_null($features)) {
    		foreach ($features as $f) {
    			array_push($allFeatures, $f[0]);
    		}
    	}
    	return $allFeatures;
    }
    
    
    public function loadAllHotelIDs() {
    	$hotelIds 			= array();
    	$hotelIdResults		= $this->select_list_of_post_meta_value(
    							RS_IB_Model_Appartment::RS_POSTTYPE, RS_IB_Model_Appartment::APPARTMENT_BOOKING_HOTELID);
    	if (!is_null($hotelIdResults)) {
    		$hotelIds 	= array();
    		foreach ($hotelIdResults as $ids) {
    			array_push($hotelIds, $ids[0]);
    		}
    	}
    	return $hotelIds;
    }
    
    /**
     * Gibt das Apartment anhand der RoomId von Booking.com zurueck
     * @param unknown $roomId
     * @return NULL|RS_IB_Model_Appartment
     */
    public function getAppartmentByBookingComRoomId($roomId) {
    	$args = array(
    		'post_type' => RS_IB_Model_Appartment::RS_POSTTYPE,
    		'meta_query' => array(
    			array(
    				'key' => RS_IB_Model_Appartment::APPARTMENT_BOOKING_ROOMID,
    				'value' => $roomId,
    				'compare' => '=',
    			)
    		)
    	);
    	$modelApartment = null;
    	$my_query 		= new WP_Query($args);
    	if( $my_query->have_posts() ) {
    		while( $my_query->have_posts() ): $my_query->the_post();
    			$postId	= get_the_ID();
    			$modelApartment = $this->getAppartment($postId);
    		endwhile;
    	}
    	return $modelApartment;
    }
    
    public function getApartmentBaseData($postId) {
    	$custom                 = get_post_custom( $postId );
    	$modelAppartment        = new RS_IB_Model_Appartment($custom, $postId);
    	
    	return $modelAppartment;
    }
    
    /* @var $appartmentOptionTable RS_IB_Table_Appartmentoption */
    /* @var $notBookableZeitTable RS_IB_TABLE_Apartment_Gesperrter_Zeitraum */
    /**
     * Gibt das Appartment mit allen Optionen zurueck
     * @param unknown $post_id
     * @return RS_IB_Model_Appartment
     */
    public function getAppartment( $my_post_id, $onlyactiveaction = false, $onlyFutureDates = false ) {
        global $RSBP_DATABASE;
        
        $options                = array();
        $aktionen               = array();
        $aktionTable            = null;
        $appartmentOptionTable  = null;
        if (class_exists("RS_IB_Model_Appartmentoption")) {
            $appartmentOptionTable  = $RSBP_DATABASE->getTable(RS_IB_Model_Appartmentoption::RS_TABLE);
        }
        if (class_exists("RS_IB_Model_Appartmentaktion")) {
            $aktionTable        = $RSBP_DATABASE->getTable(RS_IB_Model_Appartmentaktion::RS_TABLE);
        }
        
        $zeitraumTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
        $notBookableZeitTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Apartment_Gesperrter_Zeitraum::RS_TABLE);
//         print_r(get_post($post_id));
        $custom                 = get_post_custom( $my_post_id );
//         var_dump($custom);
        $modelAppartment        = new RS_IB_Model_Appartment($custom, $my_post_id);
//         $modelAppartment->setPostId($post_id);
        
        if (!is_null($appartmentOptionTable)) {
            $options            = $appartmentOptionTable->getPostAppartmentOptions( $my_post_id );
        }
        $modelAppartment->setOptionen($options);
        if (!is_null($aktionTable)) {
            $aktionen           = $aktionTable->getPostAppartmentActions( $my_post_id, $onlyactiveaction );
        }
        $modelAppartment->setAktionen($aktionen);
        $zeitraueme             = $zeitraumTable->loadApartmentZeitraume( $my_post_id );
        $modelAppartment->setZeitraumeDB($zeitraueme);
        $offeneZeitraueme       = $zeitraumTable->loadApartmentZeitraume($my_post_id, true);
        $modelAppartment->setOffenZeitraumeDB($offeneZeitraueme);
        $notbookableDates       = $notBookableZeitTable->loadApartmentGesperrteZeitraume($my_post_id, $onlyFutureDates);
        $modelAppartment->setNotbookableDates($notbookableDates);
        
//         $saisonDates            = $saisonTable->loadApartmentSaison($my_post_id);

        //TODO auslagern!!!
        if (class_exists("RS_IB_Model_Appartment_Saison")) {
            $saisonDates            = array();
            $saisonTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Saison::RS_TABLE);
            if (!is_null($saisonTable)) {
                $saisonDates        = $saisonTable->loadApartmentSaisonsGroupedByYear($my_post_id);
            }
            $modelAppartment->setYearlessPriceDates($saisonDates);
        }
        
        if (class_exists("RS_IB_Table_ApartmentDegression")) {
        	$degressions			= array();
        	$degressionTable		= $RSBP_DATABASE->getTable(RS_IB_Model_ApartmentDegression::RS_TABLE);
        	if (!is_null($degressionTable)) {
        		$degressions        = $degressionTable->loadApartmentDegression($my_post_id);
        	}
        	$modelAppartment->setDegression($degressions);
        }
//         $apNotBookableDates     = array();
//         foreach ($notbookableDates as $dates) {
//             $dateArray['from'] = $dates['date_from'];
//             $apNotBookableDates
//         }
        return $modelAppartment;
    }

    public function getAllApartmentsWithDefaultData() {
    	$type   	= RS_IB_Model_Appartment::RS_POSTTYPE;
    	$args   	= array (
    			'post_type'         => $type,
    			'post_status'       => 'publish',
    	);
    	$my_query 	= null;
    	$my_query 	= new WP_Query($args);
    	$apartmentArray             = array();
    	if( $my_query->have_posts() ) {
    		while ($my_query->have_posts()) : $my_query->the_post();
    			$postId					= get_the_ID();
		    	$custom                 = get_post_custom( $postId );
		    	$modelAppartment        = new RS_IB_Model_Appartment($custom, $postId);
		    	array_push($apartmentArray, $modelAppartment);
    		endwhile;
    	}
    	
    	return $apartmentArray;
    }
    
    public function getAllApartments($onlyFutureDates = false, $orderByTitle = false) {
        global $wpdb;
        //         global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
        $apartmentArray             = array();
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        
        $type   = RS_IB_Model_Appartment::RS_POSTTYPE;
        $args   = array (
            'post_type'         => $type,
            'post_status'       => 'publish',
        );
        if ($orderByTitle) {
            $args['orderby']    = 'title';
            $args['order']      = 'ASC';
        }
        
        $my_query = null;
        $my_query = new WP_Query($args);
        if( $my_query->have_posts() ) {
            while ($my_query->have_posts()) : $my_query->the_post();
                $postId                     = get_the_ID();
                $appartment                 = $this->getAppartment($postId, false, $onlyFutureDates);
//                 $bookedDates                = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($postId);
//                 $bookableDates              = json_encode($appartment->getBookableDates());
//                 $bookedDates                = json_encode($bookedDates);
//                 $arrivalDays                = json_encode($appartment->getArrivalDays());
                array_push($apartmentArray, $appartment);
            endwhile;
        }
        wp_reset_query();  // Restore global post data stomped by the_post().
        return $apartmentArray;
    }
    
    
    
    /* @var $appartmentTable RS_IB_Table_Appartment */
    /* @var $appartmentBuchungsTable RS_IB_Table_Appartment_Buchung */
    /* @var $appBuchungZeitraumTable RS_IB_Table_Appartment_Zeitraeume */
    /* @var $notBookableDatesTable RS_IB_Table_Apartment_Gesperrter_Zeitraum */
    /**
     * Diese Methode wird aktuell nur von indiebooking-booking.com aufgerufen!
     * @param unknown $apartmentId
     * @param unknown $minDate
     * @return array
     */
    public function getAvailableDates($apartmentId, $minDate) {
    	global $RSBP_DATABASE;

    	$futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
    	if (!$futureAvailabilityYear) {
    		$futureAvailabilityYear	= 2;
    	}
    	$curMaxDate					= new DateTime("now");
    	$addYears					= "P".$futureAvailabilityYear."Y";
    	$curMaxDate->add(new DateInterval($addYears));
    	
//     	$appBuchungZeitraumTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Zeitraeume::RS_TABLE);
    	$appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    	$notBookableDatesTable      = $RSBP_DATABASE->getTable(RS_IB_Model_Apartment_Gesperrter_Zeitraum::RS_TABLE);
    	
    	$minDate					= rs_ib_date_util::convertDateValueToDateTime($minDate);
    	$curMaxDate					= rs_ib_date_util::convertDateValueToDateTime($curMaxDate);
    	
    	$verfuegbareDates			= array();
    	$verfuegbarFrom				= null;
    	$verfuegbarTo				= null;
		$notAvailableDates			= $appartmentBuchungsTable->getNotAvailableDatesByAppartmentId($apartmentId);
		$dateOk						= true;
		for ($i = 0; $i < sizeof($notAvailableDates); $i++) {
			$date       			= $notAvailableDates[$i];
			$from       			= rs_ib_date_util::convertDateValueToDateTime($date["from"]);
			$to         			= rs_ib_date_util::convertDateValueToDateTime($date["to"]);
			 
			if ($to->getTimestamp() > $minDate->getTimestamp()) {
				if ($minDate->getTimestamp() >= $from->getTimestamp() && $minDate->getTimestamp() <= $to->getTimestamp()) {
					//minDate liegt in einem gebuchten Zeitraum.
					$dateOk			= false;
				}
				if ($dateOk) {
					//minDate ist vefuegbar
					if (is_null($verfuegbarFrom)) {
						$verfuegbarFrom 	= clone $minDate;
					}
					if ($minDate->getTimestamp() < $from->getTimestamp()) {
						$verfuegbarTo		= clone $from;
						$minDate			= clone $to; //dadurch dauerschleife!
						//     					$verfuegbarTo->sub(new DateInterval('P1D'));
					} else {
						$verfuegbarTo		= clone $minDate;
					}
				}
				// 	    			else {
				//minDate ist gebucht oder blockert. Also nicht verfuegbar
				if (!is_null($verfuegbarFrom) && !is_null($verfuegbarTo)) {
					$curVerfuegbar 			= array();
					$curVerfuegbar['from'] 	= clone $verfuegbarFrom;
					$curVerfuegbar['to'] 	= clone $verfuegbarTo;
					array_push($verfuegbareDates, $curVerfuegbar);
		
					$verfuegbarFrom			= null;
					$verfuegbarTo			= null;
				}
				$minDate					= clone $to;
				$dateOk						= true;
				// 	    			}
			} else {
				//wenn $to < $minDate, dann wurde $to bereits beachtet.
			}
			$minDate->add(new DateInterval('P1D'));
		}
		/*
		 * Braucht man die naechste Pruefung ueberhaupt noch?!
		 */
		if (!is_null($verfuegbarFrom) && !is_null($verfuegbarTo)) {
			$curVerfuegbar 			= array();
			$curVerfuegbar['from'] 	= clone $verfuegbarFrom;
			$curVerfuegbar['to'] 	= clone $verfuegbarTo;
			array_push($verfuegbareDates, $curVerfuegbar);
		}
		/*
		 * *******************************************************
		 */
		
		/*
		 * Nachdem alle Bereiche zwischen den nicht verfuegbaren Zeitrauemen eingetragen
		 * wurden. Muss nun noch der restliche Zeitraum (in dem keine Buchung oder Sperrung mehr liegt)
		 * auch noch in das Array eingefuegt werden.
		 */
    	if ($minDate->getTimestamp() <= $curMaxDate->getTimestamp()) {
    		$curVerfuegbar 			= array();
    		$curVerfuegbar['from'] 	= clone $minDate;
    		$curVerfuegbar['to'] 	= clone $curMaxDate;
    		array_push($verfuegbareDates, $curVerfuegbar);
    	}
    	return $verfuegbareDates;
    }
    
}
// endif;
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
// if ( ! class_exists( 'RS_IB_Model_Appartment' ) ) :
add_filter("rs_indiebooking_get_all_apartment_ids_from_wpml", array("RS_IB_Model_Appartment", "getAllApartmentIdsFromWPMLId"), 10, 1);
add_filter("rs_indiebooking_get_original_apartment_id_from_wpml", array("RS_IB_Model_Appartment", "getOriginalApartmentIdFromWPMLId"), 10, 1);
class RS_IB_Model_Appartment extends RS_IB_Model_Postmeta
{
	
    public function __construct($data = array(), $postId = 0) {
        parent::__construct($data, $postId);
        
        //TODO wird oft aufgerufen, da der konstruktor mehrfach aufgerufen wird!
        add_filter("rs_indiebooking_admin_getSmallestApartmentPrice", array($this, "init_getSmallestPrice"), 10, 1);
    }

    public static function getOriginalApartmentIdFromWPMLId($wpmlId) {
    	return self::getOriginalIdFromWPMLId($wpmlId);
    }
    
    /*
	 * Ermittelt alle Ids des Apartments.
	 * D.h. in allen Verfuegbaren Sprachen
     */
    public static function getAllApartmentIdsFromWPMLId($wpmlId) {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	global $RSBP_TABLEPREFIX;
    	
    	$elementType = "post_".self::RS_POSTTYPE;
    	$allIds = self::getAllIdsFromWPMLId($wpmlId, $elementType);
    	/*
    	$useWpml = true;
    	
    	$allIds = array();
    	$allActiveLanguages = array();
    	if (!$useWpml) {
    		array_push($allIds, $wpmlId);
    	}
    	if ($useWpml && function_exists('icl_object_id') ) {
    		$wpmlId 			= intval($wpmlId);
    		$originalId			= self::getOriginalIdFromWPMLId($wpmlId);
//     		$allActiveLanguages	= apply_filters( 'wpml_active_languages', NULL);
    		$trids = apply_filters('wpml_get_element_translations', NULL, $originalId, 'post_rsappartment');
    		
			foreach ($trids as $foundTrIds) {
//     			$langKurz	= $languages['language_code'];
//     			$originalId = apply_filters( 'wpml_object_id', $wpmlId, 'post', TRUE, $langKurz );
				if (!is_null($foundTrIds)) {
					$foundedId = $foundTrIds->element_id;
					if (!in_array($foundedId, $allIds)) {
						array_push($allIds, $foundedId);
	    			}
				}
    		}
    		if (sizeof($allIds) <= 0) {
    			array_push($allIds, $wpmlId);
    		}
    	} else {
    		array_push($allIds, $wpmlId);
    	}
    	*/
    	return $allIds;
    }
    
    //TODO wird oft aufgerufen, da der konstruktor mehrfach aufgerufen wird!
    //logisch, da der filter fuer jedes apartment ausgefuehrt wird. daher muss man die ID's abgleichen
    //alternativ wuere es, den Filter nur einmal zu erstellen und durch eine Abfrage sich den Preis zu ermitteln.
    public function init_getSmallestPrice($smalestPriceArray) {
        $curId  = $this->getPostId();
        $arId   = $smalestPriceArray['postId'];
        if (intval($curId) == intval($arId)) {
            if (is_null($smalestPriceArray['price']) || $smalestPriceArray['price'] == "") {
                $smalestPriceArray['price'] = $this->getPreis();
            } else {
            	$curSmallestPrice = $smalestPriceArray['price'];
            	if ($curSmallestPrice > $this->getPreis()) {
            		$smalestPriceArray['price'] = $this->getPreis();
            	}
            }
        }
        return $smalestPriceArray;
    }
    
    const RS_TABLE                  = "RS_IB_APPARTMENT_TABLE";
    const RS_POSTTYPE               = "rsappartment";
    
    const APPARTMENT_JAHR           = "rs_appartment_jahr";
    const APPARTMENT_DATES          = "rs_appartment_dates";
    const APPARTMENT_PREIS          = "rs_appartment_preis";
    const APPARTMENT_MWST_ID        = "rs_appartment_mwstId";
    const APPARTMENT_QM             = "rs_appartment_quadratmeter";
    const APPARTMENT_MIN_RANGE      = "rs_appartment_min_date_range";
    const APPARTMENT_PRICE_NET_KZ   = "rs_appartment_price_is_net";
    const APPARTMENT_PRICE_DATES    = "rs_appartment_price_dates";
    const APPARTMENT_ARRIVAL_DAYS   = "rs_appartment_arrival_days";
    
    const APPARTMENT_LOCATION_DESC  = "rs_appartment_location_description";
    const APPARTMENT_SHOWONSTART    = "rs_appartment_show_on_start_page";
    const APPARTMENT_ONLYINQUIRE	= "rs_appartment_only_inquire";
    
    const APPARTMENT_LOCATION       = "rs_appartment_location";
    const APPARTMENT_ZIPCODE        = "rs_appartment_zipcode";
    const APPARTMENT_STREET         = "rs_appartment_street";
    
    const APPARTMENT_ANZAHL_BETTEN      = "rs_appartment_anzahl_betten";
    const APPARTMENT_ANZAHL_EINZEL_BETTEN = "rs_appartment_anzahl_einzel_betten";
    const APPARTMENT_ANZAHL_DOPPEL_BETTEN = "rs_appartment_anzahl_doppel_betten";
    const APPARTMENT_PFLICHT_OPTIONEN   = "rs_appartment_pflicht_optionen";
    
    const APPARTMENT_ANZAHL_PERSONEN    = "rs_appartment_anzahl_personen";
    const APPARTMENT_ANZAHL_PERSONEN_VORBELEGUNG    = "rs_appartment_anzahl_personen_vorbelegung";
    
    const APPARTMENT_ANZAHL_ZIMMER      = "rs_appartment_anzahl_zimmer";
    const APPARTMENT_ALLOW_VARYING_PRICE = "rs_appartment_allow_varyingPrice";
    const APPARTMENT_KURZBESCHREIBUNG   = "rs_appartment_short_description";
    
    const APPARTMENT_LAT            	= "rs_appartment_lat";
    const APPARTMENT_LNG            	= "rs_appartment_lng";
    
    const APPARTMENT_BOOKING_ROOMID		= "rs_appartment_bookingcom_room_id";
    const APPARTMENT_BOOKING_HOTELID	= "rs_appartment_bookingcom_hotel_id";
    const APPARTMENT_BOOKING_RATEID 	= "rs_appartment_bookingcom_default_rate_id";
    const APPARTMENT_BOOKING_APARTMENTPRICE = "rs_appartment_bookingcom_default_apartment_price";
    const APPARTMENT_BOOKING_AUFSCHLAG = "rs_appartment_bookingcom_aufschlag";
    const APPARTMENT_BOOKING_AUFSCHLAGTYP = "rs_appartment_bookingcom_aufschlag_typ";
    
    const APPARTMENT_SECURITY_DEPOSIT	= "rs_appartment_security_deposit";
    
    const APPARTMENT_FEATURES			= "rs_appartment_features";
    const APPARTMENT_EXTRA_CHARGE		= "rs_appartment_extraCharge";
    
    private $jahr               = null;
    private $preis              = null;
    private $bookableDates      = array();
    private $notbookableDates   = array();
    private $optionen           = array();
    private $mwstId             = 0;
    private $quadratmeter       = 0;
    private $minDateRange       = 0;
    private $priceIsNet         = "off";
    private $yearlessPriceDates = array();
    private $degression 		= array();
    private $aktionen           = array();
    private $arrivalDays        = array();
    
    private $locationDescription = "";
    private $showOnStartPage    = "";
    private $onlyInquire		= "";
    private $location           = "";
    private $zipCode            = "";
    private $street             = "";
    private $anzahlBetten       = 0;
    private $anzahlEinzelBetten = 0;
    private $anzahlDoppelBetten = 0;
    private $anzahlPersonen     = 0;
    private $anzahlPersonenVorbelegung = 0;
    private $anzahlZimmer       = 0;
    private $allowVaryingPrice  = 0;
    private $shortDescription   = "";
    
    private $pflichtOptionen    = array();
    private $lat                = 0.0;
    private $lng                = 0.0;
    
    private $zeitraumeDB        = array();
    private $offenZeitraumeDB   = array();
    
    private $bookingComHotelId	= 0;
    private $bookingComRoomId	= 0;
    private $bookingComDefaultRateId = 0;
    private $bookingComAufschlag 	= 0;
    private $bookingComAufschlagTyp = 0;
    
    private $securityDeposit 	= 0;
    
    private $features			= array();
    private $extraCharge 		= array();
    
    /**
     * @return the $notbookableDates
     */
    public function getNotbookableDates()
    {
        if (is_null($this->notbookableDates)) {
            return array();
        }
        return $this->notbookableDates;
    }

    /**
     * @param multitype: $notbookableDates
     */
    public function setNotbookableDates($notbookableDates)
    {
        $this->notbookableDates = $notbookableDates;
    }

    public function exchangeArray($data) {
        parent::exchangeArray($data);
        if (isset($data[self::APPARTMENT_JAHR])) {
            $jahr = $data[self::APPARTMENT_JAHR][0];
        } else {
            $jahr = "";
        }
        if (isset($data[self::APPARTMENT_PREIS])) {
            $preis = $data[self::APPARTMENT_PREIS][0];
        } else {
            $preis = "";
        }
        if (isset($data[self::APPARTMENT_DATES])) {
            $dates = unserialize($data[self::APPARTMENT_DATES][0]);
        } else {
            $dates = array();
        }
        if (isset($data[self::APPARTMENT_MWST_ID])) {
            $mwstId = $data[self::APPARTMENT_MWST_ID][0];
        } else {
            $mwstId = 0;
        }
        if (isset($data[self::APPARTMENT_QM])) {
            $qm = $data[self::APPARTMENT_QM][0];
        } else {
            $qm = 0;
        }
        if (isset($data[self::APPARTMENT_MIN_RANGE])) {
            $minDates = $data[self::APPARTMENT_MIN_RANGE][0];
        } else {
            $minDates = 0;
        }
        if (isset($data[self::APPARTMENT_PRICE_NET_KZ])) {
            $priceIsNet = $data[self::APPARTMENT_PRICE_NET_KZ][0];
        } else {
            $priceIsNet = "off";
        }
        if (isset($data[self::APPARTMENT_PRICE_DATES])) {
            $priceDates = unserialize($data[self::APPARTMENT_PRICE_DATES][0]);
        } else {
            $priceDates = array();
        }
        if (isset($data[self::APPARTMENT_ARRIVAL_DAYS])) {
            $arrivalDays = unserialize($data[self::APPARTMENT_ARRIVAL_DAYS][0]);
        } else {
            $arrivalDays = array();
        }
        
        if (isset($data[self::APPARTMENT_LOCATION])) {
            $location = ($data[self::APPARTMENT_LOCATION][0]);
        } else {
            $location = "";
        }
        if (isset($data[self::APPARTMENT_STREET])) {
            $street = ($data[self::APPARTMENT_STREET][0]);
        } else {
            $street = "";
        }
        if (isset($data[self::APPARTMENT_ZIPCODE])) {
            $zipCode = ($data[self::APPARTMENT_ZIPCODE][0]);
        } else {
            $zipCode = "";
        }
        
        if (isset($data[self::APPARTMENT_ANZAHL_BETTEN])) {
            $anzBetten = ($data[self::APPARTMENT_ANZAHL_BETTEN][0]);
        } else {
            $anzBetten = 0;
        }
        
        if (isset($data[self::APPARTMENT_ANZAHL_EINZEL_BETTEN])) {
            $anzEinzelBetten = ($data[self::APPARTMENT_ANZAHL_EINZEL_BETTEN][0]);
        } else {
            $anzEinzelBetten = 0;
        }
        
        if (isset($data[self::APPARTMENT_ANZAHL_DOPPEL_BETTEN])) {
            $anzDoppelBetten = ($data[self::APPARTMENT_ANZAHL_DOPPEL_BETTEN][0]);
        } else {
            $anzDoppelBetten = 0;
        }
        
        if (isset($data[self::APPARTMENT_ANZAHL_PERSONEN])) {
            $anzPersonen = ($data[self::APPARTMENT_ANZAHL_PERSONEN][0]);
        } else {
            $anzPersonen = 0;
        }
        
        if (isset($data[self::APPARTMENT_ANZAHL_PERSONEN_VORBELEGUNG])) {
        	$anzPersonenVorbelegung = ($data[self::APPARTMENT_ANZAHL_PERSONEN_VORBELEGUNG][0]);
        } else {
        	$anzPersonenVorbelegung = 0;
        }
        
        if (isset($data[self::APPARTMENT_ALLOW_VARYING_PRICE])) {
            $allowVaryingPrice = ($data[self::APPARTMENT_ALLOW_VARYING_PRICE][0]);
        } else {
            $allowVaryingPrice = 0;
        }
        if (isset($data[self::APPARTMENT_SHOWONSTART])) {
            $showOnStartPageKz = $data[self::APPARTMENT_SHOWONSTART][0];
        } else {
            $showOnStartPageKz = "off";
        }
        if (isset($data[self::APPARTMENT_ONLYINQUIRE])) {
        	$onlyInquire = $data[self::APPARTMENT_ONLYINQUIRE][0];
        } else {
        	$onlyInquire = "off";
        }
        if (isset($data[self::APPARTMENT_KURZBESCHREIBUNG])) {
            $shortDesc = $data[self::APPARTMENT_KURZBESCHREIBUNG][0];
        } else {
            $shortDesc = "";
        }
        if (isset($data[self::APPARTMENT_LAT])) {
            $lat = $data[self::APPARTMENT_LAT][0];
        } else {
            $lat = 0.0;
        }
        
        if (isset($data[self::APPARTMENT_LNG])) {
            $lng = $data[self::APPARTMENT_LNG][0];
        } else {
            $lng = 0.0;
        }
        
        if (isset($data[self::APPARTMENT_LOCATION_DESC])) {
            $locationDesc = $data[self::APPARTMENT_LOCATION_DESC][0];
        } else {
            $locationDesc = "";
        }
        if (isset($data[self::APPARTMENT_ANZAHL_ZIMMER])) {
            $anzahlZimmer = $data[self::APPARTMENT_ANZAHL_ZIMMER][0];
        } else {
            $anzahlZimmer = 0;
        }
        if (isset($data[self::APPARTMENT_PFLICHT_OPTIONEN])) {
            $pflichtOptionen = unserialize($data[self::APPARTMENT_PFLICHT_OPTIONEN][0]);
        } else {
            $pflichtOptionen = array();
        }
        
        if (isset($data[self::APPARTMENT_BOOKING_HOTELID])) {
        	$bookingHotelId = $data[self::APPARTMENT_BOOKING_HOTELID][0];
        } else {
        	$bookingHotelId = 0;
        }
        
        if (isset($data[self::APPARTMENT_BOOKING_ROOMID])) {
        	$bookingRoomId = $data[self::APPARTMENT_BOOKING_ROOMID][0];
        } else {
        	$bookingRoomId = 0;
        }
        
        if (isset($data[self::APPARTMENT_BOOKING_RATEID])) {
        	$bookingDefaultRateId = $data[self::APPARTMENT_BOOKING_RATEID][0];
        } else {
        	$bookingDefaultRateId = 0;
        }
        
        if (isset($data[self::APPARTMENT_BOOKING_AUFSCHLAG])) {
        	$bookingAufschlag = $data[self::APPARTMENT_BOOKING_AUFSCHLAG][0];
        } else {
        	$bookingAufschlag = 0;
        }
        
        if (isset($data[self::APPARTMENT_BOOKING_AUFSCHLAGTYP])) {
        	$bookingAufschlagTyp = $data[self::APPARTMENT_BOOKING_AUFSCHLAGTYP][0];
        } else {
        	$bookingAufschlagTyp = 0;
        }
        
        
        if (isset($data[self::APPARTMENT_SECURITY_DEPOSIT])) {
        	$securitycDeposit = $data[self::APPARTMENT_SECURITY_DEPOSIT][0];
        } else {
        	$securitycDeposit = 0;
        }
        
        if (isset($data[self::APPARTMENT_FEATURES])) {
        	$features = $data[self::APPARTMENT_FEATURES];
        } else {
        	$features = array();
        }
        if (isset($data[self::APPARTMENT_EXTRA_CHARGE])) {
        	$extraCharge = $data[self::APPARTMENT_EXTRA_CHARGE];
        } else {
        	$extraCharge = array();
        }
        
        $this->setJahr($jahr);
        $this->setBookableDates($dates);
        $this->setPreis($preis);
        $this->setMwstId($mwstId);
        $this->setQuadratmeter($qm);
        $this->setMinDateRange($minDates);
        $this->setPriceIsNet($priceIsNet);
        $this->setYearlessPriceDates($priceDates);
        $this->setArrivalDays($arrivalDays);
        
        $this->setLocationDescription($locationDesc);
        $this->setShowOnStartPage($showOnStartPageKz);
        $this->setOnlyInquire($onlyInquire);
        $this->setLocation($location);
        $this->setStreet($street);
        $this->setZipCode($zipCode);
        $this->setAnzahlBetten($anzBetten);
        $this->setAnzahlEinzelBetten($anzEinzelBetten);
        $this->setAnzahlDoppelBetten($anzDoppelBetten);
        $this->setAnzahlPersonen($anzPersonen);
        $this->setAnzahlPersonenVorbelegung($anzPersonenVorbelegung);
        $this->setAllowVaryingPrice($allowVaryingPrice);
        $this->setShortDescription($shortDesc);
        $this->setAnzahlZimmer($anzahlZimmer);
        $this->setPflichtOptionen($pflichtOptionen);
        
        $this->setLat($lat);
        $this->setLng($lng);
        
        $this->setBookingComHotelId($bookingHotelId);
        $this->setBookingComRoomId($bookingRoomId);
        $this->setBookingComDefaultRateId($bookingDefaultRateId);
        $this->setBookingComAufschlag($bookingAufschlag);
        $this->setBookingComAufschlagTyp($bookingAufschlagTyp);
        
        $this->setSecurityDeposit($securitycDeposit);
        
        $this->setFeatures($features);
        $this->setExtraCharge($extraCharge);
    }
    
    public function setJahr($jahr) {
        $this->jahr = $jahr;
    }
    
    public function getJahr() {
        if (is_null($this->jahr)) {
            return 0;
        }
        return $this->jahr;
    }
    
    public function setMwstId($mwstId) {
        $this->mwstId = $mwstId;
    }
    
    public function getMwstId() {
        if (is_null($this->mwstId)) {
            return 0;
        }
        return $this->mwstId;
    }
    
    public function setPreis($preis) {
        $this->preis = $preis;
    }
    
    public function getPreis() {
        if (is_null($this->preis)) {
            return 0;
        }
        if (is_string($this->preis)) {
        	$this->preis = floatval(str_replace(",", ".", $this->preis));
        }
        return $this->preis;
    }
    
    public function setBookableDates($dates) {
        $this->bookableDates = $dates;
    }
    
    public function getBookableDates() {
        if (is_null($this->bookableDates)) {
            return array();
        }
        return $this->bookableDates;
    }
    
    public function setOptionen($optionen) {
        $this->optionen = $optionen;
    }
    
    public function getOptionen() {
        if (is_null($this->optionen)) {
            return array();
        }
        return $this->optionen;
    }
 /**
     * @return the $quadratmeter
     */
    public function getQuadratmeter()
    {
        return $this->quadratmeter;
    }

 /**
     * @return the $minDateRange
     */
    public function getMinDateRange()
    {
        if (is_null($this->minDateRange) || "" == $this->minDateRange) {
            return 0;
        }
        return $this->minDateRange;
    }

 /**
     * @param number $quadratmeter
     */
    public function setQuadratmeter($quadratmeter)
    {
        $this->quadratmeter = $quadratmeter;
    }

 /**
     * @param number $minDateRange
     */
    public function setMinDateRange($minDateRange) {
        $this->minDateRange = $minDateRange;
    }
 /**
     * @return the $priceIsNet
     */
    public function getPriceIsNet() {
        $options            = get_option( 'rs_indiebooking_settings' );
        $priceIsNet         = null;
        if (key_exists('netto_kz', $options)) {
            $priceIsNet     = esc_attr__( $options['netto_kz'] );
        }
        if (!isset($priceIsNet) || is_null($priceIsNet)) {
            if (is_null($this->priceIsNet)) {
                $priceIsNet = "off";
            } else {
                $priceIsNet = $this->priceIsNet;;
            }
        }
        return $priceIsNet;
    }

 /**
     * @param boolean $priceIsNet
     */
    public function setPriceIsNet($priceIsNet)
    {
        $this->priceIsNet = $priceIsNet;
    }
 /**
     * @return the $yearlessPriceDates
     */
    public function getYearlessPriceDates()
    {
        return $this->yearlessPriceDates;
    }

 /**
     * @param multitype: $yearlessPriceDates
     */
    public function setYearlessPriceDates($yearlessPriceDates)
    {
        $this->yearlessPriceDates = $yearlessPriceDates;
    }

    /**
     * Berechnet den Preis (je nach Kennzeichen Brutto/netto) und die dazugehuerige Steuer
     * @param unknown $price
     * @param unknown $tax
     * @param unknown $calcMwst
     * @param unknown $priceIsNetKz
     */
    public function calculatePricesAndTaxes(&$price, &$tax, $calcMwst, $priceIsNetKz) {
        if ($priceIsNetKz === "on") {
            $calcPrice  = round(($price * (1 + $calcMwst)), 2);
            $tax	    = round(($price * $calcMwst),2);
        } else {
            $calcPrice  = round(($price/ (1 + $calcMwst)), 2);
            $tax	    = round(($calcPrice * $calcMwst),2);
//             $calcPrice  = floatval($price);
        }
        $price          = number_format($calcPrice, 2, ",", ".");
        $tax            = number_format($tax, 2, ",", ".");
    }

    public function sortFunction( $a, $b ) {
        return strtotime($b["from"]) - strtotime($a["from"]);
    }
    
    public function sortFunction2( $a, $b ) {
        return strtotime($a["from"]) - strtotime($b["from"]);
    }
    
    public function sortFunction3( $a, $b ) {
        return strtotime($b->date_from) - strtotime($a->date_from);
    }
    
    
    public function getSmalestPrice() {
        global $RSBP_DATABASE;
    
        $smalestPrice                       = 0;
        $postId                             = $this->getPostId();
        $smalestPriceArray                  = array();
        $smalestPriceArray['postId']        = $postId;
        $smalestPriceArray['price']         = null;
        $smalestPrice                       = apply_filters("rs_indiebooking_admin_getSmallestApartmentPrice", $smalestPriceArray);
//         if (is_null($smalestPrice)) {
            //wenn kein Preis gefunden wurde, wird der defaultpreis zurueck gegeben
//             $smalestPrice                   = floatval($this->getPreis());
//         }
        $smalestPrice                       = $smalestPrice['price'];
        if (isset($smalestPrice)) {
            //Komma durch Punkt ersetzen, damit ich eine konforme Nummer habe
            $smalestPrice                   = str_replace(",", ".", $smalestPrice);
        }
//         $mwstTable                          = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
//         $dates                              = $this->getBookableDates();
//         $allMwst                            = $mwstTable->getAllMwsts();
//         $datesAndPrices                     = $this->getBookableDatesWithCalculatedPrices($allMwst);
    
//         usort($dates, array($this, "sortFunction"));
    
//         $yearPrices                         = array();
//         $yearLessPrices                     = array();
//         $defaultPrice                       = 0;
        return $smalestPrice;
    }
    
    public function isApartmentBookable() {
        $isBookable         = true;
        $preis				= $this->getPreis();
        if ($this->getPreis() != "") {
        	$preis			= str_replace(",", ".", $this->getPreis());
        	$preis 			= floatval($preis);
        }
        if ($preis == "" || $preis <= 0) {
            if (sizeof($this->getYearlessPriceDates()) <= 0) {
                $isBookable         = false;
            }
        }
        if ($isBookable) {
            $smalestPrice   = $this->getSmalestPrice();
            if ($smalestPrice == "") {
                $isBookable = false;
            }
        }
        return $isBookable;
    }
    
    public function getSaisonDatesWithPrices($allMwst, $maxYear = false, $sort = "DESC", $minYear = false) {
        global $RSBP_DATABASE;
    
        //         $saisonPriceTable   = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Saison::RS_TABLE);
        //         $saisonPriceArr     = $saisonPriceTable->loadApartmentSaisonsGroupedByYear($this->getPostId(), true);
    
        $datesAndPrices             = array();
        $datesAndPrices['yearless'] = array();
        $defaultPrice               = $this->getPreis();
    
        if (is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            $yearlessDates          = $this->getYearlessPriceDates();
    
            if ($sort == "DESC") {
                krsort($yearlessDates);
            } elseif ($sort == "ASC") {
                ksort($yearlessDates);
            }
            $today                  = new DateTime("now");
            $calcMwst               = 0;
            foreach ($allMwst as $mwst) {
                if ($mwst->getMwstId() == $this->getMwstId()) {
                    $calcMwst       = ($mwst->getMwstValue() / 100);
                    break;
                }
            }
            $index                  = 0;
            if (!$minYear) {
                $minYear            = $maxYear;
            }
    
            foreach ($yearlessDates as $validYear => $saisonDates) {
                $saisonDates = array_reverse($saisonDates);
                foreach ($saisonDates as $date) {
                    //$date->valid_to darf auch 0 sein, weil 0
                    if ($maxYear != false && $date->valid_from <= $maxYear && ($date->valid_to >= $minYear || $date->valid_to == 0)) {
                        $calcPrice  = 0;
                        $tax        = 0;
                        $calcPrice  = $date->price;//$date["price"];
                        //                 var_dump($date);
                        $this->calculatePricesAndTaxes($calcPrice, $tax, $calcMwst, $this->getPriceIsNet());
                        $datesAndPrices['yearless'][$index]['calcPrice']    = $calcPrice;
                        $datesAndPrices['yearless'][$index]['tax']          = $calcMwst;
                        $datesAndPrices['yearless'][$index]['mwstId']       = $this->getMwstId();
                        $datesAndPrices['yearless'][$index]['saisonObj']    = $date;
                        //                 $datesAndPrices['yearless'][$index]['from']         = $date->date_from;//$date["from"];
                        //                 $datesAndPrices['yearless'][$index]['to']           = $date->date_to;//$date["to"];
                        //                 $datesAndPrices['yearless'][$index]['validFrom']    = $date->valid_from;
                        $index++;
                    }
                }
            }
    
        }
        $datesAndPrices['default'] = $defaultPrice;
    
        return $datesAndPrices;
    }
    
    /**
     *
     * @param unknown $allMwst
     * @param string $maxYear
     *
     * $maxYear = alle Saisonpreise die nach $maxYear erst gueltig sind, sollen garnicht beruecksichtigt werden.
     * @return number[]
     */
    public function getBookableDatesWithCalculatedPrices($allMwst, $maxYear = false) {
        //dient nur noch zur weiterleitung. Wird ggf. auch ganz geluescht wenn an allen stellen die neue
        //Methode verwendet wird.
        return $this->getSaisonDatesWithPrices($allMwst, $maxYear);
    }
    
    /**
     * @return the $aktionen
     */
    public function getAktionen()
    {
        return $this->aktionen;
    }

    /**
     * @param multitype: $aktionen
     */
    public function setAktionen($aktionen)
    {
        $this->aktionen = $aktionen;
    }
    /**
     * @return the $arrivalDays
     */
    public function getArrivalDays()
    {
        return $this->arrivalDays;
    }

    /**
     * @param multitype: $arrivalDays
     */
    public function setArrivalDays($arrivalDays)
    {
        $this->arrivalDays = $arrivalDays;
    }
    /**
     * @return the $location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return the $zipCode
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @return the $street
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }
    /**
     * @return the $anzahlBetten
     */
    public function getAnzahlBetten()
    {
        if (is_null($this->anzahlBetten)) {
            return 0;
        }
        return $this->anzahlBetten;
    }

    /**
     * @return the $anzahlPersonen
     */
    public function getAnzahlPersonen()
    {
        if (is_null($this->anzahlPersonen)) {
            return 0;
        }
        return $this->anzahlPersonen;
    }

    /**
     * @param string $anzahlBetten
     */
    public function setAnzahlBetten($anzahlBetten)
    {
        $this->anzahlBetten = $anzahlBetten;
    }

    /**
     * @param string $anzahlPersonen
     */
    public function setAnzahlPersonen($anzahlPersonen)
    {
        $this->anzahlPersonen = $anzahlPersonen;
    }
    /**
     * @return the $allowVaryingPrice
     */
    public function getAllowVaryingPrice()
    {
        return $this->allowVaryingPrice;
    }

    /**
     * @param number $allowVaryingPrice
     */
    public function setAllowVaryingPrice($allowVaryingPrice)
    {
        $this->allowVaryingPrice = $allowVaryingPrice;
    }
    /**
     * @return the $showOnStartPage
     */
    public function getShowOnStartPage()
    {
        if (is_null($this->showOnStartPage)) {
            $this->showOnStartPage = "off";
        }
        return $this->showOnStartPage;
    }

    /**
     * @param string $showOnStartPage
     */
    public function setShowOnStartPage($showOnStartPage)
    {
        $this->showOnStartPage = $showOnStartPage;
    }
    /**
     * @return the $shortDescription
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }
    /**
     * @return the $lat
     */
    public function getLat()
    {
    	$latitude		= str_replace(",", ".", $this->lat);
        return $latitude;
    }

    /**
     * @return the $lng
     */
    public function getLng()
    {
    	$longitude		= str_replace(",", ".", $this->lng);
        return $longitude;
    }

    /**
     * @param number $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @param number $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }
    /**
     * @return the $zeitraumeDB
     */
    public function getZeitraumeDB()
    {
        return $this->zeitraumeDB;
    }

    /**
     * @param multitype: $zeitraumeDB
     */
    public function setZeitraumeDB($zeitraumeDB)
    {
        $this->zeitraumeDB = $zeitraumeDB;
    }
    /**
     * @return the $locationDescription
     */
    public function getLocationDescription()
    {
        return $this->locationDescription;
    }

    /**
     * @param string $locationDescription
     */
    public function setLocationDescription($locationDescription)
    {
        $this->locationDescription = $locationDescription;
    }
    /**
     * @return the $anzahlZimmer
     */
    public function getAnzahlZimmer()
    {
        return $this->anzahlZimmer;
    }

    /**
     * @param number $anzahlZimmer
     */
    public function setAnzahlZimmer($anzahlZimmer)
    {
        $this->anzahlZimmer = $anzahlZimmer;
    }
    /**
     * @return the $anzahlEinzelBetten
     */
    public function getAnzahlEinzelBetten()
    {
        return $this->anzahlEinzelBetten;
    }

    /**
     * @return the $anzahlDoppelBetten
     */
    public function getAnzahlDoppelBetten()
    {
        return $this->anzahlDoppelBetten;
    }

    /**
     * @param number $anzahlEinzelBetten
     */
    public function setAnzahlEinzelBetten($anzahlEinzelBetten)
    {
        $this->anzahlEinzelBetten = $anzahlEinzelBetten;
    }

    /**
     * @param number $anzahlDoppelBetten
     */
    public function setAnzahlDoppelBetten($anzahlDoppelBetten)
    {
        $this->anzahlDoppelBetten = $anzahlDoppelBetten;
    }
    /**
     * @return the $offenZeitraumeDB
     */
    public function getOffenZeitraumeDB()
    {
        return $this->offenZeitraumeDB;
    }

    /**
     * @param multitype: $offenZeitraumeDB
     */
    public function setOffenZeitraumeDB($offenZeitraumeDB)
    {
        $this->offenZeitraumeDB = $offenZeitraumeDB;
    }
    /**
     * @return the $pflichtOptionen
     */
    public function getPflichtOptionen()
    {
        return $this->pflichtOptionen;
    }

    /**
     * @param multitype: $pflichtOptionen
     */
    public function setPflichtOptionen($pflichtOptionen)
    {
        $this->pflichtOptionen = $pflichtOptionen;
    }
    
	public function getBookingComHotelId() {
		return $this->bookingComHotelId;
	}
	public function setBookingComHotelId($bookingComHotelId) {
		$this->bookingComHotelId = $bookingComHotelId;
		return $this;
	}
	public function getBookingComRoomId() {
		return $this->bookingComRoomId;
	}
	public function setBookingComRoomId($bookingComRoomId) {
		$this->bookingComRoomId = $bookingComRoomId;
		return $this;
	}
	
	
	public function getSecurityDeposit() {
		if (is_null($this->securityDeposit)) {
			return 0;
		}
		return $this->securityDeposit;
	}
	public function setSecurityDeposit($securityDeposit) {
		$this->securityDeposit = $securityDeposit;
		return $this;
	}
	
	public function getDegression() {
		return $this->degression;
	}
	
	public function setDegression($degression) {
		$this->degression = $degression;
		return $this;
	}
		
	public function getFeatures() {
		if (!isset($this->features)) {
			$this->features = array();
		}
		return $this->features;
	}
	public function setFeatures($features) {
		$this->features = $features;
		return $this;
	}
	
	public function getAnzahlPersonenVorbelegung() {
		if (is_null($this->anzahlPersonenVorbelegung) || $this->anzahlPersonenVorbelegung == 0) {
			if ($this->getAnzahlPersonen() > 0) {
				$this->anzahlPersonenVorbelegung = 1;
			}
		}
		return $this->anzahlPersonenVorbelegung;
	}
	
	public function setAnzahlPersonenVorbelegung($anzahlPersonenVorbelegung) {
		$this->anzahlPersonenVorbelegung = $anzahlPersonenVorbelegung;
		return $this;
	}
	
	public function getExtraCharge() {
		return $this->extraCharge;
	}
	public function setExtraCharge($extraCharge) {
		$this->extraCharge = $extraCharge;
		return $this;
	}
	/**
	 * @return number
	 */
	public function getBookingComDefaultRateId()
	{
		return $this->bookingComDefaultRateId;
	}

	/**
	 * @param number $bookingComDefaultRateId
	 */
	public function setBookingComDefaultRateId($bookingComDefaultRateId)
	{
		$this->bookingComDefaultRateId = $bookingComDefaultRateId;
	}
	/**
	 * @return string
	 */
	public function getOnlyInquire()
	{
		if (is_null($this->onlyInquire)) {
			$this->onlyInquire = "off";
		}
		return $this->onlyInquire;
	}

	/**
	 * @param string $onlyInquire
	 */
	public function setOnlyInquire($onlyInquire)
	{
		$this->onlyInquire = $onlyInquire;
	}

	/**
	 * @return number
	 */
	public function getBookingComAufschlag()
	{
		if (!isset($this->bookingComAufschlag) || is_null($this->bookingComAufschlag)) {
			$this->bookingComAufschlag = 0;
		}
		return $this->bookingComAufschlag;
	}

	/**
	 * @return number
	 */
	public function getBookingComAufschlagTyp()
	{
		return $this->bookingComAufschlagTyp;
	}

	/**
	 * @param number $bookingComAufschlag
	 */
	public function setBookingComAufschlag($bookingComAufschlag)
	{
		$this->bookingComAufschlag = $bookingComAufschlag;
	}

	/**
	 * @param number $bookingComAufschlagTyp
	 */
	public function setBookingComAufschlagTyp($bookingComAufschlagTyp)
	{
		$this->bookingComAufschlagTyp = $bookingComAufschlagTyp;
	}




	
	
}
// endif;
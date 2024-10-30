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
// if ( ! class_exists( 'RS_IB_Model_Appartment_Buchung' ) ) :
class RS_IB_Model_Appartment_Buchung extends RS_IB_Model_Postmeta
{
    const RS_TABLE                      = "RS_IB_APPARTMENT_BUCHUNG_TABLE";
    const RS_POSTTYPE                   = "rsappartment_buchung";
    
    const BUCHUNG_KOPF_ID               = "rs_ib_buchung_kopf_id";
    
    const BUCHUNG_APPARTMENT_ID         = "rs_buchung_appartment_id";
    const BUCHUNG_APPARTMENT_SQUARE     = "rs_buchung_appartment_square";
    const BUCHUNG_ZEITRAUM              = "buchungzeitraum";
    const BUCHUNG_OPTIONEN              = "rs_buchung_optionen";
    const BUCHUNG_KONTAKT               = "rs_buchung_contact";
    const BUCHUNG_PRICES_BY_BOOKING     = "rs_buchung_prices_by_booking";
    const BUCHUNG_FULL_BOOKING_PRICES   = "rs_buchung_full_booking_prices";
    const BUCHUNG_COUPONS               = "rs_buchung_coupons";
    const BUCHUNG_GUTSCHEINE			= "rs_buchung_gutscheine";
    const BUCHUNG_AKTIONEN              = "rs_buchung_aktionen";
    const BUCHUNG_START_BOOKING_TIME    = "rs_buchung_start_time";
    const BUCHUNG_LAST_HEARTBEAT    	= "rs_buchung_last_heartbeat";
    const BUCHUNG_PAYPALDATA    		= "rs_buchung_paypaldata";
    const BUCHUNG_AMZNDATA    			= "rs_buchung_amzndata";
    const BUCHUNG_DO_HEARTBEAT			= "rs_buchung_do_heartbeat";
    
    const BUCHUNG_VON                   = "rs_buchungszeitraum_von";
    const BUCHUNG_BIS                   = "rs_buchungszeitraum_bis";
    
    const BUCHUNG_BIGGEST_PAGEKZ        = "rs_buchung_biggest_pagekz";
    const BUCHUNG_CURRENT_PAGEKZ        = "rs_buchung_current_pagekz"; //hauptsaechlich fuer paypal angelegt
    
    private $buchungKopfId      = 0;
    
    private $appartment_id      = null;
    private $appartment_square  = 0;
    private $startDate          = null;
    private $endDate            = null;
    private $optionen           = array();
    private $contact            = array();
    private $bookingPrices      = array(); //beinhaltet die Appartmentpreise zur Zeit der Buchung.
    private $fullBookingPrices  = array();
    private $coupons            = array();
    private $gutscheine         = array();
    private $start_time         = null;
    
    private $lastHeartbeat		= 0;
    //TODO muessen noch korrekt abgesepeichert werden.
    private $aktionen           = array();
    private $priceIsNetKz       = "off";
    
    private $biggestPageKz      = 0;
    
    //Folgende Werte kommen nicht aus der Datenbank sondern werden immer wieder berechnet.
    private $remainingtTime     = 0;
    private $fullBrutto         = 0;
    private $calculatedEndBrutto   = 0;
    private $calculatedBrutto   = 0;
    private $calculatedNetto   = 0;
    private $fullNetto          = 0;
    private $buchungsPositionen = array(); //beinhaltet alle Positionen des Zeitraums + ggf. Angebote die sich auf den Zeitraum auswirken
    private $optionenPositionen = array(); //beinhaltet alle Positionen der Optionen + ggf. Angebote die sich auf den Zeitraum auswirken
    private $allTaxes           = array();
    
    private $afterAllTimePos    = array();
    
    public function exchangeArray($data) {
        parent::exchangeArray($data);
        
        if (isset($data[self::BUCHUNG_APPARTMENT_ID])) {
            $appartment_id = $data[self::BUCHUNG_APPARTMENT_ID][0];
        } else {
            $appartment_id = 0;
        }
        if (isset($data[self::BUCHUNG_APPARTMENT_SQUARE])) {
            $appSquare = $data[self::BUCHUNG_APPARTMENT_SQUARE][0];
        } else {
            $appSquare = 0;
        }
        if (isset($data[self::BUCHUNG_ZEITRAUM])) {
            $dates = array();
            $dates = unserialize($data[self::BUCHUNG_ZEITRAUM][0]);
            $start  = $dates[0];
            $end    = $dates[1];
        } else {
            $start  = 0;
            $end    = 0;
        }
        if (isset($data[self::BUCHUNG_OPTIONEN])) {
            $options = array();
            $options = unserialize($data[self::BUCHUNG_OPTIONEN][0]);
        } else {
            $options = array();
        }
        if (isset($data[self::BUCHUNG_AKTIONEN])) {
            $aktionen = array();
            $aktionen = unserialize($data[self::BUCHUNG_AKTIONEN][0]);
        } else {
            $aktionen = array();
        }
        if (isset($data[self::BUCHUNG_KONTAKT])) {
            $contact = array();
            $contact = unserialize($data[self::BUCHUNG_KONTAKT][0]);
        } else {
            $contact = array();
        }
        if (isset($data[self::BUCHUNG_PRICES_BY_BOOKING])) {
            $prices = unserialize($data[self::BUCHUNG_PRICES_BY_BOOKING][0]);
        } else {
            $prices = array();
        }
        if (isset($data[self::BUCHUNG_FULL_BOOKING_PRICES])) {
            $fullPrices = unserialize($data[self::BUCHUNG_FULL_BOOKING_PRICES][0]);
        } else {
            $fullPrices = array();
        }
        if (isset($data[self::BUCHUNG_COUPONS])) {
            $coupons = unserialize($data[self::BUCHUNG_COUPONS][0]);
        } else {
            $coupons = array();
        }
        if (isset($data[self::BUCHUNG_GUTSCHEINE])) {
        	$gutscheine = unserialize($data[self::BUCHUNG_GUTSCHEINE][0]);
        } else {
        	$gutscheine = array();
        }
        if (isset($data[self::BUCHUNG_START_BOOKING_TIME])) {
            $startTime = $data[self::BUCHUNG_START_BOOKING_TIME][0];
        } else {
            $startTime = null;
        }
        if (isset($data[self::BUCHUNG_KOPF_ID])) {
            $kopfId = $data[self::BUCHUNG_KOPF_ID][0];
        } else {
            $kopfId = 0;
        }
        
        if (isset($data[self::BUCHUNG_BIGGEST_PAGEKZ])) {
            $biggestPkz = $data[self::BUCHUNG_BIGGEST_PAGEKZ][0];
        } else {
            $biggestPkz = 0;
        }
        
        if (isset($data[self::BUCHUNG_LAST_HEARTBEAT])) {
        	$lastHeartbeat = $data[self::BUCHUNG_LAST_HEARTBEAT];
        } else {
        	$lastHeartbeat = 0;
        }
        
        $this->setBuchungKopfId($kopfId);
        $this->setStartDate($start);
        $this->setEndDate($end);
        $this->setStart_time($startTime);
        $this->setBiggestPageKz($biggestPkz);
        
        $this->setLastHeartbeat($lastHeartbeat);
        //werden nicht mehr genutzt
        //Carsten Schmitt
        //26.01.2016
        $this->setAppartment_id($appartment_id);
        $this->setAppartment_square($appSquare);
        $this->setOptions($options);
        $this->setContact($contact);
        $this->setBookingPrices($prices);
        $this->setFullBookingPrices($fullPrices);
        $this->setCoupons($coupons);
        $this->setGutscheine($gutscheine);
        $this->setAktionen($aktionen);
    }

 /**
     * @param field_type $appartment_id
     */
    public function setAppartment_id($appartment_id)
    {
        $this->appartment_id = $appartment_id;
    }
    
 /**
     * @return the $appartment_id
     */
    public function getAppartment_id()
    {
        if (is_null($this->appartment_id)) {
            return 0;
        }
        return $this->appartment_id;
    }
 /**
     * @return the $startDate
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

 /**
     * @return the $endDate
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

 /**
     * @param field_type $startDate
     */
    public function setStartDate($startDate)
    {
        if ($startDate instanceof DateTime) {
            $startDate = $startDate->format("d.m.Y");
        }
        $this->startDate = $startDate;
    }

 /**
     * @param field_type $endDate
     */
    public function setEndDate($endDate)
    {
        if ($endDate instanceof DateTime) {
            $endDate = $endDate->format("d.m.Y");
        }
        $this->endDate = $endDate;
    }

    public function setOptions($options) {
        $this->optionen = $options;
    }
    
    public function getOptions() {
        return $this->optionen;
    }
 /**
     * @return the $contact
     */
    public function getContact()
    {
        return $this->contact;
    }

 /**
     * @param multitype: $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }
 /**
     * @return the $bookingPrices
     */
    public function getBookingPrices()
    {
        return $this->bookingPrices;
    }

 /**
     * @param multitype: $bookingPrices
     */
    public function setBookingPrices($bookingPrices)
    {
        $this->bookingPrices = $bookingPrices;
    }
 /**
     * @return the $fullBookingPrices
     */
    public function getFullBookingPrices()
    {
        return $this->fullBookingPrices;
    }

 /**
     * @param multitype: $fullBookingPrices
     */
    public function setFullBookingPrices($fullBookingPrices)
    {
        $this->fullBookingPrices = $fullBookingPrices;
    }

    /**
     * @return the $coupons
     */
    public function getCoupons()
    {
        return $this->coupons;
    }
    
    /**
     * @param multitype: $coupons
     */
    public function setCoupons($coupons)
    {
        $this->coupons = $coupons;
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
     * @return the $buchungsPositionen
     */
    public function getBuchungsPositionen()
    {
        return $this->buchungsPositionen;
    }

    /**
     * @param field_type $buchungsPositionen
     */
    public function setBuchungsPositionen($buchungsPositionen)
    {
        $this->buchungsPositionen = $buchungsPositionen;
    }


    public function getAnzahlNeachte() {
        $fromDate        = $this->getStartDate();
        $toDate          = $this->getEndDate();
        $dtFromdate      = DateTime::createFromFormat("d.m.Y", $fromDate);
        $dtTodate        = DateTime::createFromFormat("d.m.Y", $toDate);
        $anzahlTage      = date_diff($dtFromdate, $dtTodate, true);
        return intval($anzahlTage->format('%a'));
    }
    /**
     * @return the $priceIsNetKz
     */
    public function getPriceIsNetKz()
    {
        return $this->priceIsNetKz;
    }

    /**
     * @param string $priceIsNetKz
     */
    public function setPriceIsNetKz($priceIsNetKz)
    {
        $this->priceIsNetKz = $priceIsNetKz;
    }
    /**
     * @return the $optionenPositionen
     */
    public function getOptionenPositionen()
    {
        return $this->optionenPositionen;
    }

    /**
     * @param multitype: $optionenPositionen
     */
    public function setOptionenPositionen($optionenPositionen)
    {
        $this->optionenPositionen = $optionenPositionen;
    }
    /**
     * @return the $appartment_square
     */
    public function getAppartment_square()
    {
        return $this->appartment_square;
    }

    /**
     * @param number $appartment_square
     */
    public function setAppartment_square($appartment_square)
    {
        $this->appartment_square = $appartment_square;
    }
    /**
     * @return the $afterAllTimePos
     */
    public function getAfterAllTimePos()
    {
        return $this->afterAllTimePos;
    }

    /**
     * @param field_type $afterAllTimePos
     */
    public function setAfterAllTimePos($afterAllTimePos)
    {
        $this->afterAllTimePos = $afterAllTimePos;
    }
    /**
     * @return the $fullBrutto
     */
    public function getFullBrutto()
    {
        return $this->fullBrutto;
    }

    /**
     * @return the $fullNetto
     */
    public function getFullNetto()
    {
        return $this->fullNetto;
    }

    /**
     * @param number $fullBrutto
     */
    public function setFullBrutto($fullBrutto)
    {
        $this->fullBrutto = $fullBrutto;
    }

    /**
     * @param number $fullNetto
     */
    public function setFullNetto($fullNetto)
    {
        $this->fullNetto = $fullNetto;
    }
    /**
     * @return the $calculatedEndBrutto
     */
    public function getCalculatedEndBrutto()
    {
        return $this->calculatedEndBrutto;
    }

    /**
     * @param number $calculatedEndBrutto
     */
    public function setCalculatedEndBrutto($calculatedEndBrutto)
    {
        $this->calculatedEndBrutto = $calculatedEndBrutto;
    }
    /**
     * @return the $allTaxes
     */
    public function getAllTaxes()
    {
        return $this->allTaxes;
    }

    /**
     * @param multitype: $allTaxes
     */
    public function setAllTaxes($allTaxes)
    {
        $this->allTaxes = $allTaxes;
    }
    /**
     * @return the $calculatedBrutto
     */
    public function getCalculatedBrutto()
    {
        return $this->calculatedBrutto;
    }

    /**
     * @param number $calculatedBrutto
     */
    public function setCalculatedBrutto($calculatedBrutto)
    {
        $this->calculatedBrutto = $calculatedBrutto;
    }
    /**
     * @return the $calculatedNetto
     */
    public function getCalculatedNetto()
    {
        return $this->calculatedNetto;
    }

    /**
     * @param number $calculatedNetto
     */
    public function setCalculatedNetto($calculatedNetto)
    {
        $this->calculatedNetto = $calculatedNetto;
    }
    /**
     * @return the $start_time
     */
    public function getStart_time()
    {
        return $this->start_time;
    }

    /**
     * @param field_type $start_time
     */
    public function setStart_time($start_time)
    {
        $this->start_time = $start_time;
    }


//     public function getRemainingTime($timeToBook = 15) {
//         $startTime  = $this->getStart_time();
//         if (!is_null($startTime)) {
//             $now        = time();
//             $diff       = ($startTime + ($timeToBook * 60)) - $now;
//             if ($diff <= 0) {
//                 $diff   = 0; //ZEIT ABGELAUFEN!!!
//             }
//             return ($diff);
//         } else {
//             return 1000;
//         }
//     }
    /**
     * @return the $remainingtTime
     */
    public function getRemainingtTime()
    {
        return $this->remainingtTime;
    }

    /**
     * @param number $remainingtTime
     */
    public function setRemainingtTime($remainingtTime)
    {
        $this->remainingtTime = $remainingtTime;
    }
    /**
     * @return the $buchungKopfId
     */
    public function getBuchungKopfId()
    {
        return $this->buchungKopfId;
    }

    /**
     * @param number $buchungKopfId
     */
    public function setBuchungKopfId($buchungKopfId)
    {
        $this->buchungKopfId = $buchungKopfId;
    }
    /**
     * @return the $biggestPageKz
     */
    public function getBiggestPageKz()
    {
        return $this->biggestPageKz;
    }

    /**
     * @param number $biggestPageKz
     */
    public function setBiggestPageKz($biggestPageKz)
    {
        $this->biggestPageKz = $biggestPageKz;
    }
	/**
	 * @return multitype:
	 */
	public function getGutscheine()
	{
		return $this->gutscheine;
	}

	/**
	 * @param multitype: $gutscheine
	 */
	public function setGutscheine($gutscheine)
	{
		$this->gutscheine = $gutscheine;
	}
	/**
	 * @return number
	 */
	public function getLastHeartbeat()
	{
		return $this->lastHeartbeat;
	}

	/**
	 * @param number $lastHeartbeat
	 */
	public function setLastHeartbeat($lastHeartbeat)
	{
		$this->lastHeartbeat = $lastHeartbeat;
	}


}
// endif;
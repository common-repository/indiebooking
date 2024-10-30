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

// if ( ! class_exists( 'RS_IB_Model_Oberbuchungkopf' ) ) :
class RS_IB_Model_Oberbuchungkopf
{
    const RS_TABLE          = "RS_IB_OBERBUCHUNG_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
    
    const OBERBUCHUNG_RECH_NR   = "rechnungNr";

    
    private $rechnung_nr;
    private $buchungen      = array();
    private $endbetrag      = 0;
    private $calcbetrag     = 0;
    private $zahlungsbetrag = 0;

    /* @var $rabatt RS_IB_Model_BuchungRabatt */
    /* @var $position RS_IB_Model_Buchungposition */
    /* @var $teilKopf RS_IB_Model_Teilbuchungskopf */
    /* @var $zahlung RS_IB_Model_BuchungZahlung */
    /* @var $buchung RS_IB_Model_Buchungskopf */
    public function calculatePrice() {
        $calcPrice  = 0;
        $zahlung    = 0;
        $offen      = 0;
        $test       = 0;
        $zuZahlen   = 0;
        foreach ($this->getBuchungen() as $buchung) {
//             $zahlungen = $buchung->getZahlungen();
//             if (sizeof($zahlungen) > 0 || ) {
//             $calcPrice = $buchung->getCalculatedPrice();
            $zuZahlen   += $buchung->getZahlungsbetrag();
//             }
//             $offen     = ($calcPrice) - ($zahlung);
//             $test       = $test + $offen;
            RS_Indiebooking_Log_Controller::write_log(__LINE__." ".__CLASS__." ".$buchung->getBuchung_nr()." Calc: ".$buchung->getCalculatedPrice()." Zahlung: ".$buchung->getZahlungsbetrag());
        }
        $this->setZahlungsbetrag($zahlung);
        $this->setCalcbetrag($calcPrice);
//         $endbetrag = $calcPrice + $zahlung;
        $this->setEndbetrag($zuZahlen);
    }
    
    
    public function addBuchung($buchung) {
        array_push($this->buchungen, $buchung);
    }
    
    /**
     * @return the $rechnung_nr
     */
    public function getRechnung_nr()
    {
        return $this->rechnung_nr;
    }

    /**
     * @return the $buchungen
     */
    public function getBuchungen()
    {
        return $this->buchungen;
    }

    /**
     * @param field_type $rechnung_nr
     */
    public function setRechnung_nr($rechnung_nr)
    {
        $this->rechnung_nr = $rechnung_nr;
    }

    /**
     * @param field_type $buchungen
     */
    public function setBuchungen($buchungen)
    {
        $this->buchungen = $buchungen;
    }
    /**
     * @return the $endbetrag
     */
    public function getEndbetrag()
    {
        return $this->endbetrag;
    }

    /**
     * @param number $endbetrag
     */
    public function setEndbetrag($endbetrag)
    {
        $this->endbetrag = $endbetrag;
    }
    /**
     * @return the $calcbetrag
     */
    public function getCalcbetrag()
    {
        return $this->calcbetrag;
    }

    /**
     * @return the $zahlungsbetrag
     */
    public function getZahlungsbetrag()
    {
        return $this->zahlungsbetrag;
    }

    /**
     * @param number $calcbetrag
     */
    public function setCalcbetrag($calcbetrag)
    {
        $this->calcbetrag = $calcbetrag;
    }

    /**
     * @param number $zahlungsbetrag
     */
    public function setZahlungsbetrag($zahlungsbetrag)
    {
        $this->zahlungsbetrag = $zahlungsbetrag;
    }
}
// endif;
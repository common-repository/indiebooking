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

// if ( ! class_exists( 'RS_IB_Model_BuchungRabatt' ) ) :
class RS_IB_Model_BuchungRabatt
{
    const RS_TABLE                  = "RS_IB_BUCHUNGRABATT_TABLE";

    const RABATT_ART_AKTION         = 1;
    const RABATT_ART_COUPON         = 2;
    const RABATT_ART_DEGRESSION     = 3;
    const RABATT_ART_AUFSCHLAG     	= 4; //Ein Aufschlag wird wie eine Aktion behandelt, die etwas auf den Preis aufschlaegt
    
    const RABATT_TYP_TOTAL          = 1;
    const RABATT_TYP_PROZENT        = 2;
    
    const RABATT_BERECHNUNG_APARTMENT_PREIS = 1;
    const RABATT_BERECHNUNG_OPTION          = 2;
    const RABATT_BERECHNUNG_TOTAL           = 3;
    const RABATT_BERECHNUNG_POSITION_PREIS 	= 4;
    
    const RABATT_RABATT_KZ          = 1;
    const RABATT_AUFSCHLAG_KZ       = 2;
    
    const RABATT_ID                 = "rabatt_id"; //wirklich auto increment??
    const RABATT_TERM_ID            = "rabatt_term_id";
    const BUCHUNG_NR                = "buchung_nr";
    const TEILBUCHUNG_NR            = "teilbuchung_id";
    const POSITION_NR               = "position_id";
    const VALID_AT_STORNO           = "valid_at_storno";
    
    const RABATT_WERT               = "rabatt_wert";
    const RABATT_TYP                = "rabatt_typ";
    const RABATT_ART                = "rabatt_art";
    const BEZEICHNUNG               = "bezeichnung";
    const GUELTIG_VON               = "gueltig_von";
    const GUELTIG_BIS               = "gueltig_bis";
    const BERECHNUNG_ART            = "berechnung_type";
    
    const PLUS_MINUS_KZ             = "plus_minus_kz";
    const RABATT_AUSSCHREIBEN_KZ    = "ausschreiben_kz";
    const RABATT_OPTION_ID			= "rabatt_option_id";
    
    const USER_ID                   = "user_id";
    
    private $rabatt_id;
    private $rabatt_term_id;
    private $buchung_nr;
    private $teilbuchung_nr;
    private $position_nr;
    private $rabatt_wert;
    private $rabatt_typ;
    private $rabatt_art; //1 = Aktion || 2 = Coupon
    private $bezeichnung;
    private $gueltig_von;
    private $gueltig_bis;
    private $berechnung_art;
    private $valid_at_storno;
    private $plus_minus_kz; //1= Rabatt || 2 = Aufschlag
    private $rabatt_ausschreiben_kz;
    private $userId;
    private $rabatt_option_id;
    
    public function exchangeArray($data) {
        if (isset($data[self::RABATT_ID])) {
            $this->rabatt_id        = $data[self::RABATT_ID];
        }
        if (isset($data[self::RABATT_TERM_ID])) {
            $this->rabatt_term_id   = $data[self::RABATT_TERM_ID];
        }
        if (isset($data[self::BUCHUNG_NR])) {
            $this->buchung_nr       = $data[self::BUCHUNG_NR];
        }
        if (isset($data[self::TEILBUCHUNG_NR])) {
            $this->teilbuchung_nr   = $data[self::TEILBUCHUNG_NR];
        }
        if (isset($data[self::POSITION_NR])) {
            $this->position_nr      = $data[self::POSITION_NR];
        }
        if (isset($data[self::RABATT_WERT])) {
            $this->rabatt_wert   = $data[self::RABATT_WERT];
        }
        if (isset($data[self::RABATT_TYP])) {
            $this->rabatt_typ   = $data[self::RABATT_TYP];
        }
        if (isset($data[self::RABATT_ART])) {
            $this->rabatt_art = $data[self::RABATT_ART];
        }
        if (isset($data[self::BEZEICHNUNG])) {
            $this->bezeichnung = $data[self::BEZEICHNUNG];
        }
        if (isset($data[self::GUELTIG_VON])) {
            $this->gueltig_von   = $data[self::GUELTIG_VON];
        }
        if (isset($data[self::GUELTIG_BIS])) {
            $this->gueltig_bis = $data[self::GUELTIG_BIS];
        }
        if (isset($data[self::BERECHNUNG_ART])) {
            $this->berechnung_art = $data[self::BERECHNUNG_ART];
        }
        if (isset($data[self::VALID_AT_STORNO])) {
            $this->valid_at_storno = $data[self::VALID_AT_STORNO];
        }
        if (isset($data[self::PLUS_MINUS_KZ])) {
            $this->plus_minus_kz = $data[self::PLUS_MINUS_KZ];
        }
        if (isset($data[self::RABATT_AUSSCHREIBEN_KZ])) {
            $this->rabatt_ausschreiben_kz = $data[self::RABATT_AUSSCHREIBEN_KZ];
        }
        if (isset($data[self::USER_ID])) {
            $this->userId = $data[self::USER_ID];
        }
        if (isset($data[self::RABATT_OPTION_ID])) {
        	$this->rabatt_option_id = $data[self::RABATT_OPTION_ID];
        }
    }
    

    /**
     * @return the $userId
     */
    public function getUserId()
    {
        if (is_null($this->userId)) {
            return 0;
        }
        return $this->userId;
    }
    
    /**
     * @param number $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }
     
        
    
    /**
     * @return the $rabatt_id
     */
    public function getRabatt_id()
    {
        return $this->rabatt_id;
    }

    /**
     * @return the $buchung_nr
     */
    public function getBuchung_nr()
    {
        return $this->buchung_nr;
    }

    /**
     * @return the $teilbuchung_nr
     */
    public function getTeilbuchung_nr()
    {
        if (is_null($this->teilbuchung_nr)) {
            return 0;
        }
        return $this->teilbuchung_nr;
    }

    /**
     * @return the $position_nr
     */
    public function getPosition_nr()
    {
        if (is_null($this->position_nr)) {
            return 0;
        }
        return $this->position_nr;
    }

    /**
     * @return the $rabatt_wert
     */
    public function getRabatt_wert()
    {
        return $this->rabatt_wert;
    }

    /**
     * @return the $rabatt_typ
     */
    public function getRabatt_typ()
    {
        return intval($this->rabatt_typ);
    }

    /**
     * @return the $rabatt_art
     */
    public function getRabatt_art()
    {
        return $this->rabatt_art;
    }

    /**
     * @return the $bezeichnung
     */
    public function getBezeichnung()
    {
        return $this->bezeichnung;
    }

    /**
     * @return the $gueltig_von
     */
    public function getGueltig_von()
    {
        return $this->gueltig_von;
    }

    /**
     * @return the $gueltig_bis
     */
    public function getGueltig_bis()
    {
        return $this->gueltig_bis;
    }

    /**
     * @return the $berechnung_art
     */
    public function getBerechnung_art()
    {
        return $this->berechnung_art;
    }

    /**
     * @param field_type $rabatt_id
     */
    public function setRabatt_id($rabatt_id)
    {
        $this->rabatt_id = $rabatt_id;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }

    /**
     * @param field_type $teilbuchung_nr
     */
    public function setTeilbuchung_nr($teilbuchung_nr)
    {
        $this->teilbuchung_nr = $teilbuchung_nr;
    }

    /**
     * @param field_type $position_nr
     */
    public function setPosition_nr($position_nr)
    {
        $this->position_nr = $position_nr;
    }

    /**
     * @param field_type $rabatt_wert
     */
    public function setRabatt_wert($rabatt_wert)
    {
        $this->rabatt_wert = $rabatt_wert;
    }

    /**
     * @param field_type $rabatt_typ
     */
    public function setRabatt_typ($rabatt_typ)
    {
        $this->rabatt_typ = $rabatt_typ;
    }

    /**
     * @param field_type $rabatt_art
     */
    public function setRabatt_art($rabatt_art)
    {
        $this->rabatt_art = $rabatt_art;
    }

    /**
     * @param field_type $bezeichnung
     */
    public function setBezeichnung($bezeichnung)
    {
        $this->bezeichnung = $bezeichnung;
    }

    /**
     * @param field_type $gueltig_von
     */
    public function setGueltig_von($gueltig_von)
    {
        $this->gueltig_von = $gueltig_von;
    }

    /**
     * @param field_type $gueltig_bis
     */
    public function setGueltig_bis($gueltig_bis)
    {
        $this->gueltig_bis = $gueltig_bis;
    }

    /**
     * @param field_type $berechnung_art
     */
    public function setBerechnung_art($berechnung_art)
    {
        $this->berechnung_art = $berechnung_art;
    }
    /**
     * @return the $rabatt_term_id
     */
    public function getRabatt_term_id()
    {
        return $this->rabatt_term_id;
    }

    /**
     * @param field_type $rabatt_term_id
     */
    public function setRabatt_term_id($rabatt_term_id)
    {
        $this->rabatt_term_id = $rabatt_term_id;
    }
    /**
     * @return the $valid_at_storno
     */
    public function getValid_at_storno()
    {
        if (is_null($this->valid_at_storno)) {
            $this->valid_at_storno = 0;
        }
        return $this->valid_at_storno;
    }

    /**
     * @param field_type $valid_at_storno
     */
    public function setValid_at_storno($valid_at_storno)
    {
        $this->valid_at_storno = $valid_at_storno;
    }
    /**
     * @return the $plus_minus_kz
     */
    public function getPlus_minus_kz()
    {
        if (is_null($this->plus_minus_kz)) {
            return 1;
        }
        return $this->plus_minus_kz;
    }

    /**
     * @return the $rabatt_ausschreiben_kz
     */
    public function getRabatt_ausschreiben_kz()
    {
        if (is_null($this->rabatt_ausschreiben_kz)) {
            return 1;
        }
        return $this->rabatt_ausschreiben_kz;
    }

    /**
     * @param field_type $plus_minus_kz
     */
    public function setPlus_minus_kz($plus_minus_kz)
    {
        $this->plus_minus_kz = $plus_minus_kz;
    }

    /**
     * @param field_type $rabatt_ausschreiben_kz
     */
    public function setRabatt_ausschreiben_kz($rabatt_ausschreiben_kz)
    {
        $this->rabatt_ausschreiben_kz = $rabatt_ausschreiben_kz;
    }
	/**
	 * @return mixed
	 */
	public function getRabatt_option_id()
	{
		if (!isset($this->rabatt_option_id) || is_null($this->rabatt_option_id)) {
			return 0;
		}
		return $this->rabatt_option_id;
	}

	/**
	 * @param mixed $rabatt_option_id
	 */
	public function setRabatt_option_id($rabatt_option_id)
	{
		$this->rabatt_option_id = $rabatt_option_id;
	}

}
// endif;
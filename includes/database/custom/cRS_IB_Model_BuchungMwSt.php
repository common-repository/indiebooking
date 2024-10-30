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

// if ( ! class_exists( 'RS_IB_Model_BuchungMwSt' ) ) :
class RS_IB_Model_BuchungMwSt
{
    const RS_TABLE              = "RS_IB_BUCHUNGMWST_TABLE";
    
    const BUCHUNG_NR      = "buchung_nr";
    const MWST_ID         = "mwst_id";
    const MWST_PROZENT    = "mwst_prozent";
    const MWST_WERT       = "mwst_wert";
    const MWST_BRUTTO	  = "mwst_brutto";
    const USER_ID         = "user_id";
    
    private $buchung_nr;
    private $mwst_id;
    private $mwst_prozent;
    private $mwst_wert;
    private $userId       	= 0;
    private $mwstBrutto 	= 0;
    
    public function exchangeArray($data) {
        if (isset($data[self::BUCHUNG_NR])) {
            $this->buchung_nr = $data[self::BUCHUNG_NR];
        }
        if (isset($data[self::MWST_ID])) {
            $this->mwst_id = $data[self::MWST_ID];
        }
        if (isset($data[self::MWST_PROZENT])) {
            $this->mwst_prozent = $data[self::MWST_PROZENT];
        }
        if (isset($data[self::MWST_WERT])) {
            $this->mwst_wert = $data[self::MWST_WERT];
        }
        if (isset($data[self::USER_ID])) {
            $this->userId = $data[self::USER_ID];
        }
        if (isset($data[self::MWST_BRUTTO])) {
        	$this->mwstBrutto = $data[self::MWST_BRUTTO];
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
     * @return the $buchung_nr
     */
    public function getBuchung_nr()
    {
        return $this->buchung_nr;
    }

    /**
     * @return the $mwst_id
     */
    public function getMwst_id()
    {
        return $this->mwst_id;
    }

    /**
     * @return the $mwst_prozent
     */
    public function getMwst_prozent()
    {
        return $this->mwst_prozent;
    }

    /**
     * @return the $mwst_wert
     */
    public function getMwst_wert()
    {
        return $this->mwst_wert;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }

    /**
     * @param field_type $mwst_id
     */
    public function setMwst_id($mwst_id)
    {
        $this->mwst_id = $mwst_id;
    }

    /**
     * @param field_type $mwst_prozent
     */
    public function setMwst_prozent($mwst_prozent)
    {
        $this->mwst_prozent = $mwst_prozent;
    }

    /**
     * @param field_type $mwst_wert
     */
    public function setMwst_wert($mwst_wert)
    {
        $this->mwst_wert = $mwst_wert;
    }
	/**
	 * @return Ambigous <number, unknown>
	 */
	public function getMwstBrutto()
	{
		return $this->mwstBrutto;
	}

	/**
	 * @param Ambigous <number, unknown> $mwstBrutto
	 */
	public function setMwstBrutto($mwstBrutto)
	{
		$this->mwstBrutto = $mwstBrutto;
	}



}
// endif;
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

// if ( ! class_exists( 'RS_IB_Model_BuchungZahlung' ) ) :
class RS_IB_Model_BuchungZahlung
{
    const RS_TABLE              = "RS_IB_BUCHUNGZAHLUNG_TABLE";
    
    const BUCHUNG_NR            = "buchung_nr";
    const ZAHLUNG_NR            = "zahlung_nr";
    const ZAHLUNGART            = "zahlungsart";
    const ZAHLUNGBETRAG         = "zahlungsbetrag";
    const ZAHLUNGZEITPUNKT      = "zahlungszeitpunkt";
    const BEZEICHNUNG           = "bezeichnung";
    const USER_ID               = "user_id";
    const CHARGEID				= "charge_id";
    const STATUS				= "status";
    
    const ZAHLART_PAYPALEXPRESS 	= 1;
    const ZAHLART_PAYPAL        	= 2;
    const ZAHLART_INVOICE       	= 3;
    const ZAHLART_STRIPE_CREDIT 	= 4;
    const ZAHLART_STRIPE_SOFORT 	= 5;
    const ZAHLART_BOOKINGCOM 		= 5;
    const ZAHLART_STRIPE_GIROPAY 	= 6;
    const ZAHLART_AMAZONPAYMENTS 	= 7;
    const ZAHLART_AMAZONPAYMENTSEXPRESS 	= 8;
    const ZAHLART_ANZAHLUNG     	= 60;
    const ZAHLART_GUTSCHEIN     	= 99;
    
    private $buchung_nr;
    private $zahlung_nr;
    private $zahlungart;
    private $zahlungbetrag;
    private $zahlungzeitpunkt;
    private $bezeichnung;
    private $userId;
    private $chargeId;
    private $status;
    
    
    public function exchangeArray($data) {
        if (isset($data[self::BUCHUNG_NR])) {
            $this->buchung_nr   = $data[self::BUCHUNG_NR];
        }
        if (isset($data[self::ZAHLUNG_NR])) {
            $this->zahlung_nr   = $data[self::ZAHLUNG_NR];
        }
        if (isset($data[self::ZAHLUNGART])) {
            $this->zahlungart   = $data[self::ZAHLUNGART];
        }
        if (isset($data[self::ZAHLUNGBETRAG])) {
            $this->zahlungbetrag = $data[self::ZAHLUNGBETRAG];
        }
        if (isset($data[self::ZAHLUNGZEITPUNKT])) {
            $this->zahlungzeitpunkt = $data[self::ZAHLUNGZEITPUNKT];
        }
        if (isset($data[self::BEZEICHNUNG])) {
            $this->bezeichnung = $data[self::BEZEICHNUNG];
        }
        if (isset($data[self::USER_ID])) {
            $this->userId = $data[self::USER_ID];
        }
        if (isset($data[self::CHARGEID])) {
        	$this->chargeId = $data[self::CHARGEID];
        }
        if (isset($data[self::STATUS])) {
        	$this->status = $data[self::STATUS];
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
     * @return the $zahlung_nr
     */
    public function getZahlung_nr()
    {
        return $this->zahlung_nr;
    }

    /**
     * @return the $zahlungart
     */
    public function getZahlungart()
    {
        return $this->zahlungart;
    }

    /**
     * @return the $zahlungbetrag
     */
    public function getZahlungbetrag()
    {
        return $this->zahlungbetrag;
    }

    /**
     * @return the $zahlungzeitpunkt
     */
    public function getZahlungzeitpunkt()
    {
        return $this->zahlungzeitpunkt;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }

    /**
     * @param field_type $zahlung_nr
     */
    public function setZahlung_nr($zahlung_nr)
    {
        $this->zahlung_nr = $zahlung_nr;
    }

    /**
     * @param field_type $zahlungart
     */
    public function setZahlungart($zahlungart)
    {
        $this->zahlungart = $zahlungart;
    }

    /**
     * @param field_type $zahlungbetrag
     */
    public function setZahlungbetrag($zahlungbetrag)
    {
        $this->zahlungbetrag = $zahlungbetrag;
    }

    /**
     * @param field_type $zahlungzeitpunkt
     */
    public function setZahlungzeitpunkt($zahlungzeitpunkt)
    {
        $this->zahlungzeitpunkt = $zahlungzeitpunkt;
    }
    /**
     * @return the $bezeichnung
     */
    public function getBezeichnung()
    {
        return $this->bezeichnung;
    }

    /**
     * @param field_type $bezeichnung
     */
    public function setBezeichnung($bezeichnung)
    {
        $this->bezeichnung = $bezeichnung;
    }
	/**
	 * @return mixed
	 */
	public function getChargeId()
	{
		if (is_null($this->chargeId)) {
			$this->chargeId = "";
		}
		return $this->chargeId;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		if (is_null($this->status)) {
			$this->status = "";
		}
		return $this->status;
	}

	/**
	 * @param mixed $chargeId
	 */
	public function setChargeId($chargeId)
	{
		$this->chargeId = $chargeId;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus($status)
	{
		if (is_null($status)) {
			$status = "";
		}
		$this->status = $status;
	}

}
// endif;
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

// if ( ! class_exists( 'RS_IB_Model_Teilbuchungskopf' ) ) :
class RS_IB_Model_Teilbuchungskopf
{
    const RS_TABLE         = "RS_IB_TEILBUCHUNGSKOPF_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
    
    
    
    const BUCHUNG_NR        = "buchung_nr";
    const TEILBUCHUNG_ID    = "teilbuchung_id";
    const APPARTMENT_ID     = "appartment_id";
    const APPARTMENT_NAME   = "appartment_name";
    const APPARTMENT_QM     = "appartment_qm";
    const TEILBUCHUNG_VON   = "teilbuchung_von";
    const TEILBUCHUNG_BIS   = "teilbuchung_bis";
    const TEILBUCHUNG_WERT  = "teilbuchung_wert";
    const ANZAHL_PERSONEN   = "anzahl_personen";
    const USER_ID           = "user_id";
    const BOOKINGCOM_ROOMID = "bcom_roomid";
    const GAST_NAME			= "gast_name";
    
    private $buchung_nr;
    private $teilbuchung_id;
    private $appartment_id;
    private $appartment_name;
    private $appartment_qm;
    private $teilbuchung_von;
    private $teilbuchung_bis;
    private $anzahlPersonen;
    private $userId;
    
    private $positionen         = array();
    private $rabatte            = array();
    private $oriCalcPrice		= 0; //Berechneter Teilbuchungswert ohne Rabatte
    private $calculatedPrice    = 0; //Berechneter Teilbuchungswert abzueglich der Rabatte
    private $bcomroomid			= 0;
    
    private $gastName			= "";
    
    function __clone() {
        $positionen = array();
        $rabatte    = array();
        if (!is_null($this->positionen) && sizeof($this->positionen) > 0) {
            foreach ($this->positionen as $key => $position) {
                array_push($positionen, clone $position);
            }
        }
        if (!is_null($this->rabatte) && isset($this->rabatte) && ($this->rabatte) && sizeof($this->rabatte) > 0) {
            foreach ($this->rabatte as $rkey => $rabatt) {
                array_push($rabatte, clone $rabatt);
            }
        }
        $this->positionen       = $positionen;
        $this->rabatte          = $rabatte;
    }
    
    
    public function exchangeArray($data) {
       	if (isset($data[self::BUCHUNG_NR])) {
           	$this->buchung_nr = $data[self::BUCHUNG_NR];
       	}
       	if (isset($data[self::TEILBUCHUNG_ID])) {
           	$this->teilbuchung_id = $data[self::TEILBUCHUNG_ID];
       	}
       	if (isset($data[self::APPARTMENT_ID])) {
           	$this->appartment_id = $data[self::APPARTMENT_ID];
       	}
       	if (isset($data[self::APPARTMENT_NAME])) {
           	$this->appartment_name = $data[self::APPARTMENT_NAME];
       	}
       	if (isset($data[self::APPARTMENT_QM])) {
           	$this->appartment_qm = $data[self::APPARTMENT_QM];
       	}
       	if (isset($data[self::TEILBUCHUNG_VON])) {
           	$this->teilbuchung_von = new DateTime($data[self::TEILBUCHUNG_VON]);
       	}
       	if (isset($data[self::TEILBUCHUNG_BIS])) {
           	$this->teilbuchung_bis = new DateTime($data[self::TEILBUCHUNG_BIS]);
       	}
       	if (isset($data[self::TEILBUCHUNG_WERT])) {
           	$this->calculatedPrice = $data[self::TEILBUCHUNG_WERT];
       	}
       	if (isset($data[self::ANZAHL_PERSONEN])) {
           	$this->anzahlPersonen = $data[self::ANZAHL_PERSONEN];
       	}
		if (isset($data[self::USER_ID])) {
       		$this->userId = $data[self::USER_ID];
       	}
       	if (isset($data[self::GAST_NAME])) {
			$this->gastName = $data[self::GAST_NAME];
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
     * @return the $teilbuchung_id
     */
    public function getTeilbuchung_id()
    {
        return $this->teilbuchung_id;
    }

    /**
     * @return the $appartment_id
     */
    public function getAppartment_id()
    {
        return $this->appartment_id;
    }

    /**
     * @return the $appartment_qm
     */
    public function getAppartment_qm()
    {
        return $this->appartment_qm;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }

    /**
     * @param field_type $teilbuchung_id
     */
    public function setTeilbuchung_id($teilbuchung_id)
    {
        $this->teilbuchung_id = $teilbuchung_id;
    }

    /**
     * @param field_type $appartment_id
     */
    public function setAppartment_id($appartment_id)
    {
        $this->appartment_id = $appartment_id;
    }

    /**
     * @param field_type $appartment_qm
     */
    public function setAppartment_qm($appartment_qm)
    {
        $this->appartment_qm = $appartment_qm;
    }
    /**
     * @return the $appartment_name
     */
    public function getAppartment_name()
    {
        return $this->appartment_name;
    }

    /**
     * @param field_type $appartment_name
     */
    public function setAppartment_name($appartment_name)
    {
        $this->appartment_name = $appartment_name;
    }
    /**
     * @return the $teilbuchung_von
     */
    public function getTeilbuchung_von()
    {
        return $this->teilbuchung_von;
    }

    /**
     * @return the $teilbuchung_bis
     */
    public function getTeilbuchung_bis()
    {
        return $this->teilbuchung_bis;
    }

    /**
     * @param field_type $teilbuchung_von
     */
    public function setTeilbuchung_von($teilbuchung_von)
    {
        $this->teilbuchung_von = $teilbuchung_von;
    }

    /**
     * @param field_type $teilbuchung_bis
     */
    public function setTeilbuchung_bis($teilbuchung_bis)
    {
        $this->teilbuchung_bis = $teilbuchung_bis;
    }
    /**
     * @return the $positionen
     */
    public function getPositionen()
    {
        return $this->positionen;
    }

    /**
     * @param multitype: $positionen
     */
    public function setPositionen($positionen)
    {
        $this->positionen = $positionen;
    }
    /**
     * @return the $rabatte
     */
    public function getRabatte()
    {
        return $this->rabatte;
    }

    /**
     * @param multitype: $rabatte
     */
    public function setRabatte($rabatte)
    {
        $this->rabatte = $rabatte;
    }
    /**
     * @return the $calculatedPrice
     */
    public function getCalculatedPrice()
    {
        return $this->calculatedPrice;
    }

    /**
     * @param number $calculatedPrice
     */
    public function setCalculatedPrice($calculatedPrice)
    {
        $this->calculatedPrice = $calculatedPrice;
    }


    public function getNumberOfNights() {
        $teilHeadVon            = $this->getTeilbuchung_von();
        $teilHeadBis            = $this->getTeilbuchung_bis();
        
        if (!$teilHeadVon instanceOf DateTime) {
            $teilHeadVon        = new DateTime($teilHeadVon);
        }
        if (!$teilHeadBis instanceOf DateTime) {
            $teilHeadBis        = new DateTime($teilHeadBis);
        }
        
        $numberOfNights         = date_diff($teilHeadVon, $teilHeadBis, false);
        $numberOfNights         = intval($numberOfNights->format('%R%a'));
        
        return $numberOfNights;
    }
    
    /* @var $rabatt RS_IB_Model_BuchungRabatt */
    /* @var $position RS_IB_Model_Buchungposition */
    public function calculatePrice() {
    	global $RSBP_DATABASE;
        $calcPrice =0;
        
//         $gutscheinTable				= null;
//         if (class_exists('RS_IB_Model_Real_Gutschein')) {
//         	$gutscheinTable         = $RSBP_DATABASE->getTable(RS_IB_Model_Gutschein::RS_TABLE);
//         }
        $rabattTbl                  = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        $appartmentBuchungTbl       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $apartmentBuchungController = new RS_IB_Appartment_Buchung_Controller(false);
        
        foreach ($this->getPositionen() as $position) {
            $calcPrice += $position->getCalculatedPrice();
        }
        $this->setOriCalcPrice($calcPrice);
        $rabatte    = $this->getRabatte();
        if (is_array($rabatte) && sizeof($rabatte) > 0) {
            foreach ($rabatte as $rabatt) {
            	$rabattOk               = true;
//             	$termId                 = $rabatt->getRabatt_term_id();
//             	if ($rabatt->getRabatt_art() == 2) { //1 = Aktion | 2 = Coupon | 3 Degression
//             		$gutschein          = $gutscheinTable->getGutscheinById($termId);
//            			$answer             = $apartmentBuchungController->checkApartmentCouponCode($gutschein->getCode(), $this->getBuchung_nr(), false, true, $this);
//            			if ($answer['CODE'] == 1) {
//            				$rabattOk       = true;
//            			} else {
//            				$rabattOk       = false;
//            			}
//             	}
            	if ($rabattOk) {
	                if ($rabatt->getPlus_minus_kz() == 1) {
	                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                        $calcPrice  = $calcPrice * (1 - ($rabatt->getRabatt_wert() / 100));
	                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                        $calcPrice  = $calcPrice - $rabatt->getRabatt_wert();
	                    }
	                } else {
	                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
	                        $calcPrice  = $calcPrice * (1 + ($rabatt->getRabatt_wert() / 100));
	                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
	                        $calcPrice  = $calcPrice + $rabatt->getRabatt_wert();
	                    }
	                }
            	} else {
//             		$buchungKopfId              = $this->getBuchung_nr();
//             		$rabattTbl->deleteBuchungRabatt($rabatt);
//             		$modelAppartmentBuchung     = $buchungTable->getAppartmentBuchungByBuchungsKopfNr($buchungKopfId);
//             		if (!is_null($modelAppartmentBuchung)) {
//             			$coupons                    = $modelAppartmentBuchung->getCoupons();
//             			$postId                     = $modelAppartmentBuchung->getPostId();
//             			$newCoupons                 = array();
            			
//             			foreach ($coupons as $coupon) {
//             				if (!$coupon->getTermId() == $termId) {
//             					array_push($newCoupons, $coupon);
//             				}
//             			}
//             			$appartmentBuchungTbl->updateBookingCoupons($postId, $newCoupons);
//             			$gutscheinTable->resetOneGutschein($gutschein);
//             		}
            	}
            	$calcPrice = round($calcPrice, 2);
            }
        }
        $calcPrice = round($calcPrice, 2);
        $this->setCalculatedPrice($calcPrice);
    }
    /**
     * @return the $anzahlPersonen
     */
    public function getAnzahlPersonen()
    {
        if (is_null($this->anzahlPersonen)) {
            $this->anzahlPersonen = 1;
        }
        return $this->anzahlPersonen;
    }

    /**
     * @param field_type $anzahlPersonen
     */
    public function setAnzahlPersonen($anzahlPersonen)
    {
        $this->anzahlPersonen = $anzahlPersonen;
    }
    
	public function getBcomroomid() {
		return $this->bcomroomid;
	}
	public function setBcomroomid($bcomroomid) {
		$this->bcomroomid = $bcomroomid;
		return $this;
	}
	
	public function getGastName() {
		if (is_null($this->gastName)) {
			return "";
		}
		return $this->gastName;
	}
	public function setGastName($gastName) {
		$this->gastName = $gastName;
		return $this;
	}
	/**
	 * @return number
	 */
	public function getOriCalcPrice()
	{
		return $this->oriCalcPrice;
	}

	/**
	 * @param number $oriCalcPrice
	 */
	public function setOriCalcPrice($oriCalcPrice)
	{
		$this->oriCalcPrice = $oriCalcPrice;
	}

	/* @var $appartmentTable RS_IB_Table_Appartment */
	public function getAppartment_category_name() {
		global $RSBP_DATABASE;
		
		$appartmentTable			= $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
		$firstCategoryName			= $appartmentTable->getApartmentFirstCategoryName($this->getAppartment_id());
		return $firstCategoryName;
	}
	
	
}
// endif;
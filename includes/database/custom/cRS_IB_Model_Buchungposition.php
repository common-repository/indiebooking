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

// if ( ! class_exists( 'RS_IB_Model_Buchungposition' ) ) :
class RS_IB_Model_Buchungposition
{
    const RS_TABLE              = "RS_IB_BUCHUNGPOSITION_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
//Muss bei allen Punkten entsprechend umgesetzt werden!!!!!
    const RS_BERECHNUNG_TYP_SUMME               = 0;
    const RS_BERECHNUNG_TYP_PREISPRONACHT       = 1;
    const RS_BERECHNUNG_TYP_PREISPROQM          = 2;
    const RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT  = 3;
    const RS_BERECHNUNG_TYP_PROZENT             = 4;
    const RS_BERECHNUNG_TYP_PREISPROPERSON      = 5;
    const RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT = 6;
    
    const BUCHUNG_NR            = "buchung_nr";
    const TEILBUCHUNG_ID        = "teilbuchung_id";
    const POSITION_ID           = "position_id";
    const POSITION_TYP          = "position_type";
    const PREIS_VON             = "preis_von";
    const PREIS_BIS             = "preis_bis";
    const ANZAHL_NAECHTE        = "anzahl_naechte";
    const EINZELPREIS           = "einzelpreis";
    const BASISPREIS            = "basispreis";
    const BERECHNUNG_TYPE       = "berechnung_type";
    const MWST_PROZENT          = "mwst_prozent";
    const RABATT_KZ             = "rabatt_kz";
    const BEZEICHNUNG           = "bezeichnung";
    const POSITION_WERT         = "position_wert";
    const BERECHNETER_WERT      = "berechneter_wert";
    const MWST_TERMID           = "mwst_termId";
    const DATA_ID               = "data_id";
    const FULL_STORNO           = "full_storno";
    const USER_ID               = "user_id";
    const KOMMENTAR             = "kommentar";
    
    private $buchung_nr;
    private $teilbuchung_id;
    private $position_id;
    private $position_typ;
    private $preis_von;
    private $preis_bis;
    private $anzahl_naechte;
    private $einzelpreis;
    private $basispreis;
    private $berechnung_type;
    private $mwst_prozent;
    private $rabatt_kz;
    private $bezeichnung;
    private $fullStorno         = 1;
    private $data_id            = 0; //bspw. die OptionId
    
    private $rabatte            = array();
    private $calculatedPrice    = 0; //Berechneter Positionswert abzueglich der Positionsrabatte
    private $calcPosPrice       = 0; //Berechneter Positionswert abzueglich von Buchungskopfrabatten (sofern vorhanden)
    private $mwst_wert          = 0;
    private $mwstTermId         = 0;
    
    private $quadratmeter       = 1; //Standardmueueig erstmal immer 1!
    private $anzahlPersonen     = 1; //Standardmueueig erstmal immer 1!
    
    private $ausschreibFullPrice = 0;
    private $ausschreibEinzelPrice = 0;
    
    private $hasDegression 			= false;
    private $degressionEinzelPrice 	= 0;
    private $degressionRabattValue 	= 0;
    private $degressionRabattTyp 	= 0;
    
    private $rabatteEinzelPrice		= array();
    
    private $nettoBetrag		= 0;
    
    private $userId             = 0;
    private $kommentar          = "";
    
    function __clone() {
        $rabatte    = array();
        if (!is_null($this->rabatte) && isset($this->rabatte) && ($this->rabatte) && sizeof($this->rabatte) > 0) {
//             var_dump($this->rabatte);
            foreach ($this->rabatte as $rkey => $rabatt) {
                array_push($rabatte, clone $rabatt);
            }
        }
        $this->rabatte          = $rabatte;
    }
    
    public function exchangeArray($data) {
        if (isset($data[self::BUCHUNG_NR])){
            $this->buchung_nr = $data[self::BUCHUNG_NR];
        }
        if (isset($data[self::TEILBUCHUNG_ID])){
            $this->teilbuchung_id = $data[self::TEILBUCHUNG_ID];
        }
        if (isset($data[self::POSITION_ID])){
            $this->position_id = $data[self::POSITION_ID];
        }
        if (isset($data[self::POSITION_TYP])){
            $this->position_typ = $data[self::POSITION_TYP];
        }
        if (isset($data[self::PREIS_VON])){
            $this->preis_von = new DateTime($data[self::PREIS_VON]);
        }
        if (isset($data[self::PREIS_BIS])){
            $this->preis_bis = new DateTime($data[self::PREIS_BIS]);
        }
        if (isset($data[self::ANZAHL_NAECHTE])){
            $this->anzahl_naechte = $data[self::ANZAHL_NAECHTE];
        }
        if (isset($data[self::EINZELPREIS])){
            $this->einzelpreis = $data[self::EINZELPREIS];
        }
        if (isset($data[self::BERECHNUNG_TYPE])){
            $this->berechnung_type = $data[self::BERECHNUNG_TYPE];
        }
        if (isset($data[self::MWST_PROZENT])){
            $this->mwst_prozent = $data[self::MWST_PROZENT];
        }
        if (isset($data[self::RABATT_KZ]))   {
            $this->rabatt_kz = $data[self::RABATT_KZ];
        }
        if (isset($data[self::BEZEICHNUNG]))   {
            $this->bezeichnung = $data[self::BEZEICHNUNG];
        }
        if (isset($data[self::POSITION_WERT]))   {
            $this->calculatedPrice = $data[self::POSITION_WERT];
        }
        if (isset($data[self::BERECHNETER_WERT]))   {
            $this->calcPosPrice = $data[self::BERECHNETER_WERT];
        }
        if (isset($data[self::MWST_TERMID]))   {
            $this->mwstTermId = $data[self::MWST_TERMID];
        }
        if (isset($data[self::DATA_ID]))   {
            $this->data_id = $data[self::DATA_ID];
        }
        if (isset($data[self::FULL_STORNO])) {
            $this->fullStorno = $data[self::FULL_STORNO];
        }
        if (isset($data[self::USER_ID])) {
            $this->userId = $data[self::USER_ID];
        }
        if (isset($data[self::KOMMENTAR])) {
            $this->kommentar = $data[self::KOMMENTAR];
        }
    }
    
    /* @var $rabatt RS_IB_Model_BuchungRabatt */
    public function calculateExpelPrice() {
        $posFullPrice    	= $this->getFullPrice();
        $posEinzelPreis  	= $this->getEinzelpreis();
        $rabattEinzelPreis	= $this->getEinzelpreis();
        $rabattPreise		= array();
        
        $this->setHasDegression(false);
        if (is_array($this->getRabatte()) && sizeof($this->getRabatte()) > 0) {
            foreach ($this->getRabatte() as $rabatt) {
                if ($rabatt->getRabatt_ausschreiben_kz() == 0) {
                	/*
                	 * der Rabatt wird nicht ausgewiesen, daher soll er an dieser Stelle sofort auf den Preis
                	 * berechnet werden.
                	 */
                    if ($this->getPosition_typ() == "appartment_price" ||
                        $this->getPosition_typ() == "appartment_option") {
//                         	if ($rabatt->getBerechnung_art() != 4) {
                        	if ($rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION) {
	                            if ($rabatt->getRabatt_typ() == 1) { //Wert
	                            	if ($rabatt->getBerechnung_art() == RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_POSITION_PREIS) {
	                            		if ($rabatt->getPlus_minus_kz() == 1) { //minus
	                            			$posFullPrice = $posFullPrice - ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
	                            		} else {
	                            			$posFullPrice = $posFullPrice + ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
	                            		}
	                            	} else {
		                                if ($rabatt->getPlus_minus_kz() == 1) { //minus
		                                    $posFullPrice = $posFullPrice - $rabatt->getRabatt_wert();
		                                } else {
		                                    $posFullPrice = $posFullPrice + $rabatt->getRabatt_wert();
		                                }
	                            	}
	                            } elseif ($rabatt->getRabatt_typ() == 2) { //Prozent
	                                if ($rabatt->getPlus_minus_kz() == 1) { //minus
// 	                                    $posFullPrice = $posFullPrice * (1 - $rabatt->getRabatt_wert());
	                                	$posFullPrice 	= $posFullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
	                                } else {
	                                	$posFullPrice 	= $posFullPrice * (1 + (abs($rabatt->getRabatt_wert()) / 100));
// 	                                    $posFullPrice = $posFullPrice * (1 + $rabatt->getRabatt_wert());
	                                }
	                            }
	                            $posEinzelPreis 	= ($posFullPrice / $this->getAnzahl_naechte());
	                            $rabattEinzelPreis 	= $posEinzelPreis;
                        	}
                        }
                } else {
                	/*
                	 * Die degression soll auf Positionsebene ausgewiesen werden. Daher muss diese Berechnung an
                	 * dieser Stelle ausgefuehrt werden.
                	 */
//                 	if ($rabatt->getBerechnung_art() == 4) {
                	if ($rabatt->getRabatt_art() == RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION) {
                		//Degression
                		$degressionEinzelPrice = 0;
                		$this->setHasDegression(true);
                		$this->setDegressionRabattTyp($rabatt->getRabatt_typ());
                		$this->setDegressionRabattValue($rabatt->getRabatt_wert());
                		if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
                			$degressionEinzelPrice 	= $posEinzelPreis * (1 - (abs($rabatt->getRabatt_wert()) / 100));
//                 			$posFullPrice 			= $posFullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
                		} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
                			$degressionEinzelPrice 	= $posEinzelPreis - $rabatt->getRabatt_wert();
// 	                		$posFullPrice 			= $posFullPrice - ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
                		}
                		$degressionEinzelPrice   	= round($degressionEinzelPrice, 2);
                		$posFullPrice 				= $degressionEinzelPrice * $this->getAnzahl_naechte();
                		$this->setDegressionEinzelPrice($degressionEinzelPrice);
                		$rabattEinzelPreis 			= $degressionEinzelPrice;
                	} else {
                		if ($rabatt->getBerechnung_art() == RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_POSITION_PREIS) {
                			$basisPreis				= $rabattEinzelPreis;
                			if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
                				$rabattEinzelPreis 	= $rabattEinzelPreis * (1 - (abs($rabatt->getRabatt_wert()) / 100));
//                 				$posFullPrice 		= $posFullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
                			} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
                				$rabattEinzelPreis 	= $rabattEinzelPreis - $rabatt->getRabatt_wert();
//                 				$posFullPrice 		= $posFullPrice - ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
                			}
                			$rabattEinzelPreis   	= round($rabattEinzelPreis, 2);
                			$posFullPrice			= $rabattEinzelPreis * $this->getAnzahl_naechte();
                			$rabattPreis = array(
                				'price' 		=> $rabattEinzelPreis,
                				'basis'			=> $basisPreis,
                				'rabattValue' 	=> $rabatt->getRabatt_wert(),
                				'rabattTyp' 	=> $rabatt->getRabatt_typ(),
                				'description' 	=> $rabatt->getBezeichnung(),
                				'rabattArt' 	=> $rabatt->getRabatt_art(),
                				'plusMinusKz' 	=> $rabatt->getPlus_minus_kz(),
                			);
                			$posRabatte = $this->getRabatteEinzelPrice();
                			array_push($posRabatte, $rabattPreis);
                			$this->setRabatteEinzelPrice($posRabatte);
                		}
                	}
                }
            } //foreach $this->getRabatte()
        }
        $this->ausschreibEinzelPrice = $posEinzelPreis;
        $this->ausschreibFullPrice   = $posFullPrice;
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
     * @return the $teilbuchung_id
     */
    public function getTeilbuchung_id()
    {
        return $this->teilbuchung_id;
    }

    /**
     * @return the $position_id
     */
    public function getPosition_id()
    {
        if (is_null($this->position_id)) {
            return 0;
        }
        return $this->position_id;
    }

    /**
     * @return the $position_typ
     */
    public function getPosition_typ()
    {
        return $this->position_typ;
    }

    /**
     * @return the $preis_von
     */
    public function getPreis_von()
    {
        return $this->preis_von;
    }

    /**
     * @return the $preis_bis
     */
    public function getPreis_bis()
    {
        return $this->preis_bis;
    }

    /**
     * @return the $anzahl_naechte
     */
    public function getAnzahl_naechte()
    {
        return $this->anzahl_naechte;
    }

    /**
     * @return the $einzelpreis
     */
    public function getEinzelpreis()
    {
        return $this->einzelpreis;
    }

    /**
     * @return the $basispreis
     */
    public function getBasispreis()
    {
        return $this->basispreis;
    }
    
    /**
     * @return the $berechnung_type
     */
    public function getBerechnung_type()
    {
        return $this->berechnung_type;
    }

    /**
     * @return the $mwst_prozent
     */
    public function getMwst_prozent()
    {
        return $this->mwst_prozent;
    }

    /**
     * @return the $rabatt_kz
     */
    public function getRabatt_kz()
    {
        if (is_null($this->rabatt_kz)) {
            return 0;
        }
        return $this->rabatt_kz;
    }

    /**
     * @param field_type $teilbuchung_id
     */
    public function setTeilbuchung_id($teilbuchung_id)
    {
        $this->teilbuchung_id = $teilbuchung_id;
    }

    /**
     * @param field_type $position_id
     */
    public function setPosition_id($position_id)
    {
        $this->position_id = $position_id;
    }

    /**
     * @param field_type $position_typ
     */
    public function setPosition_typ($position_typ)
    {
        $this->position_typ = $position_typ;
    }

    /**
     * @param field_type $preis_von
     */
    public function setPreis_von($preis_von)
    {
        $this->preis_von = $preis_von;
    }

    /**
     * @param field_type $preis_bis
     */
    public function setPreis_bis($preis_bis)
    {
        $this->preis_bis = $preis_bis;
    }

    /**
     * @param field_type $anzahl_naechte
     */
    public function setAnzahl_naechte($anzahl_naechte)
    {
        $this->anzahl_naechte = $anzahl_naechte;
    }

    /**
     * @param field_type $einzelpreis
     */
    public function setEinzelpreis($einzelpreis)
    {
        $this->einzelpreis = $einzelpreis;
    }

    /**
     * @param field_type $basispreis
     */
    public function setBasispreis($basispreis)
    {
        $this->basispreis = $basispreis;
    }
    
    /**
     * @param field_type $berechnung_type
     */
    public function setBerechnung_type($berechnung_type)
    {
        $this->berechnung_type = $berechnung_type;
    }

    /**
     * @param field_type $mwst_prozent
     */
    public function setMwst_prozent($mwst_prozent)
    {
        $this->mwst_prozent = $mwst_prozent;
    }

    /**
     * @param field_type $rabatt_kz
     */
    public function setRabatt_kz($rabatt_kz)
    {
        $this->rabatt_kz = $rabatt_kz;
    }

    /**
     * Diese Methode berechnet den Preis und gibt diesen zurueck
     * @return number
     */
    public function getFullPrice() {
        $value = 0;
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."getFullPrice");
        switch ($this->getBerechnung_type()) {
            case self::RS_BERECHNUNG_TYP_SUMME:
                $value  = $this->getEinzelpreis();
                break;
            case self::RS_BERECHNUNG_TYP_PREISPRONACHT:
                $value = $this->getAnzahl_naechte() * $this->getEinzelpreis();
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROQM:
                $value = $this->getQuadratmeter() * $this->getEinzelpreis();
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT:
                $value = $this->getAnzahl_naechte()  * $this->getEinzelpreis() * $this->getQuadratmeter();
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROPERSON:
                $value = $this->getAnzahlPersonen()  * $this->getEinzelpreis();
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT:
                $value = $value = $this->getAnzahlPersonen()  * $this->getEinzelpreis() * $this->getAnzahl_naechte();
                break;
        }
        return $value;
    }
    
    public function getBerechnungstypEinheit() {
        $value = "";
        switch ($this->getBerechnung_type()) {
            case self::RS_BERECHNUNG_TYP_SUMME:
                $value  = "SUM";
                break;
            case self::RS_BERECHNUNG_TYP_PREISPRONACHT:
                $value = "PN";
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROQM:
                $value = "PQ";
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT:
                $value = "PQN";
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROPERSON:
                $value = "PP";
                break;
            case self::RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT:
                $value = "PPN";
                break;
        }
        return $value;
    }
    
    
    public function getBerechnungsAnzahlEinheit() {
    	$value = "";
    	switch ($this->getBerechnung_type()) {
    		case self::RS_BERECHNUNG_TYP_SUMME:
    			$value  = 1;
    			break;
    		case self::RS_BERECHNUNG_TYP_PREISPRONACHT:
    			$value = $this->getAnzahl_naechte();
    			break;
    		case self::RS_BERECHNUNG_TYP_PREISPROQM:
    			$value = $this->getQuadratmeter();//"PQ";
    			break;
    		case self::RS_BERECHNUNG_TYP_PREISPROQMUNDNACHT:
    			$value = $this->getQuadratmeter() * $this->getAnzahl_naechte(); //"PQN";
    			break;
    		case self::RS_BERECHNUNG_TYP_PREISPROPERSON:
    			$value = $this->getAnzahlPersonen();
    			break;
    		case self::RS_BERECHNUNG_TYP_PREISPROPERSONUNDNACHT:
    			$value = $this->getAnzahlPersonen() * $this->getAnzahl_naechte();
    			break;
    	}
    	return $value;
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
     * @return the $buchung_nr
     */
    public function getBuchung_nr()
    {
        return $this->buchung_nr;
    }

    /**
     * @param field_type $buchung_nr
     */
    public function setBuchung_nr($buchung_nr)
    {
        $this->buchung_nr = $buchung_nr;
    }
    /**
     * @return the $rabatte
     */
    
	/* @var $rabatt RS_IB_Model_BuchungRabatt */
    public function getRabatte()
    {
    	/*
    	 * Die Nummerierung ist etwas fehlerhaft.
    	 * Es muss zuerst der Preis pro Nacht (Position Preis) beruecksichtigt werden
    	 * Danach der ApartmentPreis, dann die Option und dann Total
    	 * Daher sortiere ich in dem getter die Rabatte nun richtig
    	 *
    	 * Bei einem Rabatt auf den Preis pro Nacht, muss zuerst der Aufschlag beachtet werden,
    	 * anschlie�end kann dieser wieder rabattiert werden.
    	 *
    	 * const RABATT_BERECHNUNG_APARTMENT_PREIS = 1;
    	 * const RABATT_BERECHNUNG_OPTION          = 2;
    	 * const RABATT_BERECHNUNG_TOTAL           = 3;
    	 * const RABATT_BERECHNUNG_POSITION_PREIS  = 4;
    	 *
	     * const RABATT_ART_AKTION        = 1;
	     * const RABATT_ART_COUPON        = 2;
	     * const RABATT_ART_DEGRESSION    = 3;
	     * const RABATT_ART_AUFSCHLAG     = 4;
    	 *
    	 */
    	$rabatte 		= $this->rabatte;
    	$rightOrder 	= array();
    	$positionRabatt = array();
    	$aufschlagArray = array();
    	if (is_array($rabatte) && sizeof($rabatte) > 0) {
    		foreach ($rabatte as $rabatt) {
    			if ($rabatt->getBerechnung_art() != RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_POSITION_PREIS) {
    				array_push($rightOrder, $rabatt);
    			} else {
    				if ($rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_AUFSCHLAG) {
    					array_push($positionRabatt, $rabatt);
    				} else {
    					array_push($aufschlagArray, $rabatt);
    				}
    			}
    		}
    		if (is_array($aufschlagArray) && sizeof($aufschlagArray) > 0) {
    			for ($i = (sizeof($aufschlagArray)-1); $i > -1; $i--) {
    				array_unshift($positionRabatt, $aufschlagArray[$i]);
    			}
    		}
    		
    		if (is_array($positionRabatt) && sizeof($positionRabatt) > 0) {
    			for ($i = (sizeof($positionRabatt)-1); $i > -1; $i--) {
    				array_unshift($rightOrder, $positionRabatt[$i]);
    			}
    		}
    	} else {
    		$rightOrder = $rabatte;
    	}
    	return $rightOrder;
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
        $this->calcMwStValue($calculatedPrice);
    }

    /**
     * @return the $calcPosPrice
     */
    public function getCalcPosPrice()
    {
        return $this->calcPosPrice;
    }

    /**
     * @param number $calcPosPrice
     */
    public function setCalcPosPrice($calcPosPrice)
    {
        $this->calcPosPrice = $calcPosPrice;
        $this->calcMwStValue($calcPosPrice);
    }
    /**
     * @return the $mwst_wert
     */
    public function getMwst_wert()
    {
        return $this->mwst_wert;
    }

    /**
     * @param number $mwst_wert
     */
    public function setMwst_wert($mwst_wert)
    {
        $this->mwst_wert = $mwst_wert;
    }


    /**
     * @return the $mwstTermId
     */
    public function getMwstTermId()
    {
        return $this->mwstTermId;
    }
    
    /**
     * @param Ambigous <number, unknown> $mwstTermId
     */
    public function setMwstTermId($mwstTermId)
    {
        $this->mwstTermId = $mwstTermId;
    }
    
    
    private function calcMwStValue($brutto) {
        $mwst       = $this->getMwst_prozent() + 1;
        $netto      = $brutto/$mwst;
        $mwstWert   = $brutto - $netto;
        $mwstWert   = round($mwstWert, 2);
        $netto		= $brutto - $mwstWert;
        $this->setMwst_wert($mwstWert);
        $this->setNettoBetrag($netto);
    }

    /* @var $rabatt RS_IB_Model_BuchungRabatt */
    public function calculatePrice() {
    	global $RSBP_DATABASE;
    	
//     	$gutscheinTable             = $RSBP_DATABASE->getTable(RS_IB_Model_Gutschein::RS_TABLE);
    	$rabattTbl                  = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
    	$appartmentBuchungTbl       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
    	$apartmentBuchungController = new RS_IB_Appartment_Buchung_Controller(false);
    	
    	$rabattierterEinzelPreis 	= $this->getEinzelpreis();
    	
        $fullPrice  				= $this->getFullPrice(); //in der Methode getFullPrice wird berechnet!
        $rabatte    				= $this->getRabatte();
        if (is_array($rabatte) && sizeof($rabatte) > 0) {
            foreach ($rabatte as $rabatt) {
            	$rabattOk               = true;
//             	$termId                 = $rabatt->getRabatt_term_id();
//             	if ($rabatt->getRabatt_art() == 2) { //1 = Aktion | 2 = Coupon | 3 Degression
//             		$gutschein          = $gutscheinTable->getGutscheinById($termId);
//             		$answer             = $apartmentBuchungController->checkApartmentCouponCode($gutschein->getCode(), $this->getBuchung_nr(), false, true, null);
//             		if ($answer['CODE'] == 1) {
//             			$rabattOk       = true;
//             		} else {
//             			$rabattOk       = false;
//             		}
//             	}
            	if ($rabattOk) {
            		if ($rabatt->getRabatt_art() != RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION) { //3 = degression
		                if ($rabatt->getPlus_minus_kz() == 1) {
		                	if ($rabatt->getBerechnung_art() < 4) {
								/*
								 	const RABATT_BERECHNUNG_APARTMENT_PREIS = 1;
								    const RABATT_BERECHNUNG_OPTION          = 2;
								    const RABATT_BERECHNUNG_TOTAL           = 3;
								    const RABATT_BERECHNUNG_POSITION_PREIS 	= 4;
								 */
			                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
			                        $fullPrice  = $fullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
			                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
		                        	$fullPrice  = $fullPrice - $rabatt->getRabatt_wert();
			                    }
		                	} else {
		                		//der Rabatt geht auf den Preis pro Nacht
		                		if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
		                			/*
		                			 * bei prozentualen Angaben egal
		                			 * korrektur - 17.04, auch bei prozentualen Angaben muss mit dem GERUNDETEN Wert der
		                			 * Position gerechnet werden.
		                			 * Ansonsten kann es bei der Ausweisung der Preise zu Differenzen kommen!
		                			 */
// 		                			$fullPrice  = $fullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));

		                			$rabattierterEinzelPreis = $rabattierterEinzelPreis * (1 - (abs($rabatt->getRabatt_wert()) / 100));
		                		} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
// 		                			$fullPrice  = $fullPrice - ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
		                			$rabattierterEinzelPreis = $rabattierterEinzelPreis - $rabatt->getRabatt_wert();
		                		}
		                		$rabattierterEinzelPreis 	= round($rabattierterEinzelPreis, 2);
		                		$fullPrice  				= $rabattierterEinzelPreis * $this->getAnzahl_naechte();
		                	}
		                } else {
		                	if ($rabatt->getBerechnung_art() < 4) {
			                    if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
			                        $fullPrice  = $fullPrice * (1 + (abs($rabatt->getRabatt_wert()) / 100));
			                    } elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
			                        $fullPrice  = $fullPrice + $rabatt->getRabatt_wert();
			                    }
		                	} else {
		                		if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
// 		                			$fullPrice  = $fullPrice * (1 + (abs($rabatt->getRabatt_wert()) / 100));
		                			$rabattierterEinzelPreis = $rabattierterEinzelPreis * (1 + (abs($rabatt->getRabatt_wert()) / 100));
		                		} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
// 		                			$fullPrice  = $fullPrice + ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
		                			$rabattierterEinzelPreis = $rabattierterEinzelPreis + $rabatt->getRabatt_wert();
		                		}
		                	}
		                	$rabattierterEinzelPreis 		= round($rabattierterEinzelPreis, 2);
		                	$fullPrice  					= $rabattierterEinzelPreis * $this->getAnzahl_naechte();
		                }
	            	} else {
	            		//Degressionsrabatt
	            		if ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_PROZENT) {
// 	            			$fullPrice  = $fullPrice * (1 - (abs($rabatt->getRabatt_wert()) / 100));
	            			$rabattierterEinzelPreis = $rabattierterEinzelPreis * (1 - (abs($rabatt->getRabatt_wert()) / 100));
	            		} elseif ($rabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL) {
// 	            			$fullPrice  = $fullPrice - ($rabatt->getRabatt_wert() * $this->getAnzahl_naechte());
	            			$rabattierterEinzelPreis = $rabattierterEinzelPreis - $rabatt->getRabatt_wert();
	            		}
	            		$rabattierterEinzelPreis 	= round($rabattierterEinzelPreis, 2);
	            		$fullPrice  				= $rabattierterEinzelPreis * $this->getAnzahl_naechte();
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
            	$fullPrice = round($fullPrice, 2);
            }
        }
        $fullPrice = round($fullPrice, 2);
        $this->setCalculatedPrice($fullPrice);
        $this->setCalcPosPrice($fullPrice);
        
    }
    
    /**
     * @return the $data_id
     */
    public function getData_id()
    {
        return $this->data_id;
    }

    /**
     * @param number $data_id
     */
    public function setData_id($data_id)
    {
        $this->data_id = $data_id;
    }
    /**
     * @return the $fullStorno
     */
    public function getFullStorno()
    {
    	if (is_null($this->fullStorno)) {
    		$this->fullStorno = 1;
    	}
        return $this->fullStorno;
    }

    /**
     * @param number $fullStorno
     */
    public function setFullStorno($fullStorno)
    {
        $this->fullStorno = $fullStorno;
    }
    /**
     * @return the $quadratmeter
     */
    public function getQuadratmeter()
    {
    	if (is_null($this->quadratmeter) || empty($this->quadratmeter)) {
    		$this->quadratmeter = 1;
    	}
        return $this->quadratmeter;
    }

    /**
     * @param number $quadratmeter
     */
    public function setQuadratmeter($quadratmeter)
    {
        $this->quadratmeter = $quadratmeter;
    }
    /**
     * @return the $ausschreibFullPrice
     */
    public function getAusschreibFullPrice()
    {
        return $this->ausschreibFullPrice;
    }

    /**
     * @return the $ausschreibEinzelPrice
     */
    public function getAusschreibEinzelPrice()
    {
        return $this->ausschreibEinzelPrice;
    }
    /**
     * @return the $anzahlPersonen
     */
    public function getAnzahlPersonen()
    {
        return $this->anzahlPersonen;
    }

    /**
     * @param number $anzahlPersonen
     */
    public function setAnzahlPersonen($anzahlPersonen)
    {
        $this->anzahlPersonen = $anzahlPersonen;
    }

    /**
     * @return the $anzahlPersonen
     */
    public function getKommentar()
    {
        if (is_null($this->kommentar)) {
            return "";
        }
        return $this->kommentar;
    }
    
    /**
     * @param number $anzahlPersonen
     */
    public function setKommentar($kommentar)
    {
        $this->kommentar = $kommentar;
    }
    
	public function getHasDegression() {
		return $this->hasDegression;
	}
	
	public function setHasDegression($hasDegression) {
		$this->hasDegression = $hasDegression;
		return $this;
	}
	
	public function getDegressionEinzelPrice() {
		return $this->degressionEinzelPrice;
	}
	
	public function setDegressionEinzelPrice($degressionEinzelPrice) {
		$this->degressionEinzelPrice = $degressionEinzelPrice;
		return $this;
	}
	
	public function getDegressionRabattValue() {
		return $this->degressionRabattValue;
	}
	public function setDegressionRabattValue($degressionRabattValue) {
		$this->degressionRabattValue = $degressionRabattValue;
		return $this;
	}
	public function getDegressionRabattTyp() {
		return $this->degressionRabattTyp;
	}
	public function setDegressionRabattTyp($degressionRabattTyp) {
		$this->degressionRabattTyp = $degressionRabattTyp;
		return $this;
	}
	/**
	 * @return multitype:
	 */
	public function getRabatteEinzelPrice()
	{
		if (is_null($this->rabatteEinzelPrice)) {
			return array();
		}
		return $this->rabatteEinzelPrice;
	}
	
	/**
	 * @param multitype: $rabatteEinzelPrice
	 */
	public function setRabatteEinzelPrice($rabatteEinzelPrice)
	{
		$this->rabatteEinzelPrice = $rabatteEinzelPrice;
	}
	/**
	 * @return number
	 */
	public function getNettoBetrag()
	{
		return $this->nettoBetrag;
	}

	/**
	 * @param number $nettoBetrag
	 */
	public function setNettoBetrag($nettoBetrag)
	{
		$this->nettoBetrag = $nettoBetrag;
	}


}
// endif;
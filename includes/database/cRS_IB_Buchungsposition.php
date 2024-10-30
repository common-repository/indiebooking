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
}

// if ( ! class_exists( 'RS_IB_Buchungsposition' ) ) :
class RS_IB_Buchungsposition
{
    const ART_OPTION        = "OPTION";
    const ART_ZEITRAUM      = "ZEITRAUM";
    const ART_AKTION        = "AKTION";
    const ART_COUPON        = "COUPON";
    const ART_GUTSCHEIN     = "GUTSCHEIN";
    
    const ART_SUMME_ZEITRAUM = "SUMME_ZEITRAUM";
    
    const CALC_NORMAL       = "NORMAL";
    const CALC_DISCOUNT     = "DISCOUNT";
    const CALC_EXTRA_CHARGE = "EXTRA_CHARGE";
    
    const POSITION_TYPE_PRICE = "appartment_price";
    const POSITION_TYPE_OPTION = "appartment_option";
    
    private $positionArt;
    private $calculation;
    private $name;
    private $datumVon;
    private $datumBis;
    private $defaultPrice;
    private $brutto;
    private $netto;
    private $tax; //bspw. 0.19 || 0.0.7
    private $taxKey; //bspw. 19 || 7
    private $taxValue; //$netto * $tax
    
    private $positionObject = null;
    
    public function __construct($art) {
        $this->positionArt = $art;
    }
    
    public function getNumberOfNights() {
        $numberOfNights     = date_diff($this->getDatumBis(), $this->getDatumVon());
        $numberOfNights     = intval($numberOfNights->format('%a'));
        return $numberOfNights;
    }
    
    public function getTaxPercent() {
        return $this->tax * 100;
    }
    
    /**
     * @return the $positionArt
     */
    public function getPositionArt()
    {
        return $this->positionArt;
    }

    /**
     * @return the $calculation
     */
    public function getCalculation()
    {
        return $this->calculation;
    }

    /**
     * @return the $name
     */
    public function getName()
    {
        if (is_null($this->name)) {
            return "";
        }
        return $this->name;
    }

    /**
     * @return the $datumVon
     */
    public function getDatumVon()
    {
        return $this->datumVon;
    }

    /**
     * @return the $datumBis
     */
    public function getDatumBis()
    {
        return $this->datumBis;
    }

    /**
     * @return the $brutto
     */
    public function getBrutto()
    {
        return $this->brutto;
    }

    /**
     * @return the $netto
     */
    public function getNetto()
    {
        return $this->netto;
    }

    /**
     * @return the $tax
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @return the $defaultPrice
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * @param field_type $positionArt
     */
    public function setPositionArt($positionArt)
    {
        $this->positionArt = $positionArt;
    }

    /**
     * @param field_type $calculation
     */
    public function setCalculation($calculation)
    {
        $this->calculation = $calculation;
    }

    /**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param field_type $datumVon
     */
    public function setDatumVon($datumVon)
    {
        $this->datumVon = $datumVon;
    }

    /**
     * @param field_type $datumBis
     */
    public function setDatumBis($datumBis)
    {
        $this->datumBis = $datumBis;
    }

    /**
     * @param field_type $brutto
     */
    public function setBrutto($brutto)
    {
        $this->brutto = $brutto;
    }

    /**
     * @param field_type $netto
     */
    public function setNetto($netto)
    {
        $this->netto = $netto;
    }

    /**
     * @param field_type $tax
     */
    public function setTax($tax)
    {
        $this->tax  = $tax;
        $taxKey     = str_replace(".", "", $tax);
        $taxKey     = str_replace(",", "", $taxKey);
        $taxKey     = intval($taxKey);
        $this->setTaxKey($taxKey);
    }

    /**
     * @param field_type $defaultPrice
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->defaultPrice = $defaultPrice;
    }
    /**
     * @return the $positionObject
     */
    public function getPositionObject()
    {
        return $this->positionObject;
    }

    /**
     * @param field_type $positionObject
     */
    public function setPositionObject($positionObject)
    {
        $this->positionObject = $positionObject;
    }
    /**
     * @return the $taxKey
     */
    public function getTaxKey()
    {
        return $this->taxKey;
    }

    /**
     * @param field_type $taxKey
     */
    public function setTaxKey($taxKey)
    {
        $this->taxKey = $taxKey;
    }
    /**
     * @return the $taxValue
     */
    public function getTaxValue()
    {
        return $this->taxValue;
    }

    /**
     * @param field_type $taxValue
     */
    public function setTaxValue($taxValue)
    {
        $this->taxValue = $taxValue;
    }
}
// endif;
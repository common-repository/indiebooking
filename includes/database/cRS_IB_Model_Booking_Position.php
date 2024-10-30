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
// if ( ! class_exists( 'RS_IB_Model_Booking_Position' ) ) :
class RS_IB_Model_Booking_Position //extends RS_IB_Model_Postmeta
{
    const RS_TABLE              = "RS_IB_BOOKING_POSITION_TABLE";
//     const RS_POSTTYPE           = "rsappartment_zeitraeume";
     
    const BOOKING_ID            = "booking_id";
    const POSITION_ID           = "position_id";
    const POSITION_TYPE         = "position_type"; //appartment_price | option | coupon | campagne
    const APPARTMENT_ID         = "appartment_id";
    const DATE_FROM             = "date_from";
    const DATE_TO               = "date_to";
    const NUMBER_OF_NIGHTS      = "number_nights";
    const PRICE                 = "price";
    const CALC_TYPE             = "calc_type";
    const CALCULATION           = "calculation";
    const MWST_PERCENT          = "mwst_percent";
    const DISCOUNT_KZ           = "discount_kz";
    const META_VALUE            = "meta_value";
    const APPARTMENT_QM         = "appartment_qm";
    
    private $booking_id;
    private $position_id;
    private $position_type;
    private $appartment_id;
    private $date_from;
    private $date_to;
    private $number_of_nights;
    private $price;
    private $mwst_percent;
    private $calc_type;
    /*
     * calc_type - Bei Optionen:
     *  - 0 = total
     *  - 1 = Price / night
     *  - 2 = Price / qm
     *  - 3 = Price / qm & night
     *
     *  calc_type - Bei Aktionen:
     *  - 1 = auf Appartment
     *  - 2 = auf Option
     *  - 3 = auf Total
     */
    
    private $calculation; //wichtig bei Aktionen & Coupons
    /*
     * - 1 = total
     * - 2 = prozent
     */
    
    
    private $discount_kz;
    private $meta_value;
    private $appartment_qm;
    
    /**
     * @return the $booking_id
     */
    public function getBooking_id()
    {
        return $this->booking_id;
    }

    /**
     * @return the $position_id
     */
    public function getPosition_id()
    {
        return $this->position_id;
    }

    /**
     * @return the $position_type
     */
    public function getPosition_type()
    {
        return $this->position_type;
    }

    /**
     * @return the $appartment_id
     */
    public function getAppartment_id()
    {
        return $this->appartment_id;
    }

    /**
     * @return the $date_from
     */
    public function getDate_from()
    {
        return $this->date_from;
    }

    /**
     * @return the $date_to
     */
    public function getDate_to()
    {
        return $this->date_to;
    }

    /**
     * @return the $number_of_nights
     */
    public function getNumber_of_nights()
    {
        return $this->number_of_nights;
    }

    /**
     * @return the $price_per_night
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return the $mwst_percent
     */
    public function getMwst_percent()
    {
        return $this->mwst_percent;
    }

    /**
     * @return the $discount_kz
     */
    public function getDiscount_kz()
    {
        return $this->discount_kz;
    }

    /**
     * @return the $meta_value
     */
    public function getMeta_value()
    {
        return $this->meta_value;
    }

    /**
     * @param field_type $booking_id
     */
    public function setBooking_id($booking_id)
    {
        $this->booking_id = $booking_id;
    }

    /**
     * @param field_type $position_id
     */
    public function setPosition_id($position_id)
    {
        $this->position_id = $position_id;
    }

    /**
     * @param field_type $position_type
     */
    public function setPosition_type($position_type)
    {
        $this->position_type = $position_type;
    }

    /**
     * @param field_type $appartment_id
     */
    public function setAppartment_id($appartment_id)
    {
        $this->appartment_id = $appartment_id;
    }

    /**
     * @param field_type $date_from
     */
    public function setDate_from($date_from)
    {
        $this->date_from = $date_from;
    }

    /**
     * @param field_type $date_to
     */
    public function setDate_to($date_to)
    {
        $this->date_to = $date_to;
    }

    /**
     * @param field_type $number_of_nights
     */
    public function setNumber_of_nights($number_of_nights)
    {
        $this->number_of_nights = $number_of_nights;
    }

    /**
     * @param field_type $price_per_night
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param field_type $mwst_percent
     */
    public function setMwst_percent($mwst_percent)
    {
        $this->mwst_percent = $mwst_percent;
    }

    /**
     * @param field_type $discount_kz
     */
    public function setDiscount_kz($discount_kz)
    {
        $this->discount_kz = $discount_kz;
    }

    /**
     * @param field_type $meta_value
     */
    public function setMeta_value($meta_value)
    {
        $this->meta_value = $meta_value;
    }
    /**
     * @return the $calc_type
     */
    public function getCalc_type()
    {
        return $this->calc_type;
    }

    /**
     * @param field_type $calc_type
     */
    public function setCalc_type($calc_type)
    {
        $this->calc_type = $calc_type;
    }
    /**
     * @return the $calculation
     */
    public function getCalculation()
    {
        return $this->calculation;
    }

    /**
     * @param field_type $calculation
     */
    public function setCalculation($calculation)
    {
        $this->calculation = $calculation;
    }
    /**
     * @return the $appartment_qm
     */
    public function getAppartment_qm()
    {
        return $this->appartment_qm;
    }

    /**
     * @param field_type $appartment_qm
     */
    public function setAppartment_qm($appartment_qm)
    {
        $this->appartment_qm = $appartment_qm;
    }

}
// endif;
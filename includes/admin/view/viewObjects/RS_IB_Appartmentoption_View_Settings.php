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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// if ( ! class_exists( 'RS_IB_Appartmentoption_View_Settings' ) ) :
class RS_IB_Appartmentoption_View_Settings
{
    private $mwst;
    private $apartmentOption;
    private $uebersicht = false;
    private $stornoKz;
    
    /**
     * @return the $mwst
     */
    public function getMwst()
    {
        return $this->mwst;
    }

    /**
     * @return the $apartmentOption
     */
    public function getApartmentOption()
    {
        return $this->apartmentOption;
    }

    /**
     * @return the $uebersicht
     */
    public function getUebersicht()
    {
        return $this->uebersicht;
    }

    /**
     * @return the $stornoKz
     */
    public function getStornoKz()
    {
        return $this->stornoKz;
    }

    /**
     * @param field_type $mwst
     */
    public function setMwst($mwst)
    {
        $this->mwst = $mwst;
    }

    /**
     * @param field_type $apartmentOption
     */
    public function setApartmentOption($apartmentOption)
    {
        $this->apartmentOption = $apartmentOption;
    }

    /**
     * @param field_type $uebersicht
     */
    public function setUebersicht($uebersicht)
    {
        $this->uebersicht = $uebersicht;
    }

    /**
     * @param field_type $stornoKz
     */
    public function setStornoKz($stornoKz)
    {
        $this->stornoKz = $stornoKz;
    }

}
// endif;
?>
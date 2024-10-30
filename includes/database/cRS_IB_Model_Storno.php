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
// if ( ! class_exists( 'RS_IB_Model_Storno' ) ) :
class RS_IB_Model_Storno
{
    //OPTION : 'rs_indiebooking_settings_mwst'
    const RS_TABLE       = "RS_IB_STORNO_TABLE";
    
    
    private $id             = null;
    private $stornovalue    = null;
    private $stornodays     = null;
    
    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $stornovalue
     */
    public function getStornovalue()
    {
        return $this->stornovalue;
    }

    /**
     * @return the $stornodays
     */
    public function getStornodays()
    {
        if (is_null($this->stornodays) || $this->stornodays == "") {
            return 0;
        }
        return $this->stornodays;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $stornovalue
     */
    public function setStornovalue($stornovalue)
    {
        $this->stornovalue = $stornovalue;
    }

    /**
     * @param field_type $stornodays
     */
    public function setStornodays($stornodays)
    {
        $this->stornodays = $stornodays;
    }

}
// endif;
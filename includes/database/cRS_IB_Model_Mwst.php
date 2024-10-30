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
// if ( ! class_exists( 'RS_IB_Model_Mwst' ) ) :
class RS_IB_Model_Mwst
{
    //OPTION : 'rs_indiebooking_settings_mwst'
    const RS_TABLE       = "RS_IB_MWST_TABLE";
    
//     const MWST_ID   = "rs_appartment_jahr";
//     const MWST_VALUE  = "rs_appartment_dates";
    
    private $id           	= null;
    private $mwst          	= null;
    private $revenueAccount = null;
//     public function exchangeArray($data) {
//         if (isset($data[self::MWST_ID])) {
//             $id = $data[self::MWST_ID][0];
//         } else {
//             $id = "";
//         }
//         if (isset($data[self::MWST_VALUE])) {
//             $value = $data[self::MWST_VALUE][0];
//         } else {
//             $value = "";
//         }

//         $this->setMwstId($id);
//         $this->setMwstValue($value);
//     }
    
    public function setMwstId($id) {
        $this->id = $id;
    }
    
    public function getMwstId() {
        if (is_null($this->id)) {
            return 0;
        }
        return $this->id;
    }
    
    public function setMwstValue($value) {
        $this->mwst = $value;
    }
    
    public function getMwstValue() {
        if (is_null($this->mwst)) {
            return 0;
        }
        if (is_string($this->mwst)) {
        	$this->mwst = str_replace(",", ".", $this->mwst);
        }
        return $this->mwst;
    }
	/**
	 * @return mixed
	 */
	public function getRevenueAccount()
	{
		return $this->revenueAccount;
	}

	/**
	 * @param mixed $revenueAccount
	 */
	public function setRevenueAccount($revenueAccount)
	{
		$this->revenueAccount = $revenueAccount;
	}

}
// endif;
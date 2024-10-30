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
// if ( ! class_exists( 'RS_IB_Model_Appartment_Zeitraeume' ) ) :
class RS_IB_Model_Appartment_Zeitraeume //extends RS_IB_Model_Postmeta
{
    const RS_TABLE              = "RS_IB_APPARTMENT_ZEITRAEUME_TABLE";
    const RS_POSTTYPE           = "rsappartment_zeitraeume";
    
    const APPARTMENT_JAHR       = "rs_appartment_jahr";
    
    private $jahr               = null;
    
    public function exchangeArray($data) {
        if (isset($data[self::APPARTMENT_JAHR])) {
            $jahr = $data[self::APPARTMENT_JAHR][0];
        } else {
            $jahr = "";
        }
        
        $this->setJahr($jahr);
    }
    
    public function setJahr($jahr) {
        $this->jahr = $jahr;
    }
    
    public function getJahr() {
        if (is_null($this->jahr)) {
            return 0;
        }
        return $this->jahr;
    }
}
// endif;
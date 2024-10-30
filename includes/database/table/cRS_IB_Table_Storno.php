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
// if ( ! class_exists( 'RS_IB_Table_Storno' ) ) :
/**
 * RS_IB_Table_Appartment ist dafuer zustuendig, die Meta-Appartmentwerte in der entsprechenden Tabelle zu speichern
 * und bei Abruf als Objekt zurueck zu geben.
 * @author schmitt
 *
 */
class RS_IB_Table_Storno
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        
    }
    
    public function getNextStornoId() {
        return get_option( 'rs_indiebooking_storno_nextId' );
    }
    
    public function getAllStorno() {
        $stornos        = array();
        $results        = get_option( 'rs_indiebooking_settings_storno' );
//         var_dump($results);
        if ($results) {
            foreach ($results as $key => $result) {
                if (!empty($result['id'])) {
                    $storno       = new RS_IB_Model_Storno();
                    $storno->setId($result['id']);
                    $storno->setStornovalue($result['refund_value']);
                    $storno->setStornodays($result['days']);
                    $stornos[]    = $storno;
                }
            }
        }
        return $stornos;
    }
    
}
// endif;
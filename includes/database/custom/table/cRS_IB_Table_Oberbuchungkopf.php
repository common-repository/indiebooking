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

// if ( ! class_exists( 'RS_IB_Table_Oberbuchungkopf' ) ) :
class RS_IB_Table_Oberbuchungkopf
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    
    public function loadBookingByRechnungnr($rechnungNr) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        $bookingTbl     = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $sql            = $wpdb->prepare( "SELECT ".RS_IB_Model_Buchungskopf::BUCHUNG_NR
            ." FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR ." = %d",
            array(
                $rechnungNr
            )
        );
        $buchungen      = array();
        $oberbuchung    = new RS_IB_Model_Oberbuchungkopf();
        $oberbuchung->setRechnung_nr($rechnungNr);
        $results        = $wpdb->get_results( $sql , ARRAY_N );
        if (is_array($results) && sizeof($results) > 0) {
            foreach ($results as $result) {
                $oberbuchung->addBuchung($bookingTbl->loadBooking($result[0]));
            }
        }
        $oberbuchung->calculatePrice();
        return $oberbuchung;
    }
    
}
// endif;
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

// if ( ! class_exists( 'RS_IB_Table_BuchungMwSt' ) ) :
class RS_IB_Table_BuchungMwSt
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function loadBuchungMwSt($buchungNr, $mwstId) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
    
        $buchungMwstTbl = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungMwSt::RS_TABLE);
    
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_mwst';
        $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungMwSt::BUCHUNG_NR ." = %d"
                                        . " AND ".RS_IB_Model_BuchungMwSt::MWST_ID ." = %d",
            array(
                $buchungNr,
                $mwstId
            )
        );
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        $buchungMwst    = new RS_IB_Model_BuchungMwSt();
        if (is_array($results) && sizeof($results) > 0) {
            $buchungMwst->exchangeArray($results[0]);
        } else {
            $buchungMwst = false;
        }
        return $buchungMwst;
    }
    
    /* @var $buchungMwSt RS_IB_Model_BuchungMwSt */
    public function saveOrUpdateBuchungMwSt( RS_IB_Model_BuchungMwSt $buchungMwSt) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_mwst';
        if (!$this->loadBuchungMwSt($buchungMwSt->getBuchung_nr(), $buchungMwSt->getMwst_id())) {
            $result     = $wpdb->insert(
                $table_name,
                array(
                    RS_IB_Model_BuchungMwSt::USER_ID
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_BuchungMwSt::BUCHUNG_NR
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getBuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_BuchungMwSt::MWST_ID
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_BuchungMwSt::MWST_PROZENT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_prozent(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                    RS_IB_Model_BuchungMwSt::MWST_WERT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_wert(), RS_IB_Data_Validation::DATATYPE_NUMBER)
                ),
                array(
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                )
            );
        } else {
            $result = $wpdb->update(
                $table_name,
                array (
                    RS_IB_Model_BuchungMwSt::MWST_PROZENT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_prozent(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                    RS_IB_Model_BuchungMwSt::MWST_WERT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_wert(), RS_IB_Data_Validation::DATATYPE_NUMBER)
                ),
                array(
                    RS_IB_Model_BuchungMwSt::BUCHUNG_NR
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getBuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_BuchungMwSt::MWST_ID
                        => RS_IB_Data_Validation::check_with_whitelist($buchungMwSt->getMwst_id(), RS_IB_Data_Validation::DATATYPE_NUMBER)
                ),
                array('%s','%s'),
                array('%d','%d')
            );
        }
    }
}
// endif;
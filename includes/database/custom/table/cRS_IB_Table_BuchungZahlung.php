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

// if ( ! class_exists( 'RS_IB_Table_BuchungZahlung' ) ) :
class RS_IB_Table_BuchungZahlung
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function getNextZahlungNumber($buchung_nr) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
        $nextId         = 1;
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
    
        $field          = RS_IB_Model_BuchungZahlung::ZAHLUNG_NR;
        $bField         = RS_IB_Model_BuchungZahlung::BUCHUNG_NR;
    
        $sql            = "SELECT MAX($field) FROM $table_name WHERE $bField = %d";
        $sql            = $wpdb->prepare($sql, $buchung_nr);
    
        $results        = $wpdb->get_var( $sql );
        if (!is_null($results) && $results > 0) {
            $nextId     = $results + 1;
            //             echo "neue ID: " . $nextId;
        }
        return $nextId;
    }
    
    public function loadBuchungZahlungenByChargeId($chargeId) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$buchungMwstTbl = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
    	$zahlungen      = array();
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
    	$sqlString      = "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungZahlung::CHARGEID ." = %s";
    	$param          = array($chargeId);
    	$sql            = $wpdb->prepare( $sqlString, $param);
    	RS_Indiebooking_Log_Controller::write_log($sql);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	if (is_array($results) && sizeof($results) > 0) {
    		foreach ($results as $result) {
    			$buchungZahlung = new RS_IB_Model_BuchungZahlung();
    			$buchungZahlung->exchangeArray($result);
    			array_push($zahlungen, $buchungZahlung);
    		}
    	} else {
    		$zahlungen = false;
    	}
    	return $zahlungen;
    }
    
    public function loadBuchungZahlungen($buchungNr, $zahlungNr = 0) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
    
        $buchungMwstTbl = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
        $zahlungen      = array();
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
        $sqlString      = "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungZahlung::BUCHUNG_NR ." = %d";
        $param          = array($buchungNr);
        if ($zahlungNr > 0) {
            $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungZahlung::ZAHLUNG_NR ." = %d";
            array_push($param, $zahlungNr);
        }
        $sql            = $wpdb->prepare( $sqlString, $param);
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        if (is_array($results) && sizeof($results) > 0) {
            foreach ($results as $result) {
                $buchungZahlung = new RS_IB_Model_BuchungZahlung();
                $buchungZahlung->exchangeArray($result);
                array_push($zahlungen, $buchungZahlung);
            }
        } else {
//             $buchungZahlung = false;
        }
        return $zahlungen;
    }
    
    /* @var $buchungMwSt RS_IB_Model_BuchungMwSt */
    public function saveOrUpdateBuchungZahlung( RS_IB_Model_BuchungZahlung $buchungZahlung) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
        $dtZeitpunkt    = rs_ib_date_util::convertDateValueToDateTime($buchungZahlung->getZahlungzeitpunkt());
        $dtZeitpunkt    = date('Y-m-d H:i:s', $dtZeitpunkt->getTimestamp());
        if ($buchungZahlung->getZahlung_nr() > 0) {
            $zahlungen  = $this->loadBuchungZahlungen($buchungZahlung->getBuchung_nr(), $buchungZahlung->getZahlung_nr());
        } else {
            $zahlungen  = false;
        }
        if (!$zahlungen || sizeof($zahlungen) <= 0) {
            $zahlungNr = $this->getNextZahlungNumber($buchungZahlung->getBuchung_nr());
            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."insert Zahlung $zahlungNr - $table_name");
            $result = $wpdb->insert(
                $table_name,
                array(
                    RS_IB_Model_BuchungZahlung::USER_ID             => $buchungZahlung->getUserId(),
                    RS_IB_Model_BuchungZahlung::BUCHUNG_NR          => $buchungZahlung->getBuchung_nr(),
                    RS_IB_Model_BuchungZahlung::ZAHLUNG_NR          => $zahlungNr,
                    RS_IB_Model_BuchungZahlung::ZAHLUNGART          => $buchungZahlung->getZahlungart(),
                    RS_IB_Model_BuchungZahlung::ZAHLUNGBETRAG       => $buchungZahlung->getZahlungbetrag(),
                    RS_IB_Model_BuchungZahlung::ZAHLUNGZEITPUNKT    => $dtZeitpunkt,
                    RS_IB_Model_BuchungZahlung::BEZEICHNUNG         => $buchungZahlung->getBezeichnung(),
                	RS_IB_Model_BuchungZahlung::CHARGEID			=> $buchungZahlung->getChargeId(),
                	RS_IB_Model_BuchungZahlung::STATUS				=> $buchungZahlung->getStatus(),
                ),
                array(
                    '%d',
                    '%d',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                	'%s',
                	'%s',
                )
            );
        } else {
            $result = $wpdb->update(
                $table_name,
                array (
                    RS_IB_Model_BuchungZahlung::ZAHLUNGART       => $buchungZahlung->getZahlungart(), //TODO Zahlungsarten definieren
                    RS_IB_Model_BuchungZahlung::ZAHLUNGBETRAG    => $buchungZahlung->getZahlungbetrag(),
                    RS_IB_Model_BuchungZahlung::ZAHLUNGZEITPUNKT => $dtZeitpunkt,
                    RS_IB_Model_BuchungZahlung::BEZEICHNUNG      => $buchungZahlung->getBezeichnung(),
                	RS_IB_Model_BuchungZahlung::CHARGEID		 => $buchungZahlung->getChargeId(),
                	RS_IB_Model_BuchungZahlung::STATUS			 => $buchungZahlung->getStatus(),
                ),
                array(
                    RS_IB_Model_BuchungZahlung::BUCHUNG_NR       => $buchungZahlung->getBuchung_nr(),
                    RS_IB_Model_BuchungZahlung::ZAHLUNG_NR       => $buchungZahlung->getZahlung_nr(),
                ),
            	array('%d','%s','%s','%s','%s','%s'),
                array('%d','%d')
            );
        }
    }
}
// endif;
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

// if ( ! class_exists( 'RS_IB_Table_BuchungRabatt' ) ) :
class RS_IB_Table_BuchungRabatt
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function checkExistingRabatt(RS_IB_Model_BuchungRabatt $buchungrabatt) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
        $buchungsNr     = $buchungrabatt->getBuchung_nr();
        $teilbuchungnr  = $buchungrabatt->getTeilbuchung_nr();
        $positionNr     = $buchungrabatt->getPosition_nr();
        $rabattTermId   = $buchungrabatt->getRabatt_term_id();
        
        $teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        //         $positionTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        $rabatte        = array();
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
        
        $param          = array();
        $sqlString      = "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungRabatt::BUCHUNG_NR ." = %d";
        array_push($param, $buchungsNr);
        
        $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR." = %d";
        array_push($param, $teilbuchungnr);
        
        if ($buchungrabatt->getRabatt_typ() == RS_IB_Model_BuchungRabatt::RABATT_TYP_TOTAL
                && $buchungrabatt->getBerechnung_art() == RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_APARTMENT_PREIS) {
            /*
             Wenn es sich um einen TOTAL-Rabatt handelt und die berechnungsart auf den Apartmentpreis geht
             darf der Rabatt nur 1 mal vergeben werden und nicht pro preis-position!
             Demnach ist beim Laden eines eventuell vorhandenen Rabattes die Position irrelevant
             obwohl es sich um einen Positionsrabatt handelt
             */
        } else {
            /*
             * Ist der Rabatt Prozentual, muss dieser auf alle Positionen angewendet werden.
             *
             */
            $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::POSITION_NR." = %d";
            array_push($param, $positionNr);
        }
        
        /*
         * Update Carsten 28.12.2017
         * Da im Falle eines gefundenen Rabattes die Where-Klausel auch auf der Term_ID gesetzt ist, muss
         * natuerlich auch die vorherige Pruefung ob ein Rabatt exisitert IMMER die Term_ID beruecksichtigen.
         * Auch wenn diese 0 ist.
         * Ansonsten kann ein Rabatt gefunden werden, der anschliessend jedoch nicht aktualisiert wird.
         */
//         if ($rabattTermId > 0) {
        $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_TERM_ID ." = %d";
        array_push($param, $rabattTermId);
//         } else
        if ($buchungrabatt->getBezeichnung() == "Position Extra Charge") {
        	$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::BEZEICHNUNG ." = %s";
        	array_push($param, $buchungrabatt->getBezeichnung());
        }
        
        /*
         * Update Carsten 28.12.2017
         * Seitdem Rabatte auch auf den Preis / Nacht gemacht werden, koennen mehrere Rabatte, ohne TermId auf der
         * Kombination aus buchung_nr, teilbuchung_id und position_id sein.
         * Deshalb muss zudem die Rabatt_art beruecksichtigt werden.
         * Daher ist ein Rabatt nur noch eindeutig bei gleichheit von:
         * buchung_nr, teilbuchung_id, position_id, term_id, rabatt_art
         */
        $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_ART ." = %d";
        array_push($param, $buchungrabatt->getRabatt_art());
        
        $sql            = $wpdb->prepare( $sqlString, $param );
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        if ($results) {
            return true;
        }
        return false;
    }
    
    public function loadRabatte(RS_IB_Model_BuchungRabatt $buchungrabatt, $ignorePositionId = false) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
//         var_dump($buchungrabatt);
        
        $buchungsNr     = $buchungrabatt->getBuchung_nr();
        $teilbuchungnr  = $buchungrabatt->getTeilbuchung_nr();
        $positionNr     = $buchungrabatt->getPosition_nr();
        $rabattTermId   = $buchungrabatt->getRabatt_term_id();
        $optionId   	= $buchungrabatt->getRabatt_option_id();
        
        $teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
        //         $positionTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        $rabatte        = array();
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
        
        $param          = array();
        $sqlString      = "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungRabatt::BUCHUNG_NR ." = %d";
        array_push($param, $buchungsNr);
        
        $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR." = %d";
        array_push($param, $teilbuchungnr);
        
        if (!$ignorePositionId) {
	        $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::POSITION_NR." = %d";
	        array_push($param, $positionNr);
	        
	        if ($positionNr == 0) {
	        	// && $buchungrabatt->getBerechnung_art() != RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_OPTION
	        	$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID ." = %d";
	        	array_push($param, 0);
	        }
        }
        
        if ($rabattTermId > 0) {
            $sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_TERM_ID ." = %d";
            array_push($param, $rabattTermId);
        }
        
        if ($optionId > 0) {
        	$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID ." = %d";
        	array_push($param, $optionId);
        }
        
        $sqlString = $sqlString . " ORDER BY rabatt_id";
        
        $sql            = $wpdb->prepare( $sqlString, $param );
//         echo $sqlString."<br />";
//         var_dump($param);
//         echo "<br />";
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        if ($results) {
            foreach ($results as $result) {
                $buchungRabatt   = new RS_IB_Model_BuchungRabatt();
                $buchungRabatt->exchangeArray($result);
                array_push($rabatte, $buchungRabatt);
            }
            return $rabatte;
        }
        return false;
    }
    
    /* @var $unusedRabatts RS_IB_Model_BuchungRabatt */
    public function resetUnusedOptionDiscounts($buchungsNr, $teilbuchungnr = 0, $usedOptionIds = array()) {
    	$unusedOptions = $this->loadUnusedOptionDiscount($buchungsNr, $teilbuchungnr, $usedOptionIds);
    	foreach ($unusedOptions as $unusedRabatts) {
    		$unusedRabatts->setPosition_nr(0);
    		$this->saveOrUpdateBuchungRabatt($unusedRabatts);
    	}
    }
    
    public function loadUnusedOptionDiscount($buchungsNr, $teilbuchungnr = 0, $usedOptionIds = array()) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    	//         $positionTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
    	$rabatte        = array();
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
    	
    	$param          = array();
    	$sqlString      = "SELECT * FROM $table_name WHERE " . RS_IB_Model_BuchungRabatt::BUCHUNG_NR ." = %d";
    	array_push($param, $buchungsNr);
    	
    	$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR." = %d";
    	array_push($param, $teilbuchungnr);
    	
    	$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::BERECHNUNG_ART." = %d";
    	array_push($param, RS_IB_Model_BuchungRabatt::RABATT_BERECHNUNG_OPTION);
    	
    	if (isset($usedOptionIds) && !is_null($usedOptionIds) && sizeof($usedOptionIds) > 0) {
    		$options	= implode(",", $usedOptionIds);
    		$sqlString  = $sqlString . " AND ".RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID ." NOT IN (".$options.")";
    	}
    	
    	$sql            = $wpdb->prepare( $sqlString, $param );
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	if ($results) {
    		foreach ($results as $result) {
    			$buchungRabatt   = new RS_IB_Model_BuchungRabatt();
    			$buchungRabatt->exchangeArray($result);
    			array_push($rabatte, $buchungRabatt);
    		}
    	}
    	return $rabatte;
    }
    
    public function loadBuchungRabattByOptionId($buchungsNr, $teilbuchungnr = 0, $optionId = 0 ) {
		return $this->loadBuchungRabatt($buchungsNr, $teilbuchungnr, 0, 0, $optionId, true);
    }
    
    public function loadBuchungRabatt($buchungsNr, $teilbuchungnr = 0,
                                        $positionNr = 0, $rabattTermId = 0,
    									$optionId = 0, $ignorePositionId = false) {
        $rabattObj = new RS_IB_Model_BuchungRabatt();
        $rabattObj->setBuchung_nr($buchungsNr);
        $rabattObj->setTeilbuchung_nr($teilbuchungnr);
        $rabattObj->setPosition_nr($positionNr);
        $rabattObj->setRabatt_term_id($rabattTermId);
        $rabattObj->setRabatt_option_id($optionId);
        
        return $this->loadRabatte($rabattObj, $ignorePositionId);
    }
    
    public function deleteBuchungRabatt(RS_IB_Model_BuchungRabatt $buchungrabatt) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."deleteBuchungRabatt");
        
        $table_name             = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
        if ($buchungrabatt->getRabatt_id() > 0) {
            $result = $wpdb->delete(
                $table_name,
                array (
                    RS_IB_Model_BuchungRabatt::BUCHUNG_NR       => $buchungrabatt->getBuchung_nr(),
                    RS_IB_Model_BuchungRabatt::RABATT_TERM_ID   => $buchungrabatt->getRabatt_term_id(),
                    RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR   => $buchungrabatt->getTeilbuchung_nr(),
                    RS_IB_Model_BuchungRabatt::POSITION_NR      => $buchungrabatt->getPosition_nr()
                )
            );
        }
    }
    
    public function deleteBuchungRabattByTermId(RS_IB_Model_BuchungRabatt $buchungrabatt) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
        $buchungNr 		= $buchungrabatt->getBuchung_nr();
        $rabattTermId 	= $buchungrabatt->getRabatt_term_id();
        $bezeichnung 	= $buchungrabatt->getBezeichnung();
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."deleteBuchungRabattByTermId - Buchung: $buchungNr - Term: $rabattTermId - $bezeichnung");
    
        $table_name             = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
        if ($buchungrabatt->getRabatt_term_id() > 0) {
            $result = $wpdb->delete(
                $table_name,
                array (
                    RS_IB_Model_BuchungRabatt::BUCHUNG_NR       => $buchungrabatt->getBuchung_nr(),
                    RS_IB_Model_BuchungRabatt::RABATT_TERM_ID   => $buchungrabatt->getRabatt_term_id(),
//                     RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR   => $buchungrabatt->getTeilbuchung_nr(),
//                     RS_IB_Model_BuchungRabatt::POSITION_NR      => $buchungrabatt->getPosition_nr()
                )
            );
        }
    }
    
    public function resetDegressionRabatt($buchungNr) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	
    	RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."resetDegressionRabatt");
    	
    	$table_name             = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
    	if ($buchungNr > 0) {
    		$result = $wpdb->delete(
    			$table_name,
    			array (
    				RS_IB_Model_BuchungRabatt::BUCHUNG_NR   => $buchungNr,
    				RS_IB_Model_BuchungRabatt::RABATT_ART	=> RS_IB_Model_BuchungRabatt::RABATT_ART_DEGRESSION
    			)
    		);
    	}
    }
    
    public function resetAufschlagsRabatt($buchungNr) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	
    	RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."resetAufschlagsRabatt");
    	
    	$table_name             = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
    	if ($buchungNr > 0) {
    		$result = $wpdb->delete(
    			$table_name,
    			array (
    				RS_IB_Model_BuchungRabatt::BUCHUNG_NR   => $buchungNr,
    				RS_IB_Model_BuchungRabatt::RABATT_ART	=> RS_IB_Model_BuchungRabatt::RABATT_ART_AUFSCHLAG,
//     				RS_IB_Model_BuchungRabatt::BEZEICHNUNG	=> "Position Extra Charge",
    			)
    		);
    	}
    }
    
    public function resetAktionsRabatt($buchungNr) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	
    	RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."resetAktionsRabatt");
    	
    	$table_name             = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
    	if ($buchungNr > 0) {
    		$result = $wpdb->delete(
    			$table_name,
    			array (
    				RS_IB_Model_BuchungRabatt::BUCHUNG_NR   => $buchungNr,
    				RS_IB_Model_BuchungRabatt::RABATT_ART	=> RS_IB_Model_BuchungRabatt::RABATT_ART_AKTION,
//     				RS_IB_Model_BuchungRabatt::BEZEICHNUNG	=> "Position Extra Charge",
    			)
    		);
    	}
    }
    
    /* @var RS_IB_Model_Buchungskopf $buchungskopf */
    public function saveOrUpdateBuchungRabatt( RS_IB_Model_BuchungRabatt $buchungrabatt, $forceInsert = false) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
        $dtFrom         = RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getGueltig_von(), RS_IB_Data_Validation::DATATYPE_DATUM);
        $dtTo           = RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getGueltig_bis(), RS_IB_Data_Validation::DATATYPE_DATUM);
        $dtFrom         = rs_ib_date_util::convertDateValueToDateTime($buchungrabatt->getGueltig_von());
        $dtTo           = rs_ib_date_util::convertDateValueToDateTime($buchungrabatt->getGueltig_bis());
        $dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
        $dtTo           = date('Y-m-d', $dtTo->getTimestamp());
        $rabattExist	= false;
        if ($buchungrabatt->getRabatt_id() == 0) {
        	if (!$forceInsert) {
            	$rabattExist	= $this->checkExistingRabatt($buchungrabatt);
        	}
            if (!$rabattExist) {
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        RS_IB_Model_BuchungRabatt::USER_ID
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::BUCHUNG_NR
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::RABATT_TERM_ID
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_term_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getTeilbuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::POSITION_NR
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPosition_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::BEZEICHNUNG
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBezeichnung(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_BuchungRabatt::BERECHNUNG_ART
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBerechnung_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::GUELTIG_VON      => $dtFrom,
                        RS_IB_Model_BuchungRabatt::GUELTIG_BIS      => $dtTo,
                        RS_IB_Model_BuchungRabatt::RABATT_ART
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::RABATT_TYP
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_typ(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::RABATT_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_wert(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_BuchungRabatt::VALID_AT_STORNO
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getValid_at_storno(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::PLUS_MINUS_KZ
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPlus_minus_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_BuchungRabatt::RABATT_AUSSCHREIBEN_KZ
                            => RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_ausschreiben_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    	RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID
                    		=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_option_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    ),
                    array(
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%s', //RABATT_WERT - hier verwende ich %s um abwaertskompatibel zu bleiben. der Wert wird dennoch korrekt in der DB gespeichert.
                        '%d',
                        '%d',
                        '%d',
                    	'%d',
                    )
                );
                if ($result == false) {
                    //RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."fehler beim insert");
                	RS_Indiebooking_Log_Controller::write_log("fehler beim rabatt insert", __LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR);
                } else {
                    //RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."insert rabatt: ".$result);
                }
//             $buchungsNr = $wpdb->insert_id;
            } else {
//             	$dtFrom         = RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getGueltig_von(), RS_IB_Data_Validation::DATATYPE_DATUM);
//             	$dtTo           = RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getGueltig_bis(), RS_IB_Data_Validation::DATATYPE_DATUM);
//             	$dtFrom         = rs_ib_date_util::convertDateValueToDateTime($buchungrabatt->getGueltig_von());
//             	$dtTo           = rs_ib_date_util::convertDateValueToDateTime($buchungrabatt->getGueltig_bis());
//             	$dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
//             	$dtTo           = date('Y-m-d', $dtTo->getTimestamp());
            	
            	$result = $wpdb->update(
            			$table_name,
            			array(
            				RS_IB_Model_BuchungRabatt::USER_ID
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::BEZEICHNUNG
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBezeichnung(), RS_IB_Data_Validation::DATATYPE_TEXT),
            				RS_IB_Model_BuchungRabatt::BERECHNUNG_ART
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBerechnung_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::GUELTIG_VON      => $dtFrom,
            				RS_IB_Model_BuchungRabatt::GUELTIG_BIS      => $dtTo,
            				RS_IB_Model_BuchungRabatt::RABATT_ART
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_TYP
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_typ(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_WERT
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_wert(), RS_IB_Data_Validation::DATATYPE_NUMBER),
            				RS_IB_Model_BuchungRabatt::VALID_AT_STORNO
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getValid_at_storno(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::PLUS_MINUS_KZ
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPlus_minus_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_AUSSCHREIBEN_KZ
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_ausschreiben_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_option_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            			),
            			array(
            				RS_IB_Model_BuchungRabatt::BUCHUNG_NR
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_TERM_ID
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_term_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::TEILBUCHUNG_NR
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getTeilbuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::POSITION_NR
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPosition_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            				RS_IB_Model_BuchungRabatt::RABATT_ART
            					=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            			),
            			array (
	            			'%d',
	            			'%s',
	            			'%d',
	            			'%s',
	            			'%s',
	            			'%d',
	            			'%d',
            				'%d',
            				'%d',
            				'%d',
            				'%d',
            				'%d',
            			),
            			array(
            				'%d',
            				'%d',
            				'%d',
            				'%d',
            				'%d',
            			)
            	);
            }
        } else {
        	$result = $wpdb->update(
        		$table_name,
        		array(
        			RS_IB_Model_BuchungRabatt::POSITION_NR
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPosition_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::USER_ID
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::BEZEICHNUNG
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBezeichnung(), RS_IB_Data_Validation::DATATYPE_TEXT),
        			RS_IB_Model_BuchungRabatt::BERECHNUNG_ART
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getBerechnung_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::GUELTIG_VON      => $dtFrom,
        			RS_IB_Model_BuchungRabatt::GUELTIG_BIS      => $dtTo,
        			RS_IB_Model_BuchungRabatt::RABATT_ART
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_art(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::RABATT_TYP
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_typ(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::RABATT_WERT
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_wert(), RS_IB_Data_Validation::DATATYPE_NUMBER),
        			RS_IB_Model_BuchungRabatt::VALID_AT_STORNO
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getValid_at_storno(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::PLUS_MINUS_KZ
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getPlus_minus_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::RABATT_AUSSCHREIBEN_KZ
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_ausschreiben_kz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        			RS_IB_Model_BuchungRabatt::RABATT_OPTION_ID
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_option_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        		),
        		array(
        			RS_IB_Model_BuchungRabatt::RABATT_ID
        				=> RS_IB_Data_Validation::check_with_whitelist($buchungrabatt->getRabatt_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
        		),
        		array (
        			'%d',
        			'%d',
        			'%s',
        			'%d',
        			'%s',
        			'%s',
        			'%d',
        			'%d',
        			'%d',
        			'%d',
        			'%d',
        			'%d',
        			'%d',
        		),
        		array(
        			'%d',
        		)
        	);
        }
    }
}
// endif;
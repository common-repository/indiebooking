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

// if ( ! class_exists( 'RS_IB_Table_Buchungposition' ) ) :
class RS_IB_Table_Buchungposition
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function getNextPositionNumber($buchung_nr, $teilbuchung_id) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
        $nextId         = 1;
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
    
        $field          = RS_IB_Model_Buchungposition::POSITION_ID;
        $bField         = RS_IB_Model_Buchungposition::BUCHUNG_NR;
        $tField         = RS_IB_Model_Buchungposition::TEILBUCHUNG_ID;
    
        $sql            = "SELECT MAX($field) FROM $table_name WHERE $bField = %d AND $tField = %d ";
        $sql            = $wpdb->prepare($sql, $buchung_nr, $teilbuchung_id);
    
        $results        = $wpdb->get_var( $sql );
        if (!is_null($results) && $results > 0) {
            $nextId     = $results + 1;
            //             echo "neue ID: " . $nextId;
        }
        if (is_null($nextId) || $nextId == 0) {
            $nextId      = 1;
        }
        return $nextId;
    }
    
    public function saveOrUpdateBuchungsposition(RS_IB_Model_Buchungposition $position) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
//         var_dump($position);
        try {
            $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
            $dtFrom         = rs_ib_date_util::convertDateValueToDateTime($position->getPreis_von());
            $dtTo           = rs_ib_date_util::convertDateValueToDateTime($position->getPreis_bis());
            $dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
            $dtTo           = date('Y-m-d', $dtTo->getTimestamp());
            if ($position->getPosition_id() == 0) {
                $positionNr = $this->getNextPositionNumber($position->getBuchung_nr(), $position->getTeilbuchung_id());
                $result     = $wpdb->insert(
                    $table_name,
                    array(
                        RS_IB_Model_Buchungposition::BUCHUNG_NR
                            => RS_IB_Data_Validation::check_with_whitelist($position->getBuchung_nr(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::TEILBUCHUNG_ID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getTeilbuchung_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::POSITION_ID
                            => RS_IB_Data_Validation::check_with_whitelist($positionNr, RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::USER_ID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::POSITION_TYP
                            => RS_IB_Data_Validation::check_with_whitelist($position->getPosition_typ(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::BEZEICHNUNG
                            => RS_IB_Data_Validation::check_with_whitelist($position->getBezeichnung(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::PREIS_VON       => $dtFrom,
                        RS_IB_Model_Buchungposition::PREIS_BIS       => $dtTo,
                        RS_IB_Model_Buchungposition::ANZAHL_NAECHTE
                            => RS_IB_Data_Validation::check_with_whitelist($position->getAnzahl_naechte(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::EINZELPREIS
                            => RS_IB_Data_Validation::check_with_whitelist($position->getEinzelpreis(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::BERECHNUNG_TYPE
                            => RS_IB_Data_Validation::check_with_whitelist($position->getBerechnung_type(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::MWST_PROZENT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getMwst_prozent(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::RABATT_KZ
                            => RS_IB_Data_Validation::check_with_whitelist($position->getRabatt_kz(), RS_IB_Data_Validation::DATATYPE_ALL),
                        RS_IB_Model_Buchungposition::POSITION_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getCalculatedPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::BERECHNETER_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getCalcPosPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::MWST_TERMID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getMwstTermId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::DATA_ID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getData_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::FULL_STORNO
                            => RS_IB_Data_Validation::check_with_whitelist($position->getFullStorno(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::KOMMENTAR
                            => RS_IB_Data_Validation::check_with_whitelist($position->getKommentar(), RS_IB_Data_Validation::DATATYPE_ALL),
                    ),
                    array (
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                    )
                );
                if ($result == false) {
                    echo "Insert result false";
                    var_dump( $wpdb->last_query );
                }
            } else {
    //             $wpdb->update($table, $data, $where)
                $positionNr = $position->getPosition_id();
                $wpdb->update(
                    $table_name,
                    array(
                        RS_IB_Model_Buchungposition::USER_ID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::POSITION_TYP
                            => RS_IB_Data_Validation::check_with_whitelist($position->getPosition_typ(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::BEZEICHNUNG
                            => RS_IB_Data_Validation::check_with_whitelist($position->getBezeichnung(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::PREIS_VON       => $dtFrom,
                        RS_IB_Model_Buchungposition::PREIS_BIS       => $dtTo,
                        RS_IB_Model_Buchungposition::ANZAHL_NAECHTE
                            => RS_IB_Data_Validation::check_with_whitelist($position->getAnzahl_naechte(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::EINZELPREIS
                            => RS_IB_Data_Validation::check_with_whitelist($position->getEinzelpreis(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::BERECHNUNG_TYPE
                            => RS_IB_Data_Validation::check_with_whitelist($position->getBerechnung_type(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungposition::MWST_PROZENT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getMwst_prozent(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::RABATT_KZ
                            => RS_IB_Data_Validation::check_with_whitelist($position->getRabatt_kz(), RS_IB_Data_Validation::DATATYPE_ALL),
                        RS_IB_Model_Buchungposition::POSITION_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getCalculatedPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::BERECHNETER_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($position->getCalcPosPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::MWST_TERMID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getMwstTermId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::DATA_ID
                            => RS_IB_Data_Validation::check_with_whitelist($position->getData_id(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungposition::FULL_STORNO
                            => RS_IB_Data_Validation::check_with_whitelist($position->getFullStorno(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungposition::KOMMENTAR
                            => RS_IB_Data_Validation::check_with_whitelist($position->getKommentar(), RS_IB_Data_Validation::DATATYPE_ALL),
                    ),
                    array(
                        RS_IB_Model_Buchungposition::BUCHUNG_NR      => $position->getBuchung_nr(),
                        RS_IB_Model_Buchungposition::TEILBUCHUNG_ID  => $position->getTeilbuchung_id(),
                        RS_IB_Model_Buchungposition::POSITION_ID     => $position->getPosition_id(),
                    ),
                    array (
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                    ),
                    array (
                        '%d',
                        '%d',
                        '%d',
                    )
                );
            }
            return $positionNr;
        } catch (Exception $e) {
            RS_Indiebooking_Log_Controller::write_log(
                $e->getMessage(),
                __LINE__,
                __CLASS__,
                RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_ERROR
            );
        }
    }
    
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    public function loadBookingPartPositions($buchungsNr, $teilbuchungHeadId, $calcNew = true, $apartmentQM = 0, $anzahlPersonen = 0) {
        global $wpdb;
        global $RSBP_DATABASE;
        global $RSBP_TABLEPREFIX;
    
        
        $positionen     = array();
        $rabattTable    = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
        $sql            = $wpdb->prepare( "SELECT * FROM $table_name "
            . "WHERE " . RS_IB_Model_Buchungposition::BUCHUNG_NR . " = %d "
            . "AND " . RS_IB_Model_Buchungposition::TEILBUCHUNG_ID . " = %d",
            array(
                $buchungsNr,
                $teilbuchungHeadId
            )
        );
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        if ($teilbuchungHeadId == 2) {
            if (sizeof($results) == 0) {
                RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."loadBookingPartPositions - found: ".sizeof($results)." ".$sql);
            }
        }
        foreach ($results as $result) {
            $position = new RS_IB_Model_Buchungposition();
            $position->exchangeArray($result);
            $position->setQuadratmeter($apartmentQM);
            $position->setAnzahlPersonen($anzahlPersonen);
//             var_dump($position);
            $rabatte                = $rabattTable->loadBuchungRabatt($buchungsNr,
                                            $teilbuchungHeadId, $position->getPosition_id());
            $position->setRabatte($rabatte);
//             if ($calcNew) {
//             RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."]"."calculatePositionPrice");
            $position->calculatePrice();
//             }
            array_push($positionen, $position);
        }
        return $positionen;
    }
    
    public function deleteBuchungspositionen(RS_IB_Model_Teilbuchungskopf $teilbuchungsKopf) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
        //         $wpdb->delete(
        //             $table_name,
        //             array(
        // //                 RS_IB_Model_Buchungposition::TEILBUCHUNG_ID => $teilbuchungsKopf->getTeilbuchung_id()
        //                 "teilbuchung_id" => $teilbuchungsKopf->getTeilbuchung_id()
        //             )
        //         );
        //         echo "<br />DELETE: " . $teilbuchungsKopf->getTeilbuchung_id();
        $teilbuchungIdField     = RS_IB_Model_Buchungposition::TEILBUCHUNG_ID;
        $buchungIdField         = RS_IB_Model_Buchungposition::BUCHUNG_NR;
        $sql    = "DELETE FROM $table_name WHERE $teilbuchungIdField = %d AND $buchungIdField = %d";
//         write_log($sql);
//         write_log($teilbuchungsKopf->getTeilbuchung_id());
        $sql    = $wpdb->prepare($sql, $teilbuchungsKopf->getTeilbuchung_id(), $teilbuchungsKopf->getBuchung_nr());
        //         echo "<br />SQL1: ".$sql;
        $numberOfRows = $wpdb->query($sql);
        if (false !== $numberOfRows) {
            //             echo "<br />Number of Rows" . $numberOfRows;
        } else {
            echo "Irgend ein fehler";
        }
        return $numberOfRows;
    }
    
    public function loadBuchungApartmentOptions($buchungKopfId, $apartmentId) {
        $positionType = "appartment_option";
        
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
        $teilBuchungTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        
        $sql            = $wpdb->prepare( "SELECT data_id FROM $table_name as bp "
            . "INNER JOIN " . $teilBuchungTbl . " as tbk "
            . "ON tbk." . RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR . " = bp." . RS_IB_Model_Buchungposition::BUCHUNG_NR." "
            . "AND tbk." . RS_IB_Model_Teilbuchungskopf::APPARTMENT_ID . " = %d "
            . "WHERE bp." . RS_IB_Model_Buchungposition::BUCHUNG_NR . " = %d "
            . "AND bp." . RS_IB_Model_Buchungposition::POSITION_TYP . " = %s",
            array(
                $apartmentId,
                $buchungKopfId,
                $positionType
            )
        );
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        $optionIds      = array();

        foreach ($results as $result) {
            array_push($optionIds, $result['data_id']);
        }
        return $optionIds;
    }
}
// endif;
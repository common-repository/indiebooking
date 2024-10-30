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

// if ( ! class_exists( 'RS_IB_Table_Teilbuchungskopf' ) ) :
class RS_IB_Table_Teilbuchungskopf
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
     
    private function getNextTeilbuchungNumber($buchung_nr) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;

        $nextId         = 1;
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    
        $field          = RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_ID;
        $bField         = RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR;
    
        $sql            = "SELECT MAX($field) FROM $table_name WHERE $bField = %d";
        $sql            = $wpdb->prepare($sql, $buchung_nr);
    
        $results        = $wpdb->get_var( $sql );
        if (!is_null($results) && $results > 0) {
            $nextId     = $results + 1;
            //             echo "neue ID: " . $nextId;
        }
        return $nextId;
    }
    
    public function saveOrUpdateTeilbuchungskopf(RS_IB_Model_Teilbuchungskopf $teilHeader) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
    
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        $dtFrom         = rs_ib_date_util::convertDateValueToDateTime($teilHeader->getTeilbuchung_von());//date_create_from_format('d.m.Y', $teilHeader->getTeilbuchung_von());
        $dtTo           = rs_ib_date_util::convertDateValueToDateTime($teilHeader->getTeilbuchung_bis());//date_create_from_format('d.m.Y', $teilHeader->getTeilbuchung_bis());
        $dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
        $dtTo           = date('Y-m-d', $dtTo->getTimestamp());
        if ($teilHeader->getTeilbuchung_id() == 0) {
            //neuer Teilbuchungskopf
            $teilbuchungsNr = $this->getNextTeilbuchungNumber($teilHeader->getBuchung_nr());
            $result = $wpdb->insert(
                $table_name,
                array(
                    RS_IB_Model_Teilbuchungskopf::USER_ID         => $teilHeader->getUserId(),
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_ID  => $teilbuchungsNr,
                    RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR      => $teilHeader->getBuchung_nr(),
                    RS_IB_Model_Teilbuchungskopf::APPARTMENT_ID   => $teilHeader->getAppartment_id(),
                    RS_IB_Model_Teilbuchungskopf::APPARTMENT_NAME   => $teilHeader->getAppartment_name(),
                    RS_IB_Model_Teilbuchungskopf::APPARTMENT_QM   => $teilHeader->getAppartment_qm(),
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_VON => $dtFrom,
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_BIS => $dtTo,
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_WERT => $teilHeader->getCalculatedPrice(),
                    RS_IB_Model_Teilbuchungskopf::ANZAHL_PERSONEN => $teilHeader->getAnzahlPersonen(),
                	RS_IB_Model_Teilbuchungskopf::BOOKINGCOM_ROOMID => $teilHeader->getBcomroomid(),
                	RS_IB_Model_Teilbuchungskopf::GAST_NAME => $teilHeader->getGastName(),
                ),
                array(
                    '%d', //User ID
                    '%d', //ID
                    '%d', //BuchungNr
                    '%d', //AppartmentId
                    '%s', //AppartmentName
                    '%d', //QM
                    '%s', //TeilbuchungVon
                    '%s', //TeilbuchungBis
                    '%s', //TeilbuchungWert
                    '%d', //AnzahlPersonen
                	'%d', //Booking Com RoomId
                	'%s', //Gastname
                )
            );
            //             $teilbuchungsNr = $wpdb->insert_id;
        } else {
            //             echo "Update: " . $dtFrom . "TID: ".$teilHeader->getTeilbuchung_id() . "BNR: " . $teilHeader->getBuchung_nr();
            $result = $wpdb->update(
                $table_name,
                array(
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_VON   => $dtFrom,
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_BIS   => $dtTo,
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_WERT  => $teilHeader->getCalculatedPrice(),
                    RS_IB_Model_Teilbuchungskopf::ANZAHL_PERSONEN   => $teilHeader->getAnzahlPersonen(),
                    RS_IB_Model_Teilbuchungskopf::BOOKINGCOM_ROOMID => $teilHeader->getBcomroomid(),
                    RS_IB_Model_Teilbuchungskopf::GAST_NAME 		=> $teilHeader->getGastName(),
                ),
                array(
                    RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR        => $teilHeader->getBuchung_nr(),
                    RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_ID    => $teilHeader->getTeilbuchung_id(),
                ),
                array('%s', '%s', '%s', '%d', '%d', '%s'),
                array( '%d', '%d' )
            );
            $teilbuchungsNr = $teilHeader->getTeilbuchung_id();
        }
        return $teilbuchungsNr;
    }
    
    public function getTeilbuchungskopf($buchungsNr, $appartmentId) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
//         global $RSBP_DATABASE;
        
        $teilKopf       = null;
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR ." = %d"
                                            . " AND " . RS_IB_Model_Teilbuchungskopf::APPARTMENT_ID . " = %d",
                                            array(
                                                $buchungsNr,
                                                $appartmentId
                                            )
        );
        $result        = $wpdb->get_results( $sql , ARRAY_A );
        if (sizeof($result) > 0) {
            $teilKopf   = new RS_IB_Model_Teilbuchungskopf();
            $teilKopf->exchangeArray($result[0]);
        }
        return $teilKopf;
    }
    
    public function deleteTeilbuchungskopf($buchungsNr, $teilbuchungId) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	//         global $RSBP_DATABASE;
    
//     	$teilKopf       = null;
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    	$sql            = $wpdb->prepare( "DELETE FROM $table_name WHERE " . RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR ." = %d"
    			. " AND " . RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_ID . " = %d",
    			array(
    					$buchungsNr,
    					$teilbuchungId
    			)
    			);
    	$result = $wpdb->query($sql);
//     	$result        = $wpdb->get_results( $sql , ARRAY_A );
//     	if (sizeof($result) > 0) {
//     		$teilKopf   = new RS_IB_Model_Teilbuchungskopf();
//     		$teilKopf->exchangeArray($result[0]);
//     	}
    	return $result;
    }
    
    public function loadMinMaxBookingRange($buchungsNr) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
    	/*
    	 * SELECT MIN(teilbuchung_von), MAX(teilbuchung_bis)
    	 * FROM `rs_ib_rs_indiebooking_teilbuchungskopf`
    	 * WHERE buchung_nr = 347 GROUP BY buchung_nr
    	 */
    	$dates = array();
    	$sql   = $wpdb->prepare( "SELECT MIN(".RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_VON.") as von,".
      								" MAX(".RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_BIS.") as bis".
    								" FROM $table_name WHERE " .
    								RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR ." = %d".
      								" GROUP BY " .RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR,
    			array(
    					$buchungsNr,
    			)
			);
    	$result = $wpdb->get_results( $sql , ARRAY_A );
    	if (sizeof($result) > 0) {
    		$dates['von'] = $result[0]['von'];
    		$dates['bis'] = $result[0]['bis'];
    	}
    	return $dates;
    }
    
    
    public function loadLastApartmentBooking($apaId, $lowerThan) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	/*
    	 * Blockierte Status werden nun ebenfalls berücksichtigt, damit hier keine Überschneidung möglich ist.
    	 * 	." NOT IN ('trash', 'rs_ib-canceled', 'rs_ib-storno', 'rs_ib-storno_paid', 'rs_ib-blocked', 'rs_ib-almost_booked',"
      				." 'rs_ib-booking_info', 'rs_ib-out_of_time')
    	 */
    	$tbVon          = rs_ib_date_util::convertDateValueToDateTime($lowerThan);
    	$lowerThan		= $tbVon->format('Y-m-d');
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf tb';
    	$table_name2    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf bk';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name
    						INNER JOIN $table_name2
							ON tb.".RS_IB_Model_Buchungskopf::BUCHUNG_NR." = bk.".RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR."
							WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_STATUS
								." NOT IN ('trash', 'rs_ib-canceled', 'rs_ib-storno', 'rs_ib-storno_paid', 'rs_ib-out_of_time')
							AND tb." . RS_IB_Model_Teilbuchungskopf::APPARTMENT_ID . " = %s
							AND tb." . RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_BIS . " <= %s
							ORDER BY tb.".RS_IB_Model_Teilbuchungskopf::TEILBUCHUNG_BIS." DESC LIMIT 1",
							array(
								$apaId,
								$lowerThan
							)
						);
    	
    	return $this->execute_sql_for_booking_header($sql);
    }
    
    
    private function execute_sql_for_booking_header($sql, $loadAll = false) {
    	global $wpdb;
    	
    	$results            = $wpdb->get_results( $sql , ARRAY_A );
    	
    	$buchungskoepfe     = array();
    	foreach ($results as $result) {
    		$teilbuchungsKopf       = new RS_IB_Model_Teilbuchungskopf();
    		$teilbuchungsKopf->exchangeArray($result);
//     		if ($loadAll) {
//     			$buchungsKopf   = $this->loadBooking($buchungsKopf->getBuchung_nr(), $loadAll);
//     		}
    		$buchungskoepfe[]   = $teilbuchungsKopf;
    	}
    	return $buchungskoepfe;
    }
    
    
    /* @var $rabattTable    RS_IB_Table_BuchungRabatt */
    /* @var $positionTbl RS_IB_Table_Buchungposition */
    public function loadBookingPartHeader($buchungsNr, $appartmentId = null, $loadPositions = true, $calcNew = true) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        $positionTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
        
        $teilbuchungsheader = array();
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
        if (is_null($appartmentId)) {
            $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR ." = %d",
                array(
                    $buchungsNr
                )
            );
        } else {
            $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Teilbuchungskopf::BUCHUNG_NR ." = %d"
                . " AND " . RS_IB_Model_Teilbuchungskopf::APPARTMENT_ID . " = %d",
                array(
                    $buchungsNr,
                    $appartmentId
                )
            );
        }
        //         echo $sql;
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        foreach ($results as $result) {
            $teilbuchungskopf   = new RS_IB_Model_Teilbuchungskopf();
            $teilbuchungskopf->exchangeArray($result);
//             var_dump($teilbuchungskopf);
            
            $rabattTable            = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
            $rabatte                = $rabattTable->loadBuchungRabatt($buchungsNr, $teilbuchungskopf->getTeilbuchung_id());
            $teilbuchungskopf->setRabatte($rabatte);
            if ($loadPositions) {
//                 RS_Indiebooking_Log_Controller::write_log(
//                     "loadPositions ".$buchungsNr." teilbuchungid: ".$teilbuchungskopf->getTeilbuchung_id(),
//                     __LINE__,
//                     __CLASS__
//                 );
                $teilbuchungskopf->setPositionen($positionTbl->loadBookingPartPositions($buchungsNr, $teilbuchungskopf->getTeilbuchung_id(), $calcNew, $teilbuchungskopf->getAppartment_qm(), $teilbuchungskopf->getAnzahlPersonen()));
            }
//             if ($calcNew) {
            $teilbuchungskopf->calculatePrice();
//             }
            array_push($teilbuchungsheader, $teilbuchungskopf);
        }
    
        return $teilbuchungsheader;
    }
}
// endif;
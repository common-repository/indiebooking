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

// if ( ! class_exists( 'RS_IB_Table_Buchungskopf' ) ) :
class RS_IB_Table_Buchungskopf
{
	
	/* @var $currentBooking RS_IB_Model_Buchungskopf */
	private $currentBuchungskopf 	= null;
    private static $_instance 		= null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function getTableName() {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        
        return $table_name;
    }
    
    private function execute_sql_for_booking_header($sql, $loadAll = false) {
        global $wpdb;
    
        $results            = $wpdb->get_results( $sql , ARRAY_A );
    
        $buchungskoepfe     = array();
        foreach ($results as $result) {
            $buchungsKopf       = new RS_IB_Model_Buchungskopf();
            $buchungsKopf->exchangeArray($result);
            if ($loadAll) {
                $buchungsKopf   = $this->loadBooking($buchungsKopf->getBuchung_nr(), $loadAll);
            }
            $buchungskoepfe[]   = $buchungsKopf;
        }
        return $buchungskoepfe;
    }
    
    public function getNumberOfBookings() {
        global $wpdb;
        $table_name             = $this->getTableName();
        $sql                    = "SELECT COUNT(*) FROM $table_name";
        $results                = $wpdb->get_var( $sql );
        if (!is_null($results) && $results > 0) {
            return $results;
        }
        return 0;
    }
    
    public function getNextRechnungsbuchungsnrNumber() {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        $invoiceNrStructure     = get_option( 'rs_indiebooking_settings_invoice_number_structure' );
        $invoiceNrStartsBy      = get_option( 'rs_indiebooking_settings_invoice_number_startsby' );
        switch($invoiceNrStructure) {
            case "1":
                //bei 1 starten und hoch zaehlen
                $nextId         = 1;
                break;
            case "2":
                //bei $invoiceNrStartsBy starten und hoch zaehlen
                $nextId         = intval($invoiceNrStartsBy);
                break;
            case "3":
                //bei jahreszahl + 1 starten
                $now            = new DateTime();
                $year           = $now->format("Y");
                $nextId         = ($year*10)+1;
                break;
            default:
                $nextId         = 1;
                break;
        }
//         $nextId                 = 1;
        $table_name             = $this->getTableName(); //$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    
        $field                  = RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR;
    
        $sql                    = "SELECT MAX($field) FROM $table_name";
        $results                = $wpdb->get_var( $sql );
        if (!is_null($results) && $results > 0) {
            if ($invoiceNrStructure !== "3") {
//                 if ($invoiceNrStructure == "2" && ($results < intval($invoiceNrStartsBy))) {
//                     $nextId     = intval($invoiceNrStartsBy);
//                 } else {
                $nextId     = $results + 1;
//                 }
            } else {
                $test3          = substr(strval($results), 0, 4); //jahr der letzten rechnungsnr
                if (intval($test3) == $year) {
                    $nextId     = $results + 1;
                } else {
                    $nextId     = ($year*10)+1; //wie oben beschrieben
                }
            }
        }
        return $nextId;
    }
    
    
//     public function loadBookingByRechnungnr($rechnungNr) {
//         global $wpdb;
//         global $RSBP_TABLEPREFIX;
        
//         $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
//         $sql            = $wpdb->prepare( "SELECT ".RS_IB_Model_Buchungskopf::BUCHUNG_NR
//                             ." FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR ." = %d",
//             array(
//                 $rechnungNr
//             )
//         );
//         $buchungen      = array();
//         $results        = $wpdb->get_results( $sql , ARRAY_N );
//         if (is_array($results) && sizeof($results) > 0) {
//             foreach ($results as $result) {
//                 array_push($buchungen, $this->loadBooking($result[0]));
//             }
//         }
//         return $buchungen;
//     }
    
	/*
	 * lädt alle Buchungen, die sich derzeit in der Buchung befinden.
	 */
    public function loadOpenBookings() {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$status 	= array("rs_ib-blocked", "rs_ib-booking_info", "rs_ib-almost_booked");
    	$in_str_arr = array_fill( 0, count( $status ), '%s' );
    	$in_str 	= join( ',', $in_str_arr );
    	
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE buchung_status in (".$in_str.")",
    		$status
    	);
    	
    	return $this->execute_sql_for_booking_header($sql, false);
    }
    
    public function loadOutstandingPayments() {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
        $status 	= array("rs_ib-booked", "rs_ib-bookingcom");
        $in_str_arr = array_fill( 0, count( $status ), '%s' );
        $in_str 	= join( ',', $in_str_arr );

        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE buchung_status in (".$in_str.")",
			$status
        );
        
        return $this->execute_sql_for_booking_header($sql, true);
    }
    
    public function loadBookingsByMonth($startMonth, $year) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        
        
    }
    
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    /* @var $mwstAllObj RS_IB_Model_Mwst */
    public function loadBookingsByBillMonth($startMonth, $year) {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	
    	$calcStartMonth = $startMonth;
    	$endMonth		= $startMonth+1;
    	$endYear		= $year;
    	if (intval($calcStartMonth) < 10) {
    		$calcStartMonth = "0".$calcStartMonth;
    	}
    	if (intval($endMonth) > 12) {
    		$endYear	= $endYear + 1;
    		$endMonth	= "01";
    	} else if (intval($endMonth) < 10) {
    		$endMonth = "0".$endMonth;
    	}
    	
    	$startDatum		= $year."-".$calcStartMonth."-01";
    	$endDatum		= $endYear."-".$endMonth."-01";
    	//, 'rs_ib-storno', 'rs_ib-storno_paid'
    	$table_name     = $this->getTableName();
    	$sql            = "SELECT * FROM $table_name"
						  ." WHERE (" . RS_IB_Model_Buchungskopf::BUCHUNG_STATUS ." NOT IN ('trash', 'rs_ib-canceled', 'rs_ib-out_of_time')"
    					  ." OR (". RS_IB_Model_Buchungskopf::BUCHUNG_STATUS ." = 'rs_ib-canceled'"
    					  ." AND ".RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR." <> ''"
    					  ." AND ".RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR." <> 0))"
						  ." AND ".RS_IB_Model_Buchungskopf::RECHNUNGS_DATUM." >= '".$startDatum."'"
						  ." AND ".RS_IB_Model_Buchungskopf::RECHNUNGS_DATUM." < '".$endDatum."'"
						  ." ORDER BY ".RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR." ASC, ".RS_IB_Model_Buchungskopf::RECHNUNGS_DATUM." ASC"
						;
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
       	return $results;
    }
    
    public function loadLastBookings($numberOfBookings = 10) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        global $RSBP_DATABASE;
        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $sql            = $wpdb->prepare( "SELECT * FROM $table_name
							WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_STATUS
							." NOT IN ('trash', 'rs_ib-canceled', 'rs_ib-storno', 'rs_ib-storno_paid', 'rs_ib-blocked', 'rs_ib-almost_booked',"
    					  	." 'rs_ib-booking_info')
							ORDER BY buchungdatum DESC, buchung_nr DESC LIMIT %d",
            array(
                $numberOfBookings
            )
        );
        
        return $this->execute_sql_for_booking_header($sql);
    }
    
    private function convertResultToBuchungskopf($results, $loadAll = true) {
    	global $RSBP_DATABASE;
    	
    	
    	$buchungsKopf  	 			= new RS_IB_Model_Buchungskopf();
    	$calcNew        			= true;
    	$recalculateCoupons 		= true;
    	if (is_array($results) && sizeof($results) > 0) {
	    	$teilbuchungTbl 		= $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
	    	$rabattTable    		= $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
	    	$zahlungTable   		= $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
	    	
    		$buchungsKopf->exchangeArray($results[0]);
    		$buchungsNr				= $buchungsKopf->getBuchung_nr();
    		if ($buchungsKopf->getBuchung_status() == "rs_ib-canceled"
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-canc_request "
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-pay_confirmed"
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-out_of_time"
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-storno"
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-storno_paid"
    			|| $buchungsKopf->getBuchung_status() == "rs_ib-booked") {
    				//rs_ib-requested?
    				$calcNew        	= false;
    				$recalculateCoupons = false;
    			}
    			if ($loadAll) {
    				$rabatte        = $rabattTable->loadBuchungRabatt($buchungsNr);
    				//                 var_dump($rabatte);
    				$buchungsKopf->setRabatte($rabatte);
    				//                 var_dump($buchungsKopf->getBuchung_status());
    				$buchungsKopf->setTeilkoepfe($teilbuchungTbl->loadBookingPartHeader($buchungsNr, null, true, $calcNew));
    				$buchungsKopf->setZahlungen($zahlungTable->loadBuchungZahlungen($buchungsNr));
    				//                 if ($calcNew) {
    				$buchungsKopf->calculatePrice($recalculateCoupons);
    				//                 }
    			}
    	}
    	//         var_dump($buchungsKopf);
    	
    	return $buchungsKopf;
    }
    
    public function loadBookingByPostId($postId, $loadAll = true) {
    	global $wpdb;
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::POSTID ." = %d",
						    	array(
						    		$postId
						    	)
    						);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$buchungsKopf	= $this->convertResultToBuchungskopf($results, $loadAll);
    	
    	return $buchungsKopf;
    }
    
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    
    public function loadBooking($buchungsNr, $loadAll = true) { //loadAll ist false!
        $buchungsKopf		= null;
//         if (is_null($this->currentBuchungskopf) || $this->currentBuchungskopf->getBuchung_nr() != $buchungsNr) {
	    	global $wpdb;
	    	global $RSBP_DATABASE;

	        $teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
	        $rabattTable    = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
	        $zahlungTable   = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
	//         $positionTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
	        
	        $calcNew        = true;
	        
	        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	        $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_NR ." = %d",
	            array(
	                $buchungsNr
	            )
			);
	        $results        = $wpdb->get_results( $sql , ARRAY_A );
	        $buchungsKopf   = new RS_IB_Model_Buchungskopf();
	        $recalculateCoupons = true;
	        if (is_array($results) && sizeof($results) > 0) {
	            $buchungsKopf->exchangeArray($results[0]);
	            if ($buchungsKopf->getBuchung_status() == "rs_ib-canceled"
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-canc_request "
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-pay_confirmed"
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-out_of_time"
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-storno"
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-storno_paid"
	            		|| $buchungsKopf->getBuchung_status() == "rs_ib-booked") {
	            			//rs_ib-requested?
	                $calcNew        	= false;
	                $recalculateCoupons = false;
	            }
	            if ($loadAll) {
	                $rabatte        = $rabattTable->loadBuchungRabatt($buchungsNr);
	//                 var_dump($rabatte);
	                $buchungsKopf->setRabatte($rabatte);
	//                 var_dump($buchungsKopf->getBuchung_status());
	                $buchungsKopf->setTeilkoepfe($teilbuchungTbl->loadBookingPartHeader($buchungsNr, null, true, $calcNew));
	                $buchungsKopf->setZahlungen($zahlungTable->loadBuchungZahlungen($buchungsNr));
	//                 if ($calcNew) {
	                $buchungsKopf->calculatePrice($recalculateCoupons);
	//                 }
	            }
	        }
// 	        $this->currentBuchungskopf = $buchungsKopf;
//         } else {
//         	$buchungsKopf = $this->currentBuchungskopf;
//         }

        return $buchungsKopf;
    }
    
    public function loadAllBookings($loadAllInfo = false) {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    	$rabattTable    = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
    	$zahlungTable   = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungZahlung::RS_TABLE);
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql        	= "SELECT * FROM $table_name";
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$bkopfArray		= array();
    	if (is_array($results) && sizeof($results) > 0) {
    		foreach ($results as $bkopf) {
		    	$buchungsKopf   = new RS_IB_Model_Buchungskopf();
		    	$buchungsKopf->exchangeArray($bkopf);
		    	if ($loadAllInfo) {
		    		$rabatte        = $rabattTable->loadBuchungRabatt($buchungsNr);
		    		$buchungsKopf->setRabatte($rabatte);
		    		$buchungsKopf->setTeilkoepfe($teilbuchungTbl->loadBookingPartHeader($buchungsNr, null, true, $calcNew));
		    		$buchungsKopf->setZahlungen($zahlungTable->loadBuchungZahlungen($buchungsNr));
		    		$buchungsKopf->calculatePrice($recalculateCoupons);
		    	}
		    	array_push($bkopfArray, $buchungsKopf);
    		}
    	}
    	
    	return $bkopfArray;
    }
    
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    public function loadBookingByBookingComReservationId($reservationId) {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::BOOKINGCOM_RESERVATIONID ." = %d",
    			array(
    				$reservationId
    			)
    		);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$buchungsKopf   = new RS_IB_Model_Buchungskopf();
    	if (is_array($results) && sizeof($results) > 0) {
    		$buchungsKopf->exchangeArray($results[0]);
    	} else {
    		$buchungsKopf = null;
    	}
    	return $buchungsKopf;
    }
    
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    public function loadBookingByInvoiceNumber($invoiceNumber) {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR ." = %d",
	    	array (
	    		$invoiceNumber
	    	)
    	);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$buchungsKopf   = new RS_IB_Model_Buchungskopf();
    	if (is_array($results) && sizeof($results) > 0) {
    		$buchungsKopf->exchangeArray($results[0]);
    	} else {
    		$buchungsKopf = null;
    	}
    	return $buchungsKopf;
    }
    
    
    public function loadBookingByStripeChargeId($chargeId) {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_Buchungskopf::CHARGE_ID ." = %s",
    			array(
    				$chargeId
    			)
		);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$buchungsKopf   = new RS_IB_Model_Buchungskopf();
    	if (is_array($results) && sizeof($results) > 0) {
    		$buchungsKopf->exchangeArray($results[0]);
    	} else {
    		$buchungsKopf = null;
    	}
    	return $buchungsKopf;
    }
    
    
    /* @var $zahlungTable RS_IB_Table_BuchungZahlung */
    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $rabattTable RS_IB_Table_BuchungRabatt */
    /**
     * Gibt alle Buchungskoepfe zurueck, die noch nicht mit Booking.com synchronisiert wurden.
     * @return NULL|RS_IB_Model_Buchungskopf
     */
    public function loadNotSynchronizedBookingComBookings() {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	//         global $RSBP_TABLEPREFIX;
    	
    	$koepfe			= array();
    	$todayStr 		= new DateTime("now");
    	$todayStr 		= $todayStr->format("Y-m-d");
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " .
    							RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ ." = %d
    							AND ".RS_IB_Model_Buchungskopf::BUCHUNG_BIS .">= %s AND
    							". RS_IB_Model_Buchungskopf::BUCHUNG_STATUS."
     							IN ('rs_ib-requested', 'rs_ib-booked', 'rs_ib-payment_reg', 'rs_ib-deposit_reg', 'rs_ib-deposit_conf',
     								'rs_ib-pay_confirmed')",
//      							NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-canc_request', 'rs_ib-out_of_time', 'rs_ib-storno',
//      									 'rs_ib-canc_request', 'rs_ib-blocked')",
    			array(
    					0,
    					$todayStr
    			)
		);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	foreach ($results as $result) {
	    	$buchungsKopf   = new RS_IB_Model_Buchungskopf();
	    	if (is_array($result) && sizeof($result) > 0) {
	    		$buchungsKopf->exchangeArray($result);
	    		array_push($koepfe, $buchungsKopf);
	    	} else {
	    		$buchungsKopf = null;
	    	}
    	}
    	return $koepfe;
    }
    
    public function loadTodayOutstandingDepositBookings() {
    	$paymentlData 	= get_option( 'rs_indiebooking_settings_payment');
    	$depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
    	$koepfe			= array();
    	if ($depositKz == "on") {
	    	global $wpdb;
	    	global $RSBP_DATABASE;
    		
	    	$depositDays 	= (key_exists('deposit_days', $paymentlData)) ? esc_attr__( $paymentlData['deposit_days'] ) : 0;
	    	if (intval($depositDays) < 10) {
	    		$depositDays = "0".$depositDays;
	    	}
	    	$dateinterval 	= 'P'.$depositDays.'D';
	    	$today			= new DateTime();
	    	$today->setTime(0, 0);
	    	$strToday		= $today->format("Y-m-d H:i:s");
	    	$today->add(new DateInterval($dateinterval));
	    	$strTimeAnz		= $today->format("Y-m-d H:i:s");
	    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE "
	    						. RS_IB_Model_Buchungskopf::BUCHUNG_VON ." <= %s "
    							. 'AND '.RS_IB_Model_Buchungskopf::BUCHUNG_BIS ." > %s "
	    						. 'AND '.RS_IB_Model_Buchungskopf::BUCHUNG_VON ." > %s "
    							. 'AND '.RS_IB_Model_Buchungskopf::ANZAHLUNGMAILKZ.' = 0 '
	    						. 'AND '.RS_IB_Model_Buchungskopf::BUCHUNG_STATUS." IN ( "
    							. "'rs_ib-requested', 'rs_ib-booked', 'rs_ib-payment_reg', 'rs_ib-deposit_reg', 'rs_ib-deposit_conf')",
	    							array(
	    								$strTimeAnz,
	    								$strTimeAnz,
	    								$strToday
	    							)
								);
	    	
	    	$results        = $wpdb->get_results( $sql , ARRAY_A );
	    	foreach ($results as $result) {
	    		$buchungsKopf   = new RS_IB_Model_Buchungskopf();
	    		if (is_array($result) && sizeof($result) > 0) {
	    			$buchungsKopf->exchangeArray($result);
	    			array_push($koepfe, $buchungsKopf);
	    		} else {
	    			$buchungsKopf = null;
	    		}
	    	}
			
	    	return $koepfe;
    	}
    	
    	
    	/*
    	$koepfe			= array();
    	$todayStr 		= new DateTime("now");
    	$todayStr 		= $todayStr->format("Y-m-d");
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " .
    	RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ ." = %d
    							AND ".RS_IB_Model_Buchungskopf::BUCHUNG_BIS .">= %s AND
    							". RS_IB_Model_Buchungskopf::BUCHUNG_STATUS."
     							IN ('rs_ib-requested', 'rs_ib-booked', 'rs_ib-payment_reg', 'rs_ib-deposit_reg', 'rs_ib-deposit_conf',
     								'rs_ib-pay_confirmed')",
     								//      							NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-canc_request', 'rs_ib-out_of_time', 'rs_ib-storno',
     								//      									 'rs_ib-canc_request', 'rs_ib-blocked')",
    	array(
    	0,
    	$todayStr
    	)
    	);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	foreach ($results as $result) {
    		$buchungsKopf   = new RS_IB_Model_Buchungskopf();
    		if (is_array($result) && sizeof($result) > 0) {
    			$buchungsKopf->exchangeArray($result);
    			array_push($koepfe, $buchungsKopf);
    		} else {
    			$buchungsKopf = null;
    		}
    	}
    	return $koepfe;
    	*/
    }
    
    
    public function loadAllBookingComBookings() {
    	global $wpdb;
    	global $RSBP_DATABASE;
    	//         global $RSBP_TABLEPREFIX;
    	 
    	$koepfe			= array();
    	$todayStr 		= new DateTime("now");
    	$todayStr 		= $todayStr->format("Y-m-d");
    	$teilbuchungTbl = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
    
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	$sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " .
    							RS_IB_Model_Buchungskopf::BUCHUNG_BIS .">= %s AND
    							". RS_IB_Model_Buchungskopf::BUCHUNG_STATUS."
     							IN ('rs_ib-requested', 'rs_ib-booked', 'rs_ib-payment_reg', 'rs_ib-deposit_reg', 'rs_ib-deposit_conf',
     								'rs_ib-pay_confirmed', 'rs_ib-bookingcom') " .
     							"ORDER BY ".RS_IB_Model_Buchungskopf::BUCHUNG_VON." ASC",
         								//      							NOT IN ( 'trash', 'rs_ib-canceled', 'rs_ib-canc_request', 'rs_ib-out_of_time', 'rs_ib-storno',
         										//      									 'rs_ib-canc_request', 'rs_ib-blocked')",
    			array(
    					$todayStr
    				)
    			);
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	foreach ($results as $result) {
    		$buchungsKopf   = new RS_IB_Model_Buchungskopf();
    		if (is_array($result) && sizeof($result) > 0) {
    			$buchungsKopf->exchangeArray($result);
    			array_push($koepfe, $buchungsKopf);
    		} else {
    			$buchungsKopf = null;
    		}
    	}
    	return $koepfe;
    }
    
    
    public function setBookingComSynchronizedKz($buchungsKopfIds = array()) {
    	global $wpdb;
    	if (sizeof($buchungsKopfIds) > 0 ) {
    		$table     	= $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    		$commaList	= implode(', ', $buchungsKopfIds);
    		$query		= "UPDATE " . $table . " SET ".RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ." = 1
							WHERE ".RS_IB_Model_Buchungskopf::BUCHUNG_NR . 'IN ('.$commaList.')';
    		
    		$wpdb->query($query);
    	}
    }
    
    public function updateAdminUserStatus($buchungsNr, $adminKz) {
    	global $wpdb;
    	
    	if (!is_null($buchungsNr) && !is_null($adminKz) && !empty($buchungsNr) && !empty($adminKz) ) {
    		$table_name     = $this->getTableName();
    		if ($adminKz == 1) {
    			$adminKz = get_current_user_id();
    		}
    		$result = $wpdb->update(
    			$table_name,
    			array (
    				RS_IB_Model_Buchungskopf::ADMIN_KZ			=> $adminKz,
    			),
    			array(
    				RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungsNr,
    			),
    			array(
					'%s',
				),
				array( '%d')
			);
    	}
    }
    
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    public function updateAnzahlungMailKz($buchungskopf) {
    	global $wpdb;
    	
    	$table_name     = $this->getTableName();
    	$result = $wpdb->update(
    		$table_name,
    		array (
    			RS_IB_Model_Buchungskopf::ANZAHLUNGMAILKZ	=> $buchungskopf->getAnzahlungmailkz(),
    		),
    		array(
    			RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungskopf->getBuchung_nr(),
    		),
    		array(
    			'%s',
    		),
    		array( '%d')
    	);
    }
    
    
    public function updateBuchungsStatus($buchungsNr, $status, $adminKz = 0) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        if ($adminKz == 1) {
        	$adminKz = get_current_user_id();
        }
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        
        $result = $wpdb->update(
            $table_name,
            array (
                RS_IB_Model_Buchungskopf::BUCHUNG_STATUS    => $status,
                RS_IB_Model_Buchungskopf::ADMIN_KZ			=> $adminKz,
            ),
            array(
                RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungsNr,
            ),
            array(
                '%s',
                '%s',
            ),
            array( '%d')
        );
    }
    
    public function updateBuchungsAdminKz($buchungsNr, $adminKz = 0) {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	if ($adminKz == 1) {
    		$adminKz = get_current_user_id();
    	}
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    
    	$result = $wpdb->update(
    			$table_name,
    			array (
    					RS_IB_Model_Buchungskopf::ADMIN_KZ			=> $adminKz,
    			),
    			array(
    					RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungsNr,
    			),
    			array(
    					'%s',
    			),
    			array( '%d')
    		);
    }
    
    public function updateBuchungsAnzahlungBezahltKz($buchungsNr, $bezahltKz = 0) {
    	global $wpdb;

    	$table_name     = $this->getTableName();
    	
    	$result = $wpdb->update(
    		$table_name,
	    	array (
	    		RS_IB_Model_Buchungskopf::ANZAHLUNGBEZKZ	=> $bezahltKz,
	    	),
	    	array(
	    		RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungsNr,
	    	),
	    	array(
	    		'%s',
	    	),
	    	array( '%d')
    	);
    }
    
    public function updateBuchungHauptzahlungsartByPostId($postId, $zahlungsart) {
    	global $wpdb;
    	//         global $RSBP_TABLEPREFIX;
    	
    	$table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
    	
    	$result = $wpdb->update(
    	$table_name,
    	array (
    		RS_IB_Model_Buchungskopf::ZAHLUNGSART    => $zahlungsart,
    	),
    	array(
    		RS_IB_Model_Buchungskopf::POSTID     => $postId,
    	),
    	array(
    		'%s',
    	),
    	array( '%d')
    	);
    }
    
    public function updateBuchungHauptzahlungsart($buchungsnr, $zahlungsart) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        
        $result = $wpdb->update(
            $table_name,
            array (
                RS_IB_Model_Buchungskopf::ZAHLUNGSART    => $zahlungsart,
            ),
            array(
                RS_IB_Model_Buchungskopf::BUCHUNG_NR     => $buchungsnr,
            ),
            array(
                '%s',
            ),
            array( '%d')
        );
    }
    
    public function updateBuchungsKontakt( RS_IB_Model_Buchungskopf $buchungskopf, $contactDataArray) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        
        $contactDataArray = RS_IB_Data_Validation::check_with_whitelist($contactDataArray, RS_IB_Data_Validation::DATATYPE_CONTACT_ARRAY);
        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $result = $wpdb->update(
            $table_name,
            array (
                RS_IB_Model_Buchungskopf::KUNDE_TITEL       => $contactDataArray['titel'],
                RS_IB_Model_Buchungskopf::KUNDE_ANREDE      => $contactDataArray['anrede'],
                RS_IB_Model_Buchungskopf::KUNDE_NAME        => $contactDataArray['name'],
                RS_IB_Model_Buchungskopf::KUNDE_VORNAME     => $contactDataArray['firstName'],
                RS_IB_Model_Buchungskopf::KUNDE_STRASSE     => $contactDataArray['strasse'],
                RS_IB_Model_Buchungskopf::KUNDE_PLZ         => $contactDataArray['plz'],
                RS_IB_Model_Buchungskopf::KUNDE_ORT         => $contactDataArray['ort'],
                RS_IB_Model_Buchungskopf::KUNDE_EMAIL       => $contactDataArray['email'],
                RS_IB_Model_Buchungskopf::KUNDE_TELEFON     => $contactDataArray['telefon'],
            ),
            array(
                RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungskopf->getBuchung_nr(),
            ),
            array (
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ) ,
            array('%d')
        );
    }
    
    /**
     * Erstellt einen Buchungskopf satz, mit den Daten die Booking.com zurueck gibt wenn eine Buchung
     * direkt nach Buchung wieder storniert wird.
     * Dann gibt booking.com naemlich nur den storno-Satz rueber bei dem angaben wie bspw. Buchungszeitraum fehlen.
     * Damit diese Stornierungen trotzdem Statistisch erfasst werden koennen, speichere ich diese leer Saetze
     */
    public function createBookingComCanceledBuchungskopf($bookingComId) {
    	global $wpdb;
    	
    	RS_Indiebooking_Log_Controller::write_log("Direkte BookingCom Stornierung",
    			__LINE__, __CLASS__, RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
    	
    	$table_name     = $this->getTableName();
    	$result = $wpdb->insert(
    		$table_name,
    		array(
    			RS_IB_Model_Buchungskopf::BOOKINGCOM_RESERVATIONID
    				=> RS_IB_Data_Validation::check_with_whitelist(
    					$bookingComId,
    					RS_IB_Data_Validation::DATATYPE_NUMBER
    			),
    			RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ
    				=> RS_IB_Data_Validation::check_with_whitelist(
    					1, RS_IB_Data_Validation::DATATYPE_NUMBER
    			),
    			RS_IB_Model_Buchungskopf::BOOKINGCOM_BOOKING
    				=> RS_IB_Data_Validation::check_with_whitelist(
    					1, RS_IB_Data_Validation::DATATYPE_NUMBER
    			),
    			RS_IB_Model_Buchungskopf::BUCHUNG_STATUS
    				=> RS_IB_Data_Validation::check_with_whitelist(
    					'rs_ib-canceled', RS_IB_Data_Validation::DATATYPE_TEXT
    			),
    		),
    		array (
    			'%d',
    			'%d',
    			'%d',
    			'%s',
    		)
    	);
    	$buchungsNr = $wpdb->insert_id;
    	return $buchungsNr;
    }
    
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    public function saveOrUpdateBuchungskopf( RS_IB_Model_Buchungskopf $buchungskopf) {
        global $wpdb;
//         global $RSBP_TABLEPREFIX;
        
        $chargeId = RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getChargeId(), RS_IB_Data_Validation::DATATYPE_TEXT);
        
//         RS_Indiebooking_Log_Controller::write_log("saveOrUpdateBuchungskopf", __LINE__, __CLASS__,
//         											RS_Indiebooking_Log_Controller::RS_IB_LOG_TYPE_INFO);
        RS_Indiebooking_Log_Controller::write_log($buchungskopf->getKunde_vorname2());
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        $buchungdatum   = RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getBuchungsdatum(), RS_IB_Data_Validation::DATATYPE_DATUM);
        $buchungdatum   = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getBuchungsdatum());
        if (!$buchungdatum) {
        	$buchungdatum	= "1970-01-01";
        } else {
        	$buchungdatum   = date('Y-m-d', $buchungdatum->getTimestamp());
        }
        
        
        $rechnungsdatum   = RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getRechnungsdatum(), RS_IB_Data_Validation::DATATYPE_DATUM);
        $rechnungsdatum   = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getRechnungsdatum());
        if (!$rechnungsdatum) {
        	$rechnungsdatum	= "1970-01-01";
        } else {
        	$rechnungsdatum   = date('Y-m-d', $rechnungsdatum->getTimestamp());
        }
        
        $useAdress      = 0;
        if ($buchungskopf->getUseAdress2() == true) {
            $useAdress  = 1;
        }
        if ($buchungskopf->getBuchung_nr() == 0) {
//             echo "Buchungsnummer: ".$buchungskopf->getBuchung_nr();
//             var_dump($buchungskopf);
            $dtFrom             = RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getBuchung_von(), RS_IB_Data_Validation::DATATYPE_DATUM);
            $dtTo               = RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getBuchung_bis(), RS_IB_Data_Validation::DATATYPE_DATUM);
            $dtFrom             = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getBuchung_von());//date_create_from_format('d.m.Y', $buchungskopf->getBuchung_von());
            $dtTo               = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getBuchung_bis());//date_create_from_format('d.m.Y', $buchungskopf->getBuchung_bis());
            if (isset($dtFrom) && isset($dtTo) && ($dtFrom instanceof DateTime) && ($dtTo instanceof DateTime)) {
                $dtFrom         = date('Y-m-d', $dtFrom->getTimestamp());
                $dtTo           = date('Y-m-d', $dtTo->getTimestamp());
                //neuer Buchungskopf
//                 RS_Indiebooking_Log_Controller::write_log("insert - number of nights ".$buchungskopf->getAnzahl_naechte());
//                 RS_Indiebooking_Log_Controller::write_log("insert Buchungskopf - $table_name");
                $result = $wpdb->insert(
                    $table_name,
                    array(
                        RS_IB_Model_Buchungskopf::USER_ID
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getRechnung_nr(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::BUCHUNG_STATUS
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getBuchung_status(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::BUCHUNG_VON       => $dtFrom,
                        RS_IB_Model_Buchungskopf::BUCHUNG_BIS       => $dtTo,
                        RS_IB_Model_Buchungskopf::ANZAHL_NAECHTE
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getAnzahl_naechte(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungskopf::KUNDE_FIRMA
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_firma(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_TITEL
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_title(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_ANREDE
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_anrede(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_NAME
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_name(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_VORNAME
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_vorname(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_STRASSE
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_PLZ
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_plz(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_ORT
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_ort(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse_nr(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_LAND
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_land(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_EMAIL
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_email(), RS_IB_Data_Validation::DATATYPE_EMAIL),
                        RS_IB_Model_Buchungskopf::KUNDE_TELEFON
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_telefon(), RS_IB_Data_Validation::DATATYPE_TELEFON),
                        RS_IB_Model_Buchungskopf::KUNDE_FIRMA2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_firma2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_TITEL2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_title2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_ANREDE2
                            => RS_IB_Data_Validation::check_with_whitelist( $buchungskopf->getKunde_anrede2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_NAME2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_name2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_VORNAME2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_vorname2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_STRASSE2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_PLZ2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_plz2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_ORT2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_ort2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse_nr2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_LAND2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_land2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::KUNDE_EMAIL2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_email2(), RS_IB_Data_Validation::DATATYPE_EMAIL),
                        RS_IB_Model_Buchungskopf::KUNDE_TELEFON2
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_telefon2(), RS_IB_Data_Validation::DATATYPE_EMAIL),
                        RS_IB_Model_Buchungskopf::BUCHUNG_WERT
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getCalculatedPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                        RS_IB_Model_Buchungskopf::ZAHLUNGSART
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getHauptZahlungsart(), RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::NUTZUNGSB_KZ
                            => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getNutzungsbedinungKz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                        RS_IB_Model_Buchungskopf::BUCHUNGS_DATUM    => $buchungdatum,
                        RS_IB_Model_Buchungskopf::USE_ADRESS2       => RS_IB_Data_Validation::check_with_whitelist($useAdress, RS_IB_Data_Validation::DATATYPE_TEXT),
                        RS_IB_Model_Buchungskopf::BOOKINGCOM_RESERVATIONID
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getBcomReservationId(),
                        		RS_IB_Data_Validation::DATATYPE_NUMBER
                        ),
                        RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getBcomSynchronizedKZ(), RS_IB_Data_Validation::DATATYPE_NUMBER
                        ),
                        RS_IB_Model_Buchungskopf::BOOKINGCOM_BOOKING
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getBcomBookingKz(), RS_IB_Data_Validation::DATATYPE_NUMBER
                        ),
                        
                        
                        RS_IB_Model_Buchungskopf::KUNDE_FIRMA_NR
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getKundeFirmaNr(), RS_IB_Data_Validation::DATATYPE_NUMBER
                        ),
                        
                        RS_IB_Model_Buchungskopf::KUNDE_FIRMA_NR_TYP
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getKundeFirmaNrTyp(), RS_IB_Data_Validation::DATATYPE_TEXT
                        ),
                        
                        RS_IB_Model_Buchungskopf::KUNDE_TYP
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getKundeTyp(), RS_IB_Data_Validation::DATATYPE_TEXT
                        ),
                        
                        RS_IB_Model_Buchungskopf::BOOKINGCOM_GENIUS
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getBookingcomGenius(), RS_IB_Data_Validation::DATATYPE_NUMBER
                        ),
                        
                        RS_IB_Model_Buchungskopf::CHARGE_ID
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getChargeId(), RS_IB_Data_Validation::DATATYPE_TEXT
                        ),
                        RS_IB_Model_Buchungskopf::CUSTOM_MESSAGE
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getCustomText(), RS_IB_Data_Validation::DATATYPE_TEXTAREA
                        ),
                        RS_IB_Model_Buchungskopf::ADMIN_KZ
                        	=> RS_IB_Data_Validation::check_with_whitelist(
                        		$buchungskopf->getAdminKz(), RS_IB_Data_Validation::DATATYPE_TEXT
						),
						RS_IB_Model_Buchungskopf::IGNOREMINIMUMPERIOD
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getIgnoreMinimumPeriod(), RS_IB_Data_Validation::DATATYPE_INTEGER
						),
						RS_IB_Model_Buchungskopf::ALLOWPASTBOOKING
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getAllowPastBooking(), RS_IB_Data_Validation::DATATYPE_INTEGER
						),
						RS_IB_Model_Buchungskopf::CHANGEBILLDATE
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getChangeBillDate(), RS_IB_Data_Validation::DATATYPE_INTEGER
						),
						RS_IB_Model_Buchungskopf::BOOKINGTYPE
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getBookingType(), RS_IB_Data_Validation::DATATYPE_INTEGER
						),
						RS_IB_Model_Buchungskopf::RECHNUNGS_DATUM    => $rechnungsdatum,
						RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getKunde_abteilung(), RS_IB_Data_Validation::DATATYPE_TEXT
						),
						RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG2
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getKunde_abteilung2(), RS_IB_Data_Validation::DATATYPE_TEXT
						),
						RS_IB_Model_Buchungskopf::POSTID
							=> RS_IB_Data_Validation::check_with_whitelist(
								$buchungskopf->getPostId(), RS_IB_Data_Validation::DATATYPE_INTEGER
						),
						RS_IB_Model_Buchungskopf::ANZAHLUNG
							=> RS_IB_Data_Validation::check_with_whitelist(
							$buchungskopf->getAnzahlungsbetrag(), RS_IB_Data_Validation::DATATYPE_NUMBER
						),
						RS_IB_Model_Buchungskopf::ANZAHLUNGBEZKZ
							=> RS_IB_Data_Validation::check_with_whitelist(
							$buchungskopf->getAnzahlungBezahlt(), RS_IB_Data_Validation::DATATYPE_TEXT
						),
                    ),
                    array (
                        '%d',
                        '%d',
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
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
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
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%d',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                    )
                );
                $buchungsNr = $wpdb->insert_id;
                RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."insert Buchungskopf - $table_name - BuchungNr: $buchungsNr");
            } else {
                $buchungsNr = 0;
            }
        } else {
            //update vorhandenen Buchungskopf
            //Datum muss auch aktualisiert werden, falls die Daten sich geandert haben (aktuell nur bei Booking.com der Fall)
        	$dtFrom             = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getBuchung_von());
        	$dtTo               = rs_ib_date_util::convertDateValueToDateTime($buchungskopf->getBuchung_bis());
        	$dtFrom         	= date('Y-m-d', $dtFrom->getTimestamp());
        	$dtTo           	= date('Y-m-d', $dtTo->getTimestamp());
            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."update - number of nights ".$buchungskopf->getAnzahl_naechte());
            $result = $wpdb->update(
                $table_name,
                array (
                    RS_IB_Model_Buchungskopf::USER_ID
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getUserId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                	RS_IB_Model_Buchungskopf::BUCHUNG_RECH_NR
                		=> RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getRechnung_nr(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_FIRMA
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_firma(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_TITEL
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_title(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_ANREDE
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_anrede(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_NAME
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_name(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_VORNAME
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_vorname(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_STRASSE
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_PLZ
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_plz(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_ORT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_ort(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse_nr(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_LAND
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_land(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_EMAIL
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_email(), RS_IB_Data_Validation::DATATYPE_EMAIL),
                    RS_IB_Model_Buchungskopf::KUNDE_TELEFON
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_telefon(), RS_IB_Data_Validation::DATATYPE_TELEFON),
                    RS_IB_Model_Buchungskopf::KUNDE_FIRMA2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_firma2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_TITEL2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_title2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_ANREDE2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_anrede2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_NAME2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_name2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_VORNAME2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_vorname2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_STRASSE2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_PLZ2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_plz2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_ORT2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_ort2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_strasse_nr2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_LAND2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_land2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::KUNDE_EMAIL2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_email2(), RS_IB_Data_Validation::DATATYPE_EMAIL),
                    RS_IB_Model_Buchungskopf::KUNDE_TELEFON2
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_telefon2(), RS_IB_Data_Validation::DATATYPE_TELEFON),
                    RS_IB_Model_Buchungskopf::BUCHUNG_STATUS
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getBuchung_status(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::BUCHUNG_WERT
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getCalculatedPrice(), RS_IB_Data_Validation::DATATYPE_NUMBER),
                    RS_IB_Model_Buchungskopf::ZAHLUNGSART
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getHauptZahlungsart(), RS_IB_Data_Validation::DATATYPE_TEXT),
                    RS_IB_Model_Buchungskopf::NUTZUNGSB_KZ
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getNutzungsbedinungKz(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_Buchungskopf::BUCHUNGS_DATUM    => $buchungdatum,
                    RS_IB_Model_Buchungskopf::ANZAHL_NAECHTE
                        => RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getAnzahl_naechte(), RS_IB_Data_Validation::DATATYPE_INTEGER),
                    RS_IB_Model_Buchungskopf::USE_ADRESS2
                        => RS_IB_Data_Validation::check_with_whitelist($useAdress, RS_IB_Data_Validation::DATATYPE_TEXT),
                	RS_IB_Model_Buchungskopf::BOOKINGCOM_RESERVATIONID
                		=> RS_IB_Data_Validation::check_with_whitelist(
                				$buchungskopf->getBcomReservationId(), RS_IB_Data_Validation::DATATYPE_NUMBER
                	),
                	RS_IB_Model_Buchungskopf::BOOKINGCOM_SYNCHRONIZED_KZ
                		=> RS_IB_Data_Validation::check_with_whitelist(
                				$buchungskopf->getBcomSynchronizedKZ(), RS_IB_Data_Validation::DATATYPE_NUMBER
                	),
                	RS_IB_Model_Buchungskopf::BOOKINGCOM_BOOKING
                		=> RS_IB_Data_Validation::check_with_whitelist(
                				$buchungskopf->getBcomBookingKz(), RS_IB_Data_Validation::DATATYPE_NUMBER
                	),
                	RS_IB_Model_Buchungskopf::BUCHUNG_VON       => $dtFrom,
                	RS_IB_Model_Buchungskopf::BUCHUNG_BIS       => $dtTo,


                	RS_IB_Model_Buchungskopf::KUNDE_FIRMA_NR
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getKundeFirmaNr(), RS_IB_Data_Validation::DATATYPE_NUMBER
                	),
                		
                	RS_IB_Model_Buchungskopf::KUNDE_FIRMA_NR_TYP
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getKundeFirmaNrTyp(), RS_IB_Data_Validation::DATATYPE_TEXT
                	),
					RS_IB_Model_Buchungskopf::KUNDE_TYP
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getKundeTyp(), RS_IB_Data_Validation::DATATYPE_TEXT
                	),
                	RS_IB_Model_Buchungskopf::BOOKINGCOM_GENIUS
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getBookingcomGenius(), RS_IB_Data_Validation::DATATYPE_NUMBER
                	),
                	RS_IB_Model_Buchungskopf::CHARGE_ID
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getChargeId(), RS_IB_Data_Validation::DATATYPE_TEXT
                	),
                	RS_IB_Model_Buchungskopf::CUSTOM_MESSAGE
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getCustomText(), RS_IB_Data_Validation::DATATYPE_TEXTAREA
                	),
                	RS_IB_Model_Buchungskopf::ADMIN_KZ
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getAdminKz(), RS_IB_Data_Validation::DATATYPE_TEXT
                	),
                	RS_IB_Model_Buchungskopf::IGNOREMINIMUMPERIOD
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getIgnoreMinimumPeriod(), RS_IB_Data_Validation::DATATYPE_INTEGER
                	),
                	RS_IB_Model_Buchungskopf::ALLOWPASTBOOKING
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getAllowPastBooking(), RS_IB_Data_Validation::DATATYPE_INTEGER
                	),
                	RS_IB_Model_Buchungskopf::CHANGEBILLDATE
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getChangeBillDate(), RS_IB_Data_Validation::DATATYPE_INTEGER
                	),
                	RS_IB_Model_Buchungskopf::BOOKINGTYPE
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getBookingType(), RS_IB_Data_Validation::DATATYPE_INTEGER
                	),
                	RS_IB_Model_Buchungskopf::RECHNUNGS_DATUM    => $rechnungsdatum,
                	RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG
                		=> RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_abteilung(), RS_IB_Data_Validation::DATATYPE_TEXT),
                	RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG2
                		=> RS_IB_Data_Validation::check_with_whitelist($buchungskopf->getKunde_abteilung2(), RS_IB_Data_Validation::DATATYPE_TEXT),
                	RS_IB_Model_Buchungskopf::POSTID
                		=> RS_IB_Data_Validation::check_with_whitelist(
                			$buchungskopf->getPostId(), RS_IB_Data_Validation::DATATYPE_INTEGER
                	),
                	RS_IB_Model_Buchungskopf::ANZAHLUNG
                		=> RS_IB_Data_Validation::check_with_whitelist(
                		$buchungskopf->getAnzahlungsbetrag(), RS_IB_Data_Validation::DATATYPE_NUMBER
					),
                	RS_IB_Model_Buchungskopf::ANZAHLUNGBEZKZ
                		=> RS_IB_Data_Validation::check_with_whitelist(
                		$buchungskopf->getAnzahlungBezahlt(), RS_IB_Data_Validation::DATATYPE_TEXT
                	),
                ),
                array(
                    RS_IB_Model_Buchungskopf::BUCHUNG_NR        => $buchungskopf->getBuchung_nr(),
                ),
                array(
                    '%d',
                	'%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                    '%s',
                    '%d',
                    '%s',
                	'%d',
                	'%d',
                	'%d',
                	'%s',
                	'%s',
                	'%s',
                	'%s',
                	'%s',
                	'%d',
                	'%s',
                	'%s',
                	'%s',
                	'%d',
                	'%d',
                	'%d',
                	'%d',
                	'%s',
                	'%s',
                	'%s',
                	'%d',
                	'%s',
                	'%s',
                ),
                array( '%d')
            );
            $buchungsNr = $buchungskopf->getBuchung_nr();
            //             $teilbuchungsNr = $teilHeader->getTeilbuchung_id();
        }
        $this->currentBuchungskopf = $buchungskopf;
        return $buchungsNr;
    }
    
    public function loadAllBookingCustomers() {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $this->getTableName();//$wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
        
        $sql = vsprintf(
	        "SELECT DISTINCT CONCAT(%s, ' ', %s, ' ', %s, ' ', %s, ' ',%s, ' ', %s, ' ', %s, ' ', %s, ' ', %s, ' ', %s) as label,"
            ." %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s FROM %s"
	        ." WHERE %s != 'dummy' AND %s != ''"
            ." UNION"
            ." SELECT DISTINCT CONCAT(%s, ' ', %s, ' ', %s, ' ', %s, ' ',%s, ' ', %s, ' ', %s, ' ', %s, ' ', %s, ' ', %s) as label,"
            ." %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s, %s as %s FROM %s"
            ." WHERE %s != 'dummy' AND %s != ''",
	        array(
	        	RS_IB_Model_Buchungskopf::KUNDE_VORNAME,
	        	RS_IB_Model_Buchungskopf::KUNDE_NAME,
	        	RS_IB_Model_Buchungskopf::KUNDE_STRASSE,
	        	RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR,
	        	RS_IB_Model_Buchungskopf::KUNDE_PLZ,
	        	RS_IB_Model_Buchungskopf::KUNDE_ORT,
	        	RS_IB_Model_Buchungskopf::KUNDE_EMAIL,
	        	RS_IB_Model_Buchungskopf::KUNDE_FIRMA,
	        	RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG,
	        	RS_IB_Model_Buchungskopf::KUNDE_TELEFON,
	        	
		        RS_IB_Model_Buchungskopf::KUNDE_FIRMA,
		        'firma',
		        RS_IB_Model_Buchungskopf::KUNDE_TITEL,
		        'titel',
		        RS_IB_Model_Buchungskopf::KUNDE_ANREDE,
		        'anrede',
		        RS_IB_Model_Buchungskopf::KUNDE_NAME,
		        'name',
		        RS_IB_Model_Buchungskopf::KUNDE_VORNAME,
		        'vorname',
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE,
		        'strasse',
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR,
		        'strassenr',
		        RS_IB_Model_Buchungskopf::KUNDE_PLZ,
		        'plz',
		        RS_IB_Model_Buchungskopf::KUNDE_ORT,
		        'ort',
		        RS_IB_Model_Buchungskopf::KUNDE_LAND,
		        'land',
		        RS_IB_Model_Buchungskopf::KUNDE_EMAIL,
		        'email',
		        RS_IB_Model_Buchungskopf::KUNDE_TELEFON,
		        'telefon',
		        RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG,
		        'abteilung',
		        $table_name,
		        RS_IB_Model_Buchungskopf::KUNDE_NAME,
		        RS_IB_Model_Buchungskopf::KUNDE_NAME,
		        
		        RS_IB_Model_Buchungskopf::KUNDE_VORNAME2,
		        RS_IB_Model_Buchungskopf::KUNDE_NAME2,
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE2,
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR2,
		        RS_IB_Model_Buchungskopf::KUNDE_PLZ2,
		        RS_IB_Model_Buchungskopf::KUNDE_ORT2,
		        RS_IB_Model_Buchungskopf::KUNDE_EMAIL2,
		        RS_IB_Model_Buchungskopf::KUNDE_FIRMA2,
		        RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG2,
		        RS_IB_Model_Buchungskopf::KUNDE_TELEFON2,
		        
		        RS_IB_Model_Buchungskopf::KUNDE_FIRMA2,
		        'firma',
		        RS_IB_Model_Buchungskopf::KUNDE_TITEL2,
		        'titel',
		        RS_IB_Model_Buchungskopf::KUNDE_ANREDE2,
		        'anrede',
		        RS_IB_Model_Buchungskopf::KUNDE_NAME2,
		        'name',
		        RS_IB_Model_Buchungskopf::KUNDE_VORNAME2,
		        'vorname',
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE2,
		        'strasse',
		        RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR2,
		        'strassenr',
		        RS_IB_Model_Buchungskopf::KUNDE_PLZ2,
		        'plz',
		        RS_IB_Model_Buchungskopf::KUNDE_ORT2,
		        'ort',
		        RS_IB_Model_Buchungskopf::KUNDE_LAND2,
		        'land',
		        RS_IB_Model_Buchungskopf::KUNDE_EMAIL2,
		        'email',
		        RS_IB_Model_Buchungskopf::KUNDE_TELEFON2,
		        'telefon',
		        RS_IB_Model_Buchungskopf::KUNDE_ABTEILUNG2,
		        'abteilung',
		        $table_name,
		        RS_IB_Model_Buchungskopf::KUNDE_NAME2,
		        RS_IB_Model_Buchungskopf::KUNDE_NAME2,
		        
	        )
        );
        
        
//         $sql = vsprintf(
//         		"SELECT DISTINCT %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s FROM %s ".
//         		"WHERE %s != 'dummy'",
//         		array(
//         			RS_IB_Model_Buchungskopf::KUNDE_FIRMA,
//         		    RS_IB_Model_Buchungskopf::KUNDE_TITEL,
//         		    RS_IB_Model_Buchungskopf::KUNDE_ANREDE,
//         		    RS_IB_Model_Buchungskopf::KUNDE_NAME,
//         		    RS_IB_Model_Buchungskopf::KUNDE_VORNAME,
//         		    RS_IB_Model_Buchungskopf::KUNDE_STRASSE,
//         		    RS_IB_Model_Buchungskopf::KUNDE_STRASSE_NR,
//         		    RS_IB_Model_Buchungskopf::KUNDE_PLZ,
//         		    RS_IB_Model_Buchungskopf::KUNDE_ORT,
//         		    RS_IB_Model_Buchungskopf::KUNDE_LAND,
//         		    RS_IB_Model_Buchungskopf::KUNDE_EMAIL,
//         		    RS_IB_Model_Buchungskopf::KUNDE_TELEFON,
//         		    $table_name,
//         		    RS_IB_Model_Buchungskopf::KUNDE_NAME,
//         		)
//         	);
        
        $results        = $wpdb->get_results( $sql , ARRAY_A );
        return $results;
    }
    
    public function getTestDataBuchungskopf() {
    	$buchungsKopf 	= new RS_IB_Model_Buchungskopf();
    	$teilKopf		= new RS_IB_Model_Teilbuchungskopf();
    	$position1		= new RS_IB_Model_Buchungposition();
    	$position2		= new RS_IB_Model_Buchungposition();
    	$position3		= new RS_IB_Model_Buchungposition();
    	$position4		= new RS_IB_Model_Buchungposition();
    	$teilkoepfe		= array();
    	$positionen		= array();
    	
    	$anzahl_naechte	= 3;
    	$buchungVon		= new DateTime();
    	$buchungBis		= new DateTime();
    	$buchungBis->add(new DateInterval('P03D'));
    	
    	$fullMwstArray	= array();//fullMwstArray	Array [2]
    	$firstMwSt		= new RS_IB_Model_BuchungMwSt();
    	$firstMwSt->setBuchung_nr("776");
    	$firstMwSt->setMwst_id("1");
    	$firstMwSt->setMwst_prozent(19);
    	$firstMwSt->setMwst_wert(95.76);
    	$firstMwSt->setUserId(0);
    	
    	$secondMwSt		= new RS_IB_Model_BuchungMwSt();
    	$secondMwSt->setBuchung_nr("776");
    	$secondMwSt->setMwst_id("2");
    	$secondMwSt->setMwst_prozent(7);
    	$secondMwSt->setMwst_wert(29.44);
    	$secondMwSt->setUserId(0);
    	
    	$fullMwstArray[0] = $firstMwSt;
    	$fullMwstArray[1] = $secondMwSt;
    	
    	$pos1Rabatt		= new RS_IB_Model_BuchungRabatt();
    	$pos1Rabatt->setBerechnung_art("4");
    	$pos1Rabatt->setBezeichnung("0");
    	$pos1Rabatt->setBuchung_nr("776");
    	$pos1Rabatt->setGueltig_bis("0000-00-00 00:00:00");
    	$pos1Rabatt->setGueltig_von("0000-00-00 00:00:00");
    	$pos1Rabatt->setPlus_minus_kz("1");
    	$pos1Rabatt->setPosition_nr("1");
    	$pos1Rabatt->setRabatt_art("3");
    	$pos1Rabatt->setRabatt_ausschreiben_kz("1");
    	$pos1Rabatt->setRabatt_id("180");
    	$pos1Rabatt->setRabatt_term_id("0");
    	$pos1Rabatt->setRabatt_typ("1");
    	$pos1Rabatt->setRabatt_wert("5.00");
    	$pos1Rabatt->setTeilbuchung_nr("1");
    	$pos1Rabatt->setUserId("0");
    	$pos1Rabatt->setValid_at_storno("0");
    	
    	$pos1Rabatte	= array();
    	$pos1Rabatte[0] = $pos1Rabatt;
    	
    	$buchungsKopf->setRechnung_nr(4711);
    	$buchungsKopf->setBuchung_nr(4711);
    	$buchungsKopf->setBuchung_status("rs_ib-booked");
		$buchungsKopf->setBuchung_von($buchungVon);
    	$buchungsKopf->setBuchung_bis($buchungBis);
    	$buchungsKopf->setAnzahl_naechte($anzahl_naechte);
    	$buchungsKopf->setKunde_firma("Testfirma");
		$buchungsKopf->setKunde_title("Testtitel");
    	$buchungsKopf->setKunde_anrede("Herr");
    	$buchungsKopf->setKunde_name("Mustermann");
    	$buchungsKopf->setKunde_vorname("Max");
    	$buchungsKopf->setKunde_strasse("Musterstrasse");
    	$buchungsKopf->setKunde_plz("12345");
    	$buchungsKopf->setKunde_ort("Musterort");
    	$buchungsKopf->setKunde_email("max@muster.de");
    	$buchungsKopf->setKunde_telefon("0123456987");
    	$buchungsKopf->setKunde_strasse_nr("42");
    	$buchungsKopf->setKunde_land("Deutschland");
    	$buchungsKopf->setUseAdress2("0");
    	$buchungsKopf->setHauptZahlungsart("INVOICE");
    	$buchungsKopf->setCalculatedPrice(1050);
    	$buchungsKopf->setFullPrice(1050);
    	$buchungsKopf->setZahlungsbetrag(1050);
    	$buchungsKopf->setRabatte(false);
    	$buchungsKopf->setZahlungen(array());
    	$buchungsKopf->setNutzungsbedinungKz("0");
    	$buchungsKopf->setUserId("0");
    	$buchungsKopf->setBcomReservationId("0");
    	$buchungsKopf->setBcomSynchronizedKZ("0");
    	$buchungsKopf->setBcomBookingKz("0");
    	$buchungsKopf->setKundeTyp("");
    	$buchungsKopf->setKundeFirmaNr("0");
    	$buchungsKopf->setKundeFirmaNrTyp("");
    	$buchungsKopf->setBookingcomGenius("0");
    	$buchungsKopf->setChargeId("");
    	$buchungsKopf->setCustomText("");
    	$buchungsKopf->setAdminKz(1);
    	$buchungsKopf->setFullMwstArray($fullMwstArray);
    	$buchungsKopf->setBuchungsdatum(new DateTime("now"));

    	
    	$teilKopf->setAnzahlPersonen("3");
    	$teilKopf->setAppartment_id("204");
    	$teilKopf->setAppartment_name("Testapartment1");
    	$teilKopf->setAppartment_qm("50.00");
    	$teilKopf->setBcomroomid(0);
    	$teilKopf->setBuchung_nr("776");
    	$teilKopf->setCalculatedPrice(350);
    	$teilKopf->setGastName("");
    	$teilKopf->setRabatte(false);
    	$teilKopf->setTeilbuchung_bis($buchungBis);
    	$teilKopf->setTeilbuchung_von($buchungVon);
    	$teilKopf->setTeilbuchung_id("1");
    	$teilKopf->setUserId("0");
    	
    	$position1->setAnzahl_naechte("3");
    	$position1->setAnzahlPersonen("3");
    	$position1->setBasispreis(null);
    	$position1->setBerechnung_type("1");
    	$position1->setBezeichnung("Testapartment");
    	$position1->setBuchung_nr("776");
    	$position1->setCalcPosPrice(135);
    	$position1->setCalculatedPrice(135);
    	$position1->setData_id("0");
    	$position1->setDegressionEinzelPrice(0);
    	$position1->setDegressionRabattTyp(0);
    	$position1->setDegressionRabattValue(0);
    	$position1->setEinzelpreis("50.00");
    	$position1->setFullStorno("1");
    	$position1->setHasDegression(false);
    	$position1->setKommentar("");
    	$position1->setMwst_prozent("0.19");
    	$position1->setMwst_wert(21.55);
    	$position1->setMwstTermId("1");
    	$position1->setPosition_id("1");
    	$position1->setPosition_typ("appartment_price");
    	$position1->setPreis_bis($buchungBis);
    	$position1->setPreis_von($buchungVon);
    	$position1->setQuadratmeter("50.00");
    	$position1->setRabatt_kz("0");
    	$position1->setTeilbuchung_id("1");
    	$position1->setUserId("0");
    	$position1->setRabatte($pos1Rabatte);
    	
    	$position2->setAnzahl_naechte("3");
    	$position2->setAnzahlPersonen("3");
    	$position2->setBasispreis(null);
    	$position2->setBerechnung_type("1");
    	$position2->setBezeichnung("Endreinigung");
    	$position2->setBuchung_nr("776");
    	$position2->setCalcPosPrice(150);
    	$position2->setCalculatedPrice(150);
    	$position2->setData_id("44");
    	$position2->setDegressionEinzelPrice(0);
    	$position2->setDegressionRabattTyp(0);
    	$position2->setDegressionRabattValue(0);
    	$position2->setEinzelpreis("50.00");
    	$position2->setFullStorno("0");
    	$position2->setHasDegression(false);
    	$position2->setKommentar("");
    	$position2->setMwst_prozent("0.07");
    	$position2->setMwst_wert(9.81);
    	$position2->setMwstTermId("2");
    	$position2->setPosition_id("2");
    	$position2->setPosition_typ("appartment_option");
    	$position2->setPreis_bis($buchungBis);
    	$position2->setPreis_von($buchungVon);
    	$position2->setQuadratmeter("50.00");
    	$position2->setRabatt_kz("0");
    	$position2->setRabatte(false);
    	$position2->setTeilbuchung_id("1");
    	$position2->setUserId("0");
    	
    	$position3->setAnzahl_naechte("3");
    	$position3->setAnzahlPersonen("3");
    	$position3->setBasispreis(null);
    	$position3->setBerechnung_type("0");
    	$position3->setBezeichnung("Erstbestückung Minibar");
    	$position3->setBuchung_nr("776");
    	$position3->setCalcPosPrice(20);
    	$position3->setCalculatedPrice(20);
    	$position3->setData_id("87");
    	$position3->setDegressionEinzelPrice(0);
    	$position3->setDegressionRabattTyp(0);
    	$position3->setDegressionRabattValue(0);
    	$position3->setEinzelpreis("20.00");
    	$position3->setFullStorno("0");
    	$position3->setHasDegression(false);
    	$position3->setKommentar("");
    	$position3->setMwst_prozent("0.19");
    	$position3->setMwst_wert(3.19);
    	$position3->setMwstTermId("1");
    	$position3->setPosition_id("3");
    	$position3->setPosition_typ("appartment_option");
    	$position3->setPreis_bis($buchungBis);
    	$position3->setPreis_von($buchungVon);
    	$position3->setQuadratmeter("50.00");
    	$position3->setRabatt_kz("0");
    	$position3->setRabatte(false);
    	$position3->setTeilbuchung_id("1");
    	$position3->setUserId("0");
    	
    	
    	$position4->setAnzahl_naechte("3");
    	$position4->setAnzahlPersonen("3");
    	$position4->setBasispreis(null);
    	$position4->setBerechnung_type("5");
    	$position4->setBezeichnung("Spezial Frühstück");
    	$position4->setBuchung_nr("776");
    	$position4->setCalcPosPrice(45);
    	$position4->setCalculatedPrice(45);
    	$position4->setData_id("12");
    	$position4->setDegressionEinzelPrice(0);
    	$position4->setDegressionRabattTyp(0);
    	$position4->setDegressionRabattValue(0);
    	$position4->setEinzelpreis("15.00");
    	$position4->setFullStorno("1");
    	$position4->setHasDegression(false);
    	$position4->setKommentar("");
    	$position4->setMwst_prozent("0.19");
    	$position4->setMwst_wert(7.18);
    	$position4->setMwstTermId("1");
    	$position4->setPosition_id("4");
    	$position4->setPosition_typ("appartment_option");
    	$position4->setPreis_bis($buchungBis);
    	$position4->setPreis_von($buchungVon);
    	$position4->setQuadratmeter("50.00");
    	$position4->setRabatt_kz("0");
    	$position4->setRabatte(false);
    	$position4->setTeilbuchung_id("1");
    	$position4->setUserId("0");
    	
    	$positionen[0] 	= $position1;
    	$positionen[1] 	= $position2;
    	$positionen[2] 	= $position3;
    	$positionen[3] 	= $position4;
    	$teilKopf->setPositionen($positionen);
    	
    	$teilkoepfe[0] 	= $teilKopf;
    	
    	$teilkopf2		= clone $teilKopf;
    	$teilkopf2->setAppartment_name("Testapartment2");
    	$tk2Pos			= $teilkopf2->getPositionen();
    	$tk2Pos[0]->setBezeichnung("Testapartment2");
    	$teilkopf2->setTeilbuchung_id("2");
    	
    	$teilkopf3		= clone $teilKopf;
    	$teilkopf3->setAppartment_name("Testapartment3");
    	$tk3Pos			= $teilkopf3->getPositionen();
    	$tk3Pos[0]->setBezeichnung("Testapartment3");
    	$teilkopf3->setTeilbuchung_id("3");
    	
    	$teilkoepfe[1] 	= $teilkopf2;
    	$teilkoepfe[2] 	= $teilkopf3;
    	$buchungsKopf->setTeilkoepfe($teilkoepfe);
    	
    	return $buchungsKopf;
    }
    
}
// endif;
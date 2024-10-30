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
class RS_IB_Table_MailPrintJob
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function deleteMailPrintJob($id, $bookingPostId) {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
    	$wpdb->delete(
    		$table_name,
    		array(
    			RS_IB_Model_MailPrintJob::JOB_ID => $id,
    			RS_IB_Model_MailPrintJob::BOOKING_POST_ID => $bookingPostId
    		)
    	);
    }
    
    public function loadAllMailPrintJob() {
    	global $wpdb;
    	global $RSBP_TABLEPREFIX;
    	global $RSBP_DATABASE;
    	
    	$buchungMwstTbl = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungMwSt::RS_TABLE);
    	
    	$table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
    	$sql            = "SELECT * FROM $table_name";
    	$results        = $wpdb->get_results( $sql , ARRAY_A );
    	$mailPrintJobs	= array();
    	foreach ($results as $result) {
    		$mailPrintJob = new RS_IB_Model_MailPrintJob();
    		$mailPrintJob->exchangeArray($result);
    		
    		array_push($mailPrintJobs, $mailPrintJob);
    	}
    	return $mailPrintJobs;
    }
    
//     public function loadMailPrintJob($buchungNr) {
//         global $wpdb;
//         global $RSBP_TABLEPREFIX;
//         global $RSBP_DATABASE;
    
//         $buchungMwstTbl = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungMwSt::RS_TABLE);
    
//         $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
//         $sql            = $wpdb->prepare( "SELECT * FROM $table_name WHERE " . RS_IB_Model_MailPrintJob::BOOKING_POST_ID ." = %d",
//             array(
//                 $buchungNr,
//                 $mwstId
//             )
//         );
//         $results        = $wpdb->get_results( $sql , ARRAY_A );
//         $buchungMwst    = new RS_IB_Model_BuchungMwSt();
//         if (is_array($results) && sizeof($results) > 0) {
//             $buchungMwst->exchangeArray($results[0]);
//         } else {
//             $buchungMwst = false;
//         }
//         return $buchungMwst;
//     }
    
    /* @var $buchungMwSt RS_IB_Model_BuchungMwSt */
    public function saveOrUpdateMailPrintJob( RS_IB_Model_MailPrintJob $mailPrintJob) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $my_current_lang = "";
        if ( function_exists('icl_object_id') ) {
        	$my_current_lang = apply_filters( 'wpml_current_language', NULL );
        }
        $mailPrintJob->setPrintLanguage($my_current_lang);
        $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
        $result     = $wpdb->insert(
            $table_name,
            array(
            	RS_IB_Model_MailPrintJob::BOOKING_POST_ID
            		=> RS_IB_Data_Validation::check_with_whitelist($mailPrintJob->getBookingPostId(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            	RS_IB_Model_MailPrintJob::PRINT_TYPE
            		=> RS_IB_Data_Validation::check_with_whitelist($mailPrintJob->getPrintType(), RS_IB_Data_Validation::DATATYPE_INTEGER),
            	RS_IB_Model_MailPrintJob::PRINT_LANGUAGE
            		=> RS_IB_Data_Validation::check_with_whitelist($mailPrintJob->getPrintLanguage(), RS_IB_Data_Validation::DATATYPE_ALL),
            ),
            array(
                '%d',
                '%d',
            	'%s',
            )
        );
    }
}
// endif;
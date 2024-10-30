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
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}


// if ( ! class_exists( 'RS_IB_Mail_Controller' ) ) :

class RS_IB_Mail_Controller
{
    const LOGGED_OUT = 2;
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
//             self::$_instance = new self(WP_Async_Task::LOGGED_OUT);
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected $action = 'rs_ib_create_file_and_send_mail';
    
    protected function prepare_data( $data ) {
        $bookingPostId                      = $data[0];
        $mailArt                            = $data[1];
        if ($mailArt == 4) {
            $returnData                     = array();
            $returnData['bookingPostId']    = $bookingPostId;
            $returnData['mailArt'][]        = 1;
            $returnData['mailArt'][]        = 2;
        } else {
            $returnData                     = array();
            $returnData['bookingPostId']    = $bookingPostId;
            $returnData['mailArt'][]        = $mailArt;
        }
//         RS_Indiebooking_Log_Controller::write_log("prepare_data ".$bookingPostId." mailArt ".$mailArt);
        return $returnData;
    }
    
    protected function run_action() {
        $bookingPostId          = rsbp_getPostValue('bookingPostId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $postMailArt            = rsbp_getPostValue('mailArt');
        if (!is_null($postMailArt)) {
            $mailArten          = wp_parse_id_list($postMailArt);
            RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."run action");
            foreach ($mailArten as $mailArt) {
                RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."do async action ".$bookingPostId." mailArt ".$mailArt);
//                 do_action("wp_async_$this->action", $bookingPostId, $mailArt);
                do_action($this->action, $bookingPostId, $mailArt);
            }
        }
    }

    public function sendAnfragebestaetigung($bookingPostId) {
//     	RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."do action buchungsbestaetigung und rechnung");
		RS_Indiebooking_Log_Controller::write_log("send Anfragebestaetigung", __LINE__, __CLASS__);
    	//         do_action("rs_ib_create_file_and_send_mail", $bookingPostId, 1);
//     	rs_ib_create_file_and_send_mail($bookingPostId, 4);
		rs_ib_create_mail_log_entry($bookingPostId, 4);
    }
    
    public function sendBuchungsbestaetigungUndRechnung($bookingPostId) {
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."do action buchungsbestaetigung und rechnung");
        RS_Indiebooking_Log_Controller::write_log("do action buchungsbestaetigung und rechnung", __LINE__, __CLASS__);
//         do_action("rs_ib_create_file_and_send_mail", $bookingPostId, 1);
//         rs_ib_create_file_and_send_mail($bookingPostId, 1);
        rs_ib_create_mail_log_entry($bookingPostId, 1);
    }
    
    public function sendInfoMailToWordpressAdmin($bookingPostId, $buchungAnfrageKz) {
    	if ($buchungAnfrageKz == 1) {
    		rs_ib_create_file_and_send_mail($bookingPostId, 98);
    	} else {
    		rs_ib_create_file_and_send_mail($bookingPostId, 99);
    	}
    }
    
    public function sendWarningMailToWordpressAdmin($bookingPostId, $warningNumber) {
    	if ($warningNumber == 1) {
    		rs_ib_create_file_and_send_mail($bookingPostId, 50); //SOFORT Payment failed
    	} else if ($warningNumber == 51) {
    		rs_ib_create_file_and_send_mail($bookingPostId, $warningNumber); //Booking not found
    	} elseif ($warningNumber == 52) {
    		rs_ib_create_file_and_send_mail($bookingPostId, $warningNumber); //Event not verified by stripe
    	}
//     	else {
//     		rs_ib_create_file_and_send_mail($bookingPostId, 99);
//     	}
    }
    
    public function sendBuchungsbestaetigung($bookingPostId) {
    	RS_Indiebooking_Log_Controller::write_log("do action buchungsbestaetigung", __LINE__, __CLASS__);
//         RS_Indiebooking_Log_Controller::write_log("[".__LINE__." ".__CLASS__."] "."do action buchungsbestaetigung");
//         do_action("rs_ib_create_file_and_send_mail", $bookingPostId, 1);
//         rs_ib_create_file_and_send_mail($bookingPostId, 1);
    	rs_ib_create_mail_log_entry($bookingPostId, 1);
    }
    
    public function sendBuchungRechnung($bookingPostId) {
//         RS_Indiebooking_Log_Controller::write_log("do action buchungsrechnung");
//         do_action("rs_ib_create_file_and_send_mail", $bookingPostId, 2);
    }
    
    public function sendZahlungsbestateigung($bookingPostId) {
//         rs_ib_create_file_and_send_mail($bookingPostId, 3);
    	rs_ib_create_mail_log_entry($bookingPostId, 3);
    }
    
    /* @var $mailJobTable RS_IB_Table_MailPrintJob */
    /* @var $mailPrintJob RS_IB_Model_MailPrintJob */
    public function printAndSendAllFromJobLog() {
    	global $RSBP_DATABASE;
    	
    	$my_current_lang 	= "";
    	$mailJobTable 		= $RSBP_DATABASE->getTable(RS_IB_Model_MailPrintJob::RS_TABLE);
    	$allPrintJobs 		= $mailJobTable->loadAllMailPrintJob();
    	if ( function_exists('icl_object_id') ) {
    		global $sitepress;
    		$my_current_lang = apply_filters( 'wpml_current_language', NULL );
    	}
    	foreach ($allPrintJobs as $mailPrintJob) {
    		if ( function_exists('icl_object_id') ) {
    			if (!is_null($mailPrintJob->getPrintLanguage()) && $mailPrintJob->getPrintLanguage() != "") {
    				do_action( 'wpml_switch_language',  $mailPrintJob->getPrintLanguage() );
    				$sitepress->switch_lang($mailPrintJob->getPrintLanguage());
    			} else {
    				do_action( 'wpml_switch_language',  $my_current_lang );
    				$sitepress->switch_lang($my_current_lang);
    			}
    		}
    		rs_ib_create_file_and_send_mail($mailPrintJob->getBookingPostId(), $mailPrintJob->getPrintType());
    		$mailJobTable->deleteMailPrintJob($mailPrintJob->getJobId(), $mailPrintJob->getBookingPostId());
    	}
    	if ( function_exists('icl_object_id') && $my_current_lang != "") {
    		do_action( 'wpml_switch_language',  $my_current_lang );
    		$sitepress->switch_lang($my_current_lang);
    	}
    }
    
    /*
     * Sorgt dafuer, dass alle Buchungen, bei denen die Anzahlungsfrist abgelaufen ist,
     * eine Mail bekommen, dass Sie bitte den Restbetrag ueberweisen sollen.
     */
    /* @var $buchungTable RS_IB_Table_Buchungskopf */
    /* @var $buchungskopf RS_IB_Model_Buchungskopf */
    public function sendAllDepositBookingMails() {
    	global $RSBP_DATABASE;
    	
    	RS_Indiebooking_Log_Controller::write_log('check deposit booking', __LINE__, __CLASS__);
    	$buchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
    	$buchungskoepfe 	= $buchungTable->loadTodayOutstandingDepositBookings();
    	
    	foreach ($buchungskoepfe as $buchungskopf) {
    		$buchungskopf->setAnzahlungmailkz("1");
    		$buchungTable->updateAnzahlungMailKz($buchungskopf);
//     		$this->sendBuchungsbestaetigungUndRechnung($buchungskopf->getPostId());
    		rs_ib_create_mail_log_entry($buchungskopf->getPostId(), 7);
    	}
    }
    
}
// endif;

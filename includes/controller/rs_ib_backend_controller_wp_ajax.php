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
}
include_once 'parent_controller/rs_ib_backend_controller.php';

// if ( ! class_exists( 'RS_IB_Backend_Controller_WP_AJAX' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Backend_Controller_WP_AJAX extends RS_IB_Backend_Controller
{
    public function __construct() {
        add_action('wp_ajax_printBooking', array($this, 'printBooking'));
        
        add_action('admin_post_printBooking2', array($this, 'printBooking2'));
        
        add_action('admin_post_printTestBooking', array($this, 'printTestBooking'));
        add_action('wp_ajax_indiebookingSendTestMail', array($this, 'indiebookingSendTestMail'));
        
//         add_action('wp_ajax_printBooking2', array($this, 'printBooking2'));
        add_action('wp_ajax_resetIndiebookingWelcomeBanner', array($this, 'resetIndiebookingWelcomeBanner'));
        add_action('wp_ajax_showIndiebookingWelcomeBanner', array($this, 'showIndiebookingWelcomeBanner'));
        add_action('wp_ajax_cancelBooking_admin', array($this, 'cancelBooking'));
        add_action('wp_ajax_confirmInquiry', array($this, 'confirmInquiry'));
        add_action('wp_ajax_confirmPayment', array($this, 'confirmPayment'));
        add_action('wp_ajax_confirmDepositPayment', array($this, 'confirmDepositPayment'));
        add_action('wp_ajax_sendPaymentConfirmationMail', array($this, 'sendPaymentConfirmationMail'));
        add_action('wp_ajax_getCompanyInfos', array($this, 'getCompanyInfos'));
        add_action('wp_ajax_changeIndiebookingAllowUsingStatistic', array($this, 'changeIndiebookingAllowUsingStatistic'));
        add_action('wp_ajax_getIndiebookingLogFile', array($this, 'getIndiebookingLogFile'));
        
        add_action('wp_ajax_updateBookingContactData', array($this, 'updateBookingContactData_ajax'));
    }
    
    public function updateBookingContactData_ajax() {
    	$answer = array(
    		'CODE' => 0,
    		'MSG'  => '',
    	);
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
    		$bookingNumber 	= rsbp_getPostValue('bookingNumber');
    		$contactData	= rsbp_getPostValue('contactData');
    		$bookingPostId	= rsbp_getPostValue('bookingPostId');
    		$answer			= $this->updateBookingContactData($bookingNumber, $contactData, $bookingPostId);
    	}
    	if ($answer['CODE'] == 1) {
    		wp_send_json_success($answer);
    	} else {
    		wp_send_json_error($answer);
    	}
    }
    
    public function getIndiebookingLogFile() {
        $filename    = rsbp_getPostValue('filename', "", RS_IB_Data_Validation::DATATYPE_TEXT);
        if (empty($filename)) {
            $file    = RS_Indiebooking_Log_Controller::getLastLogFile();
        } else {
            $file    = RS_Indiebooking_Log_Controller::getLogFile($filename);
        }
        $fileContent = file_get_contents($file);
        wp_send_json_success($fileContent);
    }
    
    public function changeIndiebookingAllowUsingStatistic() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
	        $isChecked = rsbp_getPostValue('allowStatistic');
	        if ($isChecked == 'true') {
	            $isChecked 	= 'on';
	        } else {
	            $isChecked 	= 'off';
	        }
	        update_option('rs_indiebooking_settings_allow_statistics_kz', $isChecked);
	        $answer = array(
	            'CODE' => 1,
	            'MSG'  => '',
	        );
	        wp_send_json_success($answer);
    	}
    }
    
    public function resetIndiebookingWelcomeBanner() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
	        update_option('rs_indiebooking_settings_show_welcome_kz', "off");
	        $answer = array(
	            'CODE' => 1,
	            'MSG'  => '',
	        );
	        wp_send_json_success($answer);
    	}
    }
    
    public function showIndiebookingWelcomeBanner() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
	    	update_option('rs_indiebooking_settings_show_welcome_kz', "on");
	    	$answer = array(
	    			'CODE' => 1,
	    			'MSG'  => '',
	    	);
	    	wp_send_json_success($answer);
    	}
    }
    
    public function getCompanyInfos() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
	        $options                            = get_option( 'rs_indiebooking_settings' );
	        $answer                             = array();
	        if ($options) {
	            $answer['companyName']          = (key_exists('company_name', $options))       ?  esc_attr__( $options['company_name'] )      : "";
	            $answer['companyStreet']        = (key_exists('company_street', $options))     ?  esc_attr__( $options['company_street'] )    : "";
	            $answer['companyLocation']      = (key_exists('company_location', $options))   ?  esc_attr__( $options['company_location'] )  : "";
	            $answer['companyCountry']       = (key_exists('company_country', $options))    ?  esc_attr__( $options['company_country'] )   : "";
	            $answer['companyWebsite']       = (key_exists('company_website', $options))    ?  esc_attr__( $options['company_website'] )   : "";
	            $answer['companyEmail']         = (key_exists('company_email', $options))      ?  esc_attr__( $options['company_email'] )     : "";
	            $answer['companyPhone']         = (key_exists('company_phone', $options))      ?  esc_attr__( $options['company_phone'] )     : "";
	            $answer['companyFax']           = (key_exists('company_fax', $options))        ?  esc_attr__( $options['company_fax'] )       : "";
	            $answer['company_zip_code']     = (key_exists('company_zip_code', $options))   ?  esc_attr__( $options['company_zip_code'] )  : "";
	            $answer['company_ust_id']       = (key_exists('company_ust_id', $options))     ?  esc_attr__( $options['company_ust_id'] )    : "";
	            $answer['company_tax_number']   = (key_exists('company_tax_number', $options)) ?  esc_attr__( $options['company_tax_number']) : "";
	            
	            $longitude                      = (key_exists('longitude', $options))          ?  esc_attr__( $options['longitude']) : "";
	            $latitude                       = (key_exists('latitude', $options))           ?  esc_attr__( $options['latitude']) : "";
	        
	            $answer['longitude']            = str_replace(",", ".", $longitude);
	            $answer['latitude']             = str_replace(",", ".", $latitude);
	        }
	        wp_send_json_success($answer);
    	}
    }
    
    public function cancelBooking() {
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
        if ($nonceOk) {
	        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $answer         = $this->storniereBuchung($bookingId);
	        if (key_exists('CODE', $answer) && $answer['CODE'] == 1) {
	            wp_send_json_success($answer);
	        } else {
	            wp_send_json_error($answer);
	        }
	        wp_die();
        }
    }
    
    public function confirmInquiry() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
	    	$bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	    	$answer         = $this->anfrageBestaetigen($bookingId);
	    	if ($answer['CODE'] == 1) {
	    		wp_send_json_success($answer);
	    	} else {
	    		wp_send_json_error($answer);
	    	}
	    	wp_die();
    	}
    }
    
    public function confirmDepositPayment() {
    	$nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
    	if ($nonceOk) {
    		$bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
    		$answer         = $this->zahlungBestaetigen($bookingId, null, true, true);
    		if ($answer['CODE'] == 1) {
    			wp_send_json_success($answer);
    		} else {
    			wp_send_json_error($answer);
    		}
    		wp_die();
    	}
    }
    
    public function confirmPayment() {
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-admin-ajax-nonce', 'security');
        if ($nonceOk) {
	        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $answer         = $this->zahlungBestaetigen($bookingId, null, true, false);
	        if ($answer['CODE'] == 1) {
	            wp_send_json_success($answer);
	        } else {
	            wp_send_json_error($answer);
	        }
	        wp_die();
        }
    }
    
    public function printBooking() {
        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $answer         = $this->createBookingPdf($bookingId);
        if ($answer['CODE'] == 1) {
            wp_send_json_success($answer);
        } else {
            wp_send_json_error($answer);
        }
        wp_die();
    }
    
    public function printBooking2() {
        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $answer         = $this->createBookingPdf($bookingId);
        $filename       = $answer['FILE'];
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Transfer-Encoding: binary');
        readfile($filename);
        unlink($filename);
        wp_die();
    }

    public function indiebookingSendTestMail() {
    	$answer         = $this->createTestBookingPdf();
    	$filename       = $answer['FILE'];
    	
    	rs_ib_sendInfoMailToAdmin(0, "Testmail", "Testmail", $filename, false);
    	
    	$answer = array(
			'CODE' => 1,
    	);
    	wp_send_json_success($answer);
    }
    
    public function printTestBooking() {
    	$answer         = $this->createTestBookingPdf();
    	$filename       = $answer['FILE'];
    	header('Content-type: application/pdf');
    	header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    	header('Content-Transfer-Encoding: binary');
    	readfile($filename);
    	unlink($filename);
    	wp_die();
    }
    
    public function sendPaymentConfirmationMail() {
        $bookingId      = rsbp_getPostValue('bookingId', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
        $answer         = $this->createPaymentConfirmationMail($bookingId);
        if ($answer['CODE'] == 1) {
            wp_send_json_success($answer);
        } else {
            wp_send_json_error($answer);
        }
        wp_die();
    }
    
}
// endif;
new RS_IB_Backend_Controller_WP_AJAX();
<?php
/*
 Plugin Name:       Indiebooking
 Description:       Indiebooking - the Booking Software for your Homepage!
 Author:            ReWa Soft GmbH
 Text Domain:       indiebooking
 Version:           1.3.6
 */
?>
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
<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
}

/*
 * $RSBP_DATABASE = globales Objekt um auf eigene Datenbankoperationen zuzugreifen.
 * Wird in cRS_IB_DatabaseController initialisiert.
 */
// $RS_IB_VERSION          = "?v=0.3.2";
// $RS_IB_SCRIPT_VERSION   = "0.3.3";
$RS_IB_VERSION          = "?v=1.3.6";
// $RS_IB_SCRIPT_VERSION   = "1.3.6";
$RS_IB_SCRIPT_VERSION   = "1.3.6";

global $RSBP_DATABASE;
/*
 * RS_INDIEBOOKING_LOG_OBJECT ist fuer das logging zustaendig.
 */
// global $RS_INDIEBOOKING_LOG_OBJECT;
global $RSBP_TABLEPREFIX;
//test
$options       = array();
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}

function request_is_frontend_ajax()
{
    $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';

    //Try to figure out if frontend AJAX request... If we are DOING_AJAX; let's look closer
    if((defined('DOING_AJAX') && DOING_AJAX))
    {
        //From wp-includes/functions.php, wp_get_referer() function.
        //Required to fix: https://core.trac.wordpress.org/ticket/25294
        $ref = '';
        if ( ! empty( $_REQUEST['_wp_http_referer'] ) )
            $ref = wp_unslash( $_REQUEST['_wp_http_referer'] );
            elseif ( ! empty( $_SERVER['HTTP_REFERER'] ) )
            $ref = wp_unslash( $_SERVER['HTTP_REFERER'] );

            //If referer does not contain admin URL and we are using the admin-ajax.php endpoint, this is likely a frontend AJAX request
            if(((strpos($ref, admin_url()) === false) && (basename($script_filename) === 'admin-ajax.php')))
                return true;
    }

    //If no checks triggered, we end up here - not an AJAX request.
    return false;
}


/**
 * Diese Funktion wird aktuell nicht mehr benoetigt, da wir nun die Uebersetzung,
 * die auf Wordpress.org erstellt werden kann, nutzen.
 * Um falsche Uebersetzungen zu vermeiden, laden wir unsere Dateien also nicht mehr.
 */
function rs_indiebooking_i18n_init() {
    /*
    $plugin_dir = dirname(plugin_basename(__FILE__)).'/i18n/languages/';
    load_plugin_textdomain('indiebooking', false, $plugin_dir);
    */
}

/**
 * Gibt die RS-BookingPlattform Instanz zurueck.
 * Die Klasse Initialisiert das Plugin
 * @return BookingPlattform
 */
function RS_INDIEBOOKING_INIT() {
	//auskommentieren wenn in live Betrieb!
//     error_reporting(E_ALL);
//     error_reporting(0);
//     ini_set('display_errors', '1');
    
    include_once( 'rs_ib_Indiebooking_Exception.php' );
    include_once( 'includes/util/rs_indiebooking_tour_text.php' );
    include_once( 'cRS_IB_Indiebooking.php' );
    
    include_once( 'includes/install/cRS_IB_Install.php' ); //installiert das Plugin bei aktivierung
    include_once( 'includes/install/RS_IB_Update.php' );
//     include_once( 'mpdf60/mpdf.php' );
//     register_activation_hook( __FILE__, array( 'cRS_IB_Install', 'install' ) );
    register_activation_hook( __FILE__, 'rs_indiebooking_activate' );
    register_deactivation_hook(__FILE__, 'rs_indiebooking_deactivation');
//     add_action( 'get_header', 'includeCSPHeader', 1 );
    add_action( 'plugins_loaded', 'rs_indiebooking_update_db_check' );
    add_action('plugins_loaded', 'rs_indiebooking_i18n_init');
    
    add_action('wp_enqueue_scripts', array('cRS_Indiebooking', 'register_default_scripts'));
    if(!(defined('DOING_AJAX') && DOING_AJAX)) {
    	add_action('admin_enqueue_scripts', array('cRS_Indiebooking', 'register_admin_scripts'));
    	add_action('admin_enqueue_scripts', array('cRS_Indiebooking', 'register_default_scripts'));
    	//         add_action('admin_enqueue_scripts', array('cRS_Indiebooking', 'register_default_scripts'));
    }
    add_action('wp_enqueue_scripts', array('cRS_Indiebooking', 'register_default_frontend_scripts'));
    add_action('wp_enqueue_scripts', array('cRS_Indiebooking', 'register_frontend_scripts'));
    
    
    /*
     * Fuegt einen 60 Sekunden Scheduler ein, der dafuert sorgt,
     * dass jede Minute von Booking.com zu indiebooking synchronisiert wird.
     */
    add_filter('cron_schedules', function ( $schedules ) {
    	$schedules['ib_mail_everyminute'] = array(
    		'interval' => 60,
    		'display' => __('Every Minute')
    	);
    	return $schedules;
    });
    add_action('rs_indiebooking_mailjob_action', 'rs_indiebooking_mailjob_action');
    $ibMailScheduleTtimestamp = wp_next_scheduled( 'rs_indiebooking_mailjob_action' );
    if ( !$ibMailScheduleTtimestamp ) {
    	wp_schedule_event(time(), 'ib_mail_everyminute', 'rs_indiebooking_mailjob_action');
    }
    
    add_action('rs_indiebooking_deposit_action', 'rs_indiebooking_deposit_action');
    $ibDepositScheduleAction = wp_next_scheduled( 'rs_indiebooking_deposit_action' );
    if ( !$ibDepositScheduleAction ) {
    	wp_schedule_event(time(), 'daily', 'rs_indiebooking_deposit_action');
//     	wp_schedule_event(time(), 'ib_mail_everyminute', 'rs_indiebooking_mailjob_action');
    }
    
    /*
     * Fuegt einen 120 Sekunden Scheduler ein, der prueft, ob eine Buchung schon laenger
     * kein Heartbeat mehr bekommen hat.
     */
    add_filter('cron_schedules', function ( $schedules ) {
    	$schedules['ib_heartbeat_check_every_two_minutes'] = array(
    		'interval' => 120,
    		'display' => __('Every 2 Minutes')
    	);
    	return $schedules;
    });
    add_action('rs_indiebooking_heartbeat_check_action', 'rs_ib_indiebooking_checkBookingHeartbeats');
    $ibHeartbeatcheckScheduleTtimestamp = wp_next_scheduled( 'rs_indiebooking_heartbeat_check_action' );
    if ( !$ibHeartbeatcheckScheduleTtimestamp ) {
    	wp_schedule_event(time(), 'ib_heartbeat_check_every_two_minutes', 'rs_indiebooking_heartbeat_check_action');
    }
    
//     add_action('plugins_loaded', 'indiebooking_wpml_fix_ajax_install');
    include_once( 'includes/util/rs_indiebooking_wpml_fix.php');
    
//     add_action( 'pre_get_posts', 'indiebooking_switch_wpml_lang' );
    
    return cRS_Indiebooking::instance();
    
}

/* @var $buchungTable RS_IB_Table_Appartment_Buchung */
function rs_ib_indiebooking_checkBookingHeartbeats() {
	global $RSBP_DATABASE;
	
	RS_Indiebooking_Log_Controller::write_log('check heartbeat', __LINE__, __CLASS__);
	$buchungTable       = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
	$buchungTable->checkBookingHeartbeats();
}

function rs_indiebooking_session_control() {
	if (!is_admin() && !session_id()) {
		session_start();
		if (!key_exists('indiebooking_currentBookingNr', $_SESSION)) {
			$_SESSION['indiebooking_currentBookingNr'] = '0';
		}
	}
}

/* @var $mailJobTable RS_IB_Table_MailPrintJob */
function rs_indiebooking_mailjob_action() {
	$mailController	= RS_IB_Mail_Controller::instance();
	$mailController->printAndSendAllFromJobLog();
}

function rs_indiebooking_deposit_action() {
	$paymentlData 	= get_option( 'rs_indiebooking_settings_payment');
	$depositKz		= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
	if ($depositKz == "on") {
		
	}
}

// function indiebooking_switch_wpml_lang() {
// 	global $sitepress;
// 	if (is_admin()) {
// 		if (isset($_REQUEST["lang"])) {
// 			$my_current_lang = $_REQUEST['lang'];
// 			$my_current_lang = apply_filters( 'wpml_current_language', NULL );
// 			do_action( 'wpml_switch_language',  $my_current_lang );
// 			$sitepress->switch_lang($my_current_lang);
// 		}
// 	}
// }

// function indiebooking_wpml_fix_ajax_install() {
// 	if ( function_exists('icl_object_id') ) {
// 		global $sitepress;
// 		if(defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action'])){
// 			// remove WPML legacy filter, as it is not doing its job for ajax calls
// 			// 		if (key_exists('ib_lang', $_REQUEST)) {
// 			if (isset($_REQUEST["ib_lang"])) {
// 				$my_current_lang 	 = $_REQUEST['ib_lang'];
// 				remove_filter('locale', array($sitepress, 'locale'));
// 				add_filter('locale', 'wpml_ajax_fix_locale');
// 				$my_current_lang	 = apply_filters( 'wpml_current_language', NULL );
// 				do_action( 'wpml_switch_language',  $my_current_lang );
// 				$sitepress->switch_lang($my_current_lang);
// 			}
// 			function wpml_ajax_fix_locale($locale){
// 				global $sitepress;
// 				if (isset($_REQUEST["ib_lang"])) {
// 					// simply return the locale corresponding to the "lang" parameter in the request
// 					$my_current_lang = $_REQUEST['ib_lang'];
// 					// 			$my_current_lang = apply_filters( 'wpml_current_language', NULL );
// 					// 			$my_current_lang = 'en';
// 					do_action( 'wpml_switch_language',  $my_current_lang );
// 					$sitepress->switch_lang($my_current_lang);
// 				} else {
// 					$my_current_lang = $sitepress->get_current_language();
// 				}
// 				return $sitepress->get_locale($my_current_lang);
// 			}
// 		}
// 	}
// }

function includeCSPHeader() {
//     $url = plugins_url();
//     $plugin_dir = $url."/reportcspviolation.php";
//     $csp_rules = "script-src 'self' http://localhost https://maps.googleapis.com/; " .
//         "style-src 'self' http://localhost http://fonts.googleapis.com/; " .
//         "report-uri ".$plugin_dir;
    
//     foreach (array("X-WebKit-CSP", "X-Content-Security-Policy", "Content-Security-Policy") as $csp)
//     {
//         header($csp . ": " . $csp_rules);
//     }
}

function rs_indiebooking_update_db_check() {
    RS_IB_Update::check_updates();
    if (! wp_next_scheduled ( 'rs_indiebooking_logging_cron_event' )) {
        write_log("init schedule");
        wp_schedule_event(time(), 'daily', 'rs_indiebooking_logging_cron_event');
        /*
         hourly
         twicedaily
         daily
         */
    }
//     do_action('rs_indiebooking_logging_cron_event');
}

function rs_indiebooking_activate($networkwide) {
	global $wpdb;
// 	$isMultisite = false;
	if (function_exists( 'is_multisite' ) && is_multisite() ) {
		//check if it is network activation if so run the activation function for each id
		if( $networkwide ) {
			$old_blog =  $wpdb->blogid;
			//Get all blog ids
// 			$blogids =  $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
			if ( function_exists( 'get_sites' ) && function_exists( 'get_current_network_id' ) ) {
				$blogids = get_sites( array( 'fields' => 'ids', 'network_id' => get_current_network_id() ) );
			} else {
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs WHERE site_id = $wpdb->siteid;" );
			}
			foreach ( $blogids as $blog_id ) {
				switch_to_blog($blog_id);
				//Create database table if not exists
				rs_indiebooking_create_database_tables();
			}
			switch_to_blog( $old_blog );
		} else {
			rs_indiebooking_create_database_tables();
		}
	} else {
		rs_indiebooking_create_database_tables();
	}
}

function rs_indiebooking_create_database_tables() {
	rs_indiebooking_init_options();
	cRS_IB_Install::install();
	//     RS_IB_Update::check_updates();
	rs_indiebooking_update_db_check();
}

function rs_indiebooking_deactivation() {
    wp_clear_scheduled_hook('rs_indiebooking_logging_cron_event');
}

/**
 * is_edit_page
 * function to check if the current page is a post edit page
 *
 * @author Ohad Raz <admin@bainternet.info>
 *
 * @param  string  $new_edit what page to check for accepts new - new post page ,edit - edit post page, null for either
 * @return boolean
 */
function rs_indiebooking_is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;


    if($new_edit == "edit") {
        return in_array( $pagenow, array( 'post.php',  ) );
    } elseif($new_edit == "new") { //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    } else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}


function rs_indiebooking_init_options() {
//     RS_Indiebooking_Log_Controller::write_log("init_options");
    update_option('rs_indiebooking_version', cRS_Indiebooking::RS_IB_VER);
//     $test = get_option('rs_indiebooking_version');
    if( !get_option( 'rs_indiebooking_db_version' ) ) {
//         add_option('rs_indiebooking_db_version', cRS_Indiebooking::RS_IB_DB_VER);
        add_option('rs_indiebooking_db_version', 1);
    }
    
    if( !get_option( 'rs_indiebooking_db_version' ) ) {
        //         add_option('rs_indiebooking_db_version', cRS_Indiebooking::RS_IB_DB_VER);
        add_option('rs_indiebooking_db_version', 1);
    }
    $uniqid = uniqid();
//     if( !get_option( 'rs_indiebooking_start_tour' ) ) {
//         add_option('rs_indiebooking_start_tour', cRS_Indiebooking::RS_IB_START_TOUR);
//     }
    if( !get_option( 'rs_indiebooking_settings_show_welcome_kz' ) ) {
        add_option('rs_indiebooking_settings_show_welcome_kz', "on");
    }
    if( !get_option( 'rs_indiebooking_settings_allow_statistics_kz' ) ) {
        add_option('rs_indiebooking_settings_allow_statistics_kz', "off");
    }
    
    $filterData = get_option( 'rs_indiebooking_settings_filter');
    if (!$filterData) {
    	$filterData					  = array();
    }
    $filterData['category_kz']        = (key_exists('category_kz', $filterData))        ?  esc_attr__( $filterData['category_kz'] )        : "on";
    $filterData['anzahl_betten_kz']   = (key_exists('anzahl_betten_kz', $filterData))   ?  esc_attr__( $filterData['anzahl_betten_kz'] )   : "on";
    $filterData['anzahl_personen_kz'] = (key_exists('anzahl_personen_kz', $filterData)) ?  esc_attr__( $filterData['anzahl_personen_kz'] ) : "on";
    $filterData['options_kz']         = (key_exists('options_kz', $filterData))         ?  esc_attr__( $filterData['options_kz'] )         : "on";
    $filterData['rooms_kz']           = (key_exists('rooms_kz', $filterData))           ?  esc_attr__( $filterData['rooms_kz'] )           : "on";
    $filterData['region_kz']          = (key_exists('region_kz', $filterData))          ?  esc_attr__( $filterData['region_kz'] )          : "on";
    $filterData['features_kz']        = (key_exists('features_kz', $filterData))        ?  esc_attr__( $filterData['features_kz'] )        : "off";
    
	update_option('rs_indiebooking_settings_filter', $filterData);
	
	
	$requiredFilterData = get_option( 'rs_indiebooking_settings_contact_required');
	if (!$requiredFilterData) {
		$requiredFilterData				= array();
	}
	$settingsContactRequiredFirmaKz		= (key_exists('firma', $requiredFilterData))        ?  esc_attr__( $requiredFilterData['firma'] )       : "on";
	$settingsContactRequiredAbteilungKz	= (key_exists('abteilung', $requiredFilterData))   	?  esc_attr__( $requiredFilterData['abteilung'] )   : "on";
	$settingsContactRequiredAnredeKz	= (key_exists('anrede', $requiredFilterData)) 		?  esc_attr__( $requiredFilterData['anrede'] ) 		: "on";
	$settingsContactRequiredVornameKz	= (key_exists('vorname', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['vorname'] )     : "on";
	$settingsContactRequiredNachnameKz	= (key_exists('nachname', $requiredFilterData))     ?  esc_attr__( $requiredFilterData['nachname'] )    : "on";
	$settingsContactRequiredMailKz		= (key_exists('mail', $requiredFilterData))         ?  esc_attr__( $requiredFilterData['mail'] )        : "on";
	$settingsContactRequiredAdressKz	= (key_exists('address', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['address'] )		: "on";
	$settingsContactRequiredTelefonKz	= (key_exists('telefon', $requiredFilterData))      ?  esc_attr__( $requiredFilterData['telefon'] )		: "on";
	
	update_option('rs_indiebooking_settings_contact_required', $requiredFilterData);
}

/**
 * Gibt den gewuenschten Wert aus $_POST[$post_id] zurueck.
 * Ist der Wert nicht gesetzt, wird $default zurueckkegeben.
 * @param unknown $post_id
 * @param string $default
 * @return unknown|string
 */
function rsbp_getPostValue($post_id, $default = null, $type = "ALL") {
    if (isset($_POST[$post_id])) {
        return RS_IB_Data_Validation::check_with_whitelist($_POST[$post_id], $type);
    }
    return $default;
}

function rsbp_getGetValue($get_id, $default = null, $type = "ALL") {
    if (isset($_GET[$get_id])) {
        return RS_IB_Data_Validation::check_with_whitelist($_GET[$get_id], $type);
    }
    return $default;
}

function rsbp_getRequestValue($request_id, $default = null, $type = "ALL") {
    if (isset($_GET[$get_id])) {
        return RS_IB_Data_Validation::check_with_whitelist($_REQUEST[$request_id], $type);
    }
    return $default;
}

$GLOBALS['buchungsplattform'] = RS_INDIEBOOKING_INIT(); // Global for backwards compatibility.
?>
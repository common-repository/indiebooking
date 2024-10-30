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
/**
 * Der DatabaseController ist dafuer zustuendig, alle nuetigen Datenbankklassen eingebunden werden.
 * Auueerdem stellt er die Tabellen zur Kommunikation mit eben diesen Tabellen bereit.
 *
 */
// if ( ! class_exists( 'RS_IB_DatabaseController' ) ) :
class RS_IB_DatabaseController
{
    public function __construct() {
        if (ABSPATH !== 'TEST') {
            add_action( 'init', array($this, 'initializeDatabaseTables'), 0 );
            add_action( 'switch_blog', array($this, 'initializeDatabaseTables'), 0 );
        }

        add_action( 'rs_indiebooking_include_db_model', array($this, 'includeModels'), 5);
        add_action( 'rs_indiebooking_include_db_table', array($this, 'includeTables'), 5);
        add_filter( 'rs_indiebooking_get_database_table', array($this, 'getDatabaseTable'), 10, 1);
        
        include_once( 'cRS_IB_Buchungsposition.php' );
            
        include_once( 'cRS_IB_Model_Meta.php' );
        include_once( 'cRS_IB_Model_Postmeta.php' );
        include_once( 'cRS_IB_Model_Termmeta.php' );
        include_once( 'table/cRS_IB_Table_Postmeta.php' );
        include_once( 'table/cRS_IB_Table_Termmeta.php' );
        
        add_action( 'plugins_loaded', array($this, 'includeAllModels'), 5);
        
//         include_once( 'cRS_IB_Table_Booking.php' );
    }

    public function includeAllModels() {
        //die Models und Table dürfen erst eingebunden werden, wenn alle Plugins geladen wurden.
        //Da es sonst passieren kann, dass noch nicht alle Inkludes zu der Action hinzugefügt wurden.
        do_action('rs_indiebooking_include_db_model');
        do_action('rs_indiebooking_include_db_table');
    }
    
    public function includeModels() {
        include_once( 'cRS_IB_Model_Appartment_Zeitraeume.php' );
        include_once( 'cRS_IB_Model_Appartment.php' );
        include_once( 'cRS_IB_Model_Appartmentkategorie.php' );
        include_once( 'cRS_IB_Model_Appartment_Buchung.php' );
        include_once( 'cRS_IB_Model_Mwst.php' );
        include_once( 'cRS_IB_Model_Storno.php' );
        include_once( 'cRS_IB_Model_Booking_Header.php' );
        include_once( 'cRS_IB_Model_Booking_Position.php' );
        
        
        include_once( 'custom/cRS_IB_Model_Oberbuchungkopf.php' );
        include_once( 'custom/cRS_IB_Model_Buchungskopf.php' );
        include_once( 'custom/cRS_IB_Model_Teilbuchungskopf.php' );
        include_once( 'custom/cRS_IB_Model_Buchungposition.php' );
        include_once( 'custom/cRS_IB_Model_BuchungMwSt.php' );
        include_once( 'custom/cRS_IB_Model_BuchungRabatt.php' );
        include_once( 'custom/cRS_IB_Model_BuchungZahlung.php' );
        include_once( 'custom/cRS_IB_Model_Apartment_Gesperrter_Zeitraum.php');
        include_once( 'custom/cRS_IB_Model_ErrorLog.php' );
        include_once( 'custom/cRS_IB_Model_MailPrintJob.php' );
//         include_once( 'custom/cRS_IB_Model_Appartment_Saison.php');
//         include_once( 'custom/cRS_IB_Model_ApartmentDegression.php' );
    }
    
    public function includeTables() {
        include_once( 'custom/table/cRS_IB_Table_Oberbuchungkopf.php' );
        include_once( 'custom/table/cRS_IB_Table_Buchungskopf.php' );
        include_once( 'custom/table/cRS_IB_Table_Teilbuchungskopf.php' );
        include_once( 'custom/table/cRS_IB_Table_Buchungposition.php' );
        include_once( 'custom/table/cRS_IB_Table_BuchungRabatt.php' );
        include_once( 'custom/table/cRS_IB_Table_BuchungMwSt.php' );
        include_once( 'custom/table/cRS_IB_Table_BuchungZahlung.php' );
        include_once( 'custom/table/cRS_IB_Table_Apartment_Gesperrter_Zeitraum.php');
        
        include_once( 'custom/table/cRS_IB_Table_ErrorLog.php' );
        include_once( 'custom/table/cRS_IB_Table_MailPrintJob.php' );
        
//         include_once( 'custom/table/cRS_IB_Table_ApartmentDegression.php' );
//         include_once( 'custom/table/cRS_IB_Table_Appartment_Saison.php');
        
        //         include_once( 'custom/cRS_IB_Buchung_Controller.php' );
        
        include_once( 'table/cRS_IB_Table_Appartment_Zeitraeume.php' );
        include_once( 'table/cRS_IB_Table_Appartment.php' );
        include_once( 'table/cRS_IB_Table_Appartmentkategorie.php' );
        include_once( 'table/cRS_IB_Table_Appartment_Buchung.php' );
        include_once( 'table/cRS_IB_Table_Mwst.php' );
        include_once( 'table/cRS_IB_Table_Storno.php' );
    }
    
    public function initializeDatabaseTables() {
        global $wpdb;
        $termmeta_name          = 'rewabp_termmeta';
        
        $wpdb->rewabp_termmeta  = $wpdb->prefix . $termmeta_name;
        $wpdb->tables[]         = $termmeta_name; //'rewabp_termmeta';
        
        $appartmentZeitraeume   = 'rewabp_appartment_buchungszeitraum';
        $wpdb->rewabp_appartment_buchungszeitraum = $wpdb->prefix . $termmeta_name;
        $wpdb->tables[]         = $appartmentZeitraeume;
    }
    
    public function getDatabaseTable($table = false) {
        $instance   = $table;
        if (is_string($table)) {
            switch ($table) {
                case RS_IB_Model_Appartment::RS_TABLE:
                    $instance = RS_IB_Table_Appartment::instance();
                    break;
                case RS_IB_Model_Appartment_Buchung::RS_TABLE:
                    $instance = RS_IB_Table_Appartment_Buchung::instance();
                    break;
                case RS_IB_Model_Mwst::RS_TABLE:
                    $instance = RS_IB_Table_Mwst::instance();
                    break;
                case RS_IB_Model_Storno::RS_TABLE:
                    $instance = RS_IB_Table_Storno::instance();
                    break;
                case RS_IB_Model_Appartment_Zeitraeume::RS_TABLE:
                    $instance = RS_IB_Table_Appartment_Zeitraeume::instance();
                    break;
                case RS_IB_Model_Booking_Header::RS_TABLE:
                    $instance = RS_IB_Table_Booking::instance();
                    break;
                case RS_IB_Model_Buchungskopf::RS_TABLE:
                    //                 return RS_IB_Table_Booking::instance();
                    $instance = RS_IB_Table_Buchungskopf::instance();
                    break;
                case RS_IB_Model_Teilbuchungskopf::RS_TABLE:
                    //                 return RS_IB_Table_Booking::instance();
                    $instance = RS_IB_Table_Teilbuchungskopf::instance();
                    break;
                case RS_IB_Model_Buchungposition::RS_TABLE:
                    //                 return RS_IB_Table_Booking::instance();
                    $instance = RS_IB_Table_Buchungposition::instance();
                    break;
                case RS_IB_Model_BuchungMwSt::RS_TABLE:
                    $instance = RS_IB_Table_BuchungMwSt::instance();
                    break;
                case RS_IB_Model_BuchungZahlung::RS_TABLE:
                    $instance = RS_IB_Table_BuchungZahlung::instance();
                    break;
                case RS_IB_Model_BuchungRabatt::RS_TABLE:
                    $instance = RS_IB_Table_BuchungRabatt::instance();
                    break;
                case RS_IB_Model_Appartmentkategorie::RS_TABLE:
                    $instance = RS_IB_Table_Appartmentkategorie::instance();
                    break;
                case RS_IB_Model_Oberbuchungkopf::RS_TABLE:
                    $instance = RS_IB_Table_Oberbuchungkopf::instance();
                    break;
                case RS_IB_Model_Apartment_Gesperrter_Zeitraum::RS_TABLE:
                    $instance = RS_IB_Table_Apartment_Gesperrter_Zeitraum::instance();
                    break;
                case RS_IB_Model_ErrorLog::RS_TABLE:
                	$instance = RS_IB_Table_ErrorLog::instance();
                	break;
                case RS_IB_Model_MailPrintJob::RS_TABLE:
                	$instance = RS_IB_Table_MailPrintJob::instance();
                	break;
                default:
                    $instance   = $table;
            }
        }
        return $instance;
    }
    
    public function getTable($table) {
        $instance = apply_filters('rs_indiebooking_get_database_table', $table);
        return $instance;
    }
}
// endif;

global $RSBP_DATABASE;
global $RSBP_TABLEPREFIX;

$RSBP_TABLEPREFIX   = "rs_indiebooking_";
$RSBP_DATABASE      = new RS_IB_DatabaseController();
?>
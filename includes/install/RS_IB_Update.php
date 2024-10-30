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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*
 * register_activation_hook = Fuehrt eine Aktion aus, die beim aktivieren des Plugins geschehen soll
 */
// if ( ! class_exists( 'RS_IB_Update' ) ) :
/**
 * Diese Klasse stellt alle Methoden zur Verfuegung die fuer die Installation des Plugins nuetig sind.
 */
class RS_IB_Update {
	
    public static function check_updates() {
        $updateObj = new RS_IB_Update();
        $updateObj->do_updates();
//         self::testUserRole();
    }
    
	/**
	 * Install RS_IB
	 */
	function do_updates() {
// 		global $wpdb;
// 	    set_time_limit( 0 );
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php'); //ansonsten ist dbDelta evtl nicht da
		
	    $current_db_version    = get_option('rs_indiebooking_db_version');
	    if (!$current_db_version) {
	        $current_db_version = 1;
	    }
// 	    $current_db_version = 22;
	    $target_db_version     = cRS_Indiebooking::RS_IB_DB_VER;
	    if ($current_db_version < $target_db_version) {
		    while( $current_db_version < $target_db_version) {
		        $current_db_version++;
		        $func              = "rs_ib_update_routine_{$current_db_version}";
		        if (method_exists('RS_IB_Update', $func)) {
		            call_user_func( array( 'RS_IB_Update', $func));
	    	        update_option('rs_indiebooking_db_version', $current_db_version);
		        } else {
	// 	            echo "oooohweh";
		        }
		    }
	    	update_option('rs_indiebooking_db_version', $target_db_version);
	    }
	}
	
	public static function rs_ib_update_routine_2() {
	    global $wpdb;
	    global $RSBP_TABLEPREFIX;
	    
	    
	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';//$wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	         
	        $sql = "CREATE TABLE $table_name (
	        meta_id bigint(20) NOT NULL AUTO_INCREMENT,
	        post_id bigint(20) NOT NULL,
	        position_id bigint(20) NOT NULL,
	        date_from datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        date_to datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        PRIMARY KEY (meta_id),
	        CONSTRAINT rsib_appartmentinfo UNIQUE (post_id,position_id));";
	    
	        dbDelta( $sql );
	    }
	    
	    
	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_saison';//$wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    
	        $sql = "CREATE TABLE $table_name (
	        meta_id bigint(20) NOT NULL AUTO_INCREMENT,
	        post_id bigint(20) NOT NULL,
	        position_id bigint(20) NOT NULL,
	        date_from varchar(6) NOT NULL DEFAULT '',
	        date_to varchar(6) NOT NULL DEFAULT '',
	        fulldate_from datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        fulldate_to datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        meta_value DECIMAL(7,2) NOT NULL DEFAULT 0,
	        valid_from integer(4) NOT NULL,
	        added_automatic_kz INT(1) NOT NULL DEFAULT 0,
	        PRIMARY KEY (meta_id),
	        CONSTRAINT rsib_appartmentsaisoninfo UNIQUE (post_id,position_id));";

	        dbDelta( $sql );
	    }
	    
// 	    $table_name = $wpdb->prefix . 'rewabp_booking_header';
	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        	
// 	        $sql = "CREATE TABLE $table_name (
//     	        booking_id bigint(20) NOT NULL,
//     	        booking_status varchar(20) NOT NULL DEFAULT 'open',
//     	        date_from datetime NOT NULL DEFAULT 0,
//     	        date_to datetime NOT NULL DEFAULT 0,
//     	        number_nights SMALLINT NOT NULL DEFAULT 0,
//     	        customer_name varchar(255) NOT NULL DEFAULT '',
//     	        customer_first_name varchar(255) NOT NULL DEFAULT '',
//     	        customer_location varchar(255) NOT NULL DEFAULT '',
//     	        customer_email varchar(255) NOT NULL DEFAULT '',
//     	        customer_telefon varchar(255) NOT NULL DEFAULT '',
//     	        customer_strasse  varchar(255) NOT NULL DEFAULT '',
//     	        PRIMARY KEY (booking_id)
// 	        );";

	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
	        buchung_status varchar(20) NOT NULL DEFAULT 'open',
	        buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
	        kunde_firma varchar(255) DEFAULT '',
	        kunde_titel varchar(255) DEFAULT '',
	        kunde_anrede varchar(255) NOT NULL DEFAULT '',
	        kunde_name varchar(255) NOT NULL DEFAULT '',
	        kunde_vorname varchar(255) NOT NULL DEFAULT '',
	        kunde_strasse  varchar(255) NOT NULL DEFAULT '',
	        kunde_plz varchar(30) NOT NULL DEFAULT '',
	        kunde_ort varchar(255) NOT NULL DEFAULT '',
	        kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
	        kunde_land varchar(255) NOT NULL DEFAULT '',
	        kunde_email varchar(255) NOT NULL DEFAULT '',
	        kunde_telefon varchar(255) NOT NULL DEFAULT '',
	        use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
	        kunde_titel2 varchar(255) DEFAULT '',
	        kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
	        kunde_name2 varchar(255) NOT NULL DEFAULT '',
	        kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
	        kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
	        kunde_plz2 varchar(30) NOT NULL DEFAULT '',
	        kunde_ort2 varchar(255) NOT NULL DEFAULT '',
	        kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
	        kunde_land2 varchar(255) NOT NULL DEFAULT '',
	        kunde_email2 varchar(255) NOT NULL DEFAULT '',
	        kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
	        buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
	        nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
	        buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
	        PRIMARY KEY (buchung_nr));";
	        dbDelta( $sql );
	    }
	    
// 	    $table_name = $wpdb->prefix . 'rewabp_booking_position';
	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
// 	    $buchungKopfTbl = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        teilbuchung_id bigint(20) NOT NULL,
	        teilbuchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        teilbuchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        appartment_id bigint(20) NOT NULL,
	        appartment_name varchar(255) NOT NULL,
	        appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,
	        teilbuchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        anzahl_personen INT NOT NULL DEFAULT '0',
	        PRIMARY KEY (buchung_nr, teilbuchung_id));";
	        
	        dbDelta( $sql );
	        
// 	        $sql = "CREATE TABLE $table_name (
// 	        buchung_nr bigint(20) NOT NULL,
// 	        teilbuchung_id bigint(20) NOT NULL AUTO_INCREMENT,
// 	        teilbuchung_von datetime NOT NULL DEFAULT 0,
// 	        teilbuchung_bis datetime NOT NULL DEFAULT 0,
// 	        appartment_id bigint(20) NOT NULL,
// 	        appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,

// 	        PRIMARY KEY (buchung_nr, teilbuchung_id)
// 	        ADD INDEX(`teilbuchung_id`);
// 	        );";
	        
// 	        $sql = "CREATE TABLE $table_name (
// 	        booking_id bigint(20) NOT NULL,
// 	        position_id bigint(20) NOT NULL,
// 	        position_type varchar(255) NOT NULL,
// 	        appartment_id bigint(20) NOT NULL,
// 	        appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,
// 	        date_from datetime NOT NULL DEFAULT 0,
// 	        date_to datetime NOT NULL DEFAULT 0,
// 	        number_nights SMALLINT NOT NULL DEFAULT 0,
// 	        price DECIMAL(3,2) NOT NULL DEFAULT 0,
//             mwst_percent DECIMAL(3,2) NOT NULL,
//             calc_type SMALLINT(1) NOT NULL DEFAULT 1,
//             calculation SMALLINT(1) NOT NULL DEFAULT 1,
//             discount_kz SMALLINT(1) NOT NULL DEFAULT 0,
//             meta_value varchar(255) NOT NULL DEFAULT '',
// 	        PRIMARY KEY (booking_id,position_id)
// 	        );";
	    }
	    
	    $table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
// 	    $teilbuchungKopfTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        teilbuchung_id bigint(20) NOT NULL,
	        position_id bigint(20) NOT NULL,
	        position_type varchar(255) NOT NULL,
	        bezeichnung varchar(255) NOT NULL,
	        preis_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
            preis_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
	        einzelpreis DECIMAL(10,2) NOT NULL DEFAULT 0,
	        berechnung_type SMALLINT(1) NOT NULL DEFAULT 1,
	        mwst_prozent DECIMAL(5,2) NOT NULL,
	        mwst_termId bigint(20) NOT NULL DEFAULT 0,
	        rabatt_kz SMALLINT(1) NOT NULL DEFAULT 0,
	        position_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        berechneter_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        data_id bigint(20) NOT NULL DEFAULT 0,
	        full_storno SMALLINT(1) NOT NULL DEFAULT 0,
	        PRIMARY KEY (buchung_nr, teilbuchung_id, position_id));";

	        dbDelta( $sql );
	        
	        //buchung_nr hinzugefuegt, primary key angepasst, Index entfernt
	        //AUTO_INCREMENT von position_id entfernt. Muss selbst ermittelt werden!
	        
	        /* FOREIGN KEY mal noch weg lassen */
// 	        $sqlAlter = "ALTER TABLE $table_name
//     	        ADD CONSTRAINT buchungposition_FK
//     	        FOREIGN KEY (teilbuchung_id)
//     	        REFERENCES $teilbuchungKopfTbl(teilbuchung_id);
// 	        ";
// 	        dbDelta( $sqlAlter );
	    }
	    
	    $table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        rabatt_id bigint(20) NOT NULL AUTO_INCREMENT,
	        buchung_nr bigint(20) NOT NULL,
	        teilbuchung_id bigint(20) NOT NULL DEFAULT 0,
	        position_id bigint(20)  NOT NULL DEFAULT 0,
	        bezeichnung varchar(255) NOT NULL,
	        rabatt_wert DECIMAL(10,2) NOT NULL,
	        rabatt_typ SMALLINT(1) NOT NULL DEFAULT 1,
	        rabatt_art SMALLINT(1) NOT NULL DEFAULT 1,
	        berechnung_type SMALLINT(1) NOT NULL DEFAULT 1,
	        plus_minus_kz SMALLINT(1) NOT NULL DEFAULT 1,
	        ausschreiben_kz SMALLINT(1) NOT NULL DEFAULT 1,
	        gueltig_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        gueltig_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        rabatt_term_id bigint(20) NOT NULL,
	        valid_at_storno SMALLINT(1) NOT NULL DEFAULT 1,
	        PRIMARY KEY (rabatt_id));";
	    
	        dbDelta( $sql );
	    }
	    
	    $table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        zahlung_nr bigint(20) NOT NULL,
	        zahlungsart SMALLINT(1) NOT NULL DEFAULT 0,
	        zahlungsbetrag DECIMAL(10,2) NOT NULL,
	        zahlungszeitpunkt datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        bezeichnung varchar(255) NOT NULL,
	        PRIMARY KEY (buchung_nr, zahlung_nr));";

	        dbDelta( $sql );
	    }
	    
	    $table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_mwst';
	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        mwst_id bigint(20) NOT NULL,
	        mwst_prozent DECIMAL(5,2) NOT NULL,
	        mwst_wert DECIMAL(10,2) NOT NULL,
	        PRIMARY KEY (buchung_nr, mwst_id));";

	        dbDelta( $sql );
	    }
	}
	
	
	public static function rs_ib_update_routine_3() {
	    //wurde in routine 4 verschoben, da sonst nicht jeder korrekt das update bekommt
// 	    global $wpdb;
// 	    global $RSBP_TABLEPREFIX;
	    
	    
// 	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_saison';
// 	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
//             $sql    = "ALTER TABLE $table_name ADD valid_to int(4) NOT NULL DEFAULT 0";
            
//             dbDelta( $sql );
// 	    }
	}
	
	public static function rs_ib_update_routine_4() {
	    global $wpdb;
	    global $RSBP_TABLEPREFIX;
	     
	     
	    $table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_saison';
	    $result = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
	    if ($result == $table_name) {
// 	        $sql    = "ALTER TABLE $table_name ADD valid_to int(4) NOT NULL DEFAULT 0";
// 	        dbDelta( $sql );

	        $sql = "CREATE TABLE $table_name (
	        meta_id bigint(20) NOT NULL AUTO_INCREMENT,
	        post_id bigint(20) NOT NULL,
	        position_id bigint(20) NOT NULL,
	        date_from varchar(6) NOT NULL DEFAULT '',
	        date_to varchar(6) NOT NULL DEFAULT '',
	        fulldate_from datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        fulldate_to datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        meta_value DECIMAL(7,2) NOT NULL DEFAULT 0,
	        valid_from integer(4) NOT NULL,
	        valid_to int(4) NOT NULL DEFAULT 0,
	        added_automatic_kz INT(1) NOT NULL DEFAULT 0,
	        PRIMARY KEY (meta_id));";
	        //CONSTRAINT rsib_appartmentsaisoninfo UNIQUE (post_id, position_id)
	        dbDelta( $sql );
	        
	    }
	}
	
	
	public static function rs_ib_update_routine_5() {
	    global $wpdb;
	    global $RSBP_TABLEPREFIX;
	    $table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungposition';
	    // 	    $teilbuchungKopfTbl    = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
	    $result = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
	    if ($result == $table_name) {
	        $sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        teilbuchung_id bigint(20) NOT NULL,
	        position_id bigint(20) NOT NULL,
	        position_type varchar(255) NOT NULL,
	        bezeichnung varchar(255) NOT NULL,
	        preis_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        preis_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
	        einzelpreis DECIMAL(10,2) NOT NULL DEFAULT 0,
	        berechnung_type SMALLINT(1) NOT NULL DEFAULT 1,
	        mwst_prozent DECIMAL(5,2) NOT NULL,
	        mwst_termId bigint(20) NOT NULL DEFAULT 0,
	        rabatt_kz SMALLINT(1) NOT NULL DEFAULT 0,
	        position_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        berechneter_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
	        data_id bigint(20) NOT NULL DEFAULT 0,
	        full_storno SMALLINT(1) NOT NULL DEFAULT 0,
	        kommentar varchar(255) NOT NULL DEFAULT '',
	        PRIMARY KEY (buchung_nr, teilbuchung_id, position_id));";
	    
	        dbDelta( $sql );
	    }
	}
	
	
	public static function rs_ib_update_routine_6() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_firma varchar(255) DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql = "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL,
			teilbuchung_id bigint(20) NOT NULL,
			teilbuchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			teilbuchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			appartment_id bigint(20) NOT NULL,
			appartment_name varchar(255) NOT NULL,
			appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,
			teilbuchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			anzahl_personen INT NOT NULL DEFAULT '0',
			bcom_roomid BIGINT(20) NOT NULL DEFAULT '0',
			PRIMARY KEY (buchung_nr, teilbuchung_id));";
			 
			dbDelta( $sql );
		}
	}
	
	public static function rs_ib_update_routine_7() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
 		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_degression';
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result != $table_name) {
			$sql = "CREATE TABLE $table_name (
			degressionId BIGINT(20) NOT NULL DEFAULT '0',
			apartmentId bigint(20) NOT NULL,
			bedingungswert bigint(20) NOT NULL DEFAULT '0',
			bedingungstyp SMALLINT(1) NOT NULL,
			degressionswert DECIMAL(10,2) NOT NULL DEFAULT 0,
			berechnunsgtyp SMALLINT(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (degressionId, apartmentId));";
		
			try {
				$resultArray = dbDelta( $sql );
// 				if (sizeof($resultArray) > 0) {
// 					var_dump($resultArray);
// 				}
			} catch (Exception $e) {
				var_dump($e->getMessage());
			}
		}
	}
	
	public static function rs_ib_update_routine_8() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0
			PRIMARY KEY (buchung_nr));";
				
			dbDelta( $sql );
		}
		
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'teilbuchungskopf';
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql = "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL,
			teilbuchung_id bigint(20) NOT NULL,
			teilbuchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			teilbuchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			appartment_id bigint(20) NOT NULL,
			appartment_name varchar(255) NOT NULL,
			appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,
			teilbuchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			anzahl_personen INT NOT NULL DEFAULT '0',
			bcom_roomid BIGINT(20) NOT NULL DEFAULT '0',
			gast_name varchar(255) NOT NULL DEFAULT ''
			PRIMARY KEY (buchung_nr, teilbuchung_id));";
		
			dbDelta( $sql );
		}
	}
	
	public static function rs_ib_update_routine_9() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'errorlog';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result != $table_name) {
			$sql = "CREATE TABLE $table_name (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			date datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			text varchar(255) NOT NULL DEFAULT '',
			class varchar(255) NOT NULL DEFAULT '',
			line varchar(255) NOT NULL DEFAULT '',
			extra_id varchar(255) NOT NULL DEFAULT '',
			extra_text varchar(255) NOT NULL DEFAULT '',
			type varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (id));";
		
			dbDelta( $sql );
		}
	}
	
	public static function rs_ib_update_routine_10() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY (buchung_nr));";
		
			dbDelta( $sql );
		}
		
	}
	
	public static function rs_ib_update_routine_11() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			PRIMARY KEY (buchung_nr));";
	
			dbDelta( $sql );
		}
	
	}
	
	
	
	public static function rs_ib_update_routine_12() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
	
			dbDelta( $sql );
		}
	
	}
	
	public static function rs_ib_update_routine_13() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
	
			dbDelta( $sql );
		}
	
	}
	
	public static function rs_ib_update_routine_14() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
	
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
	
			dbDelta( $sql );
		}
	
// 		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_READER);
// 		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST);
// 		$result = add_role(
// 			RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST,
// 			__( 'Basic Indiebooking Customer' ),
// 			array(
// 				'indiebooking_customer_show_bookings' => true
// 			)
// 		);

// 		createUserRoles();
	}
	
	public static function rs_ib_update_routine_15() {
		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_READER);
		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST);
		$result = add_role(
			RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST,
				__( 'Basic Indiebooking Customer' ),
			array(
				'indiebooking_customer_show_bookings' => true
			)
		);
		self::createUserRoles();
	}
	
	public static function rs_ib_update_routine_16() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_rabatt';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        rabatt_id bigint(20) NOT NULL AUTO_INCREMENT,
	        buchung_nr bigint(20) NOT NULL,
	        teilbuchung_id bigint(20) NOT NULL DEFAULT 0,
	        position_id bigint(20)  NOT NULL DEFAULT 0,
	        bezeichnung varchar(255) NOT NULL,
	        rabatt_wert DECIMAL(10,2) NOT NULL,
	        rabatt_typ SMALLINT(1) NOT NULL DEFAULT 1,
	        rabatt_art SMALLINT(1) NOT NULL DEFAULT 1,
	        berechnung_type SMALLINT(1) NOT NULL DEFAULT 1,
	        plus_minus_kz SMALLINT(1) NOT NULL DEFAULT 1,
	        ausschreiben_kz SMALLINT(1) NOT NULL DEFAULT 1,
	        gueltig_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        gueltig_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        rabatt_term_id bigint(20) NOT NULL,
	        valid_at_storno SMALLINT(1) NOT NULL DEFAULT 1,
			rabatt_option_id bigint(20) NOT NULL DEFAULT 0,
	        PRIMARY KEY (rabatt_id));";
			
			dbDelta( $sql );
		}
	}
	
	public static function rs_ib_update_routine_17() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
		}
		
	}
	
	public static function rs_ib_update_routine_18() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_degression';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
// 		if ($result != $table_name) {
			$sql = "CREATE TABLE $table_name (
			degressionId BIGINT(20) NOT NULL DEFAULT '0',
			apartmentId bigint(20) NOT NULL,
			bedingungswert bigint(20) NOT NULL DEFAULT '0',
			bedingungstyp SMALLINT(1) NOT NULL,
			degressionswert DECIMAL(10,2) NOT NULL DEFAULT 0,
			berechnunsgtyp SMALLINT(1) NOT NULL DEFAULT 0,
			bookingcomrateid BIGINT(20) NOT NULL DEFAULT '0',
			PRIMARY KEY (degressionId, apartmentId));";
			
			try {
				$resultArray = dbDelta( $sql );
			} catch (Exception $e) {
				var_dump($e->getMessage());
			}
// 		}
	}
	
	public static function rs_ib_update_routine_19() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			rechnungsdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
		}
		
	}
	
	public static function rs_ib_update_routine_20() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_abteilung varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_abteilung2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			rechnungsdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
		}
		
	}
	
	public static function rs_ib_update_routine_21() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result != $table_name) {
			$sql = "CREATE TABLE $table_name (
			jobId BIGINT(20) NOT NULL AUTO_INCREMENT,
			bookingPostId bigint(20) NOT NULL DEFAULT '0',
			printType bigint(20) NOT NULL DEFAULT '0',
			
			PRIMARY KEY (jobId, bookingPostId));";
			
			dbDelta( $sql );
		}
	
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_abteilung varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_abteilung2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			rechnungsdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			createdBilKz SMALLINT(10) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
		}
		
		
	}
	
	public static function rs_ib_update_routine_22() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'mailprintjob';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql = "CREATE TABLE $table_name (
			jobId BIGINT(20) NOT NULL AUTO_INCREMENT,
			bookingPostId bigint(20) NOT NULL DEFAULT '0',
			printType bigint(20) NOT NULL DEFAULT '0',
			printLanguage varchar(5) NOT NULL DEFAULT '',
			PRIMARY KEY (jobId, bookingPostId));";
			
			dbDelta( $sql );
		}
	}
	
	
	public static function rs_ib_update_routine_23() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		global $RSBP_DATABASE;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_abteilung varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_abteilung2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			rechnungsdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			createdBilKz SMALLINT(10) NOT NULL DEFAULT 0,
			post_id BIGINT(20) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
			
			/* @var $buchungsKopfTbl RS_IB_Table_Buchungskopf */
			/* @var $buchungsKopf RS_IB_Model_Buchungskopf */
			/* @var $apartmentBuchungTbl RS_IB_Table_Appartment_Buchung */
			$buchungsKopfTbl    	= $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
			$apartmentBuchungTbl    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
			
			$allBuchungskoepfe		= $buchungsKopfTbl->loadAllBookings();
			foreach ($allBuchungskoepfe as $buchungsKopf) {
				$buchungNr			= $buchungsKopf->getBuchung_nr();
				$postId				= $apartmentBuchungTbl->getBookingPostIdByBuchungNr($buchungNr);
				if (!is_null($postId)) {
					$buchungsKopf->setPostId($postId);
					$buchungsKopfTbl->saveOrUpdateBuchungskopf($buchungsKopf);
				}
			}
		}
	}
	
	
	public static function rs_ib_update_routine_24() {
		global $wpdb;
		global $RSBP_TABLEPREFIX;
		global $RSBP_DATABASE;
		
		$table_name = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchungskopf';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$result 	= $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		if ($result == $table_name) {
			$sql 	= "CREATE TABLE $table_name (
			user_id BIGINT(20) NOT NULL DEFAULT '0',
			rechnungNr BIGINT(20) NOT NULL DEFAULT '0',
			buchung_nr bigint(20) NOT NULL AUTO_INCREMENT,
			buchung_status varchar(20) NOT NULL DEFAULT 'open',
			buchung_von datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			buchung_bis datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
			anzahl_naechte SMALLINT NOT NULL DEFAULT 0,
			kunde_typ varchar(50) NOT NULL DEFAULT '',
			kunde_firma varchar(255) DEFAULT '',
			kunde_abteilung varchar(255) DEFAULT '',
			kunde_firma_nr varchar(50) NOT NULL DEFAULT '',
			kunde_firma_nr_typ varchar(255) NOT NULL DEFAULT '',
			kunde_titel varchar(255) DEFAULT '',
			kunde_anrede varchar(255) NOT NULL DEFAULT '',
			kunde_name varchar(255) NOT NULL DEFAULT '',
			kunde_vorname varchar(255) NOT NULL DEFAULT '',
			kunde_strasse  varchar(255) NOT NULL DEFAULT '',
			kunde_plz varchar(30) NOT NULL DEFAULT '',
			kunde_ort varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr varchar(10) NOT NULL DEFAULT '',
			kunde_land varchar(255) NOT NULL DEFAULT '',
			kunde_email varchar(255) NOT NULL DEFAULT '',
			kunde_telefon varchar(255) NOT NULL DEFAULT '',
			use_adress2 char(1) NOT NULL DEFAULT 0,
			kunde_firma2 varchar(255) DEFAULT '',
			kunde_abteilung2 varchar(255) DEFAULT '',
			kunde_titel2 varchar(255) DEFAULT '',
			kunde_anrede2 varchar(255) NOT NULL DEFAULT '',
			kunde_name2 varchar(255) NOT NULL DEFAULT '',
			kunde_vorname2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse2 varchar(255) NOT NULL DEFAULT '',
			kunde_plz2 varchar(30) NOT NULL DEFAULT '',
			kunde_ort2 varchar(255) NOT NULL DEFAULT '',
			kunde_strasse_nr2 varchar(10) NOT NULL DEFAULT '',
			kunde_land2 varchar(255) NOT NULL DEFAULT '',
			kunde_email2 varchar(255) NOT NULL DEFAULT '',
			kunde_telefon2 varchar(255) NOT NULL DEFAULT '',
			buchung_wert DECIMAL(10,2) NOT NULL DEFAULT 0,
			hauptzahlungsart varchar(255) NOT NULL DEFAULT '',
			nutzungsbedingung_kz SMALLINT(1) NOT NULL DEFAULT 0,
			buchungdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			rechnungsdatum DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00',
			bcom_reservationid BIGINT(20) NOT NULL DEFAULT '0',
			bcom_synchronizedkz SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_booking SMALLINT(1) NOT NULL DEFAULT 0,
			bcom_genius_booker SMALLINT(1) NOT NULL DEFAULT 0,
			charge_id varchar(255) NOT NULL DEFAULT '',
			custom_text text NOT NULL,
			admin_kz varchar(5) NOT NULL DEFAULT 0,
			user_medium_kz varchar(1) NOT NULL DEFAULT '',
			ignore_minimum_period SMALLINT(1) NOT NULL DEFAULT 0,
			allow_past_booking SMALLINT(1) NOT NULL DEFAULT 0,
			change_bill_date SMALLINT(1) NOT NULL DEFAULT 0,
			booking_type SMALLINT(10) NOT NULL DEFAULT 1,
			createdBilKz SMALLINT(10) NOT NULL DEFAULT 0,
			post_id BIGINT(20) NOT NULL DEFAULT 0,
			anzahlung DECIMAL(10,2) NOT NULL DEFAULT 0,
			anzahlungMailKz char(1) NOT NULL DEFAULT 0,
			anzahlungBezahltKz char(1) NOT NULL DEFAULT 0,
			PRIMARY KEY (buchung_nr));";
			
			dbDelta( $sql );
			
			/*
			 * Update Carsten Schmitt 18.09.2018
			 * Damit die bisherigen Buchungen nicht von der neuen Funktionalitaet falsch
			 * behandelt werden, setzen wir die Kennzeichen direkt auf 1 bzw. 2
			 * anzahlungBezahltKz = 2 gibt an, dass diese Buchung keine Anzahlungsbuchung war
			 */
			$sql = "UPDATE $table_name SET anzahlungBezahltKz = 2";
			dbDelta( $sql );
			$sql = "UPDATE $table_name SET anzahlungMailKz = 1";
			dbDelta( $sql );
		}
		
		
		
		$table_name            = $wpdb->prefix . $RSBP_TABLEPREFIX . 'buchung_zahlungen';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$sql = "CREATE TABLE $table_name (
	        user_id BIGINT(20) NOT NULL DEFAULT '0',
	        buchung_nr bigint(20) NOT NULL,
	        zahlung_nr bigint(20) NOT NULL,
	        zahlungsart SMALLINT(1) NOT NULL DEFAULT 0,
	        zahlungsbetrag DECIMAL(10,2) NOT NULL,
	        zahlungszeitpunkt datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
	        bezeichnung varchar(255) NOT NULL,
			charge_id varchar(255) NOT NULL DEFAULT '',
			status varchar(255) NOT NULL DEFAULT '',
	        PRIMARY KEY (buchung_nr, zahlung_nr));";
			
			dbDelta( $sql );
		}
		
	}
	
	
	private static function createUserRoles() {
		/*
		 * Die Rolle soll entfertn werden, wenn diese bereits existiert.
		 * Anschliessend wird diese dann neu erstellt.
		 * Es gibt keine andere Moeglichkeit in Wordpress eine Rolle zu aktualisieren.
		 */
		if (is_null(get_role(RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST))) {
			$result = add_role(
				RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST,
				__( 'Basic Indiebooking Customer' ),
				array(
						'indiebooking_customer_show_bookings' => true
				)
			);
		}
		if (is_null(get_role(RS_IB_Data_Validation::USERROLE_IB_READER))) {
			$display_name = __("indiebooking reader only", "indiebooking");
			$berechtigung = array(
					'read' => true, //ohne read kann der Benutzer weder in das Dashboard, noch in sein Benutzerprofil
					'read_indiebooking' => true,
					'read_indiebooking_appartment' => true,
					'edit_indiebooking_appartment' => true,
					'read_indiebooking_appartments' => true,
					'edit_indiebooking_appartments' => true,
			);
			add_role(RS_IB_Data_Validation::USERROLE_IB_READER, $display_name, $berechtigung);
			$role = get_role(RS_IB_Data_Validation::USERROLE_IB_READER);
// 			$role->add_cap('read_indiebooking_appartment');
// 			$role->add_cap('edit_indiebooking_appartment');
// 			$role->add_cap('read_indiebooking_appartments');
// 			$role->add_cap('edit_indiebooking_appartments');
			
			$role = null;
			$editableRoles = self::get_edit_pages_roles();
			foreach ($editableRoles as $roleIndex => $editRole) {
				//Fuegt jeder Rolle, die eine Seite editieren darf, das Recht 'read_indiebooking' hinzu
				$role = get_role($roleIndex);
				if (!is_null($role)) {
					$role->add_cap('read_indiebooking');
// 					$role->add_cap('delete_indiebooking_appartment');
// 					$role->add_cap('delete_indiebooking_appartments');
// 					$role->add_cap('read_indiebooking_appartment');
// 					$role->add_cap('edit_indiebooking_appartment');
// 					$role->add_cap('read_indiebooking_appartments');
// 					$role->add_cap('edit_indiebooking_appartments');
				}
			}
		}
	}
	
	private static function testUserRole() {
		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_READER);
		self::delete_user_role(RS_IB_Data_Validation::USERROLE_IB_BASIC_CUST);

		
		self::createUserRoles();
	}
	
	public static function delete_user_role($role) {
		if (!is_null(get_role($role))) {
			remove_role($role);
		}
	}
	
	private static function get_editable_roles() {
	    global $wp_roles;
	
	    $all_roles 		= $wp_roles->roles;
	    $editable_roles = apply_filters('editable_roles', $all_roles);
	
	    return $editable_roles;
	}
	
	private static function get_edit_pages_roles() {
		global $wp_roles;
		
		$editpageRoles	= array();
		$all_roles 		= $wp_roles->roles;
		foreach ($all_roles as $roleIndex => $role) {
			if (key_exists('edit_pages', $role['capabilities']) && $role['capabilities']['edit_pages'] == true) {
				$editpageRoles[$roleIndex] = $role;
			}
		}
		return $editpageRoles;
	}
	
// 	public static function rs_ib_update_routine_3() {
// 	    global $wpdb;
// 	    $table_name = $wpdb->prefix . 'rewabp_booking_header';
// 	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// 	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
// // 	        $sql = "ALTER TABLE $table_name ADD customer_strasse varchar(255) NOT NULL DEFAULT ''";
// 	        $sql = "CREATE TABLE $table_name (
// 	        booking_id bigint(20) NOT NULL,
// 	        booking_status varchar(20) NOT NULL DEFAULT 'open',
// 	        date_from datetime NOT NULL DEFAULT 0,
// 	        date_to datetime NOT NULL DEFAULT 0,
// 	        number_nights SMALLINT NOT NULL DEFAULT 0,
// 	        customer_name varchar(255) NOT NULL DEFAULT '',
// 	        customer_first_name varchar(255) NOT NULL DEFAULT '',
// 	        customer_location varchar(255) NOT NULL DEFAULT '',
// 	        customer_email varchar(255) NOT NULL DEFAULT '',
// 	        customer_telefon varchar(255) NOT NULL DEFAULT '',
// 	        customer_strasse  varchar(255) NOT NULL DEFAULT '',
// 	        customer_strasse varchar(255) NOT NULL DEFAULT ''
// 	        );";
// 	        dbDelta( $sql );
// 	        //PRIMARY KEY (booking_id)
// 	    }
	    
// 	    $table_name = $wpdb->prefix . 'rewabp_booking_position';
// 	    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
//     	    $sql = "CREATE TABLE $table_name (
//     	    booking_id bigint(20) NOT NULL,
//     	    position_id bigint(20) NOT NULL,
//     	    position_type varchar(255) NOT NULL,
//     	    appartment_id bigint(20) NOT NULL,
//     	    appartment_qm DECIMAL(10,2) NOT NULL DEFAULT 0,
//     	    date_from datetime NOT NULL DEFAULT 0,
//     	    date_to datetime NOT NULL DEFAULT 0,
//     	    number_nights SMALLINT NOT NULL DEFAULT 0,
//     	    price DECIMAL(3,2) NOT NULL DEFAULT 0,
//     	    mwst_percent DECIMAL(3,2) NOT NULL,
//     	    calc_type SMALLINT(1) NOT NULL DEFAULT 1,
//     	    calculation SMALLINT(1) NOT NULL DEFAULT 1,
//     	    discount_kz SMALLINT(1) NOT NULL DEFAULT 0,
//     	    meta_value varchar(255) NOT NULL DEFAULT ''
//     	    );";
//     	    dbDelta( $sql );
// 	    }
// 	}
}
// endif;
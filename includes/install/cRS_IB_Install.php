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
// if ( ! class_exists( 'cRS_IB_Install' ) ) :
/**
 * Diese Klasse stellt alle Methoden zur Verfuegung die fuer die Installation des Plugins nuetig sind.
 */
class cRS_IB_Install {
	
	public static function create_tables() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'rewabp_termmeta';
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			
			$sql = "CREATE TABLE $table_name (
			  meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			  rewabp_term_id bigint(20) NOT NULL,
			  meta_key varchar(255) DEFAULT NULL,
			  meta_value longtext DEFAULT NULL,
			  PRIMARY KEY (meta_id)
			);";
			
			dbDelta( $sql );
		}
		
		$table_name = $wpdb->prefix . 'rewabp_appartment_buchungszeitraum';
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		    	
		    $sql = "CREATE TABLE $table_name (
    		    meta_id bigint(20) NOT NULL AUTO_INCREMENT,
    		    post_id bigint(20) NOT NULL,
    		    position_id bigint(20) NOT NULL,
    		    date_from datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
    		    date_to datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
    		    meta_value varchar(255) DEFAULT NULL,
    		    PRIMARY KEY (meta_id),
    		    CONSTRAINT rsib_appartmentinfo UNIQUE (post_id,position_id)
		    );";

		    dbDelta( $sql );
		}
// 		$table_name = $wpdb->prefix . 'rewabp_taxes';
// 		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
//     		$sql = "CREATE TABLE $table_name (
//         		tax_id bigint(20) NOT NULL AUTO_INCREMENT,
//         		tax_rate varchar(200) NOT NULL,
//         		PRIMARY KEY (tax_id)
//         		);";
    		
//     		dbDelta( $sql );
// 		}
	}
	
	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 */
	public static function create_pages() {
// 	    include_once( 'admin/wc-admin-functions.php' );
	    global $user_ID;
	    global $wpdb;
	    $pages = apply_filters( 'rs_buchungsplattform_create_pages', array(
	        'appartmentoverview' => array(
	            'name'    => 'rs_appartmentoverview',
	            'title'   => _x( 'Appartment Overview', 'Page title', 'indiebooking' ),
	            'content' => ''
	        ),
	    	'bookingsuccess' => array(
	    		'name'    => 'rs_bookingsuccess',
	    		'title'   => _x( 'Booking success', 'Page title', 'indiebooking' ),
	    		'content' => ''
	    	),
// 	        'appartmentsearch' => array(
// 	            'name'    => 'rs_appartmentsearch',
// 	            'title'   => _x( 'Search Appartment', 'Page title', 'rs_indiebooking' ),
// 	            'content' => ''
// 	        ),
// 	        'shop' => array(
// 	            'name'    => _x( 'appartmentshop', 'Page slug', 'rs_buchungsplattform' ),
// 	            'title'   => _x( 'Appartment Shop', 'Page title', 'rs_buchungsplattform' ),
// 	            'content' => ''
// 	        ),
	    ) );
	
	    foreach ( $pages as $key => $page ) {
    	    $page_data = array(
    	        'post_status'    => 'publish',
    	        'post_type'      => 'page',
    	        'post_author'    => $user_ID,
    	        'post_name'      => $page['name'],
    	        'post_title'     => $page['title'],
    	        'post_content'   => $page['content'],
    	        'post_parent'    => 0,
    	        'comment_status' => 'closed'
    	    );
    	    $name              = $page['name'];
    	    $id_ofpost_name    = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$name'");
    	    if (!$id_ofpost_name) {
    	       $page_id        = wp_insert_post( $page_data );
    	    }
	    }
	}
	
	public static function create_user_roles() {

	}
	
	/**
	 * Install RS_IB
	 */
	public static function install() {
		global $wpdb;
		do_action('rs_indiebooking_include_db_model');
		do_action('rs_indiebooking_include_db_table');
		
		self::create_tables();
		self::create_pages();
		self::create_user_roles();
		
		$path = cRS_Indiebooking::plugin_path();
		include_once ($path.'/includes/install/RS_IB_Example_Data.php');
		RS_IB_Example_Data::createDefaultData();
// 		RS_IB_Example_Data::createTestData();
	}
}
// endif;
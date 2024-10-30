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
include_once 'parent_controller/rs_ib_appartment_uebersicht_controller.php';

// if ( ! class_exists( 'RS_IB_Appartment_Buchung_Controller_WP_AJAX' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Appartment_Uebersicht_Controller_WP_AJAX extends RS_IB_Appartment_Uebersicht_Controller
{
    public function __construct() {
//         add_action( 'wp_ajax_my_action', array($this, 'addBuchung') );

        /**
         * Fuer eingeloggte Nutzer
         */
        add_action('wp_ajax_search_appartments_by_date', array($this, 'searchApparmentsByDate_Ajax'));
        add_action('wp_ajax_search_appartments', array($this, 'searchApparments_Ajax'));
        
        /**
         * Fuer nicht eingeloggte Nutzer
         */
        add_action('wp_ajax_nopriv_search_appartments_by_date', array($this, 'searchApparmentsByDate_Ajax'));
        add_action('wp_ajax_nopriv_search_appartments', array($this, 'searchApparments_Ajax'));
        
    }
    
    
    /*
	public function filter_post_types( $should_use_snippet, array $post_type ) {
		if ( ! $should_use_snippet ) {
			return false !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax' )
			       && isset( $_REQUEST['action'] ) && 'query-attachments' === $_REQUEST['action']
			       && array_key_exists( 'attachment', $post_type );
		}

		return $should_use_snippet;
	}
     */
    
    public function searchApparmentsByDate_Ajax() {
        global $RSBP_DATABASE;
        
        $post_search_From           = rsbp_getPostValue('searchDateFrom', 0, RS_IB_Data_Validation::DATATYPE_DATUM);
        $post_search_To             = rsbp_getPostValue('searchDateTo', 0, RS_IB_Data_Validation::DATATYPE_DATUM);
        
//         $dtSuche_von                = new DateTime("2015-12-11");
//         $dtSuche_bis                = new DateTime("2015-12-14");
        $answer_wp_query            = $this->searchAppartmentByDate($post_search_From, $post_search_To);

        wp_reset_postdata();
        die();
    }
    
    public function searchApparments_Ajax() {
        $nonceOk			= RS_IB_Data_Validation::check_indiebooking_ajax_referer('rs-indiebooking-frontend-ajax-nonce', 'security');
        if ($nonceOk) {
        	
	        $searchData                 = new RS_IB_SearchData();
	
	        $searchOptions              = rsbp_getPostValue('searchOptions', array(), RS_IB_Data_Validation::DATATYPE_STRINGARRAY);
	        $search_categorie           = rsbp_getPostValue('searchCategorie', 0, RS_IB_Data_Validation::DATATYPE_STRINGARRAY);
	        $post_search_From           = rsbp_getPostValue('searchDateFrom', 0, RS_IB_Data_Validation::DATATYPE_DATUM);
	        $post_search_To             = rsbp_getPostValue('searchDateTo', 0, RS_IB_Data_Validation::DATATYPE_DATUM);
	        $searchNrOfBeds             = rsbp_getPostValue('searchNrOfBeds', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $searchNrOfRooms            = rsbp_getPostValue('searchNrOfRooms', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $searchNrOfGuest            = rsbp_getPostValue('searchNrOfGuest', 0, RS_IB_Data_Validation::DATATYPE_NUMBER);
	        $searchLocation             = rsbp_getPostValue('searchLocation', "", RS_IB_Data_Validation::DATATYPE_TEXT);
	        $searchFeatures				= rsbp_getPostValue('searchFeatures', 0, RS_IB_Data_Validation::DATATYPE_STRINGARRAY);
	        
	        $searchData->setDateFrom($post_search_From);
	        $searchData->setDateTo($post_search_To);
	        $searchData->setCategorie($search_categorie);
	        $searchData->setNumberOfBeds($searchNrOfBeds);
	        $searchData->setNumberOfGuests($searchNrOfGuest);
	        $searchData->setOptions($searchOptions);
	        $searchData->setLocation($searchLocation);
	        $searchData->setNumberOfRooms($searchNrOfRooms);
	        $searchData->setFeatures($searchFeatures);
	        //         $dtSuche_von                = new DateTime("2015-12-11");
	        //         $dtSuche_bis                = new DateTime("2015-12-14");
	//         $answer_wp_query            = $this->searchAppartment($post_search_From, $post_search_To, $search_categorie);
	        $answer_wp_query            = $this->searchAppartment($searchData);
	        wp_reset_postdata();
	        die();
        }
    }
}
// endif;
new RS_IB_Appartment_Uebersicht_Controller_WP_AJAX();
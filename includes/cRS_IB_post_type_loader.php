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
// if ( ! class_exists( 'RS_IB_post_type_loader' ) ) :

/**
 * cRS_IB_post_type_loader
 * Diese Klasse ist dafuer verantwortlich, dass die custom-post-types initialisiert werden.
 * @author schmitt
 *
 */
class RS_IB_post_type_loader
{
    public function __construct() {
        $this->init();
    }
    private function inlcudes() {
        include_once( 'postTypes/cRS_IB_Appartment_post_type.php' );
        include_once( 'postTypes/cRS_IB_Appartment_Buchung_post_type.php' );
//         include_once( 'database/table/cRS_IB_Table_Gutschein.php');
    }
    
    /**
     * Hook in methods.
     */
    public function init() {
        $this->inlcudes();
        add_action( 'init', array($this, 'registerAppartmentPostType' ), 5 );
        
        add_action( 'rs_indiebooking_register_apartment_taxonomies', array('RS_IB_Appartment_post_type', 'registerAppartmentTaxonomys') );
        
        add_filter('bulk_actions-edit-rsappartment_buchung', array('RS_IB_Appartment_Buchung_post_type', 'remove_bulk_actions'));
        add_filter('post_row_actions', array($this, 'remove_row_actions'), 10, 2);
        add_filter('rsappartmentcategories_row_actions', array($this, 'remove_taxonomy_row_actions'), 10, 2);
//         add_action( 'init', array( $this, 'register_post_types' ), 5 );
//         add_action( 'init', array( $this, 'register_post_status' ), 9 );
//         add_action( 'init', array( $this, 'support_jetpack_omnisearch' ) );
//         add_filter( 'rest_api_allowed_post_types', array( $this, 'rest_api_allowed_post_types' ) );
    }


    /**
     * Entfernt die nicht benötigten Links unter den Listen.
     *
     * @param unknown $actions
     * @param unknown $post
     * @return unknown
     */
    public function remove_row_actions( $actions, $post )
    {
        global $current_screen;
        switch ($current_screen->post_type) {
            case RS_IB_Appartment_Buchung_post_type::POST_TYPE_NAME:
                unset( $actions['edit'] );
                unset( $actions['view'] );
                unset( $actions['trash'] );
                unset( $actions['inline hide-if-no-js'] );
                break;
            case RS_IB_Appartment_post_type::POST_TYPE_NAME:
//                 unset( $actions['edit'] );
//                 unset( $actions['view'] );
//                 unset( $actions['trash'] );
                unset( $actions['inline hide-if-no-js'] );
                break;
        }
    
        return $actions;
    }
    
    public function remove_taxonomy_row_actions( $actions, $tag) {
        unset( $actions['inline hide-if-no-js'] );
        unset( $actions['view'] );
        
        return $actions;
    }
    
    /**rsappartmentcategories
     * registerAppartmentPostType legt den PostType an, der fuer die Anlage / Verwaltung der Appartments nuetig zustuendig ist an.
     * Darueber hinaus werden auch die benuetigten Kategorien etc. angelegt
     */
    public function registerAppartmentPostType() {
        register_post_type(RS_IB_Appartment_post_type::POST_TYPE_NAME, RS_IB_Appartment_post_type::getAppartmentPostTypeConfig());
        register_post_type(RS_IB_Appartment_Buchung_post_type::POST_TYPE_NAME, RS_IB_Appartment_Buchung_post_type::getAppartmentBuchungPostTypeConfig());
        
//         RS_IB_Appartment_post_type::registerAppartmentTaxonomys();
        do_action('rs_indiebooking_register_apartment_taxonomies');
        RS_IB_Appartment_Buchung_post_type::registerAppartmentBuchungStatus();
        
//         RS_IB_Table_Gutschein::registerGutscheinTaxonomys();
    }
   
}
// endif;

new RS_IB_post_type_loader();
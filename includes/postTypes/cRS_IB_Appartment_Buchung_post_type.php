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
// if ( ! class_exists( 'RS_IB_Appartment_Buchung_post_type' ) ) :
class RS_IB_Appartment_Buchung_post_type
{
    const POST_TYPE_NAME  = "rsappartment_buchung";
     
    public static function remove_bulk_actions( $actions ){
        unset( $actions['edit'] );
        return $actions;
    }
    
    public static function getAppartmentBuchungPostTypeConfig() {
        $args = array(
            'label'         => __('Apartment booking', 'indiebooking'), //'AppartmentBuchungPostType',
            'labels' => array(
                'name'               => __('Bookings', 'indiebooking'),//'Appartmentbuchungsanzeige', //Ueberschrift in der ApparmentuebersichtApartment
                //                 'singular_name'      => '',
                'menu_name'          => __('Apartment booking', 'indiebooking'), //'ReWa Appartments Buchung', //<-- name im Admin Menue
                'name_admin_bar'     => __('Add new booking', 'indiebooking'),//'Neues Appartment hinzuf&uuml;gen', //<-- Beschriftung des AddNew-Buttons in der Admin-Header-Bar
                //                 'all_items'          => '',
                'add_new'            => __('Add new booking', 'indiebooking'), //<-- Beschreibung des AddNew Buttons in der Appartmentuebersicht/-bearbeitung
                'add_new_item'       => __('Add booking', 'indiebooking'), //<-- Ueberschrift nach klick auf "add_new" in der Appartmentuebersicht/-bearbeitung
                'edit_item'          => __('Edit booking', 'indiebooking'), //<-- Ueberschrift in der Ansicht/Bearbeitung eines Appartments
//                 'new_item'           => 'Bezes Uten',
                'view_item'          => __('View booking', 'indiebooking'), //<-- Wenn public = true --> Button um ins Frontend des Posts zu kommen.
                'search_items'       => __('Search booking', 'indiebooking'), //<-- Beschreibung des Suchebuttons in der Appartmentuebersicht
                'not_found'          => __('No booking found', 'indiebooking'),//'Keine Buchung gefunden', //<-- wird angezeigt, wenn keine Eintraege vorhanden sind.
                'not_found_in_trash' => __('No booking found in trash', 'indiebooking'), //<-- Wird als Text angezeigt, wenn man bspw. den Papierkorb komplett geleert hat.
//                 'parent_item_colon'  => 'paarent item colon', //<-- bei hierarchischen Posts
//                 'all_items'          => 'All Posts / All Pages',
//                 'featured_image'    => 'Appartment-Profilbild', //<-- Ueberschrift des Thumbnail
//                 'set_featured_image' => '', //<-- Beschreibung des Links um ein Bild hinzuzufuegen
//                 'remove_featured_image' => '', //<-- Beschreibung des Links um das Bild zu entfernen
//                 'use_featured_image' => '',
            ),
            'capability_type'   => 'post',
            'capabilities' => array(
                'create_posts'  => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ),
            'map_meta_cap'      => true,
            'description'       => 'Apartment_Booking', //'Test',
            'public'            => true, //<-- Gibt an, ob der PostType fuer den Endbenutzer gedacht ist. (Zeigt dann den Permlink + Button an)
            'show_ui'           => true,
            'show_in_menu'      => 'rs_indiebooking',
            'show_in_nav_menu'  => true,
            'menu_position'     => 2,
            'publicly_queryable'  => true,
            'supports' => array(
                'title',
                'editor',
                //                 'author',
                'thumbnail',
                //                 'excerpt',
                //                 'trackbacks',
                'custom-fields',
//                 'revisions',
                //                 'page-attributes',
                //                 'post-formats',
                'taxonomies',
                'has_archive',
                'rewrite'       => true,
                //                 'query_var',
                'can_export',
            )
        );
        return $args;
    }
    
    /**
     * Registriert alle Buchungstatus die bei Indiebooking genutzt werden.
     * ACHTUNG! Der Name darf maximal 20 Zeichen haben!
     */
    public static function registerAppartmentBuchungStatus() {
        
    	register_post_status( 'rs_ib-requested', array(
    			'label'                     => _x( 'Requested', 'Book status', 'indiebooking' ),
    			'public'                    => true,
    			'exclude_from_search'       => false,
    			'show_in_admin_all_list'    => true,
    			'show_in_admin_status_list' => true,
    			'label_count'               => _n_noop( 'Requested <span class="count">(%s)</span>', 'Requested <span class="count">(%s)</span>', 'indiebooking' )
    	) );
    	
    	register_post_status( 'rs_ib-canc_request', array(
    			'label'                     => _x( 'Request canceled', 'Book status', 'indiebooking' ),
    			'public'                    => true,
    			'exclude_from_search'       => false,
    			'show_in_admin_all_list'    => true,
    			'show_in_admin_status_list' => true,
    			'label_count'               => _n_noop( 'Requested canceled<span class="count">(%s)</span>', 'Requested canceled<span class="count">(%s)</span>', 'indiebooking' )
    	) );
    	
        register_post_status( 'rs_ib-booked', array(
            'label'                     => _x( 'Booked', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Booked <span class="count">(%s)</span>', 'Booked <span class="count">(%s)</span>', 'indiebooking' )
        ) );

        register_post_status( 'rs_ib-blocked', array(
            'label'                     => _x( 'Blocked', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Blocked <span class="count">(%s)</span>', 'Blocked <span class="count">(%s)</span>', 'indiebooking' )
        ) );

        register_post_status( 'rs_ib-booking_info', array(
            'label'                     => _x( 'Blocked Info', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Blocked Info <span class="count">(%s)</span>', 'Blocked Info <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-almost_booked', array(
            'label'                     => _x( 'almost booked', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'              => _n_noop( 'almost booked <span class="count">(%s)</span>', 'almost_booked <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status('rs_ib-payment_reg', array(
            'label'                     => _x( 'payment_registered', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'              => _n_noop( 'payment_registered <span class="count">(%s)</span>', 'payment registered <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status('rs_ib-deposit_reg', array(
        		'label'                     => _x( 'deposit_registered', 'Book status', 'indiebooking' ),
        		'public'                    => true,
        		'exclude_from_search'       => false,
        		'show_in_admin_all_list'    => true,
        		'show_in_admin_status_list' => true,
        		'label_count'              => _n_noop( 'deposit registered <span class="count">(%s)</span>', 'deposit registered <span class="count">(%s)</span>', 'indiebooking' )
        ) );

        register_post_status('rs_ib-deposit_conf', array(
        		'label'                     => _x( 'deposit_confirmed', 'Book status', 'indiebooking' ),
        		'public'                    => true,
        		'exclude_from_search'       => false,
        		'show_in_admin_all_list'    => true,
        		'show_in_admin_status_list' => true,
        		'label_count'              => _n_noop( 'deposit_confirmed <span class="count">(%s)</span>', 'deposit_confirmed <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-pay_confirmed', array(
            'label'                     => _x( 'payment_confirmed', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'payment_confirmed <span class="count">(%s)</span>', 'payment_confirmed <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
//         register_post_status( 'rs_ib-pay_confirmed_bcom', array(
//         	'label'                     => _x( 'payment_confirmed bcom', 'Book status', 'indiebooking' ),
//         	'public'                    => true,
//         	'exclude_from_search'       => false,
//         	'show_in_admin_all_list'    => true,
//         	'show_in_admin_status_list' => true,
//         	'label_count'               => _n_noop( 'payment_confirmed_bcom <span class="count">(%s)</span>', 'payment_confirmed_bcom <span class="count">(%s)</span>', 'indiebooking' )
//         ) );
        
        register_post_status( 'rs_ib-canceled', array(
            'label'                     => _x( 'canceled booking', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'canceled booking <span class="count">(%s)</span>', 'canceled_booking <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-storno', array(
            'label'                     => _x( 'storno booking', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'storno booking <span class="count">(%s)</span>', 'storno_booking <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-storno_paid', array(
        	'label'                     => _x( 'storno booking payed', 'Book status', 'indiebooking' ),
        	'public'                    => true,
        	'exclude_from_search'       => false,
        	'show_in_admin_all_list'    => true,
        	'show_in_admin_status_list' => true,
        	'label_count'               => _n_noop( 'storno booking payed <span class="count">(%s)</span>', 'storno_booking <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-out_of_time', array(
            'label'                     => _x( 'out of time', 'Book status', 'indiebooking' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'out of time <span class="count">(%s)</span>', 'out of time <span class="count">(%s)</span>', 'indiebooking' )
        ) );
        
        register_post_status( 'rs_ib-bookingcom', array(
        		'label'                     => _x( 'booking from booking.com', 'Book status', 'indiebooking' ),
        		'public'                    => true,
        		'exclude_from_search'       => false,
        		'show_in_admin_all_list'    => true,
        		'show_in_admin_status_list' => true,
        		'label_count'               => _n_noop( 'booking from booking.com <span class="count">(%s)</span>', 'booking from booking.com <span class="count">(%s)</span>', 'indiebooking' )
        ) );
    }
}
// endif;
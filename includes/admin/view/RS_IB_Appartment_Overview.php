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

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
include_once 'RS_IB_Overview_functions.php';

// if ( ! class_exists( 'RS_IB_Appartment_Overview' ) ) :
class RS_IB_Appartment_Overview {
    public static function configureAppartmentOverview() {
        add_action("manage_posts_custom_column", array('RS_IB_Appartment_Overview', "appartment_custom_columns" ));
        add_filter("manage_edit-rsappartment_columns", array('RS_IB_Appartment_Overview', "appartment_edit_columns" ));
    
        add_action( 'admin_action_rs_indiebooking_duplicate_post_as_draft', 'rs_indiebooking_duplicate_post_as_draft' );
        add_filter( 'post_row_actions', 'rs_indiebooking_duplicate_post_link', 10, 2 );
    }
    
    /**
     * In dieser Funktion wird definiert, wie die Daten fuer die Anzeige verarbeitet werden.
     * Aufruf: add_action("manage_posts_custom_column",  array( $this, "portfolio_custom_columns"));
     * @param unknown $column
     */
    public static function appartment_custom_columns($column) {
        global $post;
        global $RSBP_DATABASE;
    
        $appartmentTable            = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment::RS_TABLE);
        $appartmentBuchungsTable    = $RSBP_DATABASE->getTable(RS_IB_Model_Appartment_Buchung::RS_TABLE);
        $appartment                 = $appartmentTable->getAppartment($post->ID);
    
        switch ($column) {
            case "appartment_description":
                the_excerpt();
                break;
            case "appoptions":
                if (class_exists("RS_IB_Model_Appartmentoption")) {
                    echo get_the_term_list($post->ID, 'rsappartmentoption', '', ', ','');
                } else {
                    echo "";
                }
                break;
            case "apartment_thumb":
                $size = array();
                echo the_post_thumbnail('apartment-thumbnail');
                break;
            case "calendar": {
                $bookedDates          = $appartmentBuchungsTable->getBuchungszeitraeumeByAppartmentId($post->ID);
                include 'apartment_parts/RS_IB_Apartment_Zabuto_Calendar.php';
            }
        }
    }
      
    /**
     * In dieser Funktion werden die Felder definiert, die in der uebersichtstabelle angezeigt werden.
     * Aufruf: add_filter("manage_edit-rsappartment_columns", array( $this, "portfolio_edit_columns" ));
     * @param unknown $columns
     * @return multitype:string
     */
    public static function appartment_edit_columns($columns){
        $columns = array(
            "cb"                => "<input type='checkbox' />",
            "apartment_thumb"   => 'Thumbnail',
            "title"             => __('Titel', 'indiebooking'),
            "appartment_description"   => __('Description', 'indiebooking'),
            //             "year"              => "Year Completed",
            "appoptions"        => _n('Apartmentoption', 'Apartmentoptions', 2, 'indiebooking'),
            "calendar"          => __('Calendar', 'indiebooking'),
        );
        if (!is_plugin_active('indiebooking-advanced/indiebooking_advanced.php')) {
            unset( $columns['appoptions'] );
        }
        return $columns;
    }
    
}
// endif;
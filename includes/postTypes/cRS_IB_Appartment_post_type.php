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
// if ( ! class_exists( 'RS_IB_Appartment_post_type' ) ) :
class RS_IB_Appartment_post_type
{
    const POST_TYPE_NAME  = "rsappartment";
    
    public static function getAppartmentPostTypeConfig() {
        $args = array(
            'label'         => 'AppartmentPostType',
            'labels' => array(
                'name'               => __('View Apartment', 'indiebooking'), //Ueberschrift in der Apparmentuebersicht
                'menu_name'          => __('Apartments', 'indiebooking'), //<-- name im Admin Menue
                'name_admin_bar'     => __('Add new Apartment', 'indiebooking'), //<-- Beschriftung des AddNew-Buttons in der Admin-Header-Bar
                //                 'all_items'          => '',
                'add_new'            => __('Add new Apartment', 'indiebooking'), //<-- Beschreibung des AddNew Buttons in der Appartmentuebersicht/-bearbeitung
                'add_new_item'       => __('Add Apartment', 'indiebooking'), //<-- Ueberschrift nach klick auf "add_new" in der Appartmentuebersicht/-bearbeitung
                'edit_item'          => __('Edit Apartment', 'indiebooking'), //<-- Ueberschrift in der Ansicht/Bearbeitung eines Appartments
//                 'new_item'           => 'Bezes Uten',
                'view_item'          => __('View Apartment', 'indiebooking'), //<-- Wenn public = true --> Button um ins Frontend des Posts zu kommen.
                'search_items'       => __('Search Apartment', 'indiebooking'), //<-- Beschreibung des Suchebuttons in der Appartmentuebersicht
                'not_found'          => __('No Apartment found', 'indiebooking'), //<-- wird angezeigt, wenn keine Eintraege vorhanden sind.
                'not_found_in_trash' => __('No Apartment found in trash', 'indiebooking'), //<-- Wird als Text angezeigt, wenn man bspw. den Papierkorb komplett geleert hat.
                'parent_item_colon'  => 'paarent item colon', //<-- bei hierarchischen Posts
//                 'all_items'          => 'All Posts / All Pages',
               'featured_image'    => __('Apartment Thumbnail', 'indiebooking'), //<-- Ueberschrift des Thumbnail
               'set_featured_image' => __('Add Apartment Thumbnail', 'indiebooking'), //<-- Beschreibung des Links um ein Bild hinzuzufuegen
//                 'remove_featured_image' => '', //<-- Beschreibung des Links um das Bild zu entfernen
//                 'use_featured_image' => '',
            ),
            'description'       => 'Test',
            'public'            => true, //<-- Gibt an, ob der PostType fuer den Endbenutzer gedacht ist. (Zeigt dann den Permlink + Button an)
            'show_ui'           => true,
            'show_in_menu'      => 'rs_indiebooking',
            'show_in_nav_menus' => true,
//             'menu_position'     => 4,
            'publicly_queryable'  => true,
            'supports' => array(
                'title',
                'editor',
                //                 'author',
//                 'thumbnail',
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
            ),
//         	'capability_type' => array('indiebooking_appartment', 'indiebooking_appartments'),
//         	'capabilities' => array(
//         		'read_post'     		=> 'read_indiebooking_appartment',
//         		'read_private_post'     => 'read_private_indiebooking_appartment',
//         		'edit_post'				=> 'edit_indiebooking_appartment',
//         		'edit_other_post' 		=> 'edit_other_indiebooking_appartment',
//         		'delete_post'			=> 'delete_indiebooking_appartment',
//         		'edit_posts'			=> 'edit_indiebooking_appartments',
//         		'delete_posts'			=> 'delete_indiebooking_appartments',
//         		'publish_posts' 		=> 'publish_indiebooking_appartment',
//         	),
//        		'map_meta_cap' 	 => false,
//         	'capability_type' => array(
//         		RS_IB_Data_Validation
//         	)
        );
        return $args;
    }
    
    public static function registerAppartmentTaxonomys() {
//         register_taxonomy("rsappartmentoption", //<== Darf nicht Grouebuchstaben oder Leerzeichen enthalten!!!!!
        register_taxonomy("rsappartmentcategories", //<== Darf nicht Grouebuchstaben oder Leerzeichen enthalten!!!!!
            array(self::POST_TYPE_NAME),
            array(
                "label"             => _n('Apartmentcategory', 'Apartmentcategories', 2, 'indiebooking'),//"Appartmentoptionen",
                'public'            => true,
                'labels' => array(
                    'name'          => _n('Apartmentcategory', 'Apartmentcategories', 2, 'indiebooking'),
                    'sigular_name'  => _n('Apartmentcategory', 'Apartmentcategories', 1, 'indiebooking'),
                    'menu_name'     => _n('Apartmentcategory', 'Apartmentcategories', 1, 'indiebooking'),
                    'edit_item'     => __('Edit Category', 'indiebooking'),
                    'view_item'     => __('View Category', 'indiebooking'),
                    'update_item'   => __('Update Category', 'indiebooking'),
                    'add_new_item'  => __('Add new Category', 'indiebooking'),
                    //                     'new_item_name' => 'Optionsbezeichnung',
                    'popular_items' => NULL,
                ),
                "hierarchical"      => false,
//                 "singular_label"    => _n('Apartmentcategory', 'Apartmentcategories', 1, 'indiebooking'),
//                 'rewrite'           => true,
//                 'show_ui'           => false,
                'show_in_menu'      => 'rs_indiebooking',
//                 'show_in_nav_menus'  => false,
//                 'show_admin_column' => false,
            )
        );
    }
}
// endif;
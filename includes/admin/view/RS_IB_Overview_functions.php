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
/*
 * Add the duplicate link to action list for post_row_actions
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! function_exists( 'rs_indiebooking_duplicate_post_link' ) ) {
	function rs_indiebooking_duplicate_post_link( $actions, $post ) {
	    if (get_post_type($post) === RS_IB_Model_Appartment::RS_POSTTYPE ) {
	        $duplicate = true;
	        $duplicate = apply_filters("rs_indiebooking_check_add_new_apartment_head", $duplicate);
	        
	        if ($duplicate) {
	            $title  = __('Duplicate this item', 'indiebooking');
	            $link   = __('Duplicate', 'indiebooking');
	            if (current_user_can('edit_posts')) {
	                if (get_post_type($post) === RS_IB_Model_Appartment::RS_POSTTYPE ) {
	                    $actions['duplicate'] = '<a href="admin.php?action=rs_indiebooking_duplicate_post_as_draft&amp;post=' . $post->ID .
	                                            '" title="'.$title.'" rel="permalink">'.$link.'</a>';
	                }
	            }
	        }
	    }
	    return $actions;
	}
}
/*
 * Function creates post duplicate as a draft and redirects then to the edit post screen
 * http://rudrastyh.com/wordpress/duplicate-post.html
 */
/*
 * KOPIEREN
 */
if ( ! function_exists( 'rs_indiebooking_duplicate_post_as_draft' ) ) {
function rs_indiebooking_duplicate_post_as_draft(){
    global $wpdb;
    global $RSBP_DATABASE;
    global $RSBP_TABLEPREFIX;
    
    $actionRequest = rsbp_getRequestValue('action', '', RS_IB_Data_Validation::DATATYPE_TEXT);
    if (! ( isset( $_GET['post']) || isset( $_POST['post'])  ||
        ( 'rs_indiebooking_duplicate_post_as_draft' == $actionRequest ) ) ) {
            
        wp_die('No post to duplicate has been supplied!');
    }

    $duplicate      = true;
    $duplicate      = apply_filters("rs_indiebooking_check_add_new_apartment_head", $duplicate);
    
    if ($duplicate) {
        
    
    /*
     * get the original post id
     */
    $post_id        = rsbp_getGetValue('post', null, RS_IB_Data_Validation::DATATYPE_INTEGER);
    if (is_null($post_id)) {
        $post_id    = rsbp_getPostValue('post', null, RS_IB_Data_Validation::DATATYPE_INTEGER);
    }
    
    /*
     * and all the original post data then
    */
    $post = null;
    if (!is_null($post_id)) {
        $post = get_post( $post_id );
    }
    /*
     * if you don't want current user to be the new post author,
     * then change next couple of lines to this: $new_post_author = $post->post_author;
    */
    $current_user       = wp_get_current_user();
    $new_post_author    = $current_user->ID;
    
    /*
     * if post data exists, create the post duplicate
     */
    if (!is_null($post_id) && isset( $post ) && $post != null) {

        /*
         * new post data array
         */
        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft',
            'post_title'     => $post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        /*
         * insert the post by wp_insert_post() function
        */
        $new_post_id = wp_insert_post( $args );

        /*
         * get all current post terms ad set them to the new post draft
        */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
         * duplicate all post meta just in two SQL queries
         */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos) != 0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key       = $meta_info->meta_key;
                $meta_value     = addslashes($meta_info->meta_value);
                $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query.= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
        }

        $zeitraumgesperrttbl        = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
        $post_zeitraeume            = $wpdb->get_results(
                                        "SELECT post_id, position_id, date_from, date_to FROM $zeitraumgesperrttbl WHERE post_id=$post_id"
                                      );
        if (count($post_zeitraeume) != 0) {
            $sql_query_sel2         = array();
            $sql_query      = "INSERT INTO $zeitraumgesperrttbl (post_id, position_id, date_from, date_to) ";
            foreach ($post_zeitraeume as $zeitraum_info) {
                //                 $meta_key       = $meta_info->meta_key;
                $datumVon           = $zeitraum_info->date_from;
                $datumBis           = $zeitraum_info->date_to;
                $positionId         = $zeitraum_info->position_id;
//                 $meta_value         = addslashes($zeitraum_info->meta_value);
                $sql_query_sel2[]   = "SELECT $new_post_id, $positionId, '$datumVon', '$datumBis'";
            }
            $sql_query.= implode(" UNION ALL ", $sql_query_sel2);
            $wpdb->query($sql_query);
        }
        
//         $zeitraumtbl        = $wpdb->prefix .'rewabp_appartment_buchungszeitraum';
//         $post_zeitraeume    = $wpdb->get_results("SELECT post_id, position_id, date_from, date_to, meta_value FROM $zeitraumtbl WHERE post_id=$post_id");
//         if (count($post_zeitraeume) != 0) {
//             $sql_query_sel2         = array();
//             $sql_query      = "INSERT INTO $zeitraumtbl (post_id, position_id, date_from, date_to, meta_value) ";
//             foreach ($post_zeitraeume as $zeitraum_info) {
// //                 $meta_key       = $meta_info->meta_key;
//                 $datumVon           = $zeitraum_info->date_from;
//                 $datumBis           = $zeitraum_info->date_to;
//                 $positionId         = $zeitraum_info->position_id;
//                 $meta_value         = addslashes($zeitraum_info->meta_value);
//                 $sql_query_sel2[]   = "SELECT $new_post_id, $positionId, '$datumVon', '$datumBis', '$meta_value'";
//             }
//             $sql_query.= implode(" UNION ALL ", $sql_query_sel2);
//             $wpdb->query($sql_query);
//         }
        
        /*
         * finally, redirect to the edit post screen for the new draft
         */
        wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    } else {
        wp_die('Post creation failed, could not find original post: ' . $post_id);
    }
    } else {
        wp_die(_e("You can't create a new Apartment", "rs-indebooking"));
    }
}
}
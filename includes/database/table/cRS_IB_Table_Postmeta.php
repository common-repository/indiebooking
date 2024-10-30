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
// if ( ! class_exists( 'RS_IB_Table_Postmeta' ) ) :
abstract class RS_IB_Table_Postmeta
{
    /**
     * RS_IB Term Meta API - Get term meta
     *
     * @param mixed $term_id
     * @param string $key
     * @param bool $single (default: true)
     * @return mixed
     */
//     public function get_post_meta($meta_type, $term_id, $key, $single = true ) {
// //         return get_metadata( 'rewabp_term', $term_id, $key, $single );
//         return get_metadata( $meta_type, $term_id, $key, $single );
//     }
    
    /**
     * RS_IB Term Meta API - Update term meta
     *
     * @param mixed $term_id
     * @param string $meta_key
     * @param mixed $meta_value
     * @param string $prev_value (default: '')
     * @return bool
     */
    public function update_post_meta($post_id, $meta_key, $meta_value, $prev_value = '' ) {
        return update_post_meta($post_id, $meta_key, $meta_value);
    }
    
    function select_end_meta_value( $meta, $end = "max" )
    {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT ".$end."( cast( meta_value as UNSIGNED ) ) FROM {$wpdb->postmeta} WHERE meta_key='%s'",
            $meta
        );
        return $wpdb->get_var( $query );
    }
    
    
    function select_list_of_meta_value( $meta ) {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key='%s'",
            $meta
        );
        return $wpdb->get_results( $query, ARRAY_N );
    }
    
    function select_list_of_post_meta_value( $posttype, $meta ) {
    	global $wpdb;
    	$query = $wpdb->prepare(
    			"SELECT DISTINCT meta_value FROM {$wpdb->posts}
    			INNER JOIN {$wpdb->postmeta}
    			ON id = post_id
    			WHERE post_type = '%s' AND meta_key='%s'",
    			array($posttype, $meta)
    	);
    	
    	return $wpdb->get_results( $query, ARRAY_N );
    }
    
    /**
     * RS_IB Term Meta API - Add term meta
     *
     * @param mixed $term_id
     * @param mixed $meta_key
     * @param mixed $meta_value
     * @param bool $unique (default: false)
     * @return bool
     */
//     function add_term_meta($meta_type, $term_id, $meta_key, $meta_value, $unique = false ){
// //         return add_metadata( 'rewabp_term', $term_id, $meta_key, $meta_value, $unique );
//         return add_metadata( $meta_type, $term_id, $meta_key, $meta_value, $unique );
//     }
        
    
}
// endif;
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
// if ( ! class_exists( 'RS_IB_Table_Termmeta' ) ) :
abstract class RS_IB_Table_Termmeta
{
    /**
     * RS_IB Term Meta API - Get term meta
     *
     * @param mixed $term_id
     * @param string $key
     * @param bool $single (default: true)
     * @return mixed
     */
    public function get_term_meta($meta_type, $term_id, $key, $single = true ) {
//         return get_metadata( 'rewabp_term', $term_id, $key, $single );
		/*
		 * Update Carsten Schmitt 27.03.2018
		 * Damit die eigene Term-Meta-Data Tabelle nach und nach verschwinden kann, lese ich nun
		 * vorwiegend aus der Wordpress eigenen Term-Meta-Data Tabelle.
		 * Nur wenn kein Wert gefunden wird, soll noch in der selbst erstellen gesucht werden.
		 */
        $metadataValue = get_term_meta($term_id, $key, $single);
        if (!isset($metadataValue) || is_null($metadataValue) || $metadataValue == false) {
        	$metadataValue = get_metadata( $meta_type, $term_id, $key, $single );
        }
        return $metadataValue;
//         return get_metadata( $meta_type, $term_id, $key, $single );
    }
    
    /**
     * RS_IB Term Meta API - Update term meta
     *
     * @param mixed $term_id
     * @param string $meta_key
     * @param mixed $meta_value
     * @param string $prev_value (default: '')
     * @return bool
     */
    public function update_term_meta($meta_type, $term_id, $meta_key, $meta_value, $prev_value = '' ) {
//         return update_metadata( 'rewabp_term', $term_id, $meta_key, $meta_value, $prev_value );
        update_term_meta($term_id, $meta_key, $meta_value, $prev_value);
        return update_metadata( $meta_type, $term_id, $meta_key, $meta_value, $prev_value );
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
    function add_term_meta($meta_type, $term_id, $meta_key, $meta_value, $unique = false ){
//         return add_metadata( 'rewabp_term', $term_id, $meta_key, $meta_value, $unique );
        add_term_meta($term_id, $meta_key, $meta_value, $unique);
        return add_metadata( $meta_type, $term_id, $meta_key, $meta_value, $unique );
    }
        
    
}
// endif;
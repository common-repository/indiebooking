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
// if ( ! class_exists( 'RS_IB_Table_Mwst' ) ) :
/**
 * RS_IB_Table_Appartment ist dafuer zustuendig, die Meta-Appartmentwerte in der entsprechenden Tabelle zu speichern
 * und bei Abruf als Objekt zurueck zu geben.
 * @author schmitt
 *
 */
class RS_IB_Table_Mwst
{
    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {
        
    }
    
    /* @var $mwst RS_IB_Model_Mwst */
    
    public function saveMwst( $mwst ) {
//         global $wpdb;
        
//         $wpdb->insert(
//             'wp_rewabp_taxes',
//             array(
//                 'tax_rate' => $mwst->getMwstValue(),
//             ),
//             array(
//                 '%s',
//             )
//         );
    }
    
    public function updateMwst( $mwst ) {
//         global $wpdb;
//         $wpdb->update(
//             'wp_rewabp_taxes',
//             array(
//                 'tax_rate' => $mwst->getMwstValue(),
//             ),
//             array(
//                'tax_id' => $mwst->getMwstId(),
//             ),
//             array(
//                 '%s',
//             ),
//             array( '%d')
//         );
    }
    
    public function getNextMwstId() {
        return get_option( 'rs_indiebooking_settings_mwst_nextId' );
    }
    
    public function getAllMwsts() {
//         global $wpdb;
        
//         $sql            = 'SELECT * FROM wp_rewabp_taxes ';
//         $results        = $wpdb->get_results($sql);
//         $mwsts          = array();
//         foreach ($results as $result) {
//             $mwst       = new RS_IB_Model_Mwst();
//             $mwst->setMwstId($result->tax_id);
//             $mwst->setMwstValue($result->tax_rate);
//             $mwsts[]    = $mwst;
//         }
//         return $mwsts;
        $mwsts          = array();
        $results        = get_option( 'rs_indiebooking_settings_mwst' );
        if ($results) {
            foreach ($results as $key => $result) {
                $mwst       = new RS_IB_Model_Mwst();
                $mwst->setMwstId($result['id']);
                $mwst->setMwstValue($result['value']);
                if (key_exists('revenueaccount', $result)) {
                	$mwst->setRevenueAccount($result['revenueaccount']);
                } else {
                	$mwst->setRevenueAccount('');
                }
                $mwsts[]    = $mwst;
            }
        }
        return $mwsts;
    }
    
    /* @var $appartmentOptionTable RS_IB_Table_Appartmentoption */
    /**
     * Gibt das Appartment mit allen Optionen zurueck
     * @param unknown $post_id
     * @return RS_IB_Model_Appartment
     */
//     public function getAppartment( $post_id ) {
//         global $RSBP_DATABASE;
// //         print_r(get_post($post_id));
//         $custom                 = get_post_custom( $post_id );
//         $modelAppartment        = new RS_IB_Model_Appartment($custom);
//         $options                = $appartmentOptionTable->getPostAppartmentOptions( $post_id );
//         $modelAppartment->setOptionen($options);

//         return $modelAppartment;
//     }
    
}
// endif;
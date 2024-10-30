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

// if ( ! class_exists( 'RS_IB_Table_Apartment_Gesperrter_Zeitraum' ) ) :

add_action("rs_indiebooking_admin_saveOrUpdateApartment", array("RS_IB_Table_Apartment_Gesperrter_Zeitraum", "init_saveOrUpdateApartmentGesperrterZeitraum"), 10, 1);
class RS_IB_Table_Apartment_Gesperrter_Zeitraum
{

    private static $_instance = null;
    
    public static function init_saveOrUpdateApartmentGesperrterZeitraum($modelApartment) {
        $gespZeitraumTbl = RS_IB_Table_Apartment_Gesperrter_Zeitraum::instance();
        $gespZeitraumTbl->saveOrUpdateApartmentGesperrterZeitraum($modelApartment);
    }
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    private function __construct() {

    }
    
    private function getTableName() {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'apartment_gesperrter_zeitraum';
        
        return $table_name;
    }
    
    public function loadApartmentGesperrteZeitraume($apartmentId, $onlyFuture = false) {
        if ($apartmentId) {
            global $wpdb;
    
            $apartmentId	= apply_filters('rs_indiebooking_get_original_apartment_id_from_wpml', $apartmentId);
            $table      	= $this->getTableName();
//             $sql        = "SELECT date_from as 'from', date_to as 'to' FROM $table WHERE post_id = %d";
            $sql        	= "SELECT date_from, date_to FROM $table WHERE post_id = %d";
            if ($onlyFuture) {
                $todayStr 	= new DateTime("now");
                $todayStr 	= $todayStr->format("Y-m-d");
                $sql    	= $sql." AND date_to >= '$todayStr'";
            }
            $preparedSql 	= $wpdb->prepare($sql, array($apartmentId));
//             echo $preparedSql;
//                 $wpdb->prepare($sql, array($apartmentId), OBJECT)
            $results    	= $wpdb->get_results($preparedSql, OBJECT);
//             var_dump($results);
            return $results;
        }
    }
    
    public function saveOrUpdateApartmentGesperrterZeitraum(RS_IB_Model_Appartment $modelApartment) {
        $post_id    = $modelApartment->getPostId();
        if ($post_id) {
            global $wpdb;
            $notBookableDates  = $modelApartment->getNotbookableDates();
            if (!is_null($notBookableDates)) {
                //Luesche alle bisher gepflegten Zeitrueume
                $wpdb->delete(
                    $this->getTableName(),
                    array(
                        'post_id' => $post_id
                    ),
                    array(
                        '%d'
                    )
                );
                //             var_dump($bookableDates);
                $positionId     = 1;
                foreach ($notBookableDates as $notDookableDate) {
                    $dtFrom     = null;
                    $dtTo       = null;
                    $from       = RS_IB_Data_Validation::check_with_whitelist($notDookableDate['from'], RS_IB_Data_Validation::DATATYPE_DATUM);
                    $to         = RS_IB_Data_Validation::check_with_whitelist($notDookableDate['to'], RS_IB_Data_Validation::DATATYPE_DATUM);
                    if ($from != '') {
                        $dtFrom = date_create_from_format('d.m.Y', $from);
                    }
                    if ($to != '') {
                        $dtTo   = date_create_from_format('d.m.Y', $to);
                    }
                    if (!(is_null($dtFrom)) && !(is_null($dtTo))) {
                        $dtFrom     = date('Y-m-d', $dtFrom->getTimestamp());
                        $dtTo       = date('Y-m-d', $dtTo->getTimestamp());
                        //                 $dtFrom     = $dtFrom->format('Y-m-d  H:i:s');
                        //                 $dtTo       = $dtTo->format('Y-m-d  H:i:s');
                        $result = $wpdb->insert(
                            $this->getTableName(),
                            array(
                                RS_IB_Model_Apartment_Gesperrter_Zeitraum::POST_ID       => $post_id,
                                RS_IB_Model_Apartment_Gesperrter_Zeitraum::POSITION_ID   => $positionId,
                                RS_IB_Model_Apartment_Gesperrter_Zeitraum::DATE_FROM     => $dtFrom,
                                RS_IB_Model_Apartment_Gesperrter_Zeitraum::DATE_TO       => $dtTo,
                            ),
                            array(
                                '%d',
                                '%d',
                                '%s',
                                '%s',
                            )
                        );
                        //                 exit (var_dump($wpdb->last_query));
                        $positionId++;
                    }
                }
            }
        }
    }
}
// endif;
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
// if ( ! class_exists( 'RS_IB_Table_Appartment_Zeitraeume' ) ) :
/**
 * RS_IB_Table_Appartment ist dafuer zustuendig, die Meta-Appartmentwerte in der entsprechenden Tabelle zu speichern
 * und bei Abruf als Objekt zurueck zu geben.
 * @author schmitt
 *
 */
class RS_IB_Table_Appartment_Zeitraeume //extends RS_IB_Table_Postmeta
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
    
    public function loadApartmentZeitraume($apartmentId, $onlyFuture = false) {
        if ($apartmentId) {
            global $wpdb;
            
            $table      = $wpdb->prefix .'rewabp_appartment_buchungszeitraum';
            $sql        = "SELECT date_from, date_to, meta_value as price FROM $table WHERE post_id = %d";
            if ($onlyFuture) {
                $todayStr = new DateTime("now");
                $todayStr = $todayStr->format("Y-m-d");
                $sql    = $sql." AND date_to >= '$todayStr'";
            }
            $results    = $wpdb->get_results($wpdb->prepare($sql, array($apartmentId)), OBJECT);
            return $results;
        }
    }
    
    public function loadGlobalVerfuegbareZeitraueme() {
        global $wpdb;
        $table      = $wpdb->prefix .'rewabp_appartment_buchungszeitraum';
        
        $sql        = "SELECT DISTINCT c.date_from, c.date_to
                    	FROM $table AS c
                    	WHERE (c.date_from, c.date_to) NOT IN (
                    		SELECT DISTINCT b.date_from, b.date_to
                    		FROM $table AS a
                    		CROSS JOIN $table AS b
                    		WHERE a.date_to >= CURDATE() AND b.date_to >= CURDATE() AND a.post_id <> b.post_id
                    		AND ((b.date_from > a.date_from AND b.date_to < a.date_to))
                    	)
                    	AND c.date_to >= CURDATE()
                        ORDER BY c.date_from";
        $results    = $wpdb->get_results($sql, OBJECT);
        $unsetKeys  = array();
        for ($key = 0; $key < sizeof($results); $key++) {
            $dateObj = $results[$key];
            if ($key+1 < sizeof($results)) {
                $dateObj2   = $results[$key+1];
                $dateFrom1  = new Datetime($dateObj->date_from);
                $dateTo1    = new Datetime($dateObj->date_to);
                $dateFrom2  = new Datetime($dateObj2->date_from);
                $dateTo2    = new Datetime($dateObj2->date_to);
                $overlap    = rs_ib_date_util::checkDateOverlap($dateFrom1, $dateTo1, $dateFrom2, $dateTo2);
                if ($overlap) {
                    $zeitraum = rs_ib_date_util::getBiggestDateRange($dateFrom1, $dateTo1, $dateFrom2, $dateTo2);
                    $newObj = clone($dateObj2);
                    $newObj->date_from  = $zeitraum['von']->format('Y-m-d');
                    $newObj->date_to    = $zeitraum['bis']->format('Y-m-d');
                    $results[$key+1]    = $newObj;
                    $results[$key]      = null;
                    array_push($unsetKeys, $key);
                }
            }
        }
        foreach ($unsetKeys as $ukey) {
            unset($results[$ukey]);
        }
        $result_array = array_values($results);
//         return $result_array;
        return $result_array;
    }
    
    
    /*
     * Update Carsten Schmitt: 23.09.2016
     */
    /* @var $modelAppartment RS_IB_Model_Appartment */
    /**
     * @deprecated
     * wird nicht mehr gebraucht, da die Preise und Zeitrueume durch Saisons
     * und "nicht buchbare" Zeitrueume definiert werden.
     * @param unknown $modelAppartment
     */
    public function saveOrUpdateAppartmentZeitraume( $modelAppartment ) {
        $post_id    = $modelAppartment->getPostId();
        if ($post_id) {
            global $wpdb;
            
            //Luesche alle bisher gepflegten Zeitrueume
            $wpdb->delete(
                $wpdb->prefix .'rewabp_appartment_buchungszeitraum',
                array(
                    'post_id' => $post_id
                ),
                array(
                    '%d'
                )
            );
            $bookableDates  = $modelAppartment->getBookableDates();
//             var_dump($bookableDates);
            $positionId     = 1;
            foreach ($bookableDates as $bookableDate) {
                $dtFrom     = null;
                $dtTo       = null;
                $from       = $bookableDate['from'];
                $to         = $bookableDate['to'];
                $price      = $bookableDate['price'];
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
                        $wpdb->prefix .'rewabp_appartment_buchungszeitraum',
                        array(
                            "post_id"       => $post_id,
                            "date_from"     => $dtFrom,
                            "date_to"       => $dtTo,
                            "position_id"   => $positionId,
                            "meta_value"    => $price,
                        ),
                        array(
                            '%d',
                            '%s',
                            '%s',
                            '%d',
                            '%s'
                        )
                    );
    //                 exit (var_dump($wpdb->last_query));
                    $positionId++;
                }
            }
        }
    }
}
// endif;
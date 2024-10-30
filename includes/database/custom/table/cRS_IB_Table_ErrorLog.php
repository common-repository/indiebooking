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

// if ( ! class_exists( 'RS_IB_Table_ErrorLog' ) ) :
class RS_IB_Table_ErrorLog
{

    private static $_instance = null;
    
    /* Diese Klasse ist ein Singleton --> Objekterstellung ueber instance() */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /* @var $buchungMwSt RS_IB_Model_BuchungMwSt */
    public function saveErrorLog( RS_IB_Model_ErrorLog $errorLogObj) {
        global $wpdb;
        global $RSBP_TABLEPREFIX;
        
        $table_name     = $wpdb->prefix . $RSBP_TABLEPREFIX . 'errorlog';
        $dtZeitpunkt    = rs_ib_date_util::convertDateValueToDateTime($errorLogObj->getDate(), true);
        $dtZeitpunkt    = date('Y-m-d H:i:s', $dtZeitpunkt->getTimestamp());

        $result = $wpdb->insert(
        	$table_name,
            array(
				RS_IB_Model_ErrorLog::CLASSTXT => $errorLogObj->getClass(),
            	RS_IB_Model_ErrorLog::LINE => $errorLogObj->getLine(),
				RS_IB_Model_ErrorLog::DATE => $dtZeitpunkt,
            	RS_IB_Model_ErrorLog::EXTRA_ID => $errorLogObj->getExtraId(),
            	RS_IB_Model_ErrorLog::EXTRA_TEXT => $errorLogObj->getExtraText(),
            	RS_IB_Model_ErrorLog::TEXT => $errorLogObj->getText(),
            	RS_IB_Model_ErrorLog::TYPE => $errorLogObj->getType()
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            )
        );
    }
}
// endif;
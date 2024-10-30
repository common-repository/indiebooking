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
}
// if ( ! class_exists( 'RS_IB_Indiebooking_Settings_Controller' ) ) :
/**
 * @author schmitt
 *
 */
class RS_IB_Indiebooking_Settings_Controller
{
    public function __construct() {
        add_action( 'wp_ajax_saveOrUpdateSettings', array($this, 'saveOrUpdateSettings') );
    }
    
    /* @var $mwstTable RS_IB_Table_Mwst */
    public function saveOrUpdateSettings() {
        global $RSBP_DATABASE;
        
        $mwstTable          = $RSBP_DATABASE->getTable(RS_IB_Model_Mwst::RS_TABLE);
        $mwsts              = rsbp_getPostValue('mwsts', "", RS_IB_Data_Validation::DATATYPE_NUMBER);
        if (!is_null($mwsts)) {
            foreach ($mwsts as $mwst) {
                $myMwst     = new RS_IB_Model_Mwst();
                $id         = $mwst['mwstid'];
                $taxtate    = $mwst['mwst'];
                $myMwst->setMwstId($id);
                $myMwst->setMwstValue($taxtate);
                if ($id > 0) {
                    //update
                    $mwstTable->updateMwst($myMwst);
                } else {
                    //insert
                    $mwstTable->saveMwst($myMwst);
                }
            }
        } else {
            wp_send_json_error("keine daten vorhanden");
        }
    }
}
// endif;
new RS_IB_Indiebooking_Settings_Controller();
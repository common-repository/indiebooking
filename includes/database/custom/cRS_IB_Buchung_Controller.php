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
<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} ?>
<?php
// if ( ! class_exists( 'RS_IB_Buchung_Controller' ) ) :
/**
 * @author schmitt
 * Diese Klasse soll eine fertig gespeicherte Buchung vorbereiten und die preise berechnen,
 * so dass heir eine zentrale Anlaufstelle fuer eben diese berechnungen existiert.
 */
class RS_IB_Buchung_Controller {
    
    /* @var $buchungTbl     RS_IB_Table_Buchungskopf */
    /* @var $teilbuchungTbl RS_IB_Table_Teilbuchungskopf */
    /* @var $positionTbl    RS_IB_Table_Buchungposition */
    /* @var $rabattTable    RS_IB_Table_BuchungRabatt */
    public static function prepareBuchungData($buchungNr) {
//         $buchungTbl             = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungskopf::RS_TABLE);
//         $teilbuchungTbl         = $RSBP_DATABASE->getTable(RS_IB_Model_Teilbuchungskopf::RS_TABLE);
//         $positionTbl            = $RSBP_DATABASE->getTable(RS_IB_Model_Buchungposition::RS_TABLE);
//         $rabattTable            = $RSBP_DATABASE->getTable(RS_IB_Model_BuchungRabatt::RS_TABLE);
        
//         $buchungObj             = $buchungTbl->loadBooking($buchungNr);
        
    }
    
}
// endif;
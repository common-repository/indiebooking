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
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
 * In dieser Datei steht eine Variable $printDataObj zur Verfuegung.
 * Diese Variable ist vom Typ rs_ib_print_util_data_object
 * und beinhaltet alle wichtigen Informationen, die zum druck benoetigt werden.
 * Das Objekt darf und soll an weitere Methoden und Ansichten weitergeleitet werden,
 * damit alle Dateien von diesem Objekt zehren und es zu keinen unnoetigen Datenbankzugriffen kommt
 */
/* @var $printDataObj rs_ib_print_util_data_object */

$waehrung = $printDataObj->getWaehrung();
// do_action("rs_indiebooking_print_rsappartment_buchung_contact", $printDataObj);
do_action("rs_indiebooking_print_rsappartment_buchung_detailed_payment_info", $printDataObj);
?>
<!-- das br kann ansonsten eine zweite leere Seite verursachen -->
<!-- <br /> -->
<?php

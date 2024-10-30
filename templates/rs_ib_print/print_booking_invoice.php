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

/* @var $printDataObj rs_ib_print_util_data_object */
//     $waehrung      = rs_ib_currency_util::getCurrentCurrency();
$waehrung	= $printDataObj->getWaehrung();
// apply_filters("rs_indiebooking_print_rsappartment_buchung_options", $buchungObj->getOptionenPositionen());
?>
<?php
// do_action("rs_indiebooking_print_rsappartment_buchung_contact", $printDataObj);
do_action("rs_indiebooking_print_rsappartment_buchung_detailed_payment_info", $printDataObj);
?>
<!-- <br /> -->
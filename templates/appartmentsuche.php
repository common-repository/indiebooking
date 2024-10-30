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
/**
 * Template Name: Page of Appartments
*
* Selectable from a dropdown menu on the edit page screen.
*/
?>

<?php get_header();
include('global/default_template_start.php');

$controller = new RS_IB_Appartment_Uebersicht_Controller();
$from       = rsbp_getPostValue('search_booking_date_from', "", RS_IB_Data_Validation::DATATYPE_DATUM);
$to         = rsbp_getPostValue('search_booking_date_to', "", RS_IB_Data_Validation::DATATYPE_DATUM);

if ("" !== $from && "" !== $to) {
    $controller->searchAppartmentByDate($from, $to);
} else {
    $controller->getAllAppartments();
}
// $args=array(
//     'post_type'         => RS_IB_Model_Appartment::RS_POSTTYPE,
//     'post_status'       => 'publish',
//     'posts_per_page'    => -1,
//     'ignore_sticky_posts' => 1
// );

// $my_query = null;
// $my_query = new WP_Query($args);
?>
<!-- <div class="modal"></div> -->
<br />
<br />
<input type="hidden" id="rs_ib_search_by_ajax_kz" value="true" />
<?php  do_action("rs_indiebooking_rsappartment_search_box"); ?>
<div id="found_appartment_list"></div>
<?php
   include('global/default_template_end.php');
?>
<?php //get_sidebar(); ?>
<?php get_footer(); ?>
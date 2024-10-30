<?php
/*
* Indiebooking - die Buchungssoftware fuer Ihre Homepage!
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
/**
 * Template Name: Page of Appartments
*
* Selectable from a dropdown menu on the edit page screen.
*/
?>
<?php get_header(); ?>
<?php do_action("rs_indiebooking_show_extra_overview_info"); ?>
    <header id="unterseite">
		<?php
		if (function_exists('rs_indiebooking_show_indiebooking_default_header_menu')) {
			rs_indiebooking_show_indiebooking_default_header_menu();
		} ?>
<!-- 			</a> -->
        <div id="headerimage"></div>
    </header>

    <section id="buchungsplugin_unterseite">
      	<input id="rs_ib_search_by_ajax_kz" type="hidden" value="true">
      	<input type="hidden" id="rs_ib_search_page_link"
      		value="<?php echo get_permalink(rs_indiebooking_get_apartmentuebersicht_page()->ID);?>"
  		/>
		<?php do_action("rs_indiebooking_rsappartment_search_box_small"); ?>
    </section>

    <section id="apartments_uebersicht">
 		<div class="container margin-top">
		    <div class="rewasoftzabuto_test"></div>
		    <div class="rewasoftzabuto_test"></div>
		    <div class="rewasoftzabuto_test"></div>
		    <div class="rewasoftzabuto_test"></div>
		    <div id="testid"></div>
            <div id="found_appartment_list">
                <?php
                $controller = new RS_IB_Appartment_Uebersicht_Controller();
                if (isset($_POST['search_booking_date_from']) && isset($_POST['search_booking_date_to'])) {
                    $from              	= $_POST['search_booking_date_from'];
                    $to                	= $_POST['search_booking_date_to'];
                    if (!isset($from) || "undefined" == $from) {
                    	$from 			= "";
                    }
                    if (!isset($to) || "undefined" == $to) {
                    	$to 			= "";
                    }
                    $categorie         	= rsbp_getPostValue('search_category', array());
                    $searchNrOfBeds    	= rsbp_getPostValue('search_nr_of_beds', array());
                    $searchGuests      	= rsbp_getPostValue('search_nr_of_guest', array());
                    $searchRooms       	= rsbp_getPostValue('search_nr_of_rooms', array());
                    $searchLocation    	= rsbp_getPostValue('search_location', "");
                    $searchFeatures	   	= rsbp_getPostValue('search_features', array());
                    
                    $searchData        	= new RS_IB_SearchData();

                    $searchData->setDateFrom($from);
                    $searchData->setDateTo($to);
                    $searchData->setCategorie($categorie);
                    $searchData->setNumberOfBeds($searchNrOfBeds);
                    $searchData->setNumberOfGuests($searchGuests);
                    $searchData->setLocation($searchLocation);
                    $searchData->setNumberOfRooms($searchRooms);
                    $searchData->setFeatures($searchFeatures);
                    
                    $controller->searchAppartment($searchData);
                } else {
                	$searchData        	= new RS_IB_SearchData();
                	$controller->searchAppartment($searchData);
//                     $controller->getAllAppartments();
                }
            ?>
            </div>
        </div>
    </section>
    <?php //do_action("rs_indiebooking_show_extra_overview_info"); ?>
</div>
<?php get_footer(); ?>
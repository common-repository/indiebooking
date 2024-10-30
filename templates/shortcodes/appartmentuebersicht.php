<section id="apartments_uebersicht">
	<div class="container margin-top">
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
// 		        $controller->getAllAppartments();
		    }
		?>
		</div>
	</div>
</section>
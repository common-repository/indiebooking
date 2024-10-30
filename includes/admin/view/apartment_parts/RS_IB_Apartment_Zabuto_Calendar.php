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
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$bookableDates              = json_encode($appartment->getBookableDates());
$bookableDatesEng           = json_encode($appartment->getZeitraumeDB());
$arrivalDays                = json_encode($appartment->getArrivalDays());
$bookedDates                = json_encode($bookedDates);
$notbookableDates           = json_encode($appartment->getNotbookableDates());
$futureAvailabilityYear		= get_option("rs_indiebooking_settings_future_availability");
if (!$futureAvailabilityYear) {
	$futureAvailabilityYear	= 2;
}
$curMaxDate					= new DateTime("now");
$addYears					= "P".$futureAvailabilityYear."Y";
$curMaxDate->add(new DateInterval($addYears));
$curMaxDate					= $curMaxDate->format("Y-m-d");

$apartmentZabutoCalendar    = "<div class='rs_zabuto-calendar' data-appartmentId='$post->ID'
        data-bookable='$bookableDates'  data-bookableEng='$bookableDatesEng' data-booked='$bookedDates'
        data-arrivaldays='$arrivalDays' data-currentvon='' data-currentbis=''
        data-currentVonEng='' data-currentBisEng='' data-notbookabledates='$notbookableDates'
        data-maxDate='$curMaxDate'>
    </div>";
echo $apartmentZabutoCalendar;
?>
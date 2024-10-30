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
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$appartment_id      = esc_attr($appartment_id);
$bookableDates      = esc_attr($bookableDates);
$bookableDatesEng   = esc_attr($bookableDatesEng);
$bookedDates        = esc_attr($bookedDates);
$arrivalDays        = esc_attr($arrivalDays);
$buchungVon         = esc_attr($buchungVon);
$buchungBis         = esc_attr($buchungBis);
$buchungVonEng      = esc_attr($buchungVonEng);
$buchungBisEng      = esc_attr($buchungBisEng);
$minnaechte         = esc_attr($minnaechte);
$notBookableDates   = esc_attr($notBookableDates);
$curMaxDate			= esc_attr($maxDate);

//echo $minnaechte;
?>
<div class="date_container zabuto_date_container">
<!-- item_kalender -->
    <div class="<?php echo esc_attr($myClass); ?> calender_container"><!--  calender_container -->
		<?php echo "<div class='rs_zabuto-calendar calender_container rs_ib_calendar_data_container'
		              data-appartmentId='$appartment_id'
		              data-bookable='$bookableDates' data-bookableEng='$bookableDatesEng'
                      data-booked='$bookedDates' data-arrivaldays='$arrivalDays'
                      data-currentVon='$buchungVon' data-currentBis='$buchungBis'
                      data-currentVonEng='$buchungVonEng' data-currentBisEng='$buchungBisEng'
                      data-minnaechte='$minnaechte' data-notbookabledates='$notBookableDates'
                      data-maxDate='$curMaxDate'>
            </div>";
        ?>
    </div>
</div>
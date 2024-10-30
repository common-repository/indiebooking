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
//var_dump($bookableDatesEng);

//$bookedDates
//$notBookableDates
?>
<span id="synchronize_zabuto_calendars" data-synchronize="true"></span>
<div class="row rs_ib_floating_datepicker_container" data-booked='<?php echo $bookedDates;?>'
	data-freeranges='<?php echo $bookableDatesEng; ?>'
	data-notbookabledates='<?php echo $notBookableDates; ?>'
	data-arrivaldays='<?php echo $arrivalDays; ?>'
	data-minnaechte=<?php echo $minnaechte;?>'
	data-maxDate=<?php echo $curMaxDate;?>>
    <div class="col-xs-6">
        <div class="input-group">
        	<!-- <input data-toggle="popover" data-placement="top" data-trigger="manual" data-content="<?php _e("Wrong arrival Day", "indiebooking"); ?>" type="email" readonly="true" class="form-control rewa_datepicker formular_icon_kalender booking_date_from booking_datepicker rs_ib_error_popover" name="booking_date_from" id="" placeholder="Anreise"  value="<?php //echo $buchungVon; ?>"> -->
    		<input data-toggle="popover" data-placement="top" data-trigger="manual"
    				data-content="<?php _e("Wrong arrival Day", "indiebooking"); ?>"
    				type="email" readonly="true"
    				class="form-control rs_ib_floating_datepicker rs_ib_floating_datepicker_from rewa_datepicker formular_icon_kalender booking_date_from rs_ib_error_popover"
    				name="booking_date_from" id="" placeholder="<?php _e("arrival date", "indiebooking"); ?>"  value="<?php echo $buchungVon; ?>">
    		<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
		</div>
    </div>
    <div class="col-xs-6">
        <div class="input-group">
        	<!-- <input type="email" readonly="true" class="form-control rewa_datepicker formular_icon_kalender booking_date_to booking_datepicker" name="booking_date_to" id="" placeholder="Abreise" value="<?php //echo $buchungBis; ?>">-->
        	<input type="email" readonly="true" name="booking_date_to" id=""
        			class="form-control rs_ib_floating_datepicker rs_ib_floating_datepicker_to rewa_datepicker formular_icon_kalender booking_date_to"
        			placeholder="<?php _e("departure", "indiebooking"); ?>" value="<?php echo $buchungBis; ?>">
    		<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
		</div>
    </div>
</div>
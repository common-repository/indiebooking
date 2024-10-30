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
<div class="form-group date_container" data-arrivaldays='<?php echo json_encode($arrivalDays);?>'>
    <div class="row rs_ib_floating_datepicker_container" data-currentVon=<?php echo $buchungVon; ?>
    		data-currentBis=<?php echo $buchungBis; ?> data-booked='<?php echo $bookedDates;?>'
    		data-freeranges='<?php echo $bookableDatesEng; ?>'
    		data-minnaechte=<?php echo $minnaechte;?>'
    		data-arrivaldays='<?php echo json_encode($arrivalDays);?>'>
        <div class="col-xs-6">
        	<div class="input-group">
        		<input class="rs_ib_msg_wrong_arrival_day" hidden="hidden" value="<?php _e("Wrong arrival Day", "indiebooking"); ?>" />
        		<input class="rs_ib_msg_end_bigger_begin" hidden="hidden" value="<?php _e("Arrival day is greater than departure day", "indiebooking"); ?>" />
        		<!-- <input data-toggle="popover" data-placement="top" data-trigger="manual" "type="email" readonly="true" class="form-control rewa_datepicker formular_icon_kalender booking_date_from booking_datepicker rs_ib_error_popover" name="booking_date_from" id="" placeholder="Anreise"  value="<?php //echo $buchungVon; ?>"> -->
        		<input data-toggle="popover" data-placement="top" data-trigger="manual" "type="email" readonly="true" class="form-control rs_ib_floating_datepicker rs_ib_floating_datepicker_from formular_icon_kalender booking_date_from rs_ib_error_popover" name="booking_date_from" id="" placeholder="<?php _e("arrival date", "indiebooking"); ?>"  value="<?php echo $buchungVon; ?>">
        		<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
    		</div>
        </div>
        <div class="col-xs-6">
        	<div class="input-group">
        		<!-- <input type="email" readonly="true" class="form-control rewa_datepicker formular_icon_kalender booking_date_to booking_datepicker" name="booking_date_to" id="" placeholder="Abreise" value="<?php //echo $buchungBis; ?>">-->
				<input type="email" readonly="true" class="form-control rs_ib_floating_datepicker rs_ib_floating_datepicker_to formular_icon_kalender booking_date_to"
						name="booking_date_to" id="" placeholder="<?php _e("departure", "indiebooking"); ?>" value="<?php echo $buchungBis; ?>">
        		<span class="input-group-addon glyphicon glyphicon-remove remove_datepicker_date"></span>
    		</div>
        </div>
    </div>
</div>
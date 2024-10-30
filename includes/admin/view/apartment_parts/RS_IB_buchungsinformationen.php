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

global $post;
$dates                      = $appartment->getBookableDates();
?>
    <script type="text/javascript">
        var bookableDates 	= <?php echo json_encode($appartment->getBookableDates()); ?>;
        var bookedDates   	= <?php echo json_encode($bookedDates); ?>;
        var btnDeleteText 	= "<?php _e('remove', 'indiebooking'); ?>";
    </script>
    <?php
    
//         $priceIsNet         = $appartment->getPriceIsNet();
        $ib_options         = get_option( 'rs_indiebooking_settings' );
        $priceIsNet			= "";
        if (key_exists('netto_kz', $ib_options)) {
	        $priceIsNet     = esc_attr__( $ib_options['netto_kz'] );
        }
        $checked            = "";
        $styleLblNet        = 'style="display: none;"';
        $styleLblGross      = '';
        if ($priceIsNet === "on") {
            $checked        = 'checked="checked"';
            $styleLblGross  = 'style="display: none;"';
            $styleLblNet    = '';
        }
    ?>
    <div id="appartment_booking_infos">
        <div class="rsib_container-fluid">
            <div class="rsib_row">
                <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md">
                    <div class="ibui_tabitembox">
                        <div id="appartment_booking_infos">
                            <div class="ibui_h2wrap">
                            	<h2 class="ibui_h2"><?php _e('Minimum Book Range', 'indiebooking'); ?></h2>
                            </div>
                            <div class="rsib_form-group">
                                <label class="rsib_col-xs-1 rsib_nopadding_left ibui_text_left ibui_label">
                                	<?php _e('days', 'indiebooking'); ?>
                            	</label>
                                <div class="rsib_col-xs-8 rsib_nopadding_left">
                                    <input id="appartment_min_date_range"
                                    		class="onlyInteger appartment_admin_number_spinner ibui_input ibui_tooltip_item"
                                    		name="appartment_min_date_range"
                                    		maxlength="2"
                                    		value="<?php echo esc_attr($appartment->getMinDateRange()); ?>"
                                    		title="<?php _e('Definies the minimum booking range. (lowest range is 0)', 'indiebooking'); ?>"/>
                                </div>
                            </div>
                            <div class="rsib_form-group">
                                <div class="ibui_h2wrap" style="margin-top:40px"><h2 class="ibui_h2"><?php _e('global arrival days', 'indiebooking'); ?></h2></div>
                                <div class="rsib_form-group">
                                    <div class="rsib_col-xs-12 rsib_nopadding_left">
                                        <?php
                                            $arrivalDays = $appartment->getArrivalDays();
                                            $gwChecked1  = "";
                                            $gwChecked2  = "";
                                            $gwChecked3  = "";
                                            $gwChecked4  = "";
                                            $gwChecked5  = "";
                                            $gwChecked6  = "";
                                            $gwChecked7  = "";
                                            foreach ($arrivalDays as $day) {
                                                switch ($day) {
                                                    case 1:
                                                        $gwChecked1 = "checked = 'checked'";
                                                        break;
                                                    case 2:
                                                        $gwChecked2 = "checked = 'checked'";
                                                        break;
                                                    case 3:
                                                        $gwChecked3 = "checked = 'checked'";
                                                        break;
                                                    case 4:
                                                        $gwChecked4 = "checked = 'checked'";
                                                        break;
                                                    case 5:
                                                        $gwChecked5 = "checked = 'checked'";
                                                        break;
                                                    case 6:
                                                        $gwChecked6 = "checked = 'checked'";
                                                        break;
                                                    case 7:
                                                        $gwChecked7 = "checked = 'checked'";
                                                        break;
                                                }
                                            }
                                        ?>
                                        <table id="global_anreise_tage" class="widefat rs_ib_input_table rs_ib_day_cb_table sortable">
                                            <thead>
                                                <tr>
                                                    <th>Mo</th>
                                                    <th>Di</th>
                                                    <th>Mi</th>
                                                    <th>Do</th>
                                                    <th>Fr</th>
                                                    <th>Sa</th>
                                                    <th>So</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background-color:#fff;"><input id="global_weekday_mo" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked1;?> name="chk_gloabal_weekday_group[]" value="1" /><label for="global_weekday_mo">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_di" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked2;?> name="chk_gloabal_weekday_group[]" value="2" /><label for="global_weekday_di">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_mi" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked3;?> name="chk_gloabal_weekday_group[]" value="3" /><label for="global_weekday_mi">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_do" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked4;?> name="chk_gloabal_weekday_group[]" value="4" /><label for="global_weekday_do">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_fr" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked5;?> name="chk_gloabal_weekday_group[]" value="5" /><label for="global_weekday_fr">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_sa" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked6;?> name="chk_gloabal_weekday_group[]" value="6" /><label for="global_weekday_sa">&nbsp;</label></td>
                                                    <td style="background-color:#fff;"><input id="global_weekday_so" class="ibui_checkbox" type="checkbox" <?php echo $gwChecked7;?> name="chk_gloabal_weekday_group[]" value="7" /><label for="global_weekday_so">&nbsp;</label></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="ibui_h2wrap" style="margin-top:40px"><h2 class="ibui_h2">
                                	<?php _e('Not bookable periods', 'indiebooking'); ?></h2>
                            	</div>
                                <div class="rsib_form-group">
                                    <div class="rsib_col-xs-12 rsib_nopadding_left">
                                    	<?php
                                    	$nowBookedJsonDates = json_encode($bookedDates);
                                    	$jsonArrivalDays    = json_encode($appartment->getArrivalDays());
                                    	?>
                                        <!-- data-notbookable='<?php //echo $fullyBookedDays; ?>' data-freeranges='<?php //echo $freeBookingRanges; ?>' -->
                                        <table id="apartment_not_bookable_table" class="rs_ib_input_table row rsib_table ibui_table"
                                        	data-booked='<?php echo $nowBookedJsonDates; ?>' data-arrivaldays='<?php echo $jsonArrivalDays; ?>'>
                                            <thead>
                                                <tr>
                                                    <th class="dateColumn"><?php _e('Not Bookable from', 'indiebooking'); ?></th>
                                                    <th class="dateColumn"><?php _e('Not Bookable to', 'indiebooking'); ?></th>
                                                    <th class="btn_ib_td" style="text-align:center;width:50px;"><a id="appartment_btn_add_unable_zeitraum" class="ibui_iconbtn"><span class="glyphicon glyphicon-plus-sign ibui_iconbtn_green" aria-hidden="true"></span></a></th>
                                                    <!--<th class="btn_ib_td" style="text-align:center;width:50px;"><a id="appartment_btn_add_unable_zeitraum" class="btn_rewa"><?php _e('add', 'indiebooking');?></a></th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $notbookableApDates = $appartment->getNotbookableDates();
                                                usort($notbookableApDates, array($appartment, "sortFunction3"));
                                                foreach ($notbookableApDates as $notbookableDates) {
                                                    $dateFrom   = new DateTime($notbookableDates->date_from);
                                                    $dateTo     = new DateTime($notbookableDates->date_to);
                                                ?>
                                                <tr class="appartment_not_bookable_table_row rs_ib_floating_datepicker_container"
                                                	data-booked="<?php ?>">
                                                    <td>
                                                        <input name="rs_ib_not_bookable_periods_from[]"
                                                        		class="rs_ib_floating_datepicker rs_ib_floating_datepicker_from form_icon icon_kalender rewa_datepicker"
                                                        		value="<?php echo esc_attr($dateFrom->format("d.m.Y")); ?>">
                                                    </td>
                                                    <td>
                                                        <input name="rs_ib_not_bookable_periods_to[]"
                                                        		class="rs_ib_floating_datepicker rs_ib_floating_datepicker_to form_icon icon_kalender rewa_datepicker"
                                                        		value="<?php echo esc_attr($dateTo->format("d.m.Y")); ?>">
                                                    </td>
                                                    <td style="text-align:center;width:50px;">
                                                        <a class="appartment_btn_remove_zeitraum ibui_iconbtn">
                                                        	<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
                                                    	</a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_md">
                    <div class="ibui_tabitembox">
                        <div class="ibui_h2wrap"><h2 class="ibui_h2"><label><?php _e('Currently booked dates', 'indiebooking'); ?>:</label></h2></div>
                        <div class="rsib_form-group">
                            <div class="rsib_col-xs-12">
                                <div id="currently_booked_dates">
                                    <!-- <div class="appartment_booked_date"></div> -->
                                    <?php
                                    include 'RS_IB_Apartment_Zabuto_Calendar.php';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="rsib_col-lg-6 rsib_col-md-12 rsib_nopadding_right rsib_nopadding_md">
					<div id="indiebookingcalendarking"></div>
				</div>
            </div>
        </div>
    </div>
    <br class="clear" />
    <?php wp_nonce_field('save_details', 'save_appartment_nonce_field'); ?>
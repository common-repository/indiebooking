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
?>
<div class="rsib_container-fluid">
    <div class="rsib_row">
        <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Tax settings', 'indiebooking'); ?></h2></div>
                	<div class="rsib_col-sm-4 rsib_col-xs-5 rsib_nopadding_left">
                    <table id="indiebooking_mwst_settings_table" class="rsib_table ibui_table default_ib_table settings_table">
                        <thead>
                            <tr>
                                <th><?php _e('Tax (in %)', 'indiebooking'); ?></th>
                                <th><?php _e('revenue account', 'indiebooking'); ?></th>
                                <th style="text-align:center;width:50px;">
                                	<a id="btn_add_taxes" class="ibui_iconbtn">
                                		<span class="glyphicon glyphicon-plus-sign ibui_iconbtn_green" aria-hidden="true"></span>
                            		</a>
                            	</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                               $mwstFound  = false;
                               $biggestId  = 0;
                               $nextId     = 0;
                            ?>
                            <?php if (sizeof($mwsts) > 0) {
                                $nextId             = get_option( 'rs_indiebooking_settings_mwst_nextId' );
                                if (is_null($nextId) || "" === $nextId) {
                                    $nextId = 0;
                                }
                                foreach ($mwsts as $key => $mwst) {
                                    $mwstFound  = true;
                                    if ($mwst->getMwstId() > $biggestId) {
                                        $biggestId  = $mwst->getMwstId();
                                    }
                                    if (is_null($mwst->getMwstId()) || $mwst->getMwstId() == "") {
                                        $mwst->setMwstId($biggestId);
                                    }
                                    ?>
                                    <tr>
                                        <!-- <td><input data-mwstid=<?php //echo $mwst->getMwstId(); ?> type="text" class="mwst_satz onlyNumber given_price" name="mwst_satz" value=<?php //echo $mwst->getMwstValue();?>></td> -->
                                        <td>
                                        	<input type="text" class="ibui_input mwst_satz onlyNumber given_price"
                                        			name="rs_indiebooking_settings_mwst[<?php echo $mwst->getMwstId();?>][value]"
                                        			value=<?php echo esc_attr($mwst->getMwstValue());?>>
                            			</td>
                                        <td>
                                        	<input type="text" class="ibui_input mwst_satz onlyNumber"
                                        			name="rs_indiebooking_settings_mwst[<?php echo $mwst->getMwstId();?>][revenueaccount]"
                                        			value=<?php echo esc_attr($mwst->getRevenueAccount());?>>
                            			</td>
                                        <input type="hidden"
                                        		name="rs_indiebooking_settings_mwst[<?php echo $mwst->getMwstId();?>][id]"
                                        		value='<?php echo esc_attr($mwst->getMwstId());?>' class="ibfk_admin_info_id">
                                        <td style="text-align:center;">
                                        	<a class="ibui_iconbtn rs_ib_btn_remove_row">
                                        		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
                                    		</a>
                                		</td>
                                    </tr>
                            <?php }
                            }
                            if ($biggestId > $nextId) {
                                $nextId = $biggestId+1;
                            }
                            if ($mwstFound == false) {
                                if ($nextId == 0) {
                                    $nextId = 1;
                                }
                                ?>
                                <tr>
                                    <!-- <td><input data-mwstid=<?php //echo $mwst->getMwstId(); ?> type="text" class="mwst_satz onlyNumber given_price" name="mwst_satz" value=<?php //echo $mwst->getMwstValue();?>></td> -->
                                    <td>
                                    	<input type="text" class="ibui_input mwst_satz onlyNumber given_price"
                                    			name="rs_indiebooking_settings_mwst[<?php echo $nextId;?>][value]" value="">
                        			</td>
									<td>
                                    	<input type="text" class="ibui_input mwst_satz onlyNumber"
                                        			name="rs_indiebooking_settings_mwst[<?php echo $nextId;?>][revenueaccount]"
                                        			value="">
                            		</td>
                                    <input type="hidden" name="rs_indiebooking_settings_mwst[<?php echo $nextId;?>][id]"
                                    		value='<?php echo esc_attr($nextId);?>' class="ibfk_admin_info_id">
                                    <td style="text-align:center;">
                                    	<a class="ibui_iconbtn rs_ib_btn_remove_row">
                                    		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
                            			</a>
                        			</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    </div>
                    <input type="hidden" id="rs_indiebooking_mwst_nextId" name="rs_indiebooking_settings_mwst_nextId"
                    		value='<?php echo esc_attr($nextId);?>'>
            </div>
        </div>
    </div>
</div>
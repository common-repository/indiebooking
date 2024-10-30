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
        <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_xs rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('cancellation conditions', 'indiebooking'); ?></h2></div>
                <?php
                    $bookingInvoiceTxt = get_option('rs_indiebooking_settings_stornobedingung_txt'); // this var may contain previous data that was stored in mysql.
                    wp_editor($bookingInvoiceTxt,"rs_indiebooking_settings_stornobedingung_txt", $rs_indiebooking_settings = array('editor_height' => 400)) // array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
                    // wp_editor($bookingInvoiceTxt,"rs_indiebooking_settings_stornobedingung_txt") // array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
                ?>
                
            </div>
        </div>
        <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_xs rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap">
                	<h2 class="ibui_h2">Stornobedingungen festlegen</h2>
            	</div>
                <div class="ibui_notice ibui_notice_yellow">
                <?php
                    _e("Please ensure that the defined cancellation conditions correspond to the conditions described above. A check between text and defined values is not performed.", 'indiebooking');
                ?>
                </div>
                <div class="rsib_table-responsive">
                    <!--<table id="indiebooking_storno_settings_table" class="tablesorter default_ib_table settings_table">-->
                    <table id="indiebooking_storno_settings_table" class="rsib_table ibui_table settings_table">
                        <thead>
                            <tr>
                                <th><?php _e('cancellation fees (in %)', 'indiebooking'); ?></th>
                                <th><?php _e('days before check-in', 'indiebooking'); ?></th>
                                <th style="text-align:center;width:50px;">
                                	<a id="btn_add_refund" class="ibui_iconbtn">
                                		<span class="glyphicon glyphicon-plus-sign ibui_iconbtn_green" aria-hidden="true"></span>
                            		</a>
                            	</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                //                 	   $test       = get_option( 'rs_indiebooking_settings_mwst' );
                           $stornoFound        = false;
                           $biggestStornoId    = 0;
                           $nextStornoId       = 0;
                //             	       var_dump($stornos);
                                    ?>
                            <?php
                            if (sizeof($stornos) > 0) {
                                $nextStornoId             = get_option( 'rs_indiebooking_settings_storno_nextId' );

                                if (is_null($nextStornoId) || "" === $nextStornoId) {
                                    $nextStornoId = 0;
                                }
                                foreach ($stornos as $stornokey => $storno) {
                                    if ($storno->getId() > $biggestStornoId) {
                                        $stornoFound        = true;
                                        $biggestStornoId    = $storno->getId();
                                    }
                                    ?>
                                    <tr>
                                        <!-- <td><input data-mwstid=<?php //echo $mwst->getMwstId(); ?> type="text" class="mwst_satz onlyNumber given_price" name="mwst_satz" value=<?php //echo $mwst->getMwstValue();?>></td> -->
                                        <td>
                                        	<input type="text" class="ibui_input rs_indiebooking_settings_storno_satz onlyNumber given_price"
                                        			name="rs_indiebooking_settings_storno[<?php echo $storno->getId();?>][refund_value]"
                                        			value=<?php echo esc_attr($storno->getStornovalue());?>>
                            			</td>
                                        <td>
                                        	<!--
                                        	<input type="text" class="ibui_input rs_indiebooking_settings_storno_satz onlyNumber given_price"
                                        			name="rs_indiebooking_settings_storno[<?php //echo $storno->getId();?>][days]"
                                        			value=<?php //echo $storno->getStornodays();?>>
                                			 -->
                                            <input class="onlyInteger settings_admin_number_spinner ibui_input ibui_tooltip_item"
                                            		name="rs_indiebooking_settings_storno[<?php echo $storno->getId();?>][days]"
                                            		value="<?php echo esc_attr($storno->getStornodays());?>" />
                            			</td>
                                        <td style="text-align:center;">
                                            <input type="hidden" name="rs_indiebooking_settings_storno[<?php echo $storno->getId();?>][id]"
                                            		value='<?php echo esc_attr($storno->getId());?>' class="ibfk_admin_info_id">
                                        	<a class="ibui_iconbtn rs_ib_btn_remove_row">
                                        		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
                                    		</a>
                                		</td>
                                    </tr>
                            <?php }
                            }
                            if ($biggestStornoId > $nextStornoId) {
                                $nextStornoId = $biggestStornoId+1;
                            }
                            if ($stornoFound == false) {
                                if ($nextStornoId == 0) {
                                    $nextStornoId = 1;
                                }
                                ?>
                                <tr>
                                    <td>
                                    	<input type="text" class="ibui_input rs_indiebooking_settings_storno_satz onlyNumber given_price"
                                        		name="rs_indiebooking_settings_storno[<?php echo $nextStornoId;?>][refund_value]"
                                        		value="">
                            		</td>
                                    <td>
                                    	<input class="onlyInteger settings_admin_number_spinner ibui_input ibui_tooltip_item"
                                        		name="rs_indiebooking_settings_storno[<?php echo $nextStornoId;?>][days]"
                                        		value="0" />
                            		</td>
                                        <td style="text-align:center;">
                                            <input type="hidden" name="rs_indiebooking_settings_storno[<?php echo $nextStornoId;?>][id]"
                                            		value='<?php echo esc_attr($nextStornoId);?>' class="ibfk_admin_info_id">
                                        	<a class="ibui_iconbtn rs_ib_btn_remove_row">
                                        		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
                                    		</a>
                                		</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <input type="hidden" id="rs_indiebooking_storno_nextId" name="rs_indiebooking_settings_storno_nextId"
                    		value='<?php echo esc_attr($nextStornoId);?>'>
                </div>
            </div>
        </div>
    </div>
</div>
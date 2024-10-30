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

$showOnStartPage 		= $appartment->getShowOnStartPage();
$apartmentOnlyInquireKz = $appartment->getOnlyInquire();
?>

<div id="default_appartment_infos"><!-- class="rsib_container-fluid" -->
<!--     <h3 id="yourHeaderID"></h3> -->
<!--     <p class="your-paragraph-class"></p> -->
<!--     <a id="yourAnchorID" href="#url"></a> -->
    <div class="rsib_container-fluid">
        <div class="rsib_row">
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_md">
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Shortdescription', 'indiebooking'); ?></h2></div>
                    <div class="rsib_form-horizontal">
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('Shortdescription', 'indiebooking'); ?>
                            </label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_shortdescription" maxlength="140" type="text" name="appartment_shortdescription"
                            		class="ibui_input" value="<?php echo esc_attr($appartment->getShortDescription()); ?>" style="width:90%;" />
                            		
                                <span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                            		title="<?php
                            		  _e('This description is shown in the apartment overview', 'indiebooking');
                            		?>">
                        		</span>
                    		</div>
                        </div>
                        <div class="rsib_form-group step-2">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Show on start page', 'indiebooking'); ?></label>
                            <!--
                            <input id="showOnStartPageKz" class="ibui_checkbox" type="checkbox" name="showOnStartPageKz" <?php //echo ($showOnStartPage == "on") ? "checked='checked'" : "";?>/>
                            <label for="showOnStartPageKz"></label>
                             -->
                            <div class="rsib_col-xs-8">
                                <div class="ibui_switchbtn" style="float:left;">
                                   <input id="showOnStartPageKz" class="ibui_switchbtn_input" name="showOnStartPageKz" type="checkbox" <?php echo ($showOnStartPage == "on") ? "checked='checked'" : "";?> />
                                   <label for="showOnStartPageKz"></label>
                                </div>
                                <span class="glyphicon glyphicon-info-sign ibui_tooltip_item" style="padding-left: 10px;"
                            		title="<?php
                            		  _e('This option controls if the apartment should get shown on the startpage or not. ', 'indiebooking');
                            		?>">
                        		</span>
                            </div>
                        </div>
                        <div class="rsib_form-group step-2">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Apartment only over inquire ', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                                <div class="ibui_switchbtn" style="float:left;">
                                   <input id="apartmentOnlyInquireKz" class="ibui_switchbtn_input" name="apartmentOnlyInquireKz" type="checkbox" <?php echo ($apartmentOnlyInquireKz == "on") ? "checked='checked'" : "";?> />
                                   <label for="apartmentOnlyInquireKz"></label>
                                </div>
                                <span class="glyphicon glyphicon-info-sign ibui_tooltip_item" style="padding-left: 10px;"
                            		title="<?php
                            		  _e('The option indicates if the apartment can only be requested.', 'indiebooking');
                            		?>">
                        		</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibui_tabitembox">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Location', 'indiebooking'); ?></h2></div>
                    <div class="rsib_form-horizontal">
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Location description', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_locationdescription" class="input-group-field-no-bs ibui_input"
                            			type="text" name="appartment_locationdescription"
                            			value="<?php echo esc_attr($appartment->getLocationDescription()); ?>" style="width:90%;"/>
                                <span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                            		title="<?php
                            		  _e('The Location description can help the user to filter his search by location.', 'indiebooking');
                            		?>">
                        		</span>
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Street', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_street" name="appartment_street"
                            			class="input-group-field-no-bs ibui_input"
                            			value="<?php echo esc_attr($appartment->getStreet()); ?>" />
                			</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Zip code', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_zip_code" name="appartment_zip_code" class="input-group-field-no-bs ibui_input"
                            		value="<?php echo esc_attr($appartment->getZipCode()); ?>" />
                    		</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Location', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_location"  name="appartment_location" class="input-group-field-no-bs ibui_input"
                            			value="<?php echo esc_attr($appartment->getLocation()); ?>" />
            				</div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Square meter', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_square_meter" name="appartment_square_meter" class="onlyNumber input-group-field-no-bs ibui_input"
                            		value="<?php echo esc_attr($appartment->getQuadratmeter()); ?>" />
                    		</div>
                        </div>
                        <?php do_action("rs_indiebooking_apartment_settings_google_informations", $appartment); ?>
                        <div class="rsib_form-group">
                            <div style="width:100%;text-align:center;padding:25px 0px 0px 0px;">
                                <a id="appartment_btn_insert_company_infos" class="ibui_add_btn"><?php _e("Insert company infos", 'indiebooking'); ?></a>
                                <span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
                            		title="<?php
                            		  _e('With this button you insert the infos you added in the company settings. ', 'indiebooking');
                            		?>">
                        		</span>
                                <!--<a id="appartment_btn_insert_company_infos" class="btn_rewa ibui_btn"><?php //_e("Insert company infos", 'indiebooking'); ?></a>-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_md">
                <div class="ibui_tabitembox ibui_admin_apartment_room_box">
                    <div class="ibui_h2wrap"><h2 class="ibui_h2"><?php _e('Rooms', 'indiebooking'); ?></h2></div>
                    <div class="rsib_form-horizontal">
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('Rooms', 'indiebooking'); ?>
                        	</label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_anzahl_zimmer"
                            			class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                            			name="appartment_anzahl_zimmer" style="min-width:45px;" maxlength="2"
                            			value="<?php echo esc_attr($appartment->getAnzahlZimmer()); ?>" title=""/>
                			</div>
                        </div>
                        
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('number of single beds', 'indiebooking'); ?>
                        	</label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_anzahl_einzel_betten"
                            			class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                            			name="appartment_anzahl_einzel_betten"
                            			style="min-width:45px;"
                            			maxlength="2"
                            			value="<?php echo esc_attr($appartment->getAnzahlEinzelBetten()); ?>" title=""/>
                			</div>
                        </div>
                        
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('number of doule beds', 'indiebooking'); ?>
                        	</label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_anzahl_doppel_betten"
                            		class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                            		name="appartment_anzahl_doppel_betten"
                            		style="min-width:45px;"
                            		maxlength="2"
                            		value="<?php echo esc_attr($appartment->getAnzahlDoppelBetten()); ?>" title=""/>
                    		</div>
                        </div>
                        
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e('Max. number of beds', 'indiebooking'); ?></label>
                            <div class="rsib_col-xs-8">
                                <span class="ui-spinner ui-widget ui-widget-content ui-corner-all" style="height: 30px;">
                                  <input id="appartment_anzahl_betten" type="hidden"
                                  			class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                                  			name="appartment_anzahl_betten" style="min-width:45px;" maxlength="2"
                                  			value="<?php echo $appartment->getAnzahlBetten(); ?>" title=""/>
                                  <input id="appartment_anzahl_betten_2" disabled="disabled"
                                  		class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                                  		name="appartment_anzahl_betten_2" style="min-width:45px;" maxlength="2"
                                  		value="<?php echo esc_attr($appartment->getAnzahlBetten()); ?>" title=""/>
                                </span>
                               <?php //var_dump($appartment->getAnzahlBetten());?>
                            </div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('Max. number of guests', 'indiebooking'); ?>
                            </label>
                            <div class="rsib_col-xs-8">
                            	<input id="appartment_anzahl_personen"
                            			class="onlyInteger appartment_admin_number_spinner input-group-field-no-bs ibui_input"
                            			name="appartment_anzahl_personen" style="min-width:45px;" maxlength="2"
                            			value="<?php echo esc_attr($appartment->getAnzahlPersonen()); ?>" title=""/></div>
                        </div>
                        <div class="rsib_form-group">
                            <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label">
                            	<?php _e('Preservation in the apartment view', 'indiebooking'); ?>
                            </label>
                            <div class="rsib_col-xs-8">
	                            <select id="appartment_anzahl_personen_vorbelegung"
	                            		class="rewa_combobox input-group-field-no-bs ibui_select"
	                            		name="appartment_anzahl_personen_vorbelegung">
	                                <?php
	                                    $vorbelegung 	= 1;
	                                    $anzPersonen 	= $appartment->getAnzahlPersonen();
	                                    $apVorbelegung 	= $appartment->getAnzahlPersonenVorbelegung();
	                                    if ($apVorbelegung > $anzPersonen) {
	                                    	$apVorbelegung = $anzPersonen;
	                                    }
	                                    while ($vorbelegung <= $anzPersonen) {
	                                        if ($apVorbelegung == $vorbelegung) {
	                                            $selected = 'selected="selected"';
	                                        } else {
	                                            $selected = "";
	                                        }
	                                    ?>
	                                        <option <?php echo $selected;?>
	                                        		value="<?php echo esc_attr($vorbelegung);?>">
	                                        			<?php echo $vorbelegung; ?>
	                                		</option>
	                                	<?php
	                                		$vorbelegung++;
	                                    }
	                                ?>
	                            </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php do_action("rs_indiebooking_apartment_settings_bookingcom_informations", $appartment); ?>
            </div>
        </div>
    </div>
</div>
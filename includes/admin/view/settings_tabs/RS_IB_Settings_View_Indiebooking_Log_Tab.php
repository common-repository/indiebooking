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
$files = RS_Indiebooking_Log_Controller::getLastXLogFiles(7);
?>
<div class="rsib_container-fluid">
    <div class="rsib_row">
        <div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left rsib_nopadding_md2">
            <div class="ibui_tabitembox">
                <div class="ibui_h2wrap">
                	<h2 class="ibui_h2"><?php _e("Your logs", 'indiebooking');?></h2>
                </div>
<!--             	<div id="indiebooking_refresh_log" class="glyphicon glyphicon-refresh">&nbsp;</div> -->
				<div>
                	<?php
                	foreach ($files as $file) {
                	    $pagename      = get_bloginfo("name");
                	    $pagename      = str_replace(" ", "", $pagename);
                	    $pagename      = str_replace("/", "", $pagename);
                	    $pagename      = str_replace(":", "", $pagename);
                	    $filepath      = "log_".$pagename;
                	    $shortname     = str_replace($filepath, '', $file);
                	    $shortname     = str_replace('.json', '', $shortname);
                	    $jahr          = substr($shortname, 0,4);
                	    $monat         = substr($shortname, 5,2);
                	    $tag           = substr($shortname, 8,2);
                	    $shortname     = $tag.".".$monat.".".$jahr;
                	    ?>
                		<div class="indiebooking-log-file-link ibui_add_btn"
                			data-filename="<?php echo $file; ?>">
                			<?php echo $shortname; ?>
                		</div>
                	    <?php
                	}
                	?>
    				<br class="clear" />
            	</div>
            	<div id="logloading" class="small_modal">&nbsp;</div>
                <div id="indiebooking-admin-settings-log-table"></div>
            </div>
        </div>
        <!--
        <div class="rsib_col-lg-4 rsib_col-md-12 rsib_nopadding_right rsib_nopadding_md">
        	<div class="ibui_tabitembox">
        		<div class="ibui_pro_notice">
                	<a href="http://www.indiebooking.de" target="_blank">www.indiebooking.de</a>
                </div>
        	</div>
        </div>
         -->
    </div>
</div>

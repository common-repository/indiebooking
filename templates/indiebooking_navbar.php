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
/*
 * This file get's included by the function: rs_indiebooking_show_indiebooking_default_header_menu
 * if the parameter $seite is bigger than 1 it means, that this is the header for a subsite.
 * If it is 1, it means this is the startpage
 */
$navbarclass = "navbar-indiebooking_startseite";
if ($seite > 1) {
    $navbarclass = "navbar-indiebooking";
}
?>
<nav class="navbar <?php echo $navbarclass; ?> navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
            		data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        		<span class="glyphicon glyphicon glyphicon-menu-hamburger" aria-hidden="true"></span>
    		</button>
    		<?php
    		$pageImage      = get_custom_logo();
    		if (isset($pageImage) && '' !== $pageImage) {
        		echo $pageImage;
    		}
    		?>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
                <?php
                wp_nav_menu(array(
                    'menu_class' => 'nav navbar-nav navbar-right',
                    'theme_location' => 'header-menu',
                    'fallback_cb' => 'rs_indiebooking_show_indiebooking_default_menu',
                    'depth' => 1
                ));
                ?>
        </div>
    </div>
</nav>
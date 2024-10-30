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
<?php if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} ?>
<?php
// if ( ! class_exists( 'cRS_Template_Loader' ) ) :
class cRS_Template_Loader
{
    /**
     * Hook in methods
     */
    public static function init() {
//         include_once(  cRS_Indiebooking::instance()->plugin_path().'/includes/rs_ib_template_functions.php' );
//         include_once(  cRS_Indiebooking::instance()->plugin_path().'/includes/rs_ib_template_hooks.php' );

        add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
        
    }
    //nur mal einen test
    public static function rs_ib_get_template_part( $slug, $name = '' ) {
        $template               = '';
        $rs_ib_templatePath     = cRS_Indiebooking::instance()->template_path();
        $rs_ib_pluginPath       = cRS_Indiebooking::plugin_path();
    	if ( $name ) {
    		$template = locate_template( array( "{$slug}-{$name}.php", $rs_ib_templatePath . "{$slug}-{$name}.php" ) );
    	}
    
    	// Get default slug-name.php
    	if ( ! $template && $name && file_exists( $rs_ib_pluginPath . "/templates/{$slug}-{$name}.php" ) ) {
    		$template = $rs_ib_pluginPath . "/templates/{$slug}-{$name}.php";
    	}
    
    	if ( ! $template ) {
    		$template = locate_template( array( "{$slug}.php", $rs_ib_templatePath . "{$slug}.php" ) );
    	}
    
    	if ( $template ) {
    		load_template( $template, false );
    	}
    }
    
    public static function rs_ib_locate_template( $template_name, $template_path = '', $default_path = '' ) {
        if ( ! $template_path ) {
            $template_path = cRS_Indiebooking::instance()->template_path();
        }
    
        if ( ! $default_path ) {
            $default_path = cRS_Indiebooking::plugin_path() . '/templates/';
        }
    
        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                trailingslashit( $template_path ) . $template_name,
                $template_name
            )
        );
    
        // Get default template
        if ( ! $template ) {
            $template = $default_path . $template_name;
        }
    
        // Return what we found
        return apply_filters( 'rs_ib_locate_template', $template, $template_name, $template_path );
    }
    
    /**
     * Get other templates (e.g. product attributes) passing attributes and including the file.
     *
     * @access public
     * @param string $template_name
     * @param array $args (default: array())
     * @param string $template_path (default: '')
     * @param string $default_path (default: '')
     */
    public static function rs_ib_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
        if ( $args && is_array( $args ) ) {
            extract( $args );
        }
    
        $located = self::rs_ib_locate_template( $template_name, $template_path, $default_path );
    
        include( $located );
    }
    
    public static function template_loader( $template ) {
        $find = array( 'rewasoft_booking_plattform.php' );
        $file = '';
//         if ( is_page("rs_appartmentoverview") ) {
//         if (is_page("Appartment Overview")) { works!
        /*
         * Update Carsten Schmitt 06.03.2018
         * das Apartmentuebersichttemplate soll nicht mehr hardcoded sein, sondern die Uebersicht
         * soll ueber einen shortcode eingefuegt werden koennen.
         * Damit wird man etwas freier, was die wahl des Templates angeht.
         *
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page_apartment.php'
        ));
        foreach($pages as $page){
            if (is_page($page)) {
                $file 	= 'appartmentuebersicht.php';
                $find[] = $file;
                $find[] = 'templates/' . $file;
            }
        }
        */
        
//         $apartmentOverview = get_page_by_path('rs_appartmentoverview');
//         if (is_page(_x( 'Appartment Overview', 'Page title', 'rs_indiebooking' ))) {
// //         if (is_page(392)) {
//             $file 	= 'appartmentuebersicht.php';
//             $find[] = $file;
//             $find[] = 'templates/' . $file;
// //             $find[] = self::rs_ib_get_template('appartmentuebersicht.php', array());
//         }
        
        if (is_page(_x( 'Search Appartment', 'Page title', 'rs_indiebooking' ))) {
            //         if (is_page(392)) {
            $file 	= 'appartmentsuche.php';
            $find[] = $file;
            $find[] = 'templates/' . $file;
        }
        
        if ( is_single() && get_post_type() == 'rsappartment' ) {
            $file 	= 'single-rsappartment.php';
            $find[] = $file;
            $find[] = 'templates/' . $file;
    
        } elseif ( is_single() && get_post_type() == 'rsappartment_buchung' ) {
            $file 	= 'single-rsappartment_buchung.php';
            $find[] = $file;
            $find[] = 'templates/' . $file;
        }
    
        if ( $file ) {
            $template       = locate_template( array_unique( $find ) );
            if ( ! $template) {
                $template = RS_INDIEBOOKING_INIT()->plugin_path() . '/templates/' . $file;
            }
        }
    
        return $template;
    }
}
// endif;
cRS_Template_Loader::init();
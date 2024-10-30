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
}
/*
 * Diese Klasse dient dazu, eventuelle Fehler von wpml zu korrigieren.
 *
 */
class RS_Indiebooking_Fix_WPML
{
	public static function init() {
		add_action('plugins_loaded', array('RS_Indiebooking_Fix_WPML', 'checkFunctionsToFix'));
		add_action('plugins_loaded',  array('RS_Indiebooking_Fix_WPML', 'indiebooking_wpml_fix_ajax_install'));
	}
	
	public static function checkFunctionsToFix() {
		if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
			/*
			 * Pruefung ob WPML mit der Version 4.0.0 oder hoeher installiert ist.
			 * Ist das nicht der Fall, fuege ich meinen Filter hinzu, der das AJAX Problem loesen soll.
			 * Die Funktionen dazu habe ich aus dem Beta-Plugin der Version 4.0.0 herauskopiert.
			 */
			$pluginData = get_file_data(ABSPATH . 'wp-content/plugins/sitepress-multilingual-cms/sitepress.php', array('Version'));
			$version 	= $pluginData[0];
			$version 	= str_replace('.', '', $version);
			$version 	= (intval($version));
			if ($version < 400) {
				add_filter('wpml_should_use_display_as_translated_snippet', array( 'RS_Indiebooking_Fix_WPML', 'filter_post_types' ), 15, 2 );
			}
		}
	}
	
	public static function filter_post_types($should_use_snippet, array $post_type) {
		return $should_use_snippet || self::is_media_ajax_query( $post_type ) || self::is_admin_media_list_page() || self::is_frontend_ajax_request();
// 		if ( is_admin() && ! wp_doing_ajax() ) {
// 			return false !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax' )
// 			&& isset( $_REQUEST['action'] ) && 'query-attachments' === $_REQUEST['action']
// 			&& array_key_exists( 'attachment', $post_type );
// 		} else {
// 			if (array_key_exists('rsappartment', $post_type)) {
// 				return true;
// 			} else {
// 				return $should_use_snippet;
// 			}
// 		}
	}
	
	public static function indiebooking_wpml_fix_ajax_install() {
		if ( function_exists('icl_object_id') ) {
			global $sitepress;
			if(defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action'])){
				// remove WPML legacy filter, as it is not doing its job for ajax calls
				// 		if (key_exists('ib_lang', $_REQUEST)) {
				if (isset($_REQUEST["ib_lang"])) {
					$my_current_lang 	 = $_REQUEST['ib_lang'];
					remove_filter('locale', array($sitepress, 'locale'));
					add_filter('locale', 'wpml_ajax_fix_locale');
					$my_current_lang	 = apply_filters( 'wpml_current_language', NULL );
					do_action( 'wpml_switch_language',  $my_current_lang );
					$sitepress->switch_lang($my_current_lang);
				}
				function wpml_ajax_fix_locale($locale){
					global $sitepress;
					if (isset($_REQUEST["ib_lang"])) {
						// simply return the locale corresponding to the "lang" parameter in the request
						$my_current_lang = $_REQUEST['ib_lang'];
						// 			$my_current_lang = apply_filters( 'wpml_current_language', NULL );
						// 			$my_current_lang = 'en';
						do_action( 'wpml_switch_language',  $my_current_lang );
						$sitepress->switch_lang($my_current_lang);
					} else {
						$my_current_lang = $sitepress->get_current_language();
					}
					return $sitepress->get_locale($my_current_lang);
				}
			}
		}
	}
	
	private static function is_admin_media_list_page() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}
		
		$screen = get_current_screen();
		if (
			null !== $screen &&
			'upload' === $screen->base &&
			'list' === get_user_meta( get_current_user_id(), 'wp_media_library_mode', true )
		) {
			return true;
		}
		return false;
	}
	
	private static function is_media_ajax_query( array $post_type ) {
		return false !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax' )
			&& isset( $_REQUEST['action'] ) && 'query-attachments' === $_REQUEST['action']
			&& array_key_exists( 'attachment', $post_type );
	}
	
	
	public static function is_frontend_ajax_request() {
		return wpml_is_ajax() && isset( $_SERVER['HTTP_REFERER'] ) && false === strpos( $_SERVER['HTTP_REFERER'], admin_url() );
	}
	

	function wpml_is_ajax() {
		if ( defined( 'DOING_AJAX' ) ) {
			return true;
		}
		
		return ( isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) && wpml_mb_strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) == 'xmlhttprequest' ) ? true : false;
	}
	
}
RS_Indiebooking_Fix_WPML::init();
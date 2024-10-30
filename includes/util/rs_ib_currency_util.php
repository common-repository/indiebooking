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
// if ( ! class_exists( 'rs_ib_currency_util' ) ) :
/**
 * @author schmitt
 * @file
 *
 */
class rs_ib_currency_util
{
	
	private static $currentCurrency = null;
	
	public static function getAvailableCurrencyArray() {
		$currencyArray = array(
				'EUR',
				'USD',
				'GBP',
				'CHF',
				'CAD',
				'CZK',
				'AUD',
				'DKK',
				'HKD',
				'HUF',
				'JPY',
				'NOK',
				'NZD',
				'PLN',
				'SEK',
				'SGD',
		);
		asort($currencyArray);
		return $currencyArray;
	}
    
	public static function getCurrentCurrency() {
		if (is_null(self::$currentCurrency)) {
			$currency			= get_option( 'rs_indiebooking_settings_currency' );
			if (!$currency) {
				$currency		= 'EUR';
			}
			self::$currentCurrency	= $currency;
		} else {
			$currency			= self::$currentCurrency;
		}
		return $currency;
	}
}
// endif;

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
// if ( ! class_exists( 'RS_IB_Example_Data' ) ) :
class RS_IB_Example_Data
{
    public static function createTestData() {
//         if (post_type_exists('rsappartment')) {
            $testPostType = array(
                'post_title'    => 'TestInstallAppartment',
                'post_type'     => 'rsappartment',
                'post_content'  => 'Tesappartment nach installation',
            );
            $postId = wp_insert_post($testPostType);
//         } else {
//             $testPostType = array(
//                 'post_title'    => 'TestInstallAppartment',
//                 'post_type'     => 'product',
//                 'post_content'  => 'Tesappartment nach installation',
//             );
//             $postId = wp_insert_post($testPostType);
//         }
    }
    
    public static function createDefaultData() {
        self::createDefaultMwstData();
        self::createDefaultMailData();
    }
    
    public static function createDefaultMwstData() {
        $results            = get_option( 'rs_indiebooking_settings_mwst' );
        if (!$results) {
            $nextId         = get_option( 'rs_indiebooking_settings_mwst_nextId' );
            if (!$nextId) {
                $nextId     = 1;
            }
            $mwstDefault    = array();
            $mwstDef1       = array(
                'id'    => $nextId++,
                'value' => "19,00"
            );
            array_push($mwstDefault, $mwstDef1);
            $mwstDef2       = array(
                'id'    => $nextId++,
                'value' => "7,00"
            );
            array_push($mwstDefault, $mwstDef2);
            update_option('rs_indiebooking_settings_mwst', $mwstDefault);
            update_option('rs_indiebooking_settings_mwst_nextId', $nextId);
        }
    }
    
    public static function createDefaultMailData() {
        //Buchungsbestaetigung
        if (!get_option('rs_indiebooking_settings_mail_confirm_subject')) {
            add_option('rs_indiebooking_settings_mail_confirm_subject', 'Vielen Dank für Ihre Buchung');
        }
        if (!get_option('rs_indiebooking_settings_mail_booking_confirmation_txt')) {
            $text = 'Sehr geehrte(r) $$__SALUTATION__$$ $$__CUSTOMER__$$. Im Anhang finden Sie Ihre Buchungsbestätigung';
            add_option('rs_indiebooking_settings_mail_booking_confirmation_txt', $text);
        }
        
        //Zahlungsbestaetigung
        if (!get_option('rs_indiebooking_settings_mail_invoice_subject')) {
            add_option('rs_indiebooking_settings_mail_invoice_subject', 'Zahlungsbestaetigung');
        }
        if (!get_option('rs_indiebooking_settings_mail_booking_invoice_txt')) {
            $text = 'Sehr geehrte(r)  $$__SALUTATION__$$ $$__CUSTOMER__$$. Im Anhang finden Sie Ihre Zahlungsbestätigung';
            add_option('rs_indiebooking_settings_mail_booking_invoice_txt', $text);
        }
        
        //Anzahlung erinnerung
        if (!get_option('rs_indiebooking_settings_mail_deposit_reminder_subject')) {
        	add_option('rs_indiebooking_settings_mail_deposit_reminder_subject', __('Deposit reminder'));
        }
        if (!get_option('rs_indiebooking_settings_mail_deposit_reminder_txt')) {
        	$text = 'Sehr geehrte(r)  $$__SALUTATION__$$ $$__CUSTOMER__$$. Bitte denken Sie an die Restzahlung Ihrer Buchung';
        	add_option('rs_indiebooking_settings_mail_deposit_reminder_txt', $text);
        }
        
        //Stornobestaetigung
        if (!get_option('rs_indiebooking_settings_mail_storno_subject')) {
            add_option('rs_indiebooking_settings_mail_storno_subject', 'Stornierungsbestätigung');
        }
        if (!get_option('rs_indiebooking_settings_mail_storno_confirmation_txt')) {
            $text = 'Sehr geehrte(r)  $$__SALUTATION__$$ $$__CUSTOMER__$$. Im Anhang finden Sie Ihre Stornierungsbestätigung';
            add_option('rs_indiebooking_settings_mail_storno_confirmation_txt', $text);
        }
    }
}
// endif;
?>
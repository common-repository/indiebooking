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
/*
 * Diese Datei ist fuer das Hinzufuegen der Buttons in bspw. den Mail-Editoren zustaendig.
 * Damit gewaehrleistet ist, dass die Funktion erhalten bleibt, bleibt diese Datei eigenstaendig
 */
(function() {
//    tinymce.PluginManager.add('gavickpro_tc_button', function( editor, url ) {
	tinymce.PluginManager.add('rs_indiebooking_tc_button', function( editor, url ) {
    	var emailEditors = [
    	        "rs_indiebooking_settings_mail_booking_confirmation_txt",
    	        "rs_indiebooking_settings_mail_booking_invoice_txt",
    	        "rs_indiebooking_settings_mail_storno_confirmation_txt",
    	        "rs_indiebooking_settings_mail_inquiry_deny_txt",
    	        "rs_indiebooking_settings_mail_inquiry_confirmation_txt",
    	        ];
//    	if (editor.id == "rs_indiebooking_settings_mail_booking_invoice_txt") {
    	if (jQuery.inArray(editor.id, emailEditors) >= 0) {
//            editor.addButton( 'gavickpro_tc_button', {
    		editor.addButton( 'rs_indiebooking_tc_button', {
                text: 'Add Customer Name',
                icon: false,
                onclick: function() {
                    editor.insertContent('$$__CUSTOMER__$$'); //an der Cursorstelle
                }
            });
    		
    		editor.addButton( 'rs_indiebooking_tc_button_2', {
                text: 'Add Booking Number',
                icon: false,
                onclick: function() {
                    editor.insertContent('$$__BOOKINGNR__$$'); //an der Cursorstelle
                }
            });      		
    		
    		editor.addButton( 'rs_indiebooking_tc_button_3', {
    			text: 'Add saluation',
    			icon: false,
    			onclick: function() {
    				editor.insertContent('$$__SALUTATION__$$'); //an der Cursorstelle
    			}
    		});    
    		
    		editor.addButton( 'rs_indiebooking_tc_button_4', {
    			text: 'Add title',
    			icon: false,
    			onclick: function() {
    				editor.insertContent('$$__TITLE__$$'); //an der Cursorstelle
    			}
    		});     		
    		
    	} else {
    		//console.log("nope");
    	}
    });
})();
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

$rs_indiebooking_emailTextTxt	= __('E-Mail text: ', 'indiebooking');
$rs_indiebooking_settings = array(
    //'tinymce' => true,
    //'teeny' => true,
    'media_buttons' => FALSE,
    'editor_class'=>'email_editor',
    'editor_height' => 400
);
?>

<?php
function rs_indiebooking_admin_setting_show_inquire_confirm_box($emailTextTxt, $settings) {
	if ( function_exists('icl_object_id') ) {
		global $sitepress;
	}
	$mail_inquiry_subject   = get_option('rs_indiebooking_settings_mail_inquiry_subject');
	$inquiryConfirmTxt      = get_option('rs_indiebooking_settings_mail_inquiry_confirmation_txt');
	$bookingInquiriesKz		= get_option('rs_indiebooking_settings_booking_inquiries_kz');
	$notActivatedClass		= "";
	if ($bookingInquiriesKz != "on") {
		$notActivatedClass		= "ibui_h2_deactivated";
	}
	?>
    <!-- Anfragebestaetigung -->
    <div class="ibui_tabitembox rsib_nomargin_bottom">
    	<div class="ibui_h2wrap ibfc_toggle_mail_content_header">
	    	<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
        	<h2 class="ibui_h2 <?php echo $notActivatedClass; ?>">
        		<?php _e('Inquiry confirmation E-Mail: ', 'indiebooking');?>
        	</h2>
        </div>
        <div class="toggle_mail_content" style="display: none;">
	    <div class="rsib_form-group">
	    	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
	        	<?php _e('Inquiry confirmation subject: ', 'indiebooking')?>
	        </label>
	        <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_inquiry_subject'
	        		type='text' name=rs_indiebooking_settings_mail_inquiry_subject
	        		value="<?php echo $mail_inquiry_subject; ?>">
	        </div>
	        <div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
	        		<?php echo $emailTextTxt;?>
	            </label>
	            <span class="email_editor">
	            <?php
	            	wp_editor($inquiryConfirmTxt,"rs_indiebooking_settings_mail_inquiry_confirmation_txt",$settings);
	            ?>
	            </span>
	        </div>
        </div>
    </div>
	<?php
}
?>

<?php
function rs_indiebooking_admin_setting_show_booking_confirm_box($emailTextTxt, $settings) {
	
	$mail_confirm_subject   = get_option('rs_indiebooking_settings_mail_confirm_subject');
	$bookingConfirmTxt      = get_option('rs_indiebooking_settings_mail_booking_confirmation_txt');
?>
<!-- Bestuetigungsmaileinstellungen -->
	<div class="ibui_tabitembox rsib_nomargin_bottom">
    	<div class="ibui_h2wrap ibfc_toggle_mail_content_header">
        	<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
        	<h2 class="ibui_h2">
        		<?php _e('Booking confirmation E-Mail: ', 'indiebooking');?>
        	</h2>
        </div>
        <div class="toggle_mail_content" style="display: none;">
	    	<div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
	            	<?php _e('Booking confirmation subject: ', 'indiebooking')?>
	            </label>
	            <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_confirm_subject'
	            		type='text' name='rs_indiebooking_settings_mail_confirm_subject'
	            		value="<?php echo $mail_confirm_subject; ?>">
	        </div>
	        <div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large"><?php echo $emailTextTxt;?></label>
		        <span class="email_editor">
		        <?php
		        	wp_editor($bookingConfirmTxt,"rs_indiebooking_settings_mail_booking_confirmation_txt",$settings); //array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
					?>
		        </span>
	    	</div>
    	</div>
	</div>
<?php
}
?>

<?php
function rs_indiebooking_admin_setting_show_payment_confirm_box($emailTextTxt, $settings) {
	$mail_invoice_subject   = get_option('rs_indiebooking_settings_mail_invoice_subject');
	$bookingInvoiceTxt      = get_option('rs_indiebooking_settings_mail_booking_invoice_txt');
?>
	<!-- ehemalig: Rechnungsmaileinstellungen -->
    <!-- jetzt: Zahlungsbestuetigungsmaileinstellungen -->
    <div class="ibui_tabitembox rsib_nomargin_bottom">
    	<div class="ibui_h2wrap ibfc_toggle_mail_content_header">
        	<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
        	<h2 class="ibui_h2">
        		<?php _e('Payment confirmation E-Mail: ', 'indiebooking');?>
        	</h2>
        </div>
        <div class="toggle_mail_content" style="display: none;">
	    	<div class="rsib_form-group">
		        <label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
		        	<?php _e('Payment confirmation subject: ', 'indiebooking');?>
		        </label>
		        <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_invoice_subject' type='text'
		        		name='rs_indiebooking_settings_mail_invoice_subject'
		        		value="<?php echo esc_attr($mail_invoice_subject); ?>">
	        </div>
	        <div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large"><?php echo $emailTextTxt;?></label>
	            <?php
	            wp_editor($bookingInvoiceTxt,"rs_indiebooking_settings_mail_booking_invoice_txt", $settings) // array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
	            ?>
	        </div>
    	</div>
	</div>
<?php
}
?>

<?php
function rs_indiebooking_admin_setting_show_storno_confirm_box($emailTextTxt, $settings) {
	$mail_storno_subject    = get_option('rs_indiebooking_settings_mail_storno_subject');
	$StornoConfirmationTxt  = get_option('rs_indiebooking_settings_mail_storno_confirmation_txt');
?>
	<!-- Stornomaileinstellungen -->
    <div class="ibui_tabitembox rsib_nomargin_bottom">
        <div class="ibui_h2wrap ibfc_toggle_mail_content_header">
        	<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
        	<h2 class="ibui_h2">
        		<?php _e('Storno confirmation E-Mail: ', 'indiebooking');?>
        	</h2>
        </div>
        <div class="toggle_mail_content" style="display: none;">
	    	<div class="rsib_form-group">
		        <label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
		        	<?php _e('Storno subject: ', 'indiebooking');?>
		        </label>
		        <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_storno_subject' type='text'
		        		name='rs_indiebooking_settings_mail_storno_subject'
		        		value="<?php echo esc_attr($mail_storno_subject); ?>">
	        </div>
	        <div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large"><?php echo $emailTextTxt;?></label>
	            <?php
	            wp_editor($StornoConfirmationTxt,"rs_indiebooking_settings_mail_storno_confirmation_txt", $settings) // array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
	            ?>
	        </div>
    	</div>
    </div>
<?php
}
?>

<?php
function rs_indiebooking_admin_setting_show_anzahlung_errinerung_box($emailTextTxt, $settings) {
	$mail_subject   		= get_option('rs_indiebooking_settings_mail_deposit_reminder_subject');
	$mailText  				= get_option('rs_indiebooking_settings_mail_deposit_reminder_txt');
	$paymentlData 			= get_option( 'rs_indiebooking_settings_payment');
	if ($paymentlData) {
		$depositKz			= (key_exists('activedeposit_kz', $paymentlData)) ? esc_attr__( $paymentlData['activedeposit_kz'] ) : "off";
	} else {
		$depositKz 			= "off";
	}
	if ($depositKz == "on") {
	?>
	<!-- Stornomaileinstellungen -->
    <div class="ibui_tabitembox rsib_nomargin_bottom">
        <div class="ibui_h2wrap ibfc_toggle_mail_content_header">
        	<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
        	<h2 class="ibui_h2">
        		<?php _e('Deposit reminder E-Mail: ', 'indiebooking');?>
        	</h2>
        </div>
        <div class="toggle_mail_content" style="display: none;">
	    	<div class="rsib_form-group">
		        <label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
		        	<?php _e('Deposit reminder subject: ', 'indiebooking');?>
		        </label>
		        <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_deposit_reminder_subject' type='text'
		        		name='rs_indiebooking_settings_mail_deposit_reminder_subject'
		        		value="<?php echo esc_attr($mail_subject); ?>">
	        </div>
	        <div class="rsib_form-group">
	        	<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large"><?php echo $emailTextTxt;?></label>
	            <?php
	            wp_editor($mailText,"rs_indiebooking_settings_mail_deposit_reminder_txt", $settings) // array('textarea_rows'=>20, 'editor_class'=>'mytext_class'));
	            ?>
	        </div>
    	</div>
    </div>
<?php
	}
}
?>



<?php
function rs_indiebooking_admin_setting_show_inquire_deny_box($emailTextTxt, $settings) {
	$mail_inquiry_deny_subject  = get_option('rs_indiebooking_settings_mail_inquiry_deny_subject');
	$inquiryDenyTxt      		= get_option('rs_indiebooking_settings_mail_inquiry_deny_txt');
	$bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
	$notActivatedClass			= "";
	if ($bookingInquiriesKz != "on") {
		$notActivatedClass		= "ibui_h2_deactivated";
	}
?>
	<!-- Anfrageablehnung -->
	<div class="ibui_tabitembox rsib_nomargin_bottom">
		<div class="ibui_h2wrap ibfc_toggle_mail_content_header">
			<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-down"></span>
			<h2 class="ibui_h2 <?php echo $notActivatedClass; ?>">
				<?php _e('Inquiry deny E-Mail: ', 'indiebooking');?>
			</h2>
		</div>
		<div class="toggle_mail_content" style="display: none;">
			<div class="rsib_form-group">
				<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
			    	<?php _e('Inquiry deny subject: ', 'indiebooking')?>
			    </label>
			    <input class="mail_subject ibui_input" id='rs_indiebooking_settings_mail_inquiry_deny_subject'
			    		type='text' name=rs_indiebooking_settings_mail_inquiry_deny_subject
			    		value="<?php echo $mail_inquiry_deny_subject; ?>">
			</div>
			<div class="rsib_form-group">
				<label class="rsib_col-xs-12 rsib_nopadding_left ibui_label ibui_label_large">
					<?php echo $emailTextTxt;?>
				</label>
				<span class="email_editor">
				<?php
					wp_editor($inquiryDenyTxt,"rs_indiebooking_settings_mail_inquiry_deny_txt",$settings);
				?>
				</span>
			</div>
	    </div>
	</div>
<?php
}
?>

<?php
function rs_indiebooking_admin_setting_show_mail_settings() {
	$mailFrom               = get_option( 'mail_from' );
	$mailFromName           = get_option( 'mail_from_name' );
	$smtp_host              = get_option( 'smtp_host' );
	$smtp_port              = get_option( 'smtp_port' );
	$smtp_user              = get_option( 'smtp_user' );
	$smtp_pass              = get_option( 'smtp_pass' );
	
	$disabled               = "";
	$wp_mail_smtp_active    = false;
	$plugin                 = 'wp-mail-smtp/wp_mail_smtp.php';
	if (is_plugin_active($plugin)) {
		$wp_mail_smtp_active = true;
		$disabled           = 'disabled="disabled"';
	}
	?>
    <div class="ibui_h2wrap">
    	<h2 class="ibui_h2">
    		<?php _e('E-Mail SMTP Settings', 'indiebooking'); ?>:
    	</h2>
    </div>
    <div class="ibui_notice ibui_notice_yellow">
	    <?php
	    if ($wp_mail_smtp_active) {
	    	//_e("Because you're using the wp-mail-smtp-Plugin, we only show the Informations on this side. Please use the wp-mail-smtp-Plugin settings page for update your mail information", 'indiebooking');
	        _e("Because you're using the wp-mail-smtp-Plugin, we don't show the Informations on this side. Please use the wp-mail-smtp-Plugin settings page for view and update your mail information", 'indiebooking');
	    } else {
	    	_e("All settings you're entering here are global smtp settings. If you have other plugins that use the default wordpress mail functionality, these settings apply to these plugins too.", 'indiebooking');
	    	_e("On this side you can edit your smtp mail information. We recommered to use the wp-mail-smtp-Plugin for your mail settings. If you decide to use wp-mail-smtp-Plugin, no Informations you entered here will be lost.", 'indiebooking');
	   		?>
	    	<br />
	        <?php _e("More information about wp-mail-smtp:", 'indiebooking'); ?>
	        <a href="https://wordpress.org/plugins/wp-mail-smtp/" target="_blank">https://wordpress.org/plugins/wp-mail-smtp/</a>
		<?php } ?>
    </div>
    <?php
    if (!$wp_mail_smtp_active) {
    ?>
    <div class="rsib_form-group">
		<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Name", 'indiebooking');?></label>
        <div class="rsib_col-xs-8">
        	<input class='ibui_input' id='rs_indiebooking_settings_mail_name' <?php echo $disabled; ?> type='text'
            		name='mail_from_name' value="<?php echo esc_attr($mailFromName); ?>">
    	</div>
    </div>
    <div class="rsib_form-group">
		<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("Mail Adress", 'indiebooking');?></label>
    	<div class="rsib_col-xs-8">
    		<input class='ibui_input' id='rs_indiebooking_settings_mail_adress' <?php echo $disabled; ?> type='text'
    				name='mail_from' value="<?php echo esc_attr($mailFrom); ?>">
    	</div>
    </div>
    <div class="rsib_form-group">
    	<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("SMTP-Host", 'indiebooking');?></label>
    	<div class="rsib_col-xs-8">
    		<input class='ibui_input' id='rs_indiebooking_settings_smtp' <?php echo $disabled; ?> type='text'
    			name='smtp_host' value="<?php echo esc_attr($smtp_host); ?>">
    	</div>
    </div>
    <div class="rsib_form-group">
        <label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("SMTP-Port", 'indiebooking');?></label>
    	<div class="rsib_col-xs-8">
        	<input class='ibui_input' id='rs_indiebooking_settings_smtp' <?php echo $disabled; ?> type='text'
        			name='smtp_port' value="<?php echo esc_attr($smtp_port); ?>">
    	</div>
    </div>
    <div class="rsib_form-group">
    	<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("SMTP-User", 'indiebooking');?></label>
    	<div class="rsib_col-xs-8">
        	<input class='ibui_input' id='rs_indiebooking_settings_smtp' <?php echo $disabled; ?> type='text'
        			name='smtp_user' value="<?php echo esc_attr($smtp_user); ?>">
        </div>
    </div>
    <div class="rsib_form-group">
    	<label class="rsib_col-xs-4 rsib_nopadding_left ibui_label"><?php _e("SMTP-Password", 'indiebooking');?></label>
    	<div class="rsib_col-xs-8">
        	<input class='ibui_input' id='rs_indiebooking_settings_smtp' <?php echo $disabled; ?> type='text'
        			name='smtp_pass' value="<?php echo esc_attr($smtp_pass); ?>">
        </div>
    </div>
	<?php }
}

function rs_indiebooking_admin_setting_show_admin_email_adresses() { ?>
<div class="ibui_h2wrap">
	<h2 class="ibui_h2">
		<?php _e('Admin e-mails', 'indiebooking'); ?>
	</h2>
	<label class="ibui_label">
     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
     			title="
     			<?php
     			_e('Under this point you can define E-Mail adresses which are getting mails when you get new bookings.
     					When no e-mail adress is entered. The site administrator gets the booking informations', 'indiebooking');
     			?>">
    	</span>
	</label>
</div>
<div id="rs_indiebooking_admin_settings_mail_2_tour" class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left">
	<table id="indiebooking_admin_email_settings_table"
			class="rs_indiebooking_cloneable_dataTable rsib_table ibui_table default_ib_table settings_table">
    	<thead>
        	<tr>
            	<th><?php _e('Admin E-Mail', 'indiebooking'); ?></th>
            	<!--
                <th style="text-align:center;width:50px;">
                	<a id="rs_indiebooking_btn_add_admin_mailadress" class="ibui_iconbtn">
                    	<span class="glyphicon glyphicon-plus-sign ibui_iconbtn_green" aria-hidden="true"></span>
                    </a>
                </th>
                -->
			    <th class="btn_ib_td">
			        <a class="ibui_iconbtn dateTable_btn_add_clonedRow">
			        	<span class="glyphicon glyphicon-plus-sign ibui_iconbtn_green" aria-hidden="true"></span>
			        </a>
			    </th>
            </tr>
        </thead>
        <tbody>
        	<tr class="rs_indiebooking_cloneable">
	        	<td>
	            	<input type="text" class="ibui_input"
	                		data-ibfcname="rs_indiebooking_settings_admin_email[]"
	                		value="">
	            </td>
	            <td class="btn_ib_td" style="text-align:center;">
<!-- 	            	<a class="ibui_iconbtn rs_ib_btn_remove_row"> -->
					<a class="dateTable_btn_remove_cloneable_row ibui_iconbtn">
	            		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
	            	</a>
	            </td>
			</tr>
        	<?php
        	$adminEmails	= get_option('rs_indiebooking_settings_admin_email');
        	if (!isset($adminEmails) || is_null($adminEmails) || empty($adminEmails) || sizeof($adminEmails) <= 0) {
        		$adminEmails	= array();
//         		$tmpArray		= array();
//         		$tmpArray[0] 	= "";
        		array_push($adminEmails, "--dummy--");
        	}
        	foreach ($adminEmails as $adminEmail) {
        		if (trim($adminEmail) != "") {
        			if ($adminEmail == "--dummy--") {
        				$adminEmail = "";
        			}
        	?>
        	<tr>
	        	<td>
	            	<input type="text" class="ibui_input"
	                		name="rs_indiebooking_settings_admin_email[]"
	                		value="<?php echo $adminEmail; ?>">
	            </td>
	            <td class="btn_ib_td" style="text-align:center;">
<!-- 	            	<a class="ibui_iconbtn rs_ib_btn_remove_row"> -->
					<a class="dateTable_btn_remove_cloneable_row ibui_iconbtn">
	            		<span class="glyphicon glyphicon-minus-sign ibui_iconbtn_red" aria-hidden="true"></span>
	            	</a>
	            </td>
			</tr>
			<?php
        		}
        	}
			?>
    	</tbody>
	</table>
</div>
<div class="rsib_col-sm-12 rsib_col-xs-12 rsib_nopadding_left">
	<div class="ibui_tabitembox">
		<a class="ibui_add_btn" id="btnSendTestBookingEmail">
			<?php _e("test email", "indiebooking");?>
		</a>
		(<?php _e("please save added adresses, before you test", "indiebooking");?>)
   	</div>
</div>
<?php
}

function rs_indiebooking_admin_setting_show_attachments() { ?>
	<div class="ibui_h2wrap">
		<h2 class="ibui_h2">
			<?php _e('Attachments', 'indiebooking'); ?>
		</h2>
		<label class="ibui_label">
	     	<span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
	     			title="
	     			<?php
	     			_e('Under this point you can define attachments which gets send with all of your E-Mails.', 'indiebooking');
	     			?>">
	    	</span>
		</label>
	</div>
	<div class="rsib_col-sm-6 rsib_col-xs-5 rsib_nopadding_left">
		<ul id="indiebooking_settings_attachment_container">
				<?php
				$mailAttachmentIds		= get_option( 'rs_indiebooking_settings_admin_email_attachment' );
				if (!$mailAttachmentIds) {
					$mailAttachmentIds = "";
				}
				
				$attachments = array_filter( explode( ',', $mailAttachmentIds ) );
				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {
						$attachment_id 	= intval($attachment_id);
// 						$url			= wp_get_attachment_url($attachment_id);
						$attachment_title = get_the_title($attachment_id);
                		echo '<li class="mailattachment ui-state-default" data-attachment_id="'.$attachment_id.'">'
 							.'<span><span>'.$attachment_title.'</span>'
 								.'<a href="#" class="delete tips glyphicon glyphicon-remove" title="undefined"></a>'
 							.'</span>'
 						.'</li>';
					}
				}
				?>
		</ul>
	    <input id="indiebooking_mail_attachment" name="rs_indiebooking_settings_admin_email_attachment"
	    		value="<?php echo $mailAttachmentIds; ?>" type="hidden" />
	    <input id="indiebooking_upload_mailAttachment-btn" type="button" name="upload_mailattachment-btn"
	    		class="ibui_btn ibui_btn_lightgrey" value="<?php esc_attr_e( 'Upload mail attachment', 'indiebooking' ); ?>"
	    		data-choose="<?php esc_attr_e( 'Add mail attachment', 'indiebooking' ); ?>">
	    <br /><br />
	</div>

<?php
}
?>
<div class="rsib_container-fluid">
    <div class="rsib_row">
        <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_left rsib_nopadding_xs rsib_nopadding_md2" style="margin-bottom:50px;">
			<?php
			/*
			 * Steht das System auf "Alle Buchungen sind Anfragen"
			 * Werden die Anfrageboxen als erstes angezeigt.
			 * Andernfalls als letztes.
			 */
			$bookingInquiriesKz			= get_option('rs_indiebooking_settings_booking_inquiries_kz');
			if ($bookingInquiriesKz == "on") {
				rs_indiebooking_admin_setting_show_inquire_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
				rs_indiebooking_admin_setting_show_inquire_deny_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			} else {
				rs_indiebooking_admin_setting_show_booking_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			}
			
			rs_indiebooking_admin_setting_show_payment_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			rs_indiebooking_admin_setting_show_storno_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			rs_indiebooking_admin_setting_show_anzahlung_errinerung_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			if ($bookingInquiriesKz == "on") {
				rs_indiebooking_admin_setting_show_booking_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			} else {
				rs_indiebooking_admin_setting_show_inquire_confirm_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
				rs_indiebooking_admin_setting_show_inquire_deny_box($rs_indiebooking_emailTextTxt, $rs_indiebooking_settings);
			}
			?>
        </div>
        <div class="rsib_col-lg2-6 rsib_col-md2-12 rsib_nopadding_right rsib_nopadding_xs rsib_nopadding_md2">
        	<div class="ibui_tabitembox">
        		<?php rs_indiebooking_admin_setting_show_admin_email_adresses(); ?>
        	</div>
        	<div class="ibui_tabitembox">
        		<?php rs_indiebooking_admin_setting_show_attachments(); ?>
        	</div>
			<div class="ibui_tabitembox">
        		<?php do_action('rs_indiebooking_more_email_settings_items'); ?>
        	</div>
            <div class="ibui_tabitembox">
               <?php rs_indiebooking_admin_setting_show_mail_settings(); ?>
            </div>
        </div>
    </div>
</div>
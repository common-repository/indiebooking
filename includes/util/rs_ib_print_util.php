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
// if ( ! class_exists( 'rs_ib_print_util' ) ) :
// include_once($this->plugin_path().'/mpdf61/mpdf.php');

require_once cRS_Indiebooking::plugin_path().'/mpdf7/vendor/autoload.php';

add_filter("rs_indiebooking_print_document_header", array("rs_ib_print_util", "print_document_header"),10,2);
add_filter("rs_indiebooking_print_document_footer", array("rs_ib_print_util", "print_document_footer"),10);
add_action("rs_indiebooking_print_document_watermark", array("rs_ib_print_util", "print_document_watermark"),10, 1);
/**
 * @author schmitt
 *
 * @file rs_ib_print_util.php
 *
 * @class rs_ib_print_util
 *
 * @brief Test fuer eine kurze Beschreibung der Klasse
 *
 * Die Detaillierte Beschreibung soll hier folgen
 */
class rs_ib_print_util
{
	/**
	 * @brief gibt die PDF-Dateien vom Dateisystem zurueck
	 * @param unknown $buchungsNr
	 * @param unknown $filename
	 * @return NULL|string
	 */
	public static function getPrintedPDFFromFilesystem($buchungsNr, $filename) {
		$file				= null;
// 		$pluginPath         = cRS_Indiebooking::plugin_path();
		$pluginPath         = cRS_Indiebooking::file_upload_path();
		$filePath           = $pluginPath.DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.$buchungsNr.DIRECTORY_SEPARATOR.$filename.'.pdf';
		if (file_exists($filePath)) {
			$file 			= $filePath;
		}
		return $file;
	}
	
	/* @var $printDataObj rs_ib_print_util_data_object */
    public static function printData($html, $printDataObj) {
    	global $RSBP_DATABASE;
    	
        $count              = 2;
        $pluginPath         = cRS_Indiebooking::plugin_path();
        $pluginFilePath     = cRS_Indiebooking::file_upload_path();
        $filename			= $printDataObj->getFileName();
        
        $buchungsNrToCopy	= $printDataObj->getBuchungNr();
        if ($printDataObj->getBuchungsNrToCopy() > 0) {
        	$buchungsNrToCopy	= $printDataObj->getBuchungsNrToCopy();
        }
        
        $filePath           = $pluginFilePath.DIRECTORY_SEPARATOR.'pdfs'.DIRECTORY_SEPARATOR.$buchungsNrToCopy.'/';
        $imagePath          = $pluginPath.'/images/';
        if (!file_exists($filePath)) {
            mkdir($filePath, 0777, true);
        }
        $originalFile       = $printDataObj->getFileName();
        $filename			= $printDataObj->getFileName();
        if ($printDataObj->getCreateNewVersion()) {
	        while (file_exists($filePath.$filename.'.pdf')) {
	            $filename       = $originalFile . '-'.$count;
	            $count++;
	        }
        }
        $file               = $filePath . $filename . '.pdf';
        $cssPath            = cRS_Indiebooking::plugin_path().'/assets/css/';
        $cssFile            = "print_style.css";
        $cssContent         = file_get_contents($cssPath . $cssFile);
        $cssContent         = "<style>".$cssContent."</style>";
        
//         $header             = apply_filters("rs_indiebooking_print_document_header", $buchungsNr, $headerUeberschrift);
        $header             = apply_filters("rs_indiebooking_print_document_header", $printDataObj);
        $footer             = apply_filters("rs_indiebooking_print_document_footer", "");
        
        /*
         * Margin ohne Headerbeeinflussung
         */
        $marginRight		= 19; //default = 15, Nauwieser 19
        $marginLeft			= 15; //default = 15
        $marginTop			= 106; //default = 50 , Nauwieser = 90 oder 106
        $marginBottom		= 40; //default = 50
        
        /*
         * Margin mit Headerbeeinflussung
         */
        $marginHeader		= 55;
        
//         $testInvoicePaper	= false;
        
        /*
         * Default fonts:
         * ccourier
         * chelvetica
         * ctimes
         */
// 		if (!$testInvoicePaper) {
// 	        $mpdf               = new mPDF('utf-8', 'A4');
// 	        $mpdf->DeflMargin   = 15;
// 	        $mpdf->DefrMargin   = 15;
// 	        $mpdf->tMargin      = 35;
// 	        $mpdf->bMargin      = 50;
	//         $mpdf->tMargin      = 0;
	//         $mpdf->bMargin      = 0;
	//         $mpdf->setAutoTopMargin = 'pad';
	//         $mpdf->margin_header = 90;
	        
// 	        $mpdf->header_line_spacing = true;
// 	        $html               = $cssContent . $html;
// 	        do_action("rs_indiebooking_print_document_watermark", $mpdf);
// 	        $mpdf->DefHTMLHeaderByName('bookingHeader', $header);

			$mpdf               	= new mPDF('utf-8', 'A4');
        	/*
        	 * Fuer das Update auf mpdf 7:
			$mpdfConfig 		= array(
				'mode' => 'utf-8',
				'format' => 'A4'
			);
			$mpdf               = new \Mpdf\Mpdf($mpdfConfig);
			*/
			
	        $mpdf->DefHTMLFooterByName('bookingFooter', $footer);
// 	        $mpdf->SetHTMLHeaderByName('bookingHeader');
	        $mpdf->SetHTMLFooterByName('bookingFooter');
// 		} else {
			do_action("rs_indiebooking_print_document_watermark", $mpdf);
			$mpdf->DeflMargin   = $marginRight;
			$mpdf->DefrMargin   = $marginLeft;
			$mpdf->tMargin 		= $marginTop;
			$mpdf->bMargin      = $marginBottom;

			$mpdf->margin_header = $marginHeader; //verschiebt das wasserzeichen schon ab 10 und den header
			
			$mpdf->header_line_spacing = true;
			$html = $cssContent.$header.$html;
			
			//https://mpdf.github.io/headers-footers/headers-top-margins.html
			//https://mpdf.github.io/reference/mpdf-functions/setdoctemplate.html
			//https://mpdf.github.io/reference/mpdf-functions/setpagetemplate.html
			
// 			$mpdf->setAutoTopMargin = 'pad';
// 			$mpdf->margin_bottom = 50;
			 
			//$mpdf->SetHTMLHeader($header);
			//$mpdf->SetHTMLFooter($footer);
			
			/*
			 * Folgende Zeilen beziehen sich auf das ganze Dokument
			$mpdf->DefHTMLHeaderByName('bookingHeader', $header);
			$mpdf->SetHTMLHeaderByName('bookingHeader');
			*/
			
			/*
			$mpdf->DefHTMLFooterByName('bookingFooter', $footer);
			$mpdf->SetHTMLFooterByName('bookingFooter');
			*/
			//$mpdf->setAutoTopMargin = 'stretch';
			//$mpdf->setAutoBottomMargin = 'stretch';
// 		}
		$mpdf->WriteHTML($html);
			 
		$dest = "F";
		$mpdf->Output($file, $dest);
	    return $file;
    }
    
    public static function startPrintHtmlPage() {
        ob_start();
    }
    
    public static function print_document_watermark($mpdfObj) {
        $pluginPath                     = cRS_Indiebooking::plugin_path();
        $imagePath                      = $pluginPath.'/images/';
        $watermarksize                  = array(154.12,179.91); //1,7
        $mpdfObj->SetWatermarkImage($imagePath.'Logo_indiebooking.jpg', 0.1, $watermarksize);
        $mpdfObj->showWatermarkImage    = true;
    }
    
//     public static function endPrintHtmlPage($filename, $buchungsNr, $header = "", $dest ="") {
    /* @var $printDataObj rs_ib_print_util_data_object */
    public static function endPrintHtmlPage($printDataObj) {
        $ausgabe    = ob_get_contents();
        $file       = self::printData($ausgabe, $printDataObj);
        //TODO pruefen weil:
        /*
        Notice: ob_end_flush() [ref.outcontrol]:
        failed to delete and flush buffer. No buffer to delete or flush in
        /includes/util/rs_ib_print_util.php on line 60
         */
        @ob_end_clean();
        @ob_end_flush();
        return $file;
    }
    
    /* @var $printDataObj rs_ib_print_util_data_object */
    public static function print_document_header($printDataObj) {
        $options                = get_option( 'rs_indiebooking_settings' );
        $pdf_image_id			= "";
        $image					= "";
        $image_url				= "";
        $header					= "";
        $invoicePaperId			= (key_exists('invoice_papyer_id', $options)) ? esc_attr__( $options['invoice_papyer_id'] ) : "";
        $invoicePaperId 		= "";
        cRS_Template_Loader::rs_ib_get_template('rs_ib_print/print_part/print_booking_header.php', array());
        if (is_null($invoicePaperId) || ($invoicePaperId == "")) {
        	if (function_exists('rs_indiebooking_create_print_header')) {
        		$header 		= rs_indiebooking_create_print_header($printDataObj);
        	}
        } else {
        	if (function_exists('rs_indiebooking_create_print_header')) {
        		$header 		= rs_indiebooking_create_print_header($printDataObj);
        	}
        }
        return $header;
    }
    
    public static function print_document_footer() {
//         $footer = '<div style="text-align: right; font-weight: bold; font-size: 8pt; font-style: italic;">Chapter 2 Footer</div>';
    	$footer						= "";
    	$options                	= get_option( 'rs_indiebooking_settings' );
    	$invoicePaperId				= (key_exists('invoice_papyer_id', $options)) ? esc_attr__( $options['invoice_papyer_id'] ) : "";
    	if (is_null($invoicePaperId) || ($invoicePaperId == "")) {
	        $options                = get_option( 'rs_indiebooking_settings' );
	        $bankData               = get_option( 'rs_indiebooking_settings_bankdata' );
	        $bankName               = "";
	        $iban                   = "";
	        $bic                    = "";
	        $kontoInhaber           = "";
	        $companyName            = "";
	        $companyStreet          = "";
	        $company_zip_code       = "";
	        $companyLocation        = "";
	        $companyCountry         = "";
	        $companyWebsite         = "";
	        $companyPhone           = "";
	        $companyFax             = "";
	        $companyEmail           = "";
	        $company_tax_number     = "";
	        $company_ust_id         = "";
	        
	        
	        if ($bankData != false) {
		        $bankName           = esc_attr__( $bankData['bank_name'] );
		        $iban               = esc_attr__( $bankData['bank_iban'] );
		        $bic                = esc_attr__( $bankData['bank_bic'] );
		        $kontoInhaber       = esc_attr__( $bankData['bank_account'] );
	        }
	        if ($options != false) {
		        $companyName        = esc_attr__( $options['company_name']);
		        $companyStreet      = esc_attr__( $options['company_street']);
		                            
		        $company_zip_code   = esc_attr__( $options['company_zip_code']);
		        $companyLocation    = esc_attr__( $options['company_location']);
		        $companyCountry     = esc_attr__( $options['company_country']);
		        $companyWebsite     = esc_attr__( $options['company_website']);
		        $companyPhone       = esc_attr__( $options['company_phone']);
		        $companyFax         = esc_attr__( $options['company_fax']);
		        $companyEmail       = esc_attr__( $options['company_email']);
		        $company_tax_number = esc_attr__( $options['company_tax_number']);
		        $company_ust_id     = esc_attr__( $options['company_ust_id']);
		        
		        if ($companyPhone != "") {
		        	$companyPhone	= 'fon: '.$companyPhone;
		        }
	        }
	        $ibanCol			  	= "";
	        $bicCol				  	= "";
	        $faxCol					= "";
	        if (!is_null($iban) && trim($iban) != "") {
	        	$ibanCol			= 'IBAN: '.$iban;
	        }
	        
	        if (!is_null($bic) && trim($bic) != "") {
	        	$bicCol				= 'BIC: '.$bic;
	        }
	        
	        if (!is_null($companyFax) && trim($companyFax) != "") {
	        	$faxCol				= 'fax: '.$companyFax;
	        }
	        $footer = '<htmlpagefooter name="bookingFooter"><div class="printFooter">' .
	                    '<div style="width: 100%">'.
	                    '<table style="width: 100%; border-top: 1px solid black;">'.
	                        '<tr>'.
	                            '<td style="width: 33%">'.
	                                $companyName .
	                            '</td>'.
	                            '<td style="width: 33%">'.
	                                $companyPhone.
	                            '</td>'.
	                            '<td style="width: 33%">'.
	                                $bankName .
	                            '</td>'.
	                        '</tr>'.
	                        '<tr>'.
	                            '<td>'.
	                                $companyStreet .
	                            '</td>'.
	                            '<td>'.
	                                $faxCol.
	                            '</td>'.
	                            '<td>'.
	                            	$company_tax_number .
	                            '</td>'.
	                        '</tr>'.
	                        '<tr>'.
	                            '<td>'.
	                                $company_zip_code . ' ' .$companyLocation .
	                            '</td>'.
	                            '<td>'.
	                                $companyEmail.
	                            '</td>'.
	                            '<td>'.
	                                $ibanCol .
	                            '</td>'.
	                        '</tr>'.
	                        '<tr>'.
	                            '<td>'.
	                                $companyCountry .
	                            '</td>'.
	                            '<td>'.
	                                $companyWebsite.
	                            '</td>'.
	                            '<td>'.
	                                $bicCol .
	                            '</td>'.
	                        '</tr>'.
	                    '</table>'.
	                  '</div>'.
	                '</div></htmlpagefooter>';
    	} else {
    		$footer = '<div align="right" style="font-size: 11px; padding-bottom: 80px;">{PAGENO} / {nbpg}</div>';
    	}
    	
        return $footer;
    }
}
// endif;
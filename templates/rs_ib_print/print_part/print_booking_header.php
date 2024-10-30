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
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/* @var $printDataObj rs_ib_print_util_data_object */
if (!function_exists('rs_indiebooking_create_print_header')) {
function rs_indiebooking_create_print_header($printDataObj) {
	$pdfImage				= "";
	$options                = get_option( 'rs_indiebooking_settings' );
	$pdf_image_id       	= esc_attr__( $options['pdf_image_id']);
	$invoicePaperId			= (key_exists('invoice_papyer_id', $options)) ? esc_attr__( $options['invoice_papyer_id'] ) : "";
	if ($invoicePaperId != "") {
		$pdf_image_id		= "";
	}
	$buchungsNr				= $printDataObj->getBuchungNr();
	$image              	= wp_get_attachment_image_src( $pdf_image_id, 'medium' );
	$image_url          	= $image[0];
	if (!is_null($pdf_image_id) && $pdf_image_id !== "" && $pdf_image_id > 0) {
		$image          	= '<img class="rs_ib_pdf_logo" src="'.$image_url.'" />';
	} else {
		$image          	= "";
	}
	
	$buchungsNr 			= $printDataObj->getBuchungNr();
	$ueberschrift 			= $printDataObj->getUeberschrift();
	if ($ueberschrift == "") {
	    $ueberschrift       = __('Booking confirmation', 'indiebooking');
	}
	$uebersDatum            = __('Date', 'indiebooking');
	$uebersBookingnr        = __('Booking', 'indiebooking');
	$datum					= date("d.m.y");
	
	if (!is_null($pdf_image_id) && $pdf_image_id !== "" && $pdf_image_id > 0) {
		$image_url			= esc_url($image_url);
		$pdfImage			= '<img class="rs_ib_pdf_logo" src="'.$image_url.'" />';
	}
	
	$companyName		= $printDataObj->getCompanyName();
	$companyStreet		= $printDataObj->getCompanyStreet();
	$company_zip_code	= $printDataObj->getCompanyZipCode();
	$companyLocation	= $printDataObj->getCompanyLocation();
	
	$firma				= $printDataObj->getFirma();
	$abteilung			= $printDataObj->getAbteilung();
	$anrede				= $printDataObj->getAnrede();
	$name				= $printDataObj->getName();
	$strasse			= $printDataObj->getStrasse();
	$plz				= $printDataObj->getPlz();
	$ort				= $printDataObj->getOrt();
	$land				= $printDataObj->getLand();
	
	if (strlen($firma) > 0) {
		$firma = '<tr><td class="no_padding">'.$firma.'</td></tr>';
	} else {
		$firma = "";
	}
	
	if (strlen($abteilung) > 0) {
		$abteilung = '<tr><td class="no_padding">'.$abteilung.'</td></tr>';
	} else {
		$abteilung = "";
	}
	
// 	$header = '<htmlpageheader name="bookingHeader"><div class="printHeader">' .
// 			'<table style="width: 100%;">'.
// 			'<tr>'.
// 			'<td style="width: 20%;" align="left"></td>'.
// 			'<td class="print_ueberschrift" style="width: 80%;" align="center"></td>'.
// 			'<td style="width: 20%;" align="right">'.$image.'</td>'.
// 			'</tr>'.
// 			'</table>'.
// 			'</div></htmlpageheader>';
/*
			<thead>
				<tr><td style="font-size: 9px;">$companyName - $companyStreet - $company_zip_code - $companyLocation
				$firma
				<tr><td class="no_padding">$anrede</td></tr>
				<tr><td class="no_padding">$name</td></tr>
				<tr><td class="no_padding">$strasse</td></tr>
				<tr><td class="anschrift_absatz">$plz $ort</td></tr>
				<tr><td class="no_padding">$land</td></tr>
			</thead>
 */

$htmlHeader = <<<HTMLHEADER
<htmlpageheader name="firstpage" style="display:none">
	<table id="contact_table" style="width: 100%; border: none;">
		<thead>
			<tr>
				<td style="font-size: 9px; width: 80%;">
					$companyName - $companyStreet - $company_zip_code - $companyLocation
				</td>
            	<td style="width: 20%;" align="right" rowspan="5">
            		  $pdfImage
            	</td>
            </tr>
			$firma
			$abteilung
			<tr><td colspan="2" class="no_padding">$anrede</td></tr>
			<tr><td colspan="2" class="no_padding">$name</td></tr>
			<tr><td colspan="2" class="no_padding">$strasse</td></tr>
			<tr><td colspan="2" class="anschrift_absatz">$plz $ort</td></tr>
			<tr><td colspan="2" class="no_padding">$land</td></tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</htmlpageheader>
<htmlpageheader name="otherpages" style="display:none">
	<table style="width: 100%; border: none;">
		<thead>
			<tr>
				<td style="font-size: 9px; width: 80%;"></td>
            	<td style="width: 20%;" align="right" rowspan="5">
            		  $pdfImage
            	</td>
            </tr>
			<tr><td colspan="2" class="no_padding"></td></tr>
			<tr><td colspan="2" class="no_padding"></td></tr>
			<tr><td colspan="2" class="no_padding"></td></tr>
			<tr><td colspan="2" class="anschrift_absatz"></td></tr>
			<tr><td colspan="2" class="no_padding"></td></tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</htmlpageheader>
<sethtmlpageheader name="firstpage" value="on" show-this-page="1" />
<sethtmlpageheader name="otherpages" value="on" />
HTMLHEADER;
/*
 * <htmlpageheader name="otherpages" style="display:none">
    <div style="text-align:center">{PAGENO}</div>
</htmlpageheader>
$htmlHeader = <<<HTMLHEADER
<htmlpageheader name="bookingHeader">
	<div class="printHeader">
    	<table style="width: 100%;">
            <tr>
            	<td style="width: 20%;" align="left">
            		$uebersDatum: $datum
            	</td>
            	<td class="print_ueberschrift" style="width: 80%;" align="center">
            		<p>
            			$ueberschrift<br />
            			<span class="header2">$uebersBookingnr #$buchungsNr</span>
            		</p>
        		</td>
            	<td style="width: 20%;" align="right">
            		  $pdfImage
            	</td>
            </tr>
			<tr>
				<td style="font-size: 9px;">$companyName - $companyStreet - $company_zip_code - $companyLocation
				$firma
			<tr><td class="no_padding">$anrede</td></tr>
			<tr><td class="no_padding">$name</td></tr>
			<tr><td class="no_padding">$strasse</td></tr>
			<tr><td class="anschrift_absatz">$plz $ort</td></tr>
			<tr><td class="no_padding">$land</td></tr>
        </table>
    </div>
</htmlpageheader>
HTMLHEADER;
*/
return $htmlHeader;
}
}

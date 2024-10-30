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
$companyRow = sprintf("%s - %s - %s - %s", $printDataObj->getCompanyName(),
						$printDataObj->getCompanyStreet(), $printDataObj->getCompanyZipCode(),
						$printDataObj->getCompanyLocation());
?>
<table id="contact_table">
	<thead>
		<tr><td style="font-size: 9px;"><?php echo $companyRow; ?>
		<?php if (strlen($printDataObj->getFirma()) > 0) { ?>
		<tr><td class="no_padding"><?php echo $printDataObj->getFirma(); ?></td></tr>
		<?php } ?>
		<?php if (strlen($printDataObj->getAbteilung()) > 0) { ?>
		<tr><td class="no_padding"><?php echo $printDataObj->getAbteilung(); ?></td></tr>
		<?php } ?>
		<tr>
			<td class="no_padding">
				<?php
					$anrede = $printDataObj->getAnrede();
					echo $anrede;
				?>
			</td>
		</tr>
		<tr><td class="no_padding"><?php echo $printDataObj->getName(); ?></td></tr>
		<tr><td class="no_padding"><?php echo $printDataObj->getStrasse(); ?></td></tr>
		<tr><td class="anschrift_absatz"><?php echo $printDataObj->getPlz()." ".$printDataObj->getOrt(); ?></td></tr>
		<tr><td class="no_padding"><?php echo $printDataObj->getLand();?></td></tr>
	</thead>
	<tbody>
	</tbody>
</table>
<?php

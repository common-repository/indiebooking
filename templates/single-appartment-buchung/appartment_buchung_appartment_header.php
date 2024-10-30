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
$postId = get_the_ID();
?>
<input type="hidden" id="appartmentPostId" name="appartmentPostId" value="<?php echo $postId;?>">
<input type="hidden" id="bookingPostId" name="bookingPostId" value="">
<!-- <section> -->
<!--     <div class="container"> -->
<!--         <div class="col-md-9 col-centered"> -->
        	<div class="toggleLink">
        		<span style="float: right;" class="btn rs_ib_toggleBtn glyphicon glyphicon-chevron-up"></span>
        		<h2><?php echo $apartmentTitle; ?></h2>
        	</div>
<!--         </div> -->
<!--     </div> -->
<!-- </section> -->

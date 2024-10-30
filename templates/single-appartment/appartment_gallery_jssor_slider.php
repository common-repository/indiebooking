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
// $postId = get_the_ID();
// if (isset($appartmentId)) {
//     $postId = $appartmentId;
// }
$postId = $appartmentId;
?>
<div class="appartment_gallerie">
	<div class="container" style="max-width: 700px;">
        <div class="panel panel-default">
          <div class="panel-body">
	<!-- width: 1140px; height: 442px -->
        <div id="slider1_container_<?php echo $postId;?>" class="slider1_container" style="visibility: hidden; position: relative; margin: 0 auto; width: 700px; height: 442px; overflow: hidden;">

            <!-- Loading Screen -->
            <div u="loading" style="position: absolute; top: 0px; left: 0px;">
                <div style="filter: alpha(opacity=70); opacity:0.7; position: absolute; display: block;
                    background-color: #000; top: 0px; left: 0px;width: 100%; height:100%;">
                </div>
                <div style="position: absolute; display: block; background: url(<?php echo $loadingImage?>) no-repeat center center;
                	top: 0px; left: 0px;width: 100%;height:100%;">
                </div>
            </div>

            <!-- Slides Container -->
            <div u="slides" style="cursor: move; position: absolute; left: 0px; top: 0px; width: 700px; height: 442px; overflow: hidden;">
           	    <?php
//                     $profilePictureThumb  = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'thumbnail');
//                     $profilePicture       = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'full');
                    ?>
                    <!--
                    <div>
                      <img u="image" src="<?php //echo $profilePicture[0];?>" />
                    </div>
                     -->
                    <?php
            	    if ( ! empty( $attachments ) ) {
            	        $count = 0;
            	        foreach ( $attachments as $attachment_id ) {
            	            $imageThumb = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
            	            $imageFull = wp_get_attachment_image_src( $attachment_id, 'full' );
            	            ?>
            	            <div>
            	              <img u="image" src="<?php echo esc_url($imageFull[0]);?>" />
            	           </div>
            	            <?php
            	        }
            	    }
        	    ?>
            </div>
            
            <!--#region Bullet Navigator Skin Begin -->
            <!-- Help: http://www.jssor.com/development/slider-with-bullet-navigator-jquery.html -->
            <style>
                /* jssor slider bullet navigator skin 05 css */
                /*
                .jssorb05 div           (normal)
                .jssorb05 div:hover     (normal mouseover)
                .jssorb05 .av           (active)
                .jssorb05 .av:hover     (active mouseover)
                .jssorb05 .dn           (mousedown)
                */
                .jssorb05 {
                    position: absolute;
                }
                .jssorb05 div, .jssorb05 div:hover, .jssorb05 .av {
                    position: absolute;
                    /* size of bullet elment */
                    width: 16px;
                    height: 16px;
                    background: url(<?php echo $sliderImagePath; ?>b05.png) no-repeat;
                    overflow: hidden;
                    cursor: pointer;
                }
                .jssorb05 div { background-position: -7px -7px; }
                .jssorb05 div:hover, .jssorb05 .av:hover { background-position: -37px -7px; }
                .jssorb05 .av { background-position: -67px -7px; }
                .jssorb05 .dn, .jssorb05 .dn:hover { background-position: -97px -7px; }
            </style>
            <!-- bullet navigator container -->
            <div u="navigator" class="jssorb05" style="bottom: 16px; right: 6px;">
                <!-- bullet navigator item prototype -->
                <div u="prototype"></div>
            </div>
            <!--#endregion Bullet Navigator Skin End -->
            
            <!-- thumbnail navigator container -->
            <!-- <div u="thumbnavigator" class="jssort07" style="left: 0px; bottom: 0px;"> -->
            <!-- <div u="thumbnavigator" class="jssort07" style="bottom: 0px;">
                <div u="slides" style="cursor: default;">
                    <div u="prototype" class="p">
                        <div u="thumbnailtemplate" class="i"></div>
                        <div class="o"></div>
                    </div>
                </div>-->
                <!-- Thumbnail Item Skin End -->
            <!-- </div> -->
            
            <!--#region Arrow Navigator Skin Begin -->
            <!-- Help: http://www.jssor.com/development/slider-with-arrow-navigator-jquery.html -->
            <style>
                /* jssor slider arrow navigator skin 11 css */
                /*
                .jssora11l                  (normal)
                .jssora11r                  (normal)
                .jssora11l:hover            (normal mouseover)
                .jssora11r:hover            (normal mouseover)
                .jssora11l.jssora11ldn      (mousedown)
                .jssora11r.jssora11rdn      (mousedown)
                */
                .jssora11l, .jssora11r {
                    display: block;
                    position: absolute;
                    /* size of arrow element */
                    width: 37px;
                    height: 37px;
                    cursor: pointer;
                    background: url(<?php echo $sliderImagePath; ?>a11.png) no-repeat;
                    overflow: hidden;
                }
                .jssora11l { background-position: -11px -41px; }
                .jssora11r { background-position: -71px -41px; }
                .jssora11l:hover { background-position: -131px -41px; }
                .jssora11r:hover { background-position: -191px -41px; }
                .jssora11l.jssora11ldn { background-position: -251px -41px; }
                .jssora11r.jssora11rdn { background-position: -311px -41px; }
            </style>
            <!-- Arrow Left -->
            <span u="arrowleft" class="jssora11l" style="top: 123px; left: 8px;">
            </span>
            <!-- Arrow Right -->
            <span u="arrowright" class="jssora11r" style="top: 123px; right: 8px;">
            </span>
            <!--#endregion Arrow Navigator Skin End -->
            <a style="display: none" href="http://www.jssor.com">Bootstrap Slider</a>
        </div> <!-- #slider1_container -->
        <!-- Jssor Slider End -->
        </div></div>
	</div> <!-- .container -->
</div><!-- #appartment_gallerie -->
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

/**
 * WC_Meta_Box_Product_Images Class
 */
// if ( ! class_exists( 'RS_IB_Admin_Apartment_images' ) ) :
class RS_IB_Admin_Apartment_images {

	/**
	 * Output the metabox
	 */
	public static function outputLi( $post ) {
	    $thumbnailId = get_post_thumbnail_id($post);
		?>
		<div class="add_apartment_images"><!-- hide-if-no-js -->
			<a href="#" class="ibui_add_btn"
				data-choose="<?php esc_attr_e( 'Add Images to Apartment Gallery', 'indiebooking' ); ?>"
				data-update="<?php esc_attr_e( 'Add to gallery', 'indiebooking' ); ?>"
				data-delete="<?php esc_attr_e( 'Delete image', 'indiebooking' ); ?>"
				data-text="<?php esc_attr_e( 'Delete', 'indiebooking' ); ?>">
					<?php _e( 'Add apartment gallery images', 'indiebooking' ); ?>
			</a>
            <span class="glyphicon glyphicon-info-sign ibui_tooltip_item"
        		title="<?php _e('You can select one Image as a Thumbnail, when you select one Radio-Button', 'indiebooking'); ?>">
    		</span>
            <!--<a href="#" class="btn_rewa" data-choose="<?php //esc_attr_e( 'Add Images to Apartment Gallery', 'rsappartment' ); ?>" data-update="<?php //esc_attr_e( 'Add to gallery', 'rsappartment' ); ?>" data-delete="<?php //esc_attr_e( 'Delete image', 'rsappartment' ); ?>" data-text="<?php //esc_attr_e( 'Delete', 'rsappartment' ); ?>"><?php //_e( 'Add apartment gallery images', 'rsappartment' ); ?></a>-->
        </div>
		
		<div id="rs_apartment_images_container" class="ibui_imagegallery">
			<ul id="rs_appartment_sortable_image_container" class="apartment_images">
				<?php
					if ( metadata_exists( 'post', $post->ID, 'rs_apartment_image_gallery' ) ) {
						$apartment_image_gallery = get_post_meta( $post->ID, 'rs_apartment_image_gallery', true );
					} else {
						// Backwards compat
						$attachment_ids = get_posts( 'post_parent=' . $post->ID . '&numberposts=-1&post_type=attachment&orderby=menu_order&order=ASC&post_mime_type=image&fields=ids&meta_key=rs_apartment_image_gallery&meta_value=0' );
						$attachment_ids = array_diff( $attachment_ids, array( get_post_thumbnail_id() ) );
						$apartment_image_gallery = implode( ',', $attachment_ids );
					}

					$attachments = array_filter( explode( ',', $apartment_image_gallery ) );
					if ( ! empty( $attachments ) ) {
						foreach ( $attachments as $attachment_id ) {
// 							echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
// 								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
// 			                     <a href="#" class="delete tips glyphicon glyphicon-remove" data-tip="' . esc_attr__( 'Delete image', 'rsappartment' ) . '">' . __( 'Delete', 'rsappartment' ) . '</a>
// 							</li>';
                            $checked = "";
                            if ($thumbnailId != "" && $thumbnailId == $attachment_id) {
                                $checked = "checked = 'checked'";
                            }
						    echo '<li class="image ui-state-default" data-attachment_id="' . esc_attr( $attachment_id ) . '">
						         <div class="ibui_imagegallery_profilradio"><input class="rs_ib_profile_picture_radio" '.
						              $checked.' type="radio" id="rs_ib_pp_radio_'.esc_attr( $attachment_id ).
						              '" name="RS_IB_Apartment_Profilepicture" value="'.esc_attr( $attachment_id ).'"><span>'.
						                  esc_attr__( 'Apartment Thumbnail', 'indiebooking' ).'</span></div>
								' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
			                     <a href="#" class="delete tips glyphicon glyphicon-remove" data-tip="' .
			                         esc_attr__( 'Delete image', 'indiebooking' ) . '"></a>
							</li>';
						}
					}
				?>
			</ul>
			<input type="hidden" id="rs_apartment_image_gallery" name="rs_apartment_image_gallery"
					value="<?php echo esc_attr( $apartment_image_gallery ); ?>" />
			<input type="hidden" id="rs_apartment_image_profile_picture" name="rs_apartment_image_profile_picture"
					value="<?php echo esc_attr($thumbnailId); ?>" />
			
			<br class="clear" />
			<?php //submit_button(); ?>
			<br class="clear" />
		</div>
		<?php
	}

	public static function output( $post ) {
	    RS_IB_Admin_Apartment_images::outputLi($post);
    }
	
	/**
	 * Save meta box data
	 */
	public static function saveGallery( $post_id, $post ) {
	    $profilePicture = rsbp_getPostValue('rs_apartment_image_profile_picture', "");
// 	    delete_post_thumbnail( $post );
	    set_post_thumbnail($post, $profilePicture);
	    
        $attachment_ids     = rsbp_getPostValue('rs_apartment_image_gallery', null, RS_IB_Data_Validation::DATATYPE_ALL);
        if (!is_null($attachment_ids)) {
            $attachment_ids = array_filter( explode( ',', sanitize_text_field( $attachment_ids ) ) );
        } else {
            $attachment_ids = array();
        }
		
// 		$attachment_ids   = rsbp_getPostValue('rs_apartment_image_gallery', array());
		
		update_post_meta( $post_id, 'rs_apartment_image_gallery', implode( ',', $attachment_ids ) );
	}
}
// endif;
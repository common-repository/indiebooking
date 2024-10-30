/**
 * 
 */
;(function(jQuery) {
	jQuery(document).ready(function() {
		var indiebooking_fast_link_gallery_frame;
		
//		jQuery( '.widget-liquid-right').on("click", '.add_apartment_images_fastlink a', function( event ) {
		jQuery( 'body').on("click", '.add_apartment_images_fastlink a', function( event ) {	
			var $el = jQuery( this );
	
			event.preventDefault();
				
			// If the media frame already exists, reopen it.
			if ( indiebooking_fast_link_gallery_frame ) {
				//indiebooking_fast_link_gallery_frame.open();
				//return;
			}
			
			// Create the media frame.
			indiebooking_fast_link_gallery_frame = wp.media.frames.product_gallery = wp.media({
				// Set the title of the modal.
				title: $el.data( 'choose' ),
				button: {
					text: $el.data( 'update' )
				},
				library: {
					type: [ 'image' ],
				},		
				multiple: true,
			});
			
			// When an image is selected, run a callback.
			indiebooking_fast_link_gallery_frame.on( 'select', function() {
				var selection 			= indiebooking_fast_link_gallery_frame.state().get( 'selection' );
				selection.map( function( attachment ) {
						attachment = attachment.toJSON();
						if (attachment.subtype.match(/(jpeg|jpg|gif|png|ico)$/) != null) {
							if ( attachment.id ) {
//								attachment_ids   = attachment_ids ? attachment_ids + ',' + attachment.id : attachment.id;
								var attachment_image 	= attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
								var container			= jQuery($el).closest(".indiebooking_fast_link_container");
								var img 				= jQuery(container).find(".indiebooking_fast_link_widget_preview");
								jQuery(img).attr('src',attachment_image);
								jQuery(container).find(".indiebooking_fast_link_widget_img_id").val(attachment.id);
								jQuery(container).find(".indiebooking_fast_link_widget_img_id").trigger("change");
//								$rs_apartment_images.append( '<li class="image ui-state-default" data-attachment_id="' 
//											+ attachment.id + '"><div class="ibui_imagegallery_profilradio">'
//											+'<input class="rs_ib_profile_picture_radio" type="radio" id="rs_ib_pp_radio_' 
//											+ attachment.id + '" name="RS_IB_Apartment_Profilepicture" value="' 
//											+ attachment.id + '"><span>Apartment Thumbnail</span></div><img src="' + attachment_image 
//											+ '" /><a href="#" class="delete tips glyphicon glyphicon-remove" title="' 
//											+ $el.data('delete') + '"></a></li>' );
								
							}
						}
				});
				//$image_gallery_ids.val( attachment_ids );
			});
	
			// Finally, open the modal.
			indiebooking_fast_link_gallery_frame.open();

		});
	})
})(jQuery);
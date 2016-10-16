jQuery( document ).ready( function ( $ ) {
	'use strict';

	var custom_uploader,
	    target_input;

	$( '.upload_default_image' ).click(function(e) {

		target_input = $(this).prev( '.upload_image_id' );

		e.preventDefault();

		//If the uploader object has already been created, reopen the dialog
		if ( custom_uploader ) {
			custom_uploader.open();
			return;
		}

		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: ( [ objectL10n.text ] ),
			button: {
				text: ( [ objectL10n.text ] )
			},
			multiple: false,
			library: { type : 'image' }
		});

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on( 'select', function() {

			var attachment   = custom_uploader.state().get( 'selection' ).first().toJSON(),
			    preview      = $( target_input ).prevAll( '.upload_logo_preview' ),
			    previewImage = $( '<div class="upload_logo_preview"><img style="max-width:100%;" src="' + attachment.url + '" /></div>' );
			$( target_input ).val( attachment.id );
			if ( preview.length ) {
				preview.remove();
			}
			$( target_input ).before( previewImage );
		});

		//Open the uploader dialog
		custom_uploader.open();

	});

	$( '.delete_image' ).click( function() {

		target_input    = $( this ).prevAll( '.upload_image_id' );
		var previewView = $( this ).prevAll( '.upload_logo_preview' );

		$( target_input ).val( '' );
		$( previewView ).remove();

	});
});

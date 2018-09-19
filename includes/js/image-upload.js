;(function ( document, $, undefined ) {
	'use strict';

	var Scriptless = {},
	    target_input;

	Scriptless.upload = function () {
		$( '.scriptless-upload' ).on( 'click.scriptless', _openModal );
		$( '.scriptless-delete' ).on( 'click.delete', _delete );
	};

	function _openModal( e ) {
		e.preventDefault();

		var custom_uploader;
		target_input = $( this ).prev( '.scriptless-image-id' );

		//If the uploader object has already been created, reopen the dialog
		if ( custom_uploader ) {
			custom_uploader.open();
			return;
		}

		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media( {
			title: ([Scriptless.params.text]),
			button: {
				text: ([Scriptless.params.text])
			},
			multiple: false,
			library: {type: 'image'}
		} );

		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on( 'select', function () {

			var attachment   = custom_uploader.state().get( 'selection' ).first().toJSON(),
			    preview      = $( target_input ).prevAll( '.scriptless-image-preview' ),
			    previewImage = $( '<div class="scriptless-image-preview"><img style="max-width:100%;" src="' + attachment.url + '" /></div>' );
			$( target_input ).val( attachment.id );
			if ( preview.length ) {
				preview.remove();
			}
			$( target_input ).before( previewImage );
		} );

		//Open the uploader dialog
		custom_uploader.open();
	}

	function _delete() {

		target_input = $( this ).prevAll( '.scriptless-image-id' );
		var previewView = $( this ).prevAll( '.scriptless-image-preview' );

		$( target_input ).val( '' );
		$( previewView ).remove();

	}

	Scriptless.params = typeof scriptlessL10n === 'undefined' ? '' : scriptlessL10n;
	if ( typeof Scriptless.params !== 'undefined' ) {
		Scriptless.upload();
	}

})( document, jQuery );

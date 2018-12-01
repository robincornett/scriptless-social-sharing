/*
 * Copyright (c) 2018 Robin Cornett
 * @package ScriptlessSocialSharing
 */

;(function ( document, $, undefined ) {
	'use strict';

	var ScriptlessSort = {},
	    item       = '.sortable-button',
	    $container = $( item ).parent( 'td' );

	/**
	 * Initialize the script.
	 */
	ScriptlessSort.init = function () {
		if ( ! $container.length ) {
			return;
		}
		_updateOrder();
		$( 'label[for^="scriptlesssocialsharing[buttons]"] input' ).on( 'change', ScriptlessSort.manageButtons );
		ScriptlessSort.sort();
	};

	/**
	 * Add/remove buttons as needed.
	 */
	ScriptlessSort.manageButtons = function () {
		var key   = $( this ).attr( 'data-attr' ),
		    label = $( this ).parent().text();
		if ( $( this ).prop( 'checked' ) ) {
			var $button = $( '<div />', {
				'class': 'button sortable-button',
				'style': 'margin-right:4px;'
			} )
				.append( $( '<input>', {
					'type': 'hidden',
					'name': 'scriptlesssocialsharing[order][' + key + ']',
					'value': '',
				} ) )
				.append( label );
			$container.append( $button );
		} else {
			$( item + ':contains(' + label + ')' ).remove();
		}
		_updateOrder();
	};

	/**
	 * Implement the sortable script to move the buttons around.
	 */
	ScriptlessSort.sort = function () {
		$container.sortable( {
			containment: 'parent',
			cursor: 'move',
			items: item,
			tolerance: 'pointer',
			stop: function ( event, ui ) {
				_updateOrder();
			}
		} );
	};

	/**
	 * Update the order of the buttons.
	 * @private
	 */
	function _updateOrder() {
		var items = $( item ).find( 'input' ),
		    i     = 0;
		$.each( items, function () {
			$( this ).attr( 'value', i );
			i ++;
		} );
	}

	ScriptlessSort.init();
})( document, jQuery );

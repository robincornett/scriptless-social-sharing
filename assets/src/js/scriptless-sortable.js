/*
 * Copyright (c) 2018 Robin Cornett
 * @package ScriptlessSocialSharing
 */

;(function ( document, $, undefined ) {
	'use strict';

	var ScriptlessSort = {},
	    item           = '.sortable-button',
	    $container     = $( '.scriptless-sortable-buttons' ),
	    $items         = $container.find( 'input[type="number"]' ),
	    changed        = false;

	/**
	 * Initialize the script.
	 */
	ScriptlessSort.init = function () {
		if ( ! $container.length ) {
			return;
		}
		_updateOrder();
		$( 'label[for^="scriptlesssocialsharing[buttons]"] input' ).on( 'change.scriptless-buttons', ScriptlessSort.manageButtons );
		$container.on( 'change', 'input[type="number"]', function () {
			updateAllNumbers( $( this ) )
		} );
		ScriptlessSort.sort();
	};

	/**
	 * Add/remove buttons as needed.
	 */
	ScriptlessSort.manageButtons = function () {
		var key     = $( this ).attr( 'data-attr' ),
		    label   = $( this ).parent().text(),
		    $items  = $container.find( 'input[type="number"]' ),
		    new_max = $items.length + 1;
		if ( $( this ).prop( 'checked' ) ) {
			var $button = $( '<div />', {
				'class': 'button sortable-button',
				'style': 'margin-right:4px;'
			} )
				.append( $( '<input>', {
					'type': 'number',
					'name': 'scriptlesssocialsharing[order][' + key + ']',
					'value': new_max,
					'data-initial-value': new_max,
					'min': 1,
					'max': new_max
				} ) )
				.append( label );
			$container.append( $button );
		} else {
			$( item + ':contains(' + label + ')' ).remove();
			new_max = new_max - 2;
		}
		$.each( $items, function() {
			$( this ).attr( 'max', new_max );
		} );
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
		if ( changed ) {
			return;
		}
		var items = $( item ).find( 'input' ),
		    i     = 1;
		$.each( items, function () {
			var value = $( this ).val();
			$( this ).attr( 'data-initial-value', value );
			$( this ).attr( 'value', i );
			i ++;
		} );
	}

	/**
	 * Update button/number inputs if values have been changed using the inputs instead of dragging.
	 *
	 * https://codepen.io/barrytsmith/pen/kfiqj
	 * @param currObj
	 */
	function updateAllNumbers( currObj ) {
		var targets   = $container.find( 'input[type="number"]' ),
		    delta     = currObj.val() - currObj.attr( 'data-initial-value' ), //if positive, the object went down in order. If negative, it went up.
		    new_value = parseInt( currObj.val(), 10 ),
		    old_value = parseInt( currObj.attr( 'data-initial-value' ), 10 ),
		    top       = $( targets ).length;

		if ( new_value > top ) {
			currObj.val( top );
		} else if ( new_value < 1 ) {
			currObj.val( 1 );
		}

		$( targets ).not( $( currObj ) ).each( function () {
			var v = parseInt( $( this ).val(), 10 );

			if ( v >= new_value && v < old_value && delta < 0 ) {
				$( this ).val( v + 1 );
			} else if ( v <= new_value && v > old_value && delta > 0 ) {
				$( this ).val( v - 1 );
			}
		} ).promise().done( function () {
			$( targets ).each( function () {
				if ( $( this ).val() !== '' ) {
					$( this ).attr( 'data-initial-value', $( this ).val() );
				}
			} );
		} );
		if ( changed ) {
			return;
		}
		$( '.change-warning' ).show();
		$container.sortable( 'disable' );
		changed = true;
	}

	ScriptlessSort.init();
})( document, jQuery );

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
		$( 'label[for^="scriptlesssocialsharing[buttons]"] input' ).on( 'change', ScriptlessSort.manageButtons );
		$items.on( 'change', function () {
			updateAllNumbers( $( this ), $items )
		} );
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
					'type': 'number',
					'name': 'scriptlesssocialsharing[order][' + key + ']',
					'value': $items.length,
					'data-initial-value': $items.length,
					'min': 0
				} ) )
				.append( label );
			$container.append( $button );
		} else {
			$( item + ':contains(' + label + ')' ).remove();
		}
		$.each( $items, function() {
			$( this ).attr( 'max', $items.length );
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
		    i     = 0;
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
	 * @param targets
	 */
	function updateAllNumbers( currObj, targets ) {
		var delta = currObj.val() - currObj.attr( 'data-initial-value' ), //if positive, the object went down in order. If negative, it went up.
		    c     = parseInt( currObj.val(), 10 ),
		    cI    = parseInt( currObj.attr( 'data-initial-value' ), 10 ),
		    top   = $( targets ).length;

		if ( c > top ) {
			currObj.val( top );
		} else if ( c < 0 ) {
			currObj.val( 0 );
		}

		$( targets ).not( $( currObj ) ).each( function () {
			var v = parseInt( $( this ).val(), 10 );

			if ( v >= c && v < cI && delta < 0 ) {
				$( this ).val( v + 1 );
			} else if ( v <= c && v > cI && delta > 0 ) {
				$( this ).val( v - 1 );
			}
		} ).promise().done( function () {
			//after all the fields update based on new val, set their data element so further changes can be tracked
			//(but ignore if no value given yet)
			$( targets ).each( function () {
				if ( $( this ).val() !== "" ) {
					$( this ).attr( 'data-initial-value', $( this ).val() );
				}
			} );
		} );
		$( '.change-warning' ).show();
		changed = true;
	}

	ScriptlessSort.init();
})( document, jQuery );

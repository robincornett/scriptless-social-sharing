/*
 * Copyright (c) 2026 Robin Cornett
 * @package ScriptlessSocialSharing
 */

import { speak } from '@wordpress/a11y';

const list = globalThis.document.querySelector( '.scriptless-sortable-buttons' );
const itemSelector = '.scriptless-sortable-buttons__item';
const handleSelector = '.scriptless-sortable-buttons__handle';

let dragging = null;
let startIndex = null;

const items = () => Array.from( list.querySelectorAll( itemSelector ) );

/**
 * Renumber the hidden order inputs to match the current list order.
 *
 * The saved order is the key order of the posted array, so the numbers exist
 * only to keep the stored value readable.
 */
const renumber = () => {
	items().forEach( ( item, index ) => {
		const input = item.querySelector( '.scriptless-sortable-buttons__order' );
		if ( input ) {
			input.value = index + 1;
		}
	} );
};

/**
 * Announce an item's new position to screen readers.
 *
 * @param {HTMLElement} item
 */
const announce = ( item ) => {
	const label = item.querySelector( 'label' );
	const all = items();
	const template = globalThis.scriptlessSortableL10n?.moved;

	if ( ! label || ! template ) {
		return;
	}

	speak(
		template
			.replace( '%1$s', label.textContent.trim() )
			.replace( '%2$d', all.indexOf( item ) + 1 )
			.replace( '%3$d', all.length )
	);
};

/**
 * Move an item up or down the list, then report where it landed.
 *
 * @param {HTMLElement} item
 * @param {number}      offset -1 to move up, 1 to move down.
 */
const move = ( item, offset ) => {
	const neighbor = offset < 0 ? item.previousElementSibling : item.nextElementSibling;
	if ( ! neighbor ) {
		return;
	}

	if ( offset < 0 ) {
		neighbor.before( item );
	} else {
		neighbor.after( item );
	}

	renumber();
	announce( item );
};

/**
 * Find the item the pointer is currently over, ignoring the one being dragged.
 *
 * @param {number} y The pointer's vertical position.
 * @return {HTMLElement|null} The item under the pointer.
 */
const itemAt = ( y ) => items().find( ( item ) => {
	if ( item === dragging ) {
		return false;
	}
	const box = item.getBoundingClientRect();

	return y >= box.top && y <= box.bottom;
} ) ?? null;

const onDragStart = ( event ) => {
	const handle = event.target.closest( handleSelector );
	if ( ! handle ) {
		return;
	}

	dragging = handle.closest( itemSelector );
	startIndex = items().indexOf( dragging );
	dragging.classList.add( 'is-dragging' );
	event.dataTransfer.effectAllowed = 'move';
	// Firefox will not start a drag without data on the transfer.
	event.dataTransfer.setData( 'text/plain', dragging.dataset.key );
};

const onDragOver = ( event ) => {
	if ( ! dragging ) {
		return;
	}
	event.preventDefault();
	event.dataTransfer.dropEffect = 'move';

	const target = itemAt( event.clientY );
	if ( ! target ) {
		return;
	}

	const box = target.getBoundingClientRect();
	if ( event.clientY < box.top + box.height / 2 ) {
		target.before( dragging );
	} else {
		target.after( dragging );
	}
};

const onDragEnd = () => {
	if ( ! dragging ) {
		return;
	}

	dragging.classList.remove( 'is-dragging' );
	renumber();
	if ( startIndex !== items().indexOf( dragging ) ) {
		announce( dragging );
	}
	dragging = null;
	startIndex = null;
};

const onKeyDown = ( event ) => {
	const handle = event.target.closest( handleSelector );
	if ( ! handle || ( 'ArrowUp' !== event.key && 'ArrowDown' !== event.key ) ) {
		return;
	}

	event.preventDefault();
	move( handle.closest( itemSelector ), 'ArrowUp' === event.key ? -1 : 1 );
	handle.focus();
};

if ( list ) {
	list.addEventListener( 'dragstart', onDragStart );
	list.addEventListener( 'dragover', onDragOver );
	list.addEventListener( 'dragend', onDragEnd );
	list.addEventListener( 'drop', ( event ) => event.preventDefault() );
	list.addEventListener( 'keydown', onKeyDown );
}

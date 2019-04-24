/*
 * Copyright (c) 2019 Robin Cornett
 */

(function ( wp, undefined ) {
	'use strict';
	const ScriptlessBlockObject = {
		el: wp.element.createElement,
	};

	/**
	 * Initialize and register the block.
	 */
	ScriptlessBlockObject.init = function () {
		const registerBlockType = wp.blocks.registerBlockType,
		      ServerSideRender  = wp.components.ServerSideRender,
		      InspectorControls = wp.editor.InspectorControls;

		registerBlockType( ScriptlessBlockObject.params.block, {
			title: ScriptlessBlockObject.params.title,
			description: ScriptlessBlockObject.params.description,
			keywords: ScriptlessBlockObject.params.keywords,
			icon: ScriptlessBlockObject.params.icon,
			category: ScriptlessBlockObject.params.category,
			supports: {
				html: false
			},

			getEditWrapperProps( {blockAlignment} ) {
				return {'data-align': blockAlignment};
			},

			edit: props => {
				const {
					      attributes,
					      setAttributes
				      }                     = props,
				      Fragment              = wp.element.Fragment,
				      BlockControls         = wp.editor.BlockControls,
				      BlockAlignmentToolbar = wp.editor.BlockAlignmentToolbar;
				return [
					ScriptlessBlockObject.el( ServerSideRender, {
						block: ScriptlessBlockObject.params.block,
						attributes: attributes
					} ),
					ScriptlessBlockObject.el( Fragment, null,
						ScriptlessBlockObject.el( BlockControls, null,
							ScriptlessBlockObject.el( BlockAlignmentToolbar, {
								value: attributes.blockAlignment,
								controls: ['wide', 'full'],
								onChange: ( value ) => {
									setAttributes( {blockAlignment: value} );
								},
							} )
						),
					),
					ScriptlessBlockObject.el( InspectorControls, {},
						_getPanels( props )
					)
				];
			},

			save: props => {
				return null;
			},
		} );
	};

	/**
	 * Get the panels for the block controls.
	 *
	 * @param props
	 * @return {Array}
	 * @private
	 */
	function _getPanels( props ) {
		const panels    = [],
		      PanelBody = wp.components.PanelBody;
		Object.keys( ScriptlessBlockObject.params.panels ).forEach( function ( key, index ) {
			if ( ScriptlessBlockObject.params.panels.hasOwnProperty( key ) ) {
				const IndividualPanel = ScriptlessBlockObject.params.panels[key];
				panels[index] = ScriptlessBlockObject.el( PanelBody, {
					title: IndividualPanel.title,
					initialOpen: IndividualPanel.initialOpen
				}, _getControls( props, IndividualPanel.attributes ) );
			}
		} );

		return panels;
	}

	/**
	 * Get all of the block controls, with defaults and options.
	 *
	 * @param props
	 * @param fields
	 * @return {Array}
	 * @private
	 */
	function _getControls( props, fields ) {
		const controls = [];
		Object.keys( fields ).forEach( function ( key, index ) {
			if ( fields.hasOwnProperty( key ) ) {
				var skipped = [ 'blockAlignment', 'className' ];
				if ( -1 !== skipped.indexOf( key ) ) {
					return;
				}
				const IndividualField = fields[key],
				      control         = _getControlType( IndividualField.method, IndividualField.type );
				controls[index] = ScriptlessBlockObject.el( control, _getIndividualControl( key, IndividualField, props ) );
			}
		} );

		return controls;
	}

	/**
	 * Get the control type.
	 * @param method
	 * @param control_type
	 * @return {*}
	 * @private
	 */
	function _getControlType( method, control_type ) {
		const {
			      TextControl,
			      SelectControl,
			      RangeControl,
			      CheckboxControl,
			      TextareaControl
		      } = wp.components;
		const control = TextControl;
		if ( 'select' === method ) {
			return SelectControl;
		} else if ( 'number' === method && 'number' === control_type ) {
			return RangeControl;
		} else if ( 'checkbox' === method ) {
			return CheckboxControl;
		} else if ( 'textarea' === method ) {
			return TextareaControl;
		}

		return control;
	}

	/**
	 * Build the individual control object. Sets up standard properties for all
	 * controls; then adds custom properties as needed.
	 *
	 * @param key
	 * @param field
	 * @param props
	 * @return {{label: *, value: *, className: string, onChange: onChange}}
	 * @private
	 */
	function _getIndividualControl( key, field, props ) {
		const {attributes, setAttributes} = props;
		const control = {
			heading: field.heading,
			label: field.label,
			value: attributes[key],
			className: 'scriptlesssocialsharing-' + key,
			onChange: ( value ) => {
				setAttributes( {[key]: value} );
			}
		};

		if ( 'select' === field.method ) {
			control.options = field.options;
		} else if ( 'number' === field.method ) {
			control.min = field.min;
			control.max = field.max;
			if ( 'number' !== field.type ) {
				control.type = 'number';
			} else {
				control.initialPosition = field.min;
			}
		} else if ( 'checkbox' === field.method ) {
			control.checked = attributes[key];
		}

		return control;
	}

	ScriptlessBlockObject.params = typeof ScriptlessBlock === 'undefined' ? '' : ScriptlessBlock;

	if ( typeof ScriptlessBlockObject.params !== 'undefined' ) {
		ScriptlessBlockObject.init();
	}
} )( wp );

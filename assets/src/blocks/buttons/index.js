import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { Disabled, PanelBody, TextControl, CheckboxControl } from '@wordpress/components';
import { ServerSideRender } from '@wordpress/server-side-render';
import { Fragment } from '@wordpress/element';

import './editor.scss';

const BLOCK_NAME = 'scriptlesssocialsharing/buttons';
const { panels } = globalThis.ScriptlessBlock ?? {};

function getPanels( { attributes, setAttributes } ) {
	if ( ! panels ) {
		return null;
	}
	return Object.entries( panels ).map( ( [ key, panel ] ) => (
		<PanelBody
			key={ key }
			title={ panel.title }
			initialOpen={ panel.initialOpen }
			className={ `scriptless-panel-${ key }` }
		>
			{ getControls( panel.attributes, attributes, setAttributes ) }
		</PanelBody>
	) );
}

function getControls( fields, attributes, setAttributes ) {
	return Object.entries( fields )
		.map( ( [ key, field ] ) => {
			if ( field.method === 'checkbox' ) {
				return (
					<CheckboxControl
						key={ key }
						label={ field.label }
						help={ field.heading ?? undefined }
						checked={ !! attributes[ key ] }
						className={ `scriptlesssocialsharing-${ key }` }
						onChange={ ( value ) => setAttributes( { [ key ]: value } ) }
					/>
				);
			}
			return (
				<TextControl
					key={ key }
					label={ field.label }
					value={ attributes[ key ] ?? '' }
					className={ `scriptlesssocialsharing-${ key }` }
					onChange={ ( value ) => setAttributes( { [ key ]: value } ) }
				/>
			);
		} );
}

registerBlockType( BLOCK_NAME, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		return (
			<Fragment>
				<div { ...blockProps }>
					<Disabled>
						<ServerSideRender
							block={ BLOCK_NAME }
							attributes={ attributes }
						/>
					</Disabled>
				</div>
				<InspectorControls>
					{ getPanels( { attributes, setAttributes } ) }
				</InspectorControls>
			</Fragment>
		);
	},

	save() {
		return null;
	},
} );

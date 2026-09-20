import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, CheckboxControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { Fragment } from '@wordpress/element';

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
		.filter( ( [ key ] ) => key !== 'blockAlignment' && key !== 'className' )
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
					<ServerSideRender
						block={ BLOCK_NAME }
						attributes={ attributes }
					/>
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

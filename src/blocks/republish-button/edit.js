/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';

function RepublishButtonEdit( { attributes, setAttributes } ) {
	const { buttonText, message } = attributes;

	// Block supports (color, typography, spacing, border, shadow) are applied
	// to the wrapper div automatically by useBlockProps(). The inner button
	// inherits colors via CSS.
	const blockProps = useBlockProps();

	return (
		<>
			<div { ...blockProps }>
				<RichText
					tagName="p"
					className="wp-block-republication-tracker-tool-republish-button__message"
					value={ message }
					onChange={ ( val ) => setAttributes( { message: val } ) }
					placeholder={ __(
						'Add a description of the republish feature…',
						'republication-tracker-tool'
					) }
					withoutInteractiveFormatting
				/>
				<span className="wp-block-republication-tracker-tool-republish-button__button">
					<RichText
						tagName="span"
						value={ buttonText }
						onChange={ ( val ) =>
							setAttributes( { buttonText: val } )
						}
						placeholder={ __(
							'Republish This Story',
							'republication-tracker-tool'
						) }
						allowedFormats={ [] }
						aria-label={ __(
							'Button text',
							'republication-tracker-tool'
						) }
					/>
				</span>
			</div>
		</>
	);
}

export default RepublishButtonEdit;

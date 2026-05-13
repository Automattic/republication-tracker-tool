/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	RichText,
	useBlockProps,
	/* eslint-disable @wordpress/no-unsafe-wp-apis */
	__experimentalUseBorderProps as useBorderProps,
	__experimentalUseColorProps as useColorProps,
	__experimentalGetSpacingClassesAndStyles as useSpacingProps,
	/* eslint-enable @wordpress/no-unsafe-wp-apis */
} from '@wordpress/block-editor';

function RepublishButtonEdit( { attributes, setAttributes } ) {
	const { buttonText } = attributes;
	const borderProps = useBorderProps( attributes );
	const colorProps = useColorProps( attributes );
	const spacingProps = useSpacingProps( attributes );

	const innerClassNames = [
		'wp-block-button__link',
		'wp-element-button',
		'wp-block-republication-tracker-tool-republish-button',
		colorProps.className,
		borderProps.className,
	]
		.filter( Boolean )
		.join( ' ' );

	const blockProps = useBlockProps( {
		className: innerClassNames,
		style: {
			...borderProps.style,
			...colorProps.style,
			...spacingProps.style,
		},
	} );

	return (
		<div className="wp-block-buttons is-layout-flex">
			<div className="wp-block-button">
				<RichText
					tagName="button"
					{ ...blockProps }
					value={ buttonText }
					onChange={ ( val ) => setAttributes( { buttonText: val } ) }
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
			</div>
		</div>
	);
}

export default RepublishButtonEdit;

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

function RepublishLicenseEdit() {
	const blockProps = useBlockProps();

	return (
		<div { ...blockProps }>
			<ServerSideRender
				block="republication-tracker-tool/republish-license"
				EmptyResponsePlaceholder={ () => (
					<p>
						{ __(
							'No Creative Commons license is configured in the Republication Tracker settings.',
							'republication-tracker-tool'
						) }
					</p>
				) }
			/>
		</div>
	);
}

export default RepublishLicenseEdit;

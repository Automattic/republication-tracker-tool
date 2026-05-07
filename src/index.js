import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/editor';
import { ToggleControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { __ } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';

const META_KEY = 'republication-tracker-tool-hide-widget';

const RepublicationTrackerPanel = () => {
	const postType = useSelect(
		( select ) => select( 'core/editor' ).getCurrentPostType(),
		[]
	);

	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

	const filterHides = useSelect(
		( select ) =>
			select( 'core/editor' ).getEditedPostAttribute(
				'republication_tracker_tool_filter_hides'
			),
		[]
	);

	if ( 'post' !== postType ) {
		return null;
	}

	const hideWidget = !! meta?.[ META_KEY ];

	return (
		<PluginDocumentSettingPanel
			name="republication-tracker-tool-hide-widget"
			title={ __(
				'Hide Republication Widget',
				'republication-tracker-tool'
			) }
		>
			<ToggleControl
				label={ __(
					'Hide the Republication sharing widget on this post?',
					'republication-tracker-tool'
				) }
				checked={ filterHides ? true : hideWidget }
				disabled={ filterHides }
				onChange={ ( value ) =>
					setMeta( { ...meta, [ META_KEY ]: value } )
				}
				help={
					filterHides
						? createInterpolateElement(
								__(
									'The Republication sharing widget on this post is programatically disabled through the <code>hide_republication_widget</code> filter. <a>Read more about this filter</a>.',
									'republication-tracker-tool'
								),
								{
									code: <code />,
									a: (
										// eslint-disable-next-line jsx-a11y/anchor-has-content
										<a
											href="https://github.com/Automattic/republication-tracker-tool/blob/trunk/docs/removing-republish-button-from-categories.md"
											target="_blank"
											rel="noopener noreferrer"
										/>
									),
								}
						  )
						: undefined
				}
			/>
		</PluginDocumentSettingPanel>
	);
};

registerPlugin( 'republication-tracker-tool', {
	render: RepublicationTrackerPanel,
} );

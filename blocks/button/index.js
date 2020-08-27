( function( wp ) {
	/**
	 * Registers a new block provided a unique name and an object defining its behavior.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	var registerBlockType = wp.blocks.registerBlockType;
	/**
	 * Returns a new element of given type. Element is an abstraction layer atop React.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-element/
	 */
	var el = wp.element.createElement;
	/**
	 * Retrieves the translation of text.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/packages/packages-i18n/
	 */
	var __ = wp.i18n.__;

	var TextControl = wp.components.TextControl;
	var Placeholder = wp.components.Placeholder;

	/**
	 * Every block starts by registering a new block type definition.
	 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/#registering-a-block
	 */
	registerBlockType( 'republication-tracker-tool/button', {
		/**
		 * This is the display title for your block, which can be translated with `i18n` functions.
		 * The block inserter will show this name.
		 */
		title: __( 'Republication Modal Button', 'republication-tracker-tool' ),

		/**
		 * Describe the block for the block inspector
		 */
		description: __( 'Add a button which opens the Republication Tracker Tool sharing modal.', 'republication-tracker-tool' ),

		/**
		 * An icon property should be specified to make it easier to identify a block.
		 * These can be any of WordPress’ Dashicons, or a custom svg element.
		 */
		icon: 'button',

		/**
		 * Blocks are grouped into categories to help users browse and discover them.
		 * The categories provided by core are `common`, `embed`, `formatting`, `layout` and `widgets`.
		 */
		category: 'widgets',

		/**
		 * Optional block extended support features.
		 */
		supports: {
			align: false,
			alignWide: false,
			anchor: false,
			customClassName: false,
			className: true,
			html: false,
			multiple: true,
			reusable: false,
		},

		/**
		 * The edit function describes the structure of your block in the context of the editor.
		 * This represents what the editor will render when the block is used.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#edit
		 *
		 * @param {Object} [props] Properties passed from the editor.
		 * @return {Element}       Element to render.
		 */
		edit: function( props ) {
			return el(
				Placeholder,
				{
					className: props.className + ' republication-tracker-tool-button',
					label: __( 'Republication Modal Button', 'republication-tracker-tool' ),
				},
				el ( TextControl, {
					label: __( 'Button Label', 'republication-tracker-tool' ),
					value: props.attributes.label,
					placeholder: __( 'Republish This Story', 'republication-tracker-tool' ),
					onChange: ( value ) => { props.setAttributes( { label: value } ); },
				} )
			);
		},

		/**
		 * The save function defines the way in which the different attributes should be combined
		 * into the final markup, which is then serialized by Gutenberg into `post_content`.
		 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/block-api/block-edit-save/#save
		 *
		 * @return {null}  There is no element to render
		 */
		save: function( props ) {
			// no element created here, but this function is needed in order to let the props get passed along.
			return null;
		}
	} );
} )(
	window.wp
);

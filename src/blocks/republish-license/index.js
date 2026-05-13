/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';
import { shield as icon } from '@wordpress/icons';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';

registerBlockType( metadata, {
	edit: Edit,
	icon,
	save: () => null,
} );

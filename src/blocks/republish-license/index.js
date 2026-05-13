/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';

registerBlockType( metadata, {
	edit: Edit,
	save: () => null,
} );

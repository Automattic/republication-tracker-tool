/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import Edit from './edit';
import './style.scss';

const { name } = metadata;
export { metadata, name };

export const settings = {
	edit: Edit,
	save: () => null,
};

registerBlockType( { name, ...metadata }, settings );

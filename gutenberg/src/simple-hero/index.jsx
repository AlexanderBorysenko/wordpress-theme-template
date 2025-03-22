import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

registerBlockType('blocks/simple-hero', {
	edit,
	save,
	attributes: {
		mainImage: {
			type: 'object',
			default: {}
		},
		marginBottom: {
			type: 'string',
			default: ''
		}
	}
});

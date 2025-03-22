import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

registerBlockType('blocks/container', {
	edit,
	save,
	attributes: {
		containerType: {
			type: 'string',
			default: 'container'
		},
		marginBottom: {
			type: 'string',
			default: ''
		}
	}
});

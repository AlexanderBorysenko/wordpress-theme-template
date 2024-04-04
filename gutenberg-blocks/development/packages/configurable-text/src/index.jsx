import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

registerBlockType('blocks/configurable-text', {
	edit,
	save,
	icon: 'text',
	attributes: {
		tagName: {
			type: 'string',
			default: 'p'
		},
		content: {
			type: 'string',
			source: 'html',
			selector: '*'
		},
		fontSize: {
			type: 'string',
			default: ''
		},
		fontWeight: {
			type: 'string',
			default: ''
		},
		lineHeight: {
			type: 'string',
			default: ''
		},
		align: {
			type: 'string',
			default: ''
		},
		fontFamily: {
			type: 'string',
			default: ''
		},
		marginBottom: {
			type: 'string',
			default: ''
		}
	}
});

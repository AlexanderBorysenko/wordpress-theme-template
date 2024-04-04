import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

export const backgroundColorsOptions = [
	{ label: 'Cultured', value: 'background-cultured' },
	{ label: 'Weldon Blue', value: 'background-weldon-blue' },
	{ label: 'Dark', value: 'background-dark' }
];

export const colorOptions = [
	{ label: 'White', value: 'color-white' },
	{ label: 'Dark', value: 'color-dark' }
];

registerBlockType('blocks/styled-wrapper', {
	edit,
	save,
	attributes: {
		backgroundColor: {
			type: 'string',
			default: 'background-cultured'
		},
		color: {
			type: 'string',
			default: 'color-dark'
		},
		marginBottom: {
			type: 'string',
			default: ''
		},
		enableWave: {
			type: 'boolean',
			default: true
		}
	}
});

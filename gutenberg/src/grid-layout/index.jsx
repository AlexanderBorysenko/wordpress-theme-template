import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

import './smart-layout-column/index.jsx';

registerBlockType('blocks/grid-layout', {
	category: 'theme-layout',
	edit,
	save,
	attributes: {
		marginBottom: {
			type: 'string',
			default: ''
		},
		containerType: {
			type: 'string',
			default: ''
		}
	},
	icon: (
		<svg width='20' height='20' viewBox='0 0 20 20' fill='none'>
			<rect x='0.5' y='0.5' width='4.45455' height='4' stroke='black' />
			<rect x='0.5' y='8' width='11.7273' height='4' stroke='black' />
			<rect
				x='15.0449'
				y='0.5'
				width='4.45455'
				height='4'
				stroke='black'
			/>
			<rect x='15.0449' y='8' width='4.45455' height='4' stroke='black' />
			<rect
				x='7.77344'
				y='0.5'
				width='4.45455'
				height='4'
				stroke='black'
			/>
			<rect x='0.5' y='15.5' width='19' height='4' stroke='black' />
		</svg>
	)
});

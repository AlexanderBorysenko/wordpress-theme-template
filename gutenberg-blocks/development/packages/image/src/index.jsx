import { registerBlockType } from '@wordpress/blocks';

import edit from './edit';
import save from './save';

registerBlockType('blocks/image', {
	edit,
	save,
	attributes: {
		imageSizes: {
			type: 'object',
			default: {}
		},
		imageSrc: {
			type: 'string',
			source: 'attribute',
			selector: 'img',
			attribute: 'src',
			default: ''
		},
		imageAlt: {
			type: 'string',
			source: 'attribute',
			selector: 'img',
			attribute: 'alt',
			default: ''
		},
		imageId: {
			type: 'string',
			source: 'attribute',
			selector: 'img',
			attribute: 'data-id',
			default: ''
		},
		desktopHeight: {
			type: 'string',
			default: '600px'
		},
		enableHeightResponsive: {
			type: 'boolean',
			default: false
		},
		tabletHeight: {
			type: 'string',
			default: '450px'
		},
		mobileHeight: {
			type: 'string',
			default: '300px'
		},
		uniqueID: {
			type: 'string',
			default: ''
		},
		imagePosition: {
			type: 'string',
			default: 'contain'
		}
	}
});

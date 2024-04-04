import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save.jsx';

registerBlockType('blocks/smart-layout-column', {
	parent: ['blocks/smart-layout'],
	title: 'Column',
	icon: 'welcome-write-blog',
	description: 'Column',
	supports: {
		reusable: false,
		html: false,
		customClassName: false,
		anchor: false
	},
	attributes: {
		columnsSpan: {
			type: 'object',
			default: {
				initial: 'g-col-6',
				smallDesktop: 'g-sdw-col-12',
				largeMobile: 'g-lmw-col-12'
			}
		},
		verticalAlignment: {
			type: 'string',
			default: ''
		},
		paddingLeft: {
			type: 'object',
			default: {
				initial: '',
				smallDesktop: '',
				largeMobile: ''
			}
		},
		paddingRight: {
			type: 'object',
			default: {
				initial: '',
				smallDesktop: '',
				largeMobile: ''
			}
		}
	},
	edit: Edit,
	save
});

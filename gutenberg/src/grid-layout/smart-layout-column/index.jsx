import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit.jsx';
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
				initial: 'grid-col-5',
				small: 'grid-sw-col-10',
				mobile: 'grid-mw-col-10'
			}
		},
		selfAlign: {
			type: 'string',
			default: ''
		},
		selfJustify: {
			type: 'string',
			default: ''
		}
	},
	edit: Edit,
	save
});

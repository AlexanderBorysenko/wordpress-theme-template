import { InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function save({ attributes }) {
	const { 
		columnsSpan, 
		selfAlign,
		selfJustify,
	 } =
		attributes;

	const classes = classNames(
		Object.values(columnsSpan),
		selfAlign,
		selfJustify
	);

	return (
		<>
			<div className={classes}>
				<InnerBlocks.Content />
			</div>
		</>
	);
}

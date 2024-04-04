import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function save({ attributes }) {
	const { columnsSpan, paddingLeft, paddingRight, verticalAlignment } =
		attributes;

	const classes = classNames(
		Object.values(columnsSpan),
		Object.values(paddingLeft),
		Object.values(paddingRight),
		verticalAlignment
	);

	return (
		<>
			<div className={classes}>
				<InnerBlocks.Content />
			</div>
		</>
	);
}

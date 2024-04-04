import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function Save({ attributes }) {
	const { gap, marginBottom } = attributes;
	const className = classNames(
		'g-layout',
		Object.values(gap),
		'gap-h-lmw-0',
		marginBottom
	);

	return (
		<div className={className}>
			<InnerBlocks.Content />
		</div>
	);
}

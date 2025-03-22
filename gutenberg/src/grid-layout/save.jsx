import { InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function Save({ attributes }) {
	const { marginBottom, containerType } = attributes;
	const className = classNames('grid', marginBottom, containerType);

	return (
		<div className={className}>
			<InnerBlocks.Content />
		</div>
	);
}

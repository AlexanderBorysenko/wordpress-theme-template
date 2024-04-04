import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function Save({ attributes }) {
	const { containerType, marginBottom } = attributes;
	const blockProps = useBlockProps.save({
		className: classNames(containerType, marginBottom)
	});

	return (
		<div {...blockProps}>
			<InnerBlocks.Content />
		</div>
	);
}

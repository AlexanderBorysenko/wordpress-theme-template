import { useBlockProps } from '@wordpress/block-editor';
import { RichText } from '@wordpress/block-editor';
import classNames from 'classnames';

export default function Save({ attributes }) {
	const {
		content,
		fontSize,
		fontWeight,
		align,
		tagName,
		lineHeight,
		fontFamily,
		marginBottom
	} = attributes;
	const blockProps = useBlockProps.save({
		className: classNames(
			fontSize,
			fontFamily,
			{ [`text-${align}`]: align },
			lineHeight,
			fontWeight,
			marginBottom
		)
	});
	blockProps.className = blockProps.className
		.replace(/wp-block-blocks-configurable-text/, '')
		.trim();

	return (
		<RichText.Content {...blockProps} tagName={tagName} value={content} />
	);
}

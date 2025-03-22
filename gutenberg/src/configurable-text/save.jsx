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
		marginBottom,
		widthLimiter,
		color
	} = attributes;
	const blockProps = useBlockProps.save({
		className: classNames(
			fontSize,
			{ [`text-${align}`]: align },
			lineHeight,
			fontWeight,
			marginBottom,
			widthLimiter,
			color
		)
	});
	blockProps.className = blockProps.className
		.replace(/wp-block-blocks-configurable-text/, '')
		.trim();

	return content && content.length > 0 ? (
		<RichText.Content {...blockProps} tagName={tagName} value={content} />
	) : null;
}

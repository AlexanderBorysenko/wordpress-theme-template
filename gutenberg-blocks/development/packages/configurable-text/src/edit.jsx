import {
	BlockControls,
	AlignmentControl,
	useBlockProps
} from '@wordpress/block-editor';
import { RichText, InspectorControls } from '@wordpress/block-editor';
import { SelectControl, PanelBody } from '@wordpress/components';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import classNames from 'classnames';
import { useOnEnter } from './use-enter';
import './editor-style.scss';
import MarginBottomSelector from '../../../components/MarginBottomSelector';
import { createBlock } from '@wordpress/blocks';
import {
	fontSizeOptions,
	fontWeightOptions,
	lineHeightOptions
} from '../../../constants/fonts';

export default function Edit({
	attributes,
	mergeBlocks,
	onReplace,
	onRemove,
	setAttributes,
	clientId
}) {
	const {
		content,
		fontSize,
		fontWeight,
		lineHeight,
		tagName,
		align,
		fontFamily,
		marginBottom
	} = attributes;
	const blockProps = useBlockProps({
		ref: useOnEnter({ clientId, content }),
		className: classNames(
			fontSize,
			fontFamily,
			`text-${align}`,
			fontWeight,
			lineHeight,
			marginBottom
		)
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title='Font'>
					<AlignmentControl
						value={align}
						onChange={newAlign =>
							setAttributes({
								align: newAlign
							})
						}
					/>
					<SelectControl
						label='Tag Name'
						value={tagName}
						onChange={tagName => setAttributes({ tagName })}
						options={[
							{ label: 'H2', value: 'h2' },
							{ label: 'H3', value: 'h3' },
							{ label: 'H4', value: 'h4' },
							{ label: 'H5', value: 'h5' },
							{ label: 'H6', value: 'h6' },
							{ label: 'P', value: 'p' }
						]}
					/>
					<SelectControl
						label='Font Family'
						value={fontFamily}
						onChange={fontFamily => setAttributes({ fontFamily })}
						options={[
							{ label: 'Default', value: '' },
							{ label: 'Heading', value: 'ff-heading' }
						]}
					/>
					<SelectControl
						label='Font Size'
						value={fontSize}
						onChange={fontSize => setAttributes({ fontSize })}
						options={fontSizeOptions}
					/>
					<SelectControl
						label='Font Weight'
						value={fontWeight}
						onChange={fontWeight => setAttributes({ fontWeight })}
						options={fontWeightOptions}
					/>
					<SelectControl
						label='Line Height'
						value={lineHeight}
						onChange={lineHeight => setAttributes({ lineHeight })}
						options={lineHeightOptions}
					/>
				</PanelBody>
			</InspectorControls>
			<MarginBottomSelector
				marginBottom={marginBottom}
				setMarginBottom={marginBottom =>
					setAttributes({ marginBottom })
				}
			/>
			<RichText
				identifier='content'
				tagName={tagName}
				{...blockProps}
				value={content}
				onChange={newContent => setAttributes({ content: newContent })}
				onSplit={(value, isOriginal) => {
					let newAttributes;

					if (isOriginal || value) {
						newAttributes = {
							...attributes,
							content: value
						};
					}

					const block = createBlock(
						'blocks/configurable-text',
						newAttributes
					);

					if (isOriginal) {
						block.clientId = clientId;
					}

					return block;
				}}
				onMerge={mergeBlocks}
				onReplace={onReplace}
				onRemove={onRemove}
				placeholder='Type / to choose a block'
				allowedFormats={[
					'theme/underline-decoration',
					'core/bold',
					'core/italic',
					'core/link',
					'core/strikethrough'
				]}
			/>
		</>
	);
}

registerFormatType('theme/underline-decoration', {
	title: 'Underline Decoration',
	tagName: 'span',
	className: 'underline-decoration',
	edit({ isActive, value, onChange }) {
		const onToggle = () => {
			onChange(
				toggleFormat(value, { type: 'theme/underline-decoration' })
			);
		};

		return (
			<BlockControls
				controls={[
					{
						icon: 'editor-underline',
						title: 'Underline Decoration',
						onClick: onToggle,
						isActive: isActive
					}
				]}></BlockControls>
		);
	}
});

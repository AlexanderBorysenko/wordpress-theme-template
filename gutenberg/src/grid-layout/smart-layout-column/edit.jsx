import { InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { select } from '@wordpress/data';
import RenderAppenderElement from '../../../components/RenderAppenderElement';
import classNames from 'classnames';
import { SelectControl } from '@wordpress/components';

export default function Edit({
	attributes,
	setAttributes,
	clientId,
	isSelected
}) {
	const { columnsSpan, selfAlign, selfJustify } = attributes;

	const blocks = select('core/block-editor').getBlocksByClientId(clientId);
	const innerCount =
		blocks && blocks.length > 0 ? blocks[0].innerBlocks.length : 0;

	const columnNumbersArray = [3, 4, 5, 6, 7, 8, 10];

	return (
		<>
			<InspectorControls>
				<PanelBody title='Self Align' initialOpen={true}>
					<SelectControl
						label='Self Align'
						value={selfAlign}
						onChange={value => {
							setAttributes({ selfAlign: value });
						}}
						options={[
							{ label: 'Initial', value: '' },
							{ label: 'Center', value: 'align-self-center' },
							{ label: 'End', value: 'align-self-end' }
						]}
					/>
				</PanelBody>
				<PanelBody title='Self Justify' initialOpen={true}>
					<SelectControl
						label='Self Justify'
						value={selfJustify}
						onChange={value => {
							setAttributes({ selfJustify: value });
						}}
						options={[
							{ label: 'Initial', value: '' },
							{ label: 'Center', value: 'justify-self-center' },
							{ label: 'End', value: 'justify-self-end' }
						]}
					/>
				</PanelBody>

				<PanelBody title='Fill Columns' initialOpen={true}>
					<SelectControl
						label='Initial Columns'
						value={columnsSpan.initial}
						onChange={value => {
							setAttributes({
								columnsSpan: { ...columnsSpan, initial: value }
							});
						}}
						options={[3, 4, 5, 6, 7, 10].map(number => ({
							label: number,
							value: `grid-col-${number}`
						}))}
					/>
					<SelectControl
						label='Small Screen Columns'
						value={columnsSpan.small}
						onChange={value => {
							setAttributes({
								columnsSpan: {
									...columnsSpan,
									small: value
								}
							});
						}}
						options={[4, 5, 6, 10].map(number => ({
							label: number,
							value: `grid-tablet-col-${number}`
						}))}
					/>
					<SelectControl
						label='Mobile Columns'
						value={columnsSpan.mobile}
						onChange={value => {
							setAttributes({
								columnsSpan: {
									...columnsSpan,
									mobile: value
								}
							});
						}}
						options={[5, 6].map(number => ({
							label: number,
							value: `grid-mobile-col-${number}`
						}))}
					/>
				</PanelBody>
			</InspectorControls>
			<InnerBlocks
				renderAppender={
					isSelected || innerCount < 1
						? () => RenderAppenderElement({ clientId })
						: false
				}
			/>
		</>
	);
}

const { createHigherOrderComponent } = wp.compose;
const withCustomClassName = createHigherOrderComponent(BlockListBlock => {
	return props => {
		if (props.attributes.columnsSpan) {
			return (
				<BlockListBlock
					{...props}
					className={classNames(
						Object.values(props.attributes.columnsSpan),
						props.attributes.selfAlign,
						props.attributes.selfJustify,
						'has-ui-select-helper'
					)}
				/>
			);
		}

		return <BlockListBlock {...props} />;
	};
}, 'withClientIdClassName');

wp.hooks.addFilter(
	'editor.BlockListBlock',
	'my-plugin/add-layout-column-span-list-class',
	withCustomClassName
);

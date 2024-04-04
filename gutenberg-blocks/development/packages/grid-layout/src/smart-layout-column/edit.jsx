import { InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { select } from '@wordpress/data';
import RenderAppenderElement from '../../../../components/RenderAppenderElement';
import classNames from 'classnames';
import { SelectControl } from '@wordpress/components';
import {
	paddingLeftInitialOptions,
	paddingLeftSmallDesktopOptions,
	paddingRightInitialOptions,
	paddingRightLargeMobileOptions,
	paddingRightSmallDesktopOptions
} from '../../../../constants/paddings';

export default function Edit({
	attributes,
	setAttributes,
	clientId,
	isSelected
}) {
	const { columnsSpan, paddingLeft, paddingRight, verticalAlignment } =
		attributes;

	const blocks = select('core/block-editor').getBlocksByClientId(clientId);
	const innerCount =
		blocks && blocks.length > 0 ? blocks[0].innerBlocks.length : 0;

	return (
		<>
			<InspectorControls>
				<PanelBody title='Vertical Alignment' initialOpen={true}>
					<SelectControl
						label='Vertical Alignment'
						value={verticalAlignment}
						onChange={value => {
							setAttributes({ verticalAlignment: value });
						}}
						options={[
							{ label: 'Top', value: 'align-self-start' },
							{ label: 'Middle', value: 'align-self-center' },
							{ label: 'Bottom', value: 'align-self-end' }
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
						options={[
							{ label: '2', value: 'g-col-2' },
							{ label: '3', value: 'g-col-3' },
							{ label: '4', value: 'g-col-4' },
							{ label: '6', value: 'g-col-6' },
							{ label: '7', value: 'g-col-7' },
							{ label: '8', value: 'g-col-8' },
							{ label: '10', value: 'g-col-10' },
							{ label: '12', value: 'g-col-12' }
						]}
					/>
					<SelectControl
						label='Small Desktop Columns'
						value={columnsSpan.smallDesktop}
						onChange={value => {
							setAttributes({
								columnsSpan: {
									...columnsSpan,
									smallDesktop: value
								}
							});
						}}
						options={[
							{ label: '4', value: 'g-sdw-col-4' },
							{ label: '6', value: 'g-sdw-col-6' },
							{ label: '8', value: 'g-sdw-col-8' },
							{ label: '10', value: 'g-sdw-col-10' },
							{ label: '12', value: 'g-sdw-col-12' }
						]}
					/>
					<SelectControl
						label='Large Mobile Columns'
						value={columnsSpan.largeMobile}
						onChange={value => {
							setAttributes({
								columnsSpan: {
									...columnsSpan,
									largeMobile: value
								}
							});
						}}
						options={[
							{ label: '6', value: 'g-lmw-col-6' },
							{ label: '8', value: 'g-lmw-col-8' },
							{ label: '10', value: 'g-lmw-col-10' },
							{ label: '12', value: 'g-lmw-col-12' }
						]}
					/>
				</PanelBody>
				<PanelBody title='Padding Left' initialOpen={false}>
					<SelectControl
						label='Initial Padding Left'
						value={paddingLeft.initial}
						onChange={value => {
							setAttributes({
								paddingLeft: { ...paddingLeft, initial: value }
							});
						}}
						options={paddingLeftInitialOptions}
					/>
					<SelectControl
						label='Small Desktop Padding Left'
						value={paddingLeft.smallDesktop}
						onChange={value => {
							setAttributes({
								paddingLeft: {
									...paddingLeft,
									smallDesktop: value
								}
							});
						}}
						options={paddingLeftSmallDesktopOptions}
					/>
					<SelectControl
						label='Large Mobile Padding Left'
						value={paddingLeft.largeMobile}
						onChange={value => {
							setAttributes({
								paddingLeft: {
									...paddingLeft,
									largeMobile: value
								}
							});
						}}
						options={paddingLeftInitialOptions}
					/>
				</PanelBody>
				<PanelBody title='Padding Right' initialOpen={false}>
					<SelectControl
						label='Initial Padding Right'
						value={paddingRight.initial}
						onChange={value => {
							setAttributes({
								paddingRight: {
									...paddingRight,
									initial: value
								}
							});
						}}
						options={paddingRightInitialOptions}
					/>
					<SelectControl
						label='Small Desktop Padding Right'
						value={paddingRight.smallDesktop}
						onChange={value => {
							setAttributes({
								paddingRight: {
									...paddingRight,
									smallDesktop: value
								}
							});
						}}
						options={paddingRightSmallDesktopOptions}
					/>
					<SelectControl
						label='Large Mobile Padding Right'
						value={paddingRight.largeMobile}
						onChange={value => {
							setAttributes({
								paddingRight: {
									...paddingRight,
									largeMobile: value
								}
							});
						}}
						options={paddingRightLargeMobileOptions}
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
						Object.values(props.attributes.paddingLeft),
						Object.values(props.attributes.paddingRight),
						props.attributes.verticalAlignment,
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

import {
	InnerBlocks,
	useBlockProps,
	InspectorControls
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import './editor-style.scss';
import classNames from 'classnames';
import MarginBottomSelector from '../../../components/MarginBottomSelector';

export default function Edit({ attributes, setAttributes }) {
	const { gap, marginBottom } = attributes;

	const blockProps = useBlockProps({
		className: classNames(
			Object.values(gap),
			'gap-h-lmw-0',
			marginBottom,
			'has-ui-select-helper'
		)
	});

	const defaultTamplate = [];
	for (let i = 0; i < 2; i++) {
		defaultTamplate.push(['blocks/smart-layout-column']);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title='Gap' initialOpen={true}>
					<SelectControl
						label='Initial Gap'
						value={gap.initial}
						onChange={value => {
							setAttributes({ gap: { ...gap, initial: value } });
						}}
						options={[
							{ label: 'Initial', value: '' },
							{ label: '1', value: 'gap-1' },
							{ label: '2', value: 'gap-2' },
							{ label: '3', value: 'gap-3' },
							{ label: '4', value: 'gap-4' },
							{ label: '5', value: 'gap-5' },
							{ label: '6', value: 'gap-6' }
						]}
					/>
					<SelectControl
						label='Small Desktop Gap'
						value={gap.smallDesktop}
						onChange={value => {
							setAttributes({
								gap: { ...gap, smallDesktop: value }
							});
						}}
						options={[
							{ label: 'Initial', value: '' },
							{ label: '1', value: 'gap-sdw-1' },
							{ label: '2', value: 'gap-sdw-2' },
							{ label: '3', value: 'gap-sdw-3' },
							{ label: '4', value: 'gap-sdw-4' },
							{ label: '5', value: 'gap-sdw-5' },
							{ label: '6', value: 'gap-sdw-6' }
						]}
					/>
					<SelectControl
						label='Large Mobile Gap'
						value={gap.largeMobile}
						onChange={value => {
							setAttributes({
								gap: { ...gap, largeMobile: value }
							});
						}}
						options={[
							{ label: 'Initial', value: '' },
							{ label: '1', value: 'gap-lmw-1' },
							{ label: '2', value: 'gap-lmw-2' },
							{ label: '3', value: 'gap-lmw-3' },
							{ label: '4', value: 'gap-lmw-4' },
							{ label: '5', value: 'gap-lmw-5' },
							{ label: '6', value: 'gap-lmw-6' }
						]}
					/>
				</PanelBody>
			</InspectorControls>
			<MarginBottomSelector
				marginBottom={marginBottom}
				setMarginBottom={value =>
					setAttributes({ marginBottom: value })
				}
			/>
			<div {...blockProps}>
				<InnerBlocks
					template={defaultTamplate}
					allowedBlocks={['blocks/smart-layout-column']}
					orientation='horizontal'
				/>
			</div>
		</>
	);
}

const { createHigherOrderComponent } = wp.compose;
const withCustomClassName = createHigherOrderComponent(BlockListBlock => {
	return props => {
		if (props.name === 'blocks/grid-layout') {
			return <BlockListBlock {...props} className={'g-layout'} />;
		}

		return <BlockListBlock {...props} />;
	};
}, 'withClientIdClassName');

wp.hooks.addFilter(
	'editor.BlockListBlock',
	'my-plugin/add-grid-layout-classes',
	withCustomClassName
);

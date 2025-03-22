import {
	InnerBlocks,
	useBlockProps,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody,SelectControl } from '@wordpress/components';
import './editor-style.scss';
import classNames from 'classnames';
import MarginBottomSelector from '../../components/MarginBottomSelector';

export default function Edit({ attributes, setAttributes }) {
	const { 
		marginBottom,
		containerType
	 } = attributes;

	const blockProps = useBlockProps({
		className: classNames(marginBottom, 
			containerType,
			'has-ui-select-helper')
	});

	const defaultTamplate = [];
	for (let i = 0; i < 2; i++) {
		defaultTamplate.push(['blocks/smart-layout-column']);
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title='Container Type'>
					<SelectControl
						label='Container Type'
						value={containerType}
						onChange={value => {
							setAttributes({ containerType: value });
						}}
						options={[
							{ label: 'None', value: '' },
							{ label: 'Large', value: 'container-large' },
							{ label: 'Regular', value: 'container' },
							{ label: 'Slim', value: 'container-slim' }
						]}
					/>
				</PanelBody>
				<MarginBottomSelector
					marginBottom={marginBottom}
					setMarginBottom={value =>
						setAttributes({ marginBottom: value })
					}
				/>
			</InspectorControls>
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
			return <BlockListBlock {...props} className={'grid'} />;
		}

		return <BlockListBlock {...props} />;
	};
}, 'withClientIdClassName');

wp.hooks.addFilter(
	'editor.BlockListBlock',
	'my-plugin/add-grid-layout-classes',
	withCustomClassName
);

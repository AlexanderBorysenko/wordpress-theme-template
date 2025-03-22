import {
	InnerBlocks,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import './editor-style.scss';
import { PanelBody } from '@wordpress/components';
import { SelectControl } from '@wordpress/components';
import classNames from 'classnames';
import MarginBottomSelector from '../../components/MarginBottomSelector';
import RenderAppenderElement from '../../components/RenderAppenderElement';
import { select } from '@wordpress/data';

export default function Edit({
	attributes,
	setAttributes,
	isSelected,
	clientId
}) {
	const { containerType, marginBottom } = attributes;
	const blockProps = useBlockProps({
		className: classNames(
			containerType,
			marginBottom,
			'has-ui-select-helper'
		)
	});

	const blocks = select('core/block-editor').getBlocksByClientId(clientId);
	const innerCount =
		blocks && blocks.length > 0 ? blocks[0].innerBlocks.length : 0;

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
							{ label: 'Large', value: 'container-large' },
							{ label: 'Regular', value: 'container' },
							{ label: 'Slim', value: 'container-slim' }
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
			<div key='render' {...blockProps}>
				<InnerBlocks
					renderAppender={
						isSelected || innerCount < 1
							? () => RenderAppenderElement({ clientId })
							: false
					}
				/>
			</div>
		</>
	);
}

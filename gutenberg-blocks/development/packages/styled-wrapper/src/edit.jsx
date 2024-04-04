import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import classNames from 'classnames';
import { InspectorControls } from '@wordpress/block-editor';
import MarginBottomSelector from '../../../components/MarginBottomSelector';

import './editor-style.scss';
import { WaveIcon } from './WaveIcon';
import {
	PanelBody,
	SelectControl,
	CheckboxControl
} from '@wordpress/components';
import { backgroundColorsOptions } from '.';
import { colorOptions } from '.';

export default function Edit({ attributes, setAttributes, isSelected }) {
	const { backgroundColor, color, marginBottom, enableWave } = attributes;
	const blockProps = useBlockProps({
		className: classNames(
			'styled-wrapper',
			'pb-56 pb-sdw-32',
			backgroundColor,
			color,
			marginBottom
		)
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title='Background Color'>
					<SelectControl
						label='Background Color'
						value={backgroundColor}
						options={backgroundColorsOptions}
						onChange={backgroundColor =>
							setAttributes({ backgroundColor })
						}
					/>
				</PanelBody>
				<PanelBody title='Text Color'>
					<SelectControl
						label='Text Color'
						value={color}
						options={colorOptions}
						onChange={color => setAttributes({ color })}
					/>
				</PanelBody>
				<MarginBottomSelector
					marginBottom={marginBottom}
					setMarginBottom={marginBottom =>
						setAttributes({ marginBottom })
					}
				/>
				<PanelBody title='Enable Wave'>
					<CheckboxControl
						label='Enable Wave'
						checked={enableWave}
						onChange={enableWave => setAttributes({ enableWave })}
					/>
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{enableWave ? (
					<WaveIcon className='block mb-80 mb-sdw-56 w-100' />
				) : null}
				<InnerBlocks />
			</div>
		</>
	);
}

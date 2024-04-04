import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { WaveIcon } from './WaveIcon';
import classNames from 'classnames';

export default function Save({ attributes }) {
	const { backgroundColor, color, marginBottom, enableWave } = attributes;
	const blockProps = useBlockProps.save({
		className: classNames(
			'styled-wrapper',
			'pb-56 pb-sdw-32',
			backgroundColor,
			color,
			marginBottom
		)
	});

	return (
		<div {...blockProps}>
			{enableWave ? (
				<WaveIcon className='block mb-80 mb-sdw-56 w-100' />
			) : null}
			<InnerBlocks.Content />
		</div>
	);
}

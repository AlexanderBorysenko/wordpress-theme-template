import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

import classNames from 'classnames';
import {
	generateImageSizes,
	generateImageSrcSet
} from '../../utils/imageUtils';

export default function Save({ attributes }) {
	const { marginBottom, mainImage } = attributes;
	const blockProps = useBlockProps.save({
		className: classNames('simple-hero', marginBottom, {
			'_no-image': !mainImage.url
		})
	});

	return (
		<section {...blockProps}>
			<div className='simple-hero__main-container'>
				<InnerBlocks.Content />
				{mainImage && mainImage.sizes && (
					<div class='simple-hero__image-shadow'>
						<img
							src={mainImage.url}
							alt={mainImage.alt}
							width={mainImage.sizes.full.width}
							height={mainImage.sizes.full.height}
							srcSet={generateImageSrcSet(mainImage.sizes)}
							sizes={generateImageSizes(mainImage.sizes)}
							loading='eager'
						/>
					</div>
				)}
			</div>
			{mainImage && mainImage.sizes && (
				<div className='simple-hero__image-container'>
					<img
						src={mainImage.url}
						alt={mainImage.alt}
						width={mainImage.sizes.full.width}
						height={mainImage.sizes.full.height}
						srcSet={generateImageSrcSet(mainImage.sizes)}
						sizes={generateImageSizes(mainImage.sizes)}
						loading='eager'
					/>
				</div>
			)}
		</section>
	);
}

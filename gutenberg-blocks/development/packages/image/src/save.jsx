import classNames from 'classnames';
import {
	generateImageSizes,
	generateImageSrcSet
} from '../../../utils/imageUtils';

export default function save({ attributes }) {
	const {
		imageSrc,
		imageSizes,
		imageAlt,
		imageId,
		enableHeightResponsive,
		desktopHeight,
		tabletHeight,
		mobileHeight,
		uniqueID,
		maxWidth,
		imagePosition,
		roundedBorder
	} = attributes;

	const blockProps = {
		className: classNames('image-block', imagePosition, uniqueID, {
			'rounded-border': roundedBorder
		})
	};

	const srcset = generateImageSrcSet(imageSizes);
	const sizes = generateImageSizes(imageSizes);

	return (
		<>
			{Object.keys(sizes).length > 0 && (
				<picture {...blockProps}>
					<img
						src={imageSrc}
						alt={imageAlt}
						data-id={imageId}
						srcSet={srcset}
						sizes={sizes}
						loading='lazy'
						width={imageSizes.full.width}
						height={imageSizes.full.height}
						className='image-block__image'
					/>
				</picture>
			)}
			{enableHeightResponsive && (
				<style>
					{enableHeightResponsive &&
						`.${uniqueID} {height: ${desktopHeight};}
					@media (max-width: 1250px) {.${uniqueID}  {height: ${tabletHeight};}}
					@media (max-width: 700px) {.${uniqueID} {height: ${mobileHeight};}}
					`}
					{maxWidth &&
						`.${uniqueID} {max-width: ${maxWidth}!important;}
					`}
				</style>
			)}
		</>
	);
}

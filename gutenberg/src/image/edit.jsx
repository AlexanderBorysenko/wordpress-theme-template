import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl
} from '@wordpress/components';

import classNames from 'classnames';
import { useEffect } from '@wordpress/element';
import ImagePlaceholder from '../../components/ImagePlaceholder';
import ImageSelectControl from '../../components/ImageSelectControl';

export default function Edit({ attributes, setAttributes }) {
	const {
		imageSizes,
		imageAlt,
		imageSrc,
		imageId,
		enableHeightResponsive,
		desktopHeight,
		smallHeight,
		mobileHeight,
		uniqueID,
		maxWidth,
		imagePosition
	} = attributes;

	const blockProps = useBlockProps({
		className: classNames('image-block', uniqueID, imagePosition)
	});

	useEffect(() => {
		if (!uniqueID) {
			setAttributes({
				uniqueID: `image-block-${Math.floor(Math.random() * 100000)}`
			});
		}
	}, []);

	return (
		<>
			<InspectorControls>
				<PanelBody title='Before Image'>
					<ImageSelectControl
						label='Image'
						value={imageId}
						onChange={image => {
							image
								? setAttributes({
										imageId: image.id,
										imageAlt: image.alt,
										imageSrc: image.url,
										imageSizes: image.sizes
								  })
								: setAttributes({
										imageId: null,
										imageAlt: null,
										imageSrc: null,
										imageSizes: null
								  });
						}}
					/>
				</PanelBody>
				<PanelBody title='Style'>
					<SelectControl
						label='Image Position'
						value={imagePosition}
						options={[
							{ label: 'Contain', value: 'contain' },
							{ label: 'Cover', value: 'cover' }
						]}
						onChange={value =>
							setAttributes({ imagePosition: value })
						}
					/>
					<TextControl
						label='Max Width'
						value={maxWidth}
						onChange={value => setAttributes({ maxWidth: value })}
					/>
					<ToggleControl
						label='Enable Responsive'
						checked={enableHeightResponsive}
						onChange={value =>
							setAttributes({ enableHeightResponsive: value })
						}
					/>
					{enableHeightResponsive && (
						<>
							<TextControl
								label='Desktop Height 1250px+'
								value={desktopHeight}
								onChange={value =>
									setAttributes({
										desktopHeight: value
									})
								}
							/>
							<TextControl
								label='sw Height 700px-1250px'
								value={smallHeight}
								onChange={value =>
									setAttributes({
										smallHeight: value
									})
								}
							/>
							<TextControl
								label='Mobile Height 0px-700px'
								value={mobileHeight}
								onChange={value =>
									setAttributes({
										mobileHeight: value
									})
								}
							/>
						</>
					)}
				</PanelBody>
			</InspectorControls>
			<div {...blockProps}>
				{imageSrc ? (
					<img src={imageSrc} className='image-block__image' />
				) : (
					<ImagePlaceholder />
				)}
			</div>
			<style>
				{enableHeightResponsive &&
					`.${uniqueID} {height: ${desktopHeight};}
					@media (max-width: 1250px) {.${uniqueID}  {height: ${smallHeight};}}
					@media (max-width: 700px) {.${uniqueID} {height: ${mobileHeight};}}
					`}
				{maxWidth &&
					`.${uniqueID} {max-width: ${maxWidth}!important;}
					`}
			</style>
		</>
	);
}

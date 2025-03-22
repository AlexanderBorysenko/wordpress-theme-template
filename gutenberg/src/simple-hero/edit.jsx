import {
	InnerBlocks,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import ImageSelectControl from '../../components/ImageSelectControl';
import { PanelBody } from '@wordpress/components';
import MarginBottomSelector from '../../components/MarginBottomSelector';
import classNames from 'classnames';
import './editor-style.scss';

export default function Edit({ attributes, setAttributes }) {
	const { marginBottom, mainImage } = attributes;
	const blockProps = useBlockProps({
		className: classNames('simple-hero', marginBottom, {
			'_no-image': !mainImage.url
		})
	});

	return (
		<>
			<InspectorControls>
				<PanelBody title='Main Image'>
					<ImageSelectControl
						value={mainImage ? mainImage.id : null}
						onChange={mainImage =>
							setAttributes({
								mainImage: mainImage
									? {
											id: mainImage.id,
											alt: mainImage.alt,
											url: mainImage.url,
											sizes: mainImage.sizes
									  }
									: {}
							})
						}
					/>
				</PanelBody>
				<MarginBottomSelector
					marginBottom={marginBottom}
					setMarginBottom={marginBottom =>
						setAttributes({ marginBottom })
					}
				/>
			</InspectorControls>
			<section {...blockProps}>
				<div className='simple-hero__main-container'>
					<InnerBlocks
						templateLock={false}
						template={[
							[
								'blocks/configurable-text',
								{
									tagName: 'h1',
									fontSize: 'fs-4',
									lineHeight: 'lh-104',
									marginBottom: 'mb-1-5'
								}
							],
							[
								'blocks/configurable-text',
								{
									tagName: 'p',
									fontWeight: 'fw-400',
									fontSize: 'fs-17px'
								}
							]
						]}
					/>
					{mainImage && mainImage.url && (
						<div class='simple-hero__image-shadow'>
							<img src={mainImage.url} loading='eager' />
						</div>
					)}
				</div>
				{mainImage && mainImage.url && (
					<div className='simple-hero__image-container'>
						<img src={mainImage.url} loading='eager' />
					</div>
				)}
			</section>
		</>
	);
}

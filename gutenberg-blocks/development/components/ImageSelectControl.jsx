import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, Icon } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ImageThumbnail from './ImageThumbnail';

const ImageSelectControl = ({
	onChange,
	value = null,
	selectText = 'Choose Image'
}) => {
	const imageObject = useSelect(
		select => {
			const { getMedia } = select('core');
			return value ? getMedia(value) : null;
		},
		[value]
	);

	// const imageSizes = useSelect(select => {
	// 	return select(blockEditorStore).getSettings().imageSizes;
	// }, []);

	return (
		<>
			<div className='image-select-control'>
				<div className='image-preview image-container'>
					{value ? (
						<ImageThumbnail imageID={value} size='thumbnail' />
					) : (
						<svg
							width='64'
							height='64'
							viewBox='0 0 64 64'
							fill='none'
							className='image-placeholder'>
							<path
								d='M8.66656 62C4.98474 62 2 59.0152 2 55.3333V8.6667C2 4.9848 4.98474 2 8.66656 2H55.3321C59.014 2 61.9987 4.9848 61.9987 8.6667V55.3333C61.9987 59.0152 59.014 62 55.3321 62H8.66656ZM8.66656 62L45.3337 25.3333L62 41.9999M20.3316 25.3333C23.093 25.3333 25.3315 23.0947 25.3315 20.3333C25.3315 17.5718 23.093 15.3333 20.3316 15.3333C17.5703 15.3333 15.3317 17.5718 15.3317 20.3333C15.3317 23.0947 17.5703 25.3333 20.3316 25.3333Z'
								stroke='currentColor'
								strokeWidth='3'
								strokeLinecap='round'
								strokeLinejoin='round'
							/>
						</svg>
					)}
				</div>
				<div className='image-select-control__controls'>
					<Button
						onClick={() => onChange(null)}
						style={{
							display: 'block',
							width: '100%',
							marginTop: '3px'
						}}
						isDestructive
						icon={<Icon icon='trash' />}>
						Clear
					</Button>
					<MediaUploadCheck>
						<MediaUpload
							onSelect={image => {
								onChange(image);
							}}
							allowedTypes={['image', 'image/gif']}
							value={value}
							multiple={false}
							render={({ open }) => (
								<Button
									onClick={open}
									className='components-button is-primary'
									style={{
										display: 'block',
										width: '100%',
										marginTop: '5px'
									}}>
									{selectText}
									<Icon
										icon='format-gallery'
										style={{ $marginLeft: '10px' }}
									/>
								</Button>
							)}
						/>
					</MediaUploadCheck>
				</div>
			</div>
		</>
	);
};

export default ImageSelectControl;

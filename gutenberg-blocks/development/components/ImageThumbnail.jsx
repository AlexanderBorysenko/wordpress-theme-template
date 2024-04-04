import { Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';

const ImageThumbnail = ({ imageID, size = 'thumbnail' }) => {
	const image = useSelect(
		select => {
			const { getMedia } = select('core');
			return imageID ? getMedia(imageID) : null;
		},
		[imageID]
	);

	let imageUrl = '';
	if (image && image.media_details.sizes[size]) {
		imageUrl = image.media_details.sizes[size].source_url;
	} else if (image) {
		imageUrl = image.source_url;
	}

	return image ? (
		<img src={imageUrl} style={{ $width: '100%' }} alt={image.alt} />
	) : (
		<div
			className='loading'
			style={{
				background: 'gray',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center',
				minHeight: '150px',
				width: '100%',
				height: '100%'
			}}>
			<Spinner />
		</div>
	);
};

export default ImageThumbnail;

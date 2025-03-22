export const generateImageSrcSet = imageSizes => {
	const imageSizeValues = imageSizes ? Object.values(imageSizes) : [];

	return imageSizeValues.length
		? imageSizeValues
				.filter(size => size.width && size.url)
				.map(size => `${size.url} ${size.width}w`)
				.join(', ')
		: '';
};

export const generateImageSizes = imageSizes => {
	const imageSizeValues = imageSizes ? Object.values(imageSizes) : [];

	return imageSizeValues.length
		? imageSizeValues
				.filter(size => size.width)
				.map(size => `(max-width: ${size.width}px) ${size.width}px`)
				.join(', ')
		: '';
};

import sizeKeys from './sizes';

export const fontSizeOptions = sizeKeys.map(sizeKey => {
	return {
		label: sizeKey,
		value: `fs-${sizeKey}`
	};
});

export const lineHeightOptions = [
	{ label: 'Initial', value: '' },
	{ label: '104%', value: 'lh-104' },
	{ label: '122%', value: 'lh-122' },
	{ label: '133%', value: 'lh-133' }
];

export const fontWeightOptions = [
	{ label: 'Initial', value: '' },
	{ label: '700', value: 'fw-700' },
	{ label: '600', value: 'fw-600' },
	{ label: '500', value: 'fw-500' },
	{ label: '400', value: 'fw-400' }
];

export const textAlignmentOptions = [
	{ label: 'Initial', value: '' },
	{ label: 'Center', value: 'text-center' }
];

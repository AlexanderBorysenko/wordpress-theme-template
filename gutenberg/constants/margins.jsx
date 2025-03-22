import sizeKeys from './sizes';

const marginOptions =  sizeKeys.map(sizeKey => {
	return {
		label: sizeKey,
		value: `mb-${sizeKey}`
	};
});

marginOptions.unshift({
	label: 'Initial',
	value: ''
});

export {marginOptions};
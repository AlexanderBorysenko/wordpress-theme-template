import { SelectControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { marginOptions } from '../constants/margins';

const MarginBottomSelector = props => {
	const { marginBottom, setMarginBottom } = props;
	return (
		<>
			<InspectorControls>
				<PanelBody title='Margin Bottom' initialOpen={true}>
					<SelectControl
						label='Margin'
						value={marginBottom}
						onChange={marginBottom => setMarginBottom(marginBottom)}
						options={marginOptions}
					/>
				</PanelBody>
			</InspectorControls>
		</>
	);
};

export default MarginBottomSelector;

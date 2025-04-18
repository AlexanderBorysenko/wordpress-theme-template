import { Inserter } from '@wordpress/block-editor';

const RenderAppenderElement = ({ clientId }) => (
	<Inserter
		rootClientId={clientId}
		renderToggle={({ onToggle, disabled }) => (
			<button
				type='button'
				aria-haspopup='true'
				aria-expanded='false'
				className='components-button block-editor-button-block-appender'
				aria-label='Add block'
				onClick={onToggle}
				disabled={disabled}
				style={{
					display: 'flex',
					justifyContent: 'center',
					alignItems: 'center'
				}}>
				<svg
					xmlns='http://www.w3.org/2000/svg'
					viewBox='0 0 24 24'
					width='24'
					height='24'
					aria-hidden='true'
					focusable='false'>
					<path d='M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z'></path>
				</svg>
			</button>
		)}
		isAppender
	/>
);

export default RenderAppenderElement;

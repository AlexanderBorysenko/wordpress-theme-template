import { Button, Icon } from '@wordpress/components';
import { arrayMoveImmutable } from 'array-move';
import uniqueId from 'lodash/uniqueId';
import { SortableContainer, SortableElement } from 'react-sortable-hoc';

const listItemStyles = {
	marginBottom: '10px',
	border: '1px solid #ccc',
	padding: '4px',
	backgroundColor: '#fff'
};

const ListItem = ({ attributes, renderItem, value, onChange }) => {
	return (
		<div
			className='complex-field-item__inner'
			style={listItemStyles}
			data-drag-handle-area='true'>
			<div style={{ display: 'flex', marginBottom: '6px' }}>
				<div
					data-drag-handle-area='true'
					style={{
						cursor: 'grab',
						display: 'flex',
						alignItems: 'center',
						justifyContent: 'center',
						padding: '5px',
						flex: 1,
						backgroundColor: '#f5f5f5'
					}}>
					<Icon icon='sort' className='drag-handle-area' />
				</div>
				<Button
					onClick={() => {
						const newValue = value.filter(
							item => item.key !== attributes.key
						);
						onChange(newValue);
					}}
					isSecondary
					isDestructive>
					<Icon icon='trash' />
				</Button>
			</div>
			{renderItem(attributes)}
		</div>
	);
};
const SortableItem = SortableElement(ListItem);

const List = ({ value, onChange, renderItem }) => {
	return (
		<div className='complex-field-items'>
			{value.map((attributes, index) => {
				const itemKey = !attributes.key ? uniqueId() : attributes.key;
				attributes.key = itemKey;
				attributes.index = index;
				attributes.setItemAttributes = values => {
					onChange(
						value.map(item => {
							if (item.key === attributes.key) {
								return {
									...item,
									...values
								};
							}
							return item;
						})
					);
				};
				return (
					<SortableItem
						attributes={attributes}
						onChange={onChange}
						renderItem={renderItem}
						index={index}
						key={itemKey}
						value={value}
					/>
				);
			})}
		</div>
	);
};
const SortableList = SortableContainer(List);

export const ComplexField = ({
	value,
	onChange,
	renderItem,
	defaultItemAttributes
}) => {
	const onSortEnd = e => {
		const newValue = arrayMoveImmutable(value, e.oldIndex, e.newIndex);
		onChange(newValue);
	};

	return (
		<>
			<div>
				<SortableList
					value={value}
					onChange={onChange}
					renderItem={renderItem}
					onSortEnd={onSortEnd}
					lockAxis='y'
					lockToContainerEdges={true}
					lockOffset={['0%', '100%']}
					shouldCancelStart={e => {
						return !e.target.dataset.dragHandleArea;
					}}
				/>
			</div>

			<Button
				onClick={() => {
					const newValue = [
						...value,
						{
							key: uniqueId(),
							...defaultItemAttributes
						}
					];
					onChange(newValue);
				}}
				className='components-button is-primary'
				style={{
					display: 'block',
					width: '100%',
					marginTop: '20px'
				}}>
				<Icon icon='plus' />
			</Button>
		</>
	);
};

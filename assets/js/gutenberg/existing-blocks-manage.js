function addColumnAttributes(settings, name) {
	if (typeof settings.attributes !== 'undefined') {
		if (name == 'core/column') {
			settings.attributes = Object.assign(settings.attributes, {
				additional_styles: {
					type: 'string',
				}
			});
		}
	}
	return settings;
}
 
wp.hooks.addFilter(
	'blocks.registerBlockType',
	'trf/column-custom-attribute',
	addColumnAttributes
);

const columnAdvancedControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { Fragment } = wp.element;
		const { TextareaControl } = wp.components;
		const { InspectorAdvancedControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
        if (isSelected && props.name == 'core/column') {
            
        }
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'core/column') && 
					<InspectorAdvancedControls>
                        <TextareaControl
                            help="Enter additional styles"
                            label="Additional Styles"
                            value={attributes?.additional_styles}
                            onChange={(value) => setAttributes({ additional_styles: value })}
                        />
					</InspectorAdvancedControls>
				}
			</Fragment>
		);
	};
}, 'columnAdvancedControls');
 
wp.hooks.addFilter(
	'editor.BlockEdit',
	'trf/column-advanced-control',
	columnAdvancedControls
);

function columnView(extraProps, blockType, attributes) {
	const { additional_styles } = attributes;
 
	if (typeof additional_styles !== 'undefined' && additional_styles !== '') {
		extraProps.data = JSON.stringify({ additional_styles: additional_styles });
	}
	return extraProps;
}
 
wp.hooks.addFilter(
	'blocks.getSaveContent.extraProps',
	'trf/column-view',
	columnView
);

function addColumnsAttributes(settings, name) {
	if (typeof settings.attributes !== 'undefined') {
		if (name == 'core/columns') {
			settings.attributes = Object.assign(settings.attributes, {
				additional_styles: {
					type: 'string',
				}
			});
		}
	}
	return settings;
}
 
wp.hooks.addFilter(
	'blocks.registerBlockType',
	'trf/columns-custom-attribute',
	addColumnsAttributes
);

const columnsAdvancedControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
	return (props) => {
		const { Fragment } = wp.element;
		const { TextareaControl } = wp.components;
		const { InspectorAdvancedControls } = wp.blockEditor;
		const { attributes, setAttributes, isSelected } = props;
		return (
			<Fragment>
				<BlockEdit {...props} />
				{isSelected && (props.name == 'core/columns') && 
					<InspectorAdvancedControls>
                        <TextareaControl
                            help="Enter additional styles"
                            label="Additional Styles"
                            value={attributes?.additional_styles}
                            onChange={(value) => setAttributes({ additional_styles: value })}
                        />
					</InspectorAdvancedControls>
				}
			</Fragment>
		);
	};
}, 'columnsAdvancedControls');
 
wp.hooks.addFilter(
	'editor.BlockEdit',
	'trf/columns-advanced-control',
	columnsAdvancedControls
);


function columnsView(extraProps, blockType, attributes) {
	const { additional_styles } = attributes;
 
	if (typeof additional_styles !== 'undefined' && additional_styles !== '') {
		extraProps.data = JSON.stringify({ additional_styles: additional_styles });
	}
	return extraProps;
}
 
wp.hooks.addFilter(
	'blocks.getSaveContent.extraProps',
	'trf/columns-view',
	columnsView
);
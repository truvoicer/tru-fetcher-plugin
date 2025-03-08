export class ColumnBlock {
	constructor() {
	}

	buildAtts() {
		function buildAtts(settings, name) {
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
			buildAtts
		);
	}

	buildControls() {
		const buildControls = wp.compose.createHigherOrderComponent((BlockEdit) => {
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
		}, 'buildControls');
		
		wp.hooks.addFilter(
			'editor.BlockEdit',
			'trf/column-advanced-control',
			buildControls
		);
	}
}
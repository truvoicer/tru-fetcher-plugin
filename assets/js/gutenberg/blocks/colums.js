export class ColumnsBlock {
	constructor() {
		wp.hooks.addFilter(
			'blocks.registerBlockType',
			'trf/columns-custom-attribute',
			this.buildAtts
		);
		wp.hooks.addFilter(
			'editor.BlockEdit',
			'trf/columns-advanced-control',
			this.buildControls
		);
	}

	buildAtts(settings, name) {
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

	buildControls() {
		wp.compose.createHigherOrderComponent((BlockEdit) => {
			return (props) => {
				const { Fragment } = wp.element;
				const { TextareaControl } = wp.components;
				const { InspectorAdvancedControls } = wp.blockEditor;
				const { attributes, setAttributes, isSelected } = props;
				if (isSelected && props.name == 'core/columns') {

				}
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
		}, 'buildControls');
	}
}
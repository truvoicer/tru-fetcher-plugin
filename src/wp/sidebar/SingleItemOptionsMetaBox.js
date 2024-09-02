import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch, useSelect} from '@wordpress/data';
import {PluginDocumentSettingPanel} from '@wordpress/editor';
import {PanelRow, TextareaControl, ToggleControl, SelectControl, TabPanel} from '@wordpress/components';

const POST_TYPES = ['ft_single_comparison'];
const SingleItemsOptionsMetaBox = ({config, post, categories,  postType, metaFields, setMetaFields}) => {
    if (!POST_TYPES.includes(postType)) return null;
    function buildOptions() {
        if (!categories) return [];
        return categories.map((category) => {
            return {
                value: category.id,
                label: category.name,
            }
        })
    }
    return (
        <PluginDocumentSettingPanel
            title={__('Post Options')}
            icon="book"
            initialOpen={true}
        >
            <PanelRow>
                <SelectControl
                    label={__("Post Template Category")}
                    value={metaFields.trf_post_options_post_options_post_template_category}
                    options={buildOptions()}
                    onChange={(value) => {
                        setMetaFields({trf_post_options_post_options_post_template_category: parseInt(value)})
                    }}
                    __nextHasNoMarginBottom
                />
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
}

const applyWithSelect = withSelect((select) => {
    return {
        metaFields: select('core/editor').getEditedPostAttribute('meta'),
        postType: select('core/editor').getCurrentPostType(),
        post: select('core/editor').getCurrentPost(),
        categories: select('core').getEntityRecords('taxonomy', 'category', {per_page: -1}),
    };
});

const applyWithDispatch = withDispatch((dispatch) => {
    return {
        setMetaFields(newValue) {
            dispatch('core/editor').editPost({meta: newValue})
        }
    }
});

export default compose([
    applyWithSelect,
    applyWithDispatch
])(SingleItemsOptionsMetaBox);

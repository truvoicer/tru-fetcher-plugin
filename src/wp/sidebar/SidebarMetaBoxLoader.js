import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {PanelRow, TextareaControl, ToggleControl, SelectControl, TabPanel} from '@wordpress/components';
import {useState} from '@wordpress/element';
import PostOptionsMetaBox from "./PostOptionsMetaBox";
import PageOptionsMetaBox from "./PageOptionsMetaBox";
import SingleItemOptionsMetaBox from "./SingleItemOptionsMetaBox";

const POST_TYPES = ['post'];
const SidebarMetaBoxLoader = (props) => {
    const metaFields = tru_fetcher_react?.meta?.metaFields || [];
    function getMetaFieldConfig() {
        if (!Array.isArray(metaFields)) {
            return false;
        }
        return metaFields.find((metaField) => {
            if (props.postType === 'post' && metaField?.name === 'post_options') {
                return true;
            }
            if (props.postType === 'page' && metaField?.name === 'page_options') {
                return true;
            }
            if (props.postType === 'trf_single_item' && metaField?.name === 'page_options') {
                return true;
            }
            if (props.postType === 'trf_category_tpl' && metaField?.name === 'page_options') {
                return true;
            }
            if (props.postType === 'trf_item_view_tpl' && metaField?.name === 'page_options') {
                return true;
            }
            if (props.postType === 'trf_post_tpl' && metaField?.name === 'page_options') {
                return true;
            }
            return false;
        });
    }
    function getMetaBoxComponent() {
        const metaFieldConfig = getMetaFieldConfig();
        if (!metaFieldConfig) {
            console.warn('No meta field config found');
            return null;
        }
        switch (props.postType) {
            case 'post':
                return <PostOptionsMetaBox {...props} config={metaFieldConfig} />;
            case 'page':
            case 'trf_item_view_tpl':
            case 'trf_post_tpl':
            case 'trf_category_tpl':
                return <PageOptionsMetaBox {...props} config={metaFieldConfig} />;
            case 'trf_single_item':
                return <SingleItemOptionsMetaBox {...props} config={metaFieldConfig} />;
            default:
                return null;
        }
    }
    return getMetaBoxComponent();
}

const applyWithSelect = withSelect((select) => {
    return {
        metaFields: select('core/editor').getEditedPostAttribute('meta'),
        postType: select('core/editor').getCurrentPostType()
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
])(SidebarMetaBoxLoader);

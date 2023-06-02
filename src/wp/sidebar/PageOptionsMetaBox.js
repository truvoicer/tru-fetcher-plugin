import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {PanelRow, TextareaControl, ToggleControl, SelectControl, TabPanel} from '@wordpress/components';
import {useState} from '@wordpress/element';
import { dispatch } from '@wordpress/data';
import {useEffect} from "../../../../../../wp-includes/js/dist/vendor/react";

const POST_TYPES = ['item_view_templates', 'page'];
const PageOptionsMetaBox = ({postType, metaFields, setMetaFields}) => {
    console.log({metaFields})
    const [tabName, setTabName] = useState('page_scripts');
    if (!POST_TYPES.includes(postType)) return null;
    return (
        <PluginDocumentSettingPanel
            title={__('Page Options')}
            icon="book"
            initialOpen={true}
        >
            <PanelRow>
                <SelectControl
                    label={__("Page Type")}
                    value={metaFields._meta_fields_page_options_page_type}
                    options={[
                        {value: 'general', label: 'General Page'},
                        {value: 'login', label: 'Login Page'},
                        {value: 'register', label: 'Register Page'},
                        {value: 'logout', label: 'Logout Page'},
                        {value: 'user_account', label: 'User Account Page'},
                    ]}
                    onChange={(value) => dispatch('core/editor').editPost({page_type: value})}
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            <PanelRow>
                <TabPanel
                    className="my-tab-panel"
                    activeClass="active-tab"
                    onSelect={(tabName) => {
                        setTabName(tabName);
                    }}
                    tabs={[
                        {
                            name: 'page_scripts',
                            title: 'Page Scripts',
                            className: 'tab-page-scripts',
                        },
                    ]}
                >
                    {(tab) => {
                        return (
                            <>
                                {tab.name === 'page_scripts' && (
                                    <>
                                        <PanelRow>
                                            <ToggleControl
                                                label="Header Scripts Override"
                                                checked={metaFields._meta_fields_page_options_header_override}
                                                onChange={() => {
                                                    setMetaFields({_meta_fields_page_options_header_override: !metaFields._meta_fields_page_options_header_override})
                                                }}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <TextareaControl
                                                label="Header Scripts"
                                                value={metaFields._meta_fields_page_options_header_scripts}
                                                onChange={(value) => setMetaFields({_meta_fields_page_options_header_scripts: !metaFields._meta_fields_page_options_header_scripts})}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <ToggleControl
                                                label="Footer Scripts Override"
                                                checked={metaFields._meta_fields_page_options_footer_override}
                                                onChange={() => {
                                                    setMetaFields({_meta_fields_page_options_footer_override: !metaFields._meta_fields_page_options_footer_override})
                                                }}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <TextareaControl
                                                label="Footer Scripts"
                                                value={metaFields._meta_fields_page_options_footer_scripts}
                                                onChange={(value) => setMetaFields({_meta_fields_page_options_footer_scripts: !metaFields._meta_fields_page_options_footer_scripts})}
                                            />
                                        </PanelRow>
                                    </>
                                )}
                            </>
                        )
                    }}
                </TabPanel>
            </PanelRow>
        </PluginDocumentSettingPanel>
    );
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
])(PageOptionsMetaBox);

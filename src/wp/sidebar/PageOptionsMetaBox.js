import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {PanelRow, TextareaControl, ToggleControl, SelectControl, TabPanel} from '@wordpress/components';
import {useState} from '@wordpress/element';

const POST_TYPES = ['item_view_templates', 'page'];
const PageOptionsMetaBox = ({config, postType, sidebars, metaFields, setMetaFields}) => {
    function buildSidebarSelectOptions() {
        const sidebarSelectOptions = [
            {
                disabled: true,
                label: 'Select an Option',
                value: ''
            },
        ];
        sidebars?.map(sidebar => {
            sidebarSelectOptions.push({
                label: sidebar?.name,
                value: sidebar?.id
            })
        });
        return sidebarSelectOptions;
    }

    return (
        <PluginDocumentSettingPanel
            title={__('Page Options')}
            icon="book"
            initialOpen={true}
        >
            <PanelRow>
                <SelectControl
                    label={__("Page Type")}
                    value={metaFields.trf_gut_pmf_page_options_page_type}
                    options={[
                        {value: 'general', label: 'General Page'},
                        {value: 'login', label: 'Login Page'},
                        {value: 'register', label: 'Register Page'},
                        {value: 'logout', label: 'Logout Page'},
                        {value: 'user_account', label: 'User Account Page'},
                    ]}
                    onChange={(value) => setMetaFields({trf_gut_pmf_page_options_page_type: value})}
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            <PanelRow>
                <SelectControl
                    label={__("Layout")}
                    value={metaFields.trf_gut_pmf_page_options_layout}
                    options={[
                        {value: 'full-width', label: 'Full Width'},
                        {value: 'sidebar', label: 'Sidebar'},
                    ]}
                    onChange={(value) => setMetaFields({trf_gut_pmf_page_options_layout: value})}
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            {metaFields.trf_gut_pmf_page_options_layout === 'sidebar' && (
                <PanelRow>
                    <SelectControl
                        label="Select Sidebar"
                        onChange={(value) => setMetaFields({trf_gut_pmf_page_options_sidebar: value})}
                        value={metaFields.trf_gut_pmf_page_options_sidebar}
                        options={buildSidebarSelectOptions()}
                    />
                </PanelRow>
            )}
            <PanelRow>
                <TabPanel
                    className="my-tab-panel"
                    activeClass="active-tab"
                    onSelect={(tabName) => {
                        // setTabName(tabName);
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
                                                checked={metaFields.trf_gut_pmf_page_options_header_override}
                                                onChange={() => {
                                                    setMetaFields({trf_gut_pmf_page_options_header_override: !metaFields.trf_gut_pmf_page_options_header_override})
                                                }}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <TextareaControl
                                                label="Header Scripts"
                                                value={metaFields.trf_gut_pmf_page_options_header_scripts}
                                                onChange={(value) => setMetaFields({trf_gut_pmf_page_options_header_scripts: value})}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <ToggleControl
                                                label="Footer Scripts Override"
                                                checked={metaFields.trf_gut_pmf_page_options_footer_override}
                                                onChange={() => {
                                                    setMetaFields({trf_gut_pmf_page_options_footer_override: !metaFields.trf_gut_pmf_page_options_footer_override})
                                                }}
                                            />
                                        </PanelRow>
                                        <PanelRow>
                                            <TextareaControl
                                                label="Footer Scripts"
                                                value={metaFields.trf_gut_pmf_page_options_footer_scripts}
                                                onChange={(value) => setMetaFields({trf_gut_pmf_page_options_footer_scripts: value})}
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
        postType: select('core/editor').getCurrentPostType(),
        sidebars: select('core').getSidebars()
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

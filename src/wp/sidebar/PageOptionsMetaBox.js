import {__} from '@wordpress/i18n';
import {compose} from '@wordpress/compose';
import {withSelect, withDispatch} from '@wordpress/data';
import {PluginDocumentSettingPanel} from '@wordpress/edit-post';
import {
    Panel,
    PanelBody,
    PanelRow,
    TextareaControl,
    ToggleControl,
    SelectControl,
    TabPanel
} from '@wordpress/components';

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

    function updateSidebar({key, value}, index) {
        const newSidebars = [...metaFields.trf_gut_pmf_page_options_sidebars];
        newSidebars[index][key] = value;
        setMetaFields({
            trf_gut_pmf_page_options_sidebar: newSidebars,
            trf_gut_pmf_page_options_sidebar_updated: (metaFields?.trf_gut_pmf_page_options_sidebar_updated || 0) + 1
        });
    }

    function addSidebar() {
        const newSidebars = [...metaFields.trf_gut_pmf_page_options_sidebars];
        newSidebars.push({name: '', position: 'left'});
        setMetaFields({trf_gut_pmf_page_options_sidebars: newSidebars});
    }

    function removeSidebar(index) {
        const newSidebars = [...metaFields.trf_gut_pmf_page_options_sidebars];
        newSidebars.splice(index, 1);
        setMetaFields({trf_gut_pmf_page_options_sidebars: newSidebars});
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
                    ]}
                    onChange={(value) => setMetaFields({trf_gut_pmf_page_options_page_type: value})}
                    __nextHasNoMarginBottom
                />
            </PanelRow>
            <PanelRow>
                <SelectControl
                    label={__("Access Control")}
                    value={metaFields.trf_gut_pmf_page_options_access_control}
                    options={[
                        {value: 'public', label: 'Public'},
                        {value: 'protected', label: 'Protected'},
                    ]}
                    onChange={(value) => setMetaFields({trf_gut_pmf_page_options_access_control: value})}
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
                <div style={{'margin-top': 10}}>
                    <Panel style={{'margin-top': 10}}>
                        <PanelBody title="Sidebar" initialOpen={false}>
                            <>
                                {Array.isArray(metaFields.trf_gut_pmf_page_options_sidebars) && metaFields.trf_gut_pmf_page_options_sidebars.map((sidebar, index) => {
                                    return (
                                        <Panel key={index}>
                                            <PanelBody
                                                title={`${index + 1}. ${sidebar?.name || 'sidebar'} (${sidebar?.position || ''})`}
                                                initialOpen={false}>
                                                <PanelRow>
                                                    <SelectControl
                                                        label="Select Sidebar"
                                                        onChange={(value) => updateSidebar({
                                                            key: 'name',
                                                            value: value
                                                        }, index)}
                                                        value={sidebar.name}
                                                        options={buildSidebarSelectOptions()}
                                                    />
                                                </PanelRow>
                                                <PanelRow>
                                                    <SelectControl
                                                        label="Sidebar position"
                                                        onChange={(value) => updateSidebar({
                                                            key: 'position',
                                                            value: value
                                                        }, index)}
                                                        value={sidebar.position}
                                                        options={[
                                                            {value: 'left', label: 'Left'},
                                                            {value: 'right', label: 'Right'},
                                                            {value: 'top', label: 'Top'},
                                                            {value: 'bottom', label: 'Bottom'},
                                                        ]}
                                                    />
                                                </PanelRow>
                                                <PanelRow>

                                                    <button className="button button-secondary" onClick={e => removeSidebar(index)}>
                                                        Delete
                                                    </button>
                                                </PanelRow>
                                            </PanelBody>
                                        </Panel>
                                    )
                                })}
                                <PanelRow>
                                    <button className="button button-secondary" onClick={addSidebar}>Add Sidebar
                                    </button>
                                </PanelRow>
                            </>
                        </PanelBody>
                    </Panel>
                </div>
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
    const itemViewSidebar = select('core').getSidebar('item-view-sidebar');
    const sidebars = select('core').getSidebars();
    let sideBarData = [];
    if (Array.isArray(sidebars) && itemViewSidebar) {
        sideBarData = [...sidebars, itemViewSidebar];
    }
    return {
        metaFields: select('core/editor').getEditedPostAttribute('meta'),
        postType: select('core/editor').getCurrentPostType(),
        sidebars: sideBarData
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

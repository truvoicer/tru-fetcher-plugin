import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import OptInInfoTab from "./tabs/OptInInfoTab";
import FormComponent from "../components/form/FormComponent";
import Carousel from "../components/carousel/Carousel";

const OptInBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
            {
                name: 'optin_info',
                title: 'Opt In Info',
                component: OptInInfoTab
            },
        ];
        if (attributes?.optin_type === 'form') {
            FormComponent.defaultProps = {
                data: attributes,
                onChange: formChangeHandler
            };
            tabConfig.push({
                name: 'form',
                title: 'Form',
                component: FormComponent
            });
        }
        if (attributes?.show_carousel) {
            Carousel.defaultProps = {
                data: attributes,
                onChange: formChangeHandler
            };
            tabConfig.push({
                name: 'carousel',
                title: 'Carousel',
                component: Carousel
            });
        }
        return tabConfig;
    }
    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }
    return (
        <Panel>
            <PanelBody title="Opt In Block" initialOpen={true}>
                <TabPanel
                    className="my-tab-panel"
                    activeClass="active-tab"
                    onSelect={(tabName) => {
                        // setTabName(tabName);
                    }}
                    tabs={
                        getTabConfig().map((tab) => {
                            return {
                                name: tab.name,
                                title: tab.title,
                            }
                        })
                    }>
                    {(tab) => {
                        return (
                            <>
                                {getTabConfig().map((item) => {
                                    if (item.name === tab.name) {
                                        return getTabComponent(item);
                                    }
                                    return null;
                                })}
                            </>
                        )

                    }}
                </TabPanel>
            </PanelBody>
        </Panel>
    );
};

export default OptInBlockEdit;

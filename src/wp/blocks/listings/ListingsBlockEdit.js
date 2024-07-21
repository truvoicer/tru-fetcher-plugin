import React from 'react';
import {useState, useEffect} from "@wordpress/element";
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

import GeneralTab from "./tabs/GeneralTab";
import DisplayTab from "./tabs/DisplayTab";
import ApiTab from "./tabs/ApiTab";
import WordpressDataTab from "./tabs/WordpressDataTab";
import SearchTab from "./tabs/SearchTab";
import CustomItemsTab from "./tabs/CustomItemsTab";
import SidebarTab from "./tabs/SidebarTab";
import GlobalOptionsTabConfig from "../components/global/tabs/GlobalOptionsTabConfig";
import {StateMiddleware} from "../../../library/api/StateMiddleware";
import ProviderRequestContext, {providerRequestData} from "../components/list/ProviderRequestContext";
import fetcherApiConfig from "../../../library/api/fetcher-api/fetcherApiConfig";

const ListingsBlockEdit = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);

    function updateProviderRequestData(updateData) {
        setProviderRequestState(prevState => {
            let cloneState = {...prevState};
            Object.keys(updateData).forEach((key) => {
                cloneState[key] = updateData[key];
            });
            return cloneState;
        })
    }

    const [providerRequestState, setProviderRequestState] = useState({
        ...providerRequestData,
        update: updateProviderRequestData
    });

    async function serviceListRequest() {
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/list`,
        });
        if (Array.isArray(results?.data?.data?.services)) {
            updateProviderRequestData({services: results.data.data.services})
        }
    }

    async function providerListRequest(serviceName) {
        if (!serviceName) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${serviceName}/providers`,
        });
        if (Array.isArray(results?.data?.data?.providers)) {
            updateProviderRequestData({providers: results.data.data.providers})
        }
    }

    useEffect(() => {
        providerListRequest(providerRequestState.selectedService);
    }, [providerRequestState.selectedService]);

    useEffect(() => {
        providerListRequest(attributes?.api_listings_service);
    }, [attributes?.api_listings_service]);

    useEffect(() => {
        serviceListRequest();
    }, []);

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
    }

    function getTabConfig() {
        let tabConfig = [];
        tabConfig.push({
            name: 'general',
            title: 'General',
            component: GeneralTab
        });
        if (props.attributes?.source === 'wordpress') {
            tabConfig.push({
                name: 'wordpress_settings',
                title: 'Wordpress Settings',
                component: WordpressDataTab
            });
        }
        tabConfig.push({
            name: 'display',
            title: 'Display',
            component: DisplayTab
        });
        tabConfig.push({
            name: 'sidebar',
            title: 'Sidebar',
            component: SidebarTab
        });
        if (props.attributes?.source === 'api') {
            tabConfig.push({
                name: 'api_settings',
                title: 'Api Settings',
                component: ApiTab
            });
        }
        tabConfig.push({
            name: 'search',
            title: 'Search',
            component: SearchTab
        });
        tabConfig.push({
            name: 'custom_items',
            title: 'Custom Items',
            component: CustomItemsTab
        });
        tabConfig = [...tabConfig, ...GlobalOptionsTabConfig];
        return tabConfig;
    }
    function getContainerProps() {
        if (props?.source === 'api') {
            return {
                className: 'listings-block-container listings-block-container-api'
            }
        }
        return useBlockProps();
    }

    return (
        <div {...getContainerProps()}>
            <ProviderRequestContext.Provider value={providerRequestState}>
            <Panel>
                <PanelBody title="Listings Block" initialOpen={true}>
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
            </ProviderRequestContext.Provider>
        </div>
    );
};

export default ListingsBlockEdit;

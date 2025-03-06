import React from 'react';
import {useState, useEffect} from "@wordpress/element";
import {TabPanel, Panel, PanelBody, PanelRow} from "@wordpress/components";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

import {isNotEmpty} from "../../../library/helpers/utils-helpers";
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
import { InspectorControls } from '@wordpress/block-editor';
import BlockView from '../common/BlockView';

import {findTaxonomyIdIdentifier, findTaxonomySelectOptions} from "../../helpers/wp-helpers";


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

    async function dataKeysRequest(selectedService) {
        if (!isNotEmpty(selectedService)) {
            return;
        }
        const results = await stateMiddleware.fetchRequest({
            config: fetcherApiConfig,
            endpoint: `${fetcherApiConfig.endpoints.service}/${selectedService}/response-key/list`,
            params: {
                pagination: false,
            }
        });
        if (Array.isArray(results?.data?.data?.service_response_keys)) {
            updateProviderRequestData({responseKeys: results.data.data.service_response_keys})
        }
    }
    function onServiceChange(serviceName) {
        providerListRequest(serviceName);
        dataKeysRequest(
            providerRequestState.services.find((service) => service?.name === serviceName)?.id
        );
    }
    useEffect(() => {
        onServiceChange(providerRequestState.selectedService);

    }, [providerRequestState.selectedService]);

    useEffect(() => {
        onServiceChange(attributes?.api_listings_service);
    }, [providerRequestState.services]);

    useEffect(() => {
        onServiceChange(attributes?.api_listings_service);
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

    const listingsCategoryId = findTaxonomyIdIdentifier('trf_listings_category');
    const listingsCategoryies = findTaxonomySelectOptions('trf_listings_category');
    
    function buildViewConfig() {
        let viewConfig = [];
        let listingsBlockChildren = [];
        let listingsBlockConfig = {
            open: true,
            title: 'Listings Block',
            children: []
        };
        listingsBlockChildren.push({name: 'Id', key: 'listing_block_id'});
        listingsBlockChildren.push({name: 'Primary listing', key: 'primary_listing'});
        listingsBlockChildren.push({name: 'Api source', key: 'source'});
        listingsBlockChildren.push({name: 'Listings Category', key: () => {
            if (!attributes?.[listingsCategoryId]) {
                return '';
            }
            return listingsCategoryies.find(category => category.value === parseInt(attributes[listingsCategoryId]))?.label || 'Error';
        }});
        listingsBlockChildren.push({name: 'Display As', key: 'display_as'});
        listingsBlockChildren.push({name: 'Template', key: 'template'});
        listingsBlockChildren.push({name: 'Posts Per Page', key: 'posts_per_page'});
        
        if (props.attributes?.source === 'api') {
            listingsBlockChildren.push({name: 'Api Fetch Type', key: 'api_fetch_type'});
            listingsBlockChildren.push({name: 'Api Listings Service', key: 'api_listings_service'});
        }
        listingsBlockConfig.children = listingsBlockChildren;
        viewConfig.push(listingsBlockConfig);
        return viewConfig;
    }
    return (
        <div {...getContainerProps()}>

            <InspectorControls key="setting">
                <ProviderRequestContext.Provider value={providerRequestState}>
                    <Panel>
                        <PanelBody title="Listings Block" initialOpen={true}>
                            <TabPanel
                                orientation="vertical"
                                className="tab-panel--collapse"
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
            </InspectorControls>
            <BlockView
                {...props}
                viewConfig={buildViewConfig()} />
        </div>
    );
};

export default ListingsBlockEdit;

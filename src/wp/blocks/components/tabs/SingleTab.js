import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import Carousel from "../carousel/Carousel";
import TextContent from "../text-content/TextContent";
import FormComponent from "../form/FormComponent";
import {getBlockAttributesById} from "../../../helpers/wp-helpers";
import {isObject, isObjectEmpty} from "../../../../library/helpers/utils-helpers";

const SingleTab = (props) => {

    const {
        data = [],
        onChange
    } = props;
    function formChangeHandler({key, value}) {
        const cloneTabs = [...data];
        if (typeof onChange === 'function') {
            onChange({key, value});
        }
    }
    function getTabProps(block_id) {
        let dataProps = {};
        const blockAtts =  getBlockAttributesById(block_id);
        if (isObject(blockAtts) && !isObjectEmpty(blockAtts)) {
            dataProps = {...dataProps, ...blockAtts};
        }
        if (typeof data[block_id] === 'object') {
            dataProps = {...dataProps, ...data[block_id]};
        }
        console.log({dataProps})
        return dataProps;
    }
    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
        ];
        if (data?.custom_tabs_type === 'custom_carousel') {
            tabConfig.push({
                name: 'custom_carousel',
                title: 'Custom Carousel',
                component: Carousel
            });
        }
        if (data?.custom_tabs_type === 'custom_content') {
            tabConfig.push({
                name: 'custom_content',
                title: 'Custom Content',
                component: TextContent
            });
        }
        if (data?.custom_tabs_type === 'form') {
            tabConfig.push({
                name: 'form',
                title: 'Form',
                component: FormComponent
            });
        }
        return tabConfig;
    }

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let componentProps = {
            onChange: formChangeHandler
        };
        switch (tab.name) {
            case 'custom_carousel':
                componentProps.data = getTabProps('carousel_block');
                break;
            case 'custom_content':
                componentProps.data = data?.custom_content;
                break;
            case 'form':
                componentProps.data = getTabProps('form_block');
                break;
            default:
                componentProps.data = data;
                break;
        }
        let TabComponent = tab.component;
        return <TabComponent {...componentProps} />;
    }
console.log({data})
    return (
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
    );
};

export default SingleTab;

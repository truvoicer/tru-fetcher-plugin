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
    function formChangeHandler({key, value, index}) {
        // const cloneTabs = [...data];
        // if (typeof onChange === 'function') {
        //     onChange({key: 'tabs', value: cloneTabs});
        // }
    }
    function getTabProps(block_id) {
        let dataProps = {};
        const blockAtts =  getBlockAttributesById(block_id);
        console.log({blockAtts})
        if (isObject(blockAtts) && !isObjectEmpty(blockAtts)) {
            dataProps = {...dataProps, ...blockAtts};
        }
        if (typeof data[block_id] === 'object') {
            dataProps = {...dataProps, ...data[block_id]};
        }
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
            Carousel.defaultProps = {
                data: getTabProps('carousel_block'),
                onChange: formChangeHandler
            }
            tabConfig.push({
                name: 'custom_carousel',
                title: 'Custom Carousel',
                component: Carousel
            });
        }
        if (data?.custom_tabs_type === 'custom_content') {
            TextContent.defaultProps = {
                data: data?.custom_content,
                onChange: formChangeHandler
            }
            tabConfig.push({
                name: 'custom_content',
                title: 'Custom Content',
                component: TextContent
            });
        }
        if (data?.custom_tabs_type === 'form') {
            FormComponent.defaultProps = {
                data: data?.form,
                onChange: formChangeHandler
            }
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
        let TabComponent = tab.component;
        return <TabComponent {...props} />;
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

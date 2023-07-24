import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import GeneralTab from "./tabs/GeneralTab";
import Carousel from "../carousel/Carousel";
import TextContent from "../text-content/TextContent";
import FormComponent from "../form/FormComponent";
import {getBlockAttributesById} from "../../../helpers/wp-helpers";
import {isObject, isObjectEmpty} from "../../../../library/helpers/utils-helpers";

const SingleTab = (props) => {

    const {
        data,
        onChange,
        index,
        moveUp,
        moveDown,
        deleteTab,
    } = props;


    function formChangeHandler({key, value, blockId}) {
        if (blockId) {
            let blockAttributes = getBlockAttributesById(blockId);
            let dataBlockAttributes = data[blockId];
            if (!isObject(blockAttributes)) {
                blockAttributes = {};
            }
            if (!isObject(dataBlockAttributes)) {
                dataBlockAttributes = {};
            }
            const newAttributes = {
                ...{...blockAttributes, ...dataBlockAttributes},
                [key]: value
            }
            if (typeof onChange === 'function') {
                onChange({key: blockId, value: newAttributes});
            }
        } else {
            if (typeof onChange === 'function') {
                onChange({key, value});
            }
        }
    }

    function getTabProps(block_id) {
        let dataProps = {};
        const blockAtts = getBlockAttributesById(block_id);
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
                componentProps.onChange = ({key, value}) => {
                    formChangeHandler({key, value, blockId: 'carousel_block'});
                }
                break;
            case 'form':
                componentProps.data = getTabProps('form_block');
                componentProps.onChange = ({key, value}) => {
                    formChangeHandler({key, value, blockId: 'form_block'});
                }
                break;
            case 'custom_content':
            default:
                componentProps.data = data;
                break;
        }
        let TabComponent = tab.component;
        return <TabComponent {...componentProps} />;
    }

    return (
        <div className="tf--list--item tf--list--item--no-header">
            <div className="tf--list--item--content">
                <Panel>
                    <PanelBody title={`Tab (${index})`} initialOpen={true}>
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
            </div>
            <div className={'tf--list--item--actions'}>
                <a onClick={() => {
                    moveUp()
                }}>
                    <Icon icon={chevronUp}/>
                </a>
                <a onClick={() => {
                    moveDown()
                }}>
                    <Icon icon={chevronDown}/>
                </a>
                <a onClick={(e) => {
                    e.preventDefault()
                    deleteTab();
                }}>
                    <Icon icon={trash}/>
                </a>
            </div>
        </div>
    );
};

export default SingleTab;

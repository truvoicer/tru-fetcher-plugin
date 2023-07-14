import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import {Icon, chevronDown, chevronUp, trash} from "@wordpress/icons";
import GeneralTab from "./tabs/GeneralTab";
import Carousel from "../carousel/Carousel";
import TextContent from "../text-content/TextContent";
import FormComponent from "../form/FormComponent";
import {getBlockAttributesById} from "../../../helpers/wp-helpers";
import {isObject, isObjectEmpty} from "../../../../library/helpers/utils-helpers";
import SourceTab from "./tabs/SourceTab";

const SingleFilter = (props) => {

    const {
        data,
        onChange,
        index,
        moveUp,
        moveDown,
        deleteTab,
    } = props;


    function formChangeHandler({key, value}) {
        if (typeof onChange === 'function') {
            onChange({key, value});
        }
    }

    function getTabConfig() {
        let tabConfig = [
            {
                name: 'general',
                title: 'General',
                component: GeneralTab
            },
        ];
        if (data?.type === 'list') {
            tabConfig.push({
                name: 'source',
                title: 'Source',
                component: SourceTab
            });
        }

        return tabConfig;
    }

    function getTabComponent(tab) {
        if (!tab?.component) {
            return null;
        }
        let componentProps = {
            onChange: formChangeHandler,
            data: data,
        };
        let TabComponent = tab.component;
        return <TabComponent {...componentProps} />;
    }

    return (
        <div className="tf--list--item tf--list--item--no-header">
            <div className="tf--list--item--content">
                <Panel>
                    <PanelBody title={`Filter (${index})`} initialOpen={true}>
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

export default SingleFilter;

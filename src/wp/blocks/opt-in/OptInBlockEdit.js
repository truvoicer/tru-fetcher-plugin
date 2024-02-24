import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import GeneralTab from "./tabs/GeneralTab";
import OptInInfoTab from "./tabs/OptInInfoTab";
import { useInnerBlocksProps, InnerBlocks, useBlockProps, store as blockEditorStore } from '@wordpress/block-editor';
import {getBlockAttributesById, getChildBlockIds, getChildBlockParams} from "../../helpers/wp-helpers";
import Carousel from "../components/carousel/Carousel";
import FormComponent from "../components/form/FormComponent";
import { useSelect, useDispatch } from '@wordpress/data';


const OptInBlockEdit = (props) => {
    const {attributes, setAttributes, clientId} = props;
    const blockProps = useBlockProps();

    function formChangeHandler({key, value, blockId}) {
        if (blockId) {
            let blockAttributes = attributes[blockId] || getBlockAttributesById(blockId);
            if (typeof blockAttributes === 'undefined' || !blockAttributes) {
                blockAttributes = {};
            }
            const newAttributes = {
                ...blockAttributes,
                [key]: value
            }
            setAttributes({
                ...attributes,
                [blockId]: newAttributes
            });
        } else {
            setAttributes({
                ...attributes,
                [key]: value
            });
        }
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
                data:  {...getBlockAttributesById('form_block'), ...attributes?.form_block},
                onChange: ({key, value}) => {
                    formChangeHandler({key, value, blockId: 'form_block'});
                }
            };
            tabConfig.push({
                name: 'form',
                title: 'Form',
                component: FormComponent
            });
        }
        if (attributes?.show_carousel) {
            Carousel.defaultProps = {
                data: {...getBlockAttributesById('carousel_block'), ...attributes?.carousel_block},
                onChange: ({key, value}) => {
                    formChangeHandler({key, value, blockId: 'carousel_block'});
                }
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
        <div { ...useBlockProps() }>
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
        </div>
    );
};

export default OptInBlockEdit;

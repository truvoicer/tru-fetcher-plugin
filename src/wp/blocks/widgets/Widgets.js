import React from 'react';
import {DropdownMenu, Panel, PanelBody} from "@wordpress/components";
import UserStats from "../components/user-stats/UserStats";
import UserSocial from "../components/user-social/UserSocial";
import UserProfile from "../components/user-profile/UserProfile";
import FormProgress from "../components/form-progress/FormProgress";
import {
    plusCircleFilled,
    more,
    Icon, chevronDown, chevronUp, trash
} from '@wordpress/icons';
import widgetConfig from "../configs/widget-config";
import {getBlockAttributesById} from "../../helpers/wp-helpers";
import {isObjectEmpty, isObject} from "../../../library/helpers/utils-helpers";
import Tabs from "../components/tabs/Tabs";

const Widgets = (props) => {
    const {
        data = [],
        onChange,
    } = props;

    function formChangeHandler({key, value, widget, index}) {
        const cloneWidgets = [...data];
        const findWidgetIndex = cloneWidgets?.findIndex((storedWidget, i) => i === index && storedWidget?.id === widget?.id);

        if (findWidgetIndex === -1) {
            return;
        }
        cloneWidgets[findWidgetIndex][key] = value;
        if (typeof onChange === 'function') {
            onChange({widgets: cloneWidgets});
        }
    }

    function getWidgetProps(block_id, index) {
        let dataProps = {};
        const blockAtts = getBlockAttributesById(block_id);
        if (isObject(blockAtts) && !isObjectEmpty(blockAtts)) {
            dataProps = {...dataProps, ...blockAtts};
        }
        if (typeof data[index] === 'object') {
            dataProps = {...dataProps, ...data[index]};
        }
        return dataProps;
    }

    function getWidgetComponent(widget, index) {
        let Component;
        let block_id;
        switch (widget?.id) {
            case 'user-stats':
                Component = UserStats;
                block_id = 'user_stats_widget_block';
                break;
            case 'user-social':
                Component = UserSocial;
                block_id = 'user_social_widget_block';
                break;
            case 'user-profile':
                Component = UserProfile;
                block_id = 'user_profile_widget_block';
                break;
            case 'form-progress':
                Component = FormProgress;
                block_id = 'form_progress_widget_block';
                break;
            case 'tab-block':
                Component = Tabs;
                block_id = 'tabs_block';
                break;
            default:
                return null;
        }
        const widgetData = getWidgetProps(block_id, index);
        Component.defaultProps = {
            attributes: widgetData,
            data: widgetData,
            onChange: ({key, value}) => {
                formChangeHandler({key, value, widget, index});
            },
            setAttributes: (dataObj) => {
                Object.keys(dataObj).forEach((key) => {
                    formChangeHandler({
                        key,
                        value: dataObj[key],
                        widget,
                        index
                    });
                })
            }
        }
        return <Component/>;
    }

    function insertWidget(widget) {
        if (typeof onChange === 'function') {
            onChange({
                widgets: [
                    ...data,
                    widget
                ]
            });
        }
    }

    function getDropDownControls() {
        return widgetConfig?.map((widget) => {
            return {
                title: widget?.title,
                icon: more,
                onClick: () => {
                    insertWidget(widget);
                }
            }
        });
    }

    function moveFormItem({index, widget, direction}) {
        let cloneWidgets = [...data];
        let newIndex = index + direction;
        if (newIndex < 0) {
            newIndex = 0;
        }
        if (newIndex > cloneWidgets.length - 1) {
            newIndex = cloneWidgets.length - 1;
        }
        cloneWidgets.splice(index, 1);
        cloneWidgets.splice(newIndex, 0, widget);
        if (typeof onChange === 'function') {
            onChange({widgets: cloneWidgets});
        }
    }

    function deleteWidget({index}) {
        let cloneWidgets = [...data];
        cloneWidgets.splice(index, 1);
        if (typeof onChange === 'function') {
            onChange({widgets: cloneWidgets});
        }
    }

    return (
        <div className={'tf--widgets'}>
            <div className={'tf--widgets--container'}>
                {Array.isArray(data) && data.map((widget, index) => {
                    return (
                        <div className="tf--list--item tf--list--item--no-header">
                            <div className="tf--list--item--content">
                                <Panel>
                                    <PanelBody title={widget?.title || ''} initialOpen={true}>
                                        {getWidgetComponent(widget, index)}
                                    </PanelBody>
                                </Panel>
                            </div>
                            <div className={'tf--list--item--actions'}>
                                <a onClick={() => {
                                    moveFormItem({index, widget, direction: -1})
                                }}>
                                    <Icon icon={chevronUp}/>
                                </a>
                                <a onClick={() => {
                                    moveFormItem({index, widget, direction: 1})
                                }}>
                                    <Icon icon={chevronDown}/>
                                </a>
                                <a onClick={(e) => {
                                    e.preventDefault()
                                    deleteWidget({index})
                                }}>
                                    <Icon icon={trash}/>
                                </a>
                            </div>
                        </div>
                    );
                })}
            </div>
            <DropdownMenu
                icon={plusCircleFilled}
                label="Select a widget"
                controls={getDropDownControls()}
            />
        </div>
    );
};

export default Widgets;

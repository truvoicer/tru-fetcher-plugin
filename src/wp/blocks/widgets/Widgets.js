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
import {getBlockAttributesById} from "../../helpers/wp-helpers";
import {isObjectEmpty, isObject} from "../../../library/helpers/utils-helpers";
import widgetConfig from "../configs/widget-config";

const Widgets = (props) => {
    const {
        data = [],
        onChange,
        childConfigs,
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
        const blockAtts = childConfigs[block_id]
        if (isObject(blockAtts) && !isObjectEmpty(blockAtts)) {
            dataProps = {...dataProps, ...blockAtts};
        }
        if (typeof data[index] === 'object') {
            dataProps = {...dataProps, ...data[index]};
        }
        return dataProps;
    }

    function getWidgetComponent(widget, index) {
        if (!widget?.id) {
            return null;
        }

        let Component = widgetConfig.find((config) => config.id === widget.id)?.component;
        if (!Component) {
            return null;
        }
        if (!childConfigs?.[widget.id]) {
            return null;
        }
        const widgetData = getWidgetProps(widget.id, index);
        Component.defaultProps = {
            data: widgetData,
            onChange: ({key, value}) => {
                formChangeHandler({key, value, widget, index});
            },
            attributes: widgetData,
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

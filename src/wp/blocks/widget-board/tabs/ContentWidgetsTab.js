import React from 'react';
import {DropdownMenu, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import UserStats from "../../components/user-stats/UserStats";
import UserSocial from "../../components/user-social/UserSocial";
import UserProfile from "../../components/user-profile/UserProfile";
import FormProgress from "../../components/form-progress/FormProgress";
import {
    plusCircleFilled,
    more,
    arrowLeft,
    arrowRight,
    arrowUp,
    arrowDown,
} from '@wordpress/icons';
import widgetConfig from "../../configs/widget-config";

const ContentWidgetsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    function formChangeHandler({key, value, widget}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }
    function getWidgetComponent(widget) {
        let Component;
        switch (widget?.id) {
            case 'user-stats':
                Component = UserStats;
                break;
            case 'user-social':
                Component = UserSocial;
                break;
            case 'user-profile':
                Component = UserProfile;
                break;
            case 'form-progress':
                Component = FormProgress;
                break;
            default:
                return null;
        }
        Component.defaultProps = {
            data: attributes,
            onChange: ({key, value}) => {
                formChangeHandler({key, value, widget});
            }
        }
        return <Component />;
    }
    function insertWidget(widget) {
        setAttributes({
            ...attributes,
            content_widgets: [
                ...attributes?.content_widgets,
                widget
            ]
        });
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
    return (
        <div>
            {attributes?.content_widgets?.map((widget, index) => {
                return (
                    <div>
                        {getWidgetComponent(widget)}
                    </div>
                );
            })}
            <DropdownMenu
                icon={ plusCircleFilled }
                label="Select a widget"
                controls={ getDropDownControls() }
            />
        </div>
    );
};

export default ContentWidgetsTab;

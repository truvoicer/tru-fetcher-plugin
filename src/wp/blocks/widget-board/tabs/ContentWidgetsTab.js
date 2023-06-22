import React from 'react';
import {TabPanel, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import UserStats from "../../components/user-stats/UserStats";
import UserSocial from "../../components/user-social/UserSocial";
import UserProfile from "../../components/user-profile/UserProfile";
import FormProgress from "../../components/form-progress/FormProgress";

const ContentWidgetsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    function formChangeHandler({key, value, component}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }
    function getWidgetComponent(widget) {
        let Component;
        switch (widget?.component) {
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
                formChangeHandler({key, value, component: widget?.component});
            }
        }
        return <Component />;
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
        </div>
    );
};

export default ContentWidgetsTab;

import React from 'react';
import {DropdownMenu, Panel, PanelBody, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import UserStats from "../../components/user-stats/UserStats";
import UserSocial from "../../components/user-social/UserSocial";
import UserProfile from "../../components/user-profile/UserProfile";
import FormProgress from "../../components/form-progress/FormProgress";
import {
    plusCircleFilled,
    more,
    Icon, chevronDown, chevronUp, trash
} from '@wordpress/icons';
import widgetConfig from "../../configs/widget-config";
import {getBlockAttributesById} from "../../../helpers/wp-helpers";
import Widgets from "../../widgets/Widgets";

const SidebarWidgetsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;

    function formChangeHandler({widgets}) {
        setAttributes({
            ...attributes,
            sidebar_widgets: widgets
        });
    }

    return (
        <Widgets
            data={attributes?.sidebar_widgets}
            onChange={formChangeHandler}
        />
    );
};

export default SidebarWidgetsTab;

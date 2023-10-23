import React from 'react';
import {ToggleControl} from "@wordpress/components";
import Sidebar from "../../common/Sidebar";

const SidebarTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    return (
        <>
            <Sidebar {...props}/>
        </>
    );
};

export default SidebarTab;

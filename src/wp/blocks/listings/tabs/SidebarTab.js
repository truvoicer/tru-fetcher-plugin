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
            <Sidebar {...props}>
                {attributes?.show_sidebar &&
                    <>
                        <ToggleControl
                            label="Show Sidebar widgets in listings sidebar"
                            checked={attributes?.show_sidebar_widgets_in_listings_sidebar}
                            onChange={(value) => {
                                setAttributes({show_sidebar_widgets_in_listings_sidebar: value});
                            }}
                        />
                    </>
                }
            </Sidebar>
        </>
    );
};

export default SidebarTab;

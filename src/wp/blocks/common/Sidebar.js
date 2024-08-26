import React from 'react';
import {TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import {useSelect} from "@wordpress/data";
import Grid from "../../../components/Grid";

const Sidebar = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    const {sidebars} = useSelect(
        (select) => {
            const core = select('core');
            return {
                sidebars: core.getSidebars()
            }
        }
    );

    function buildSidebarSelectOptions() {
        const sidebarSelectOptions = [
            {
                disabled: true,
                label: 'Select an Option',
                value: ''
            },
        ];
        sidebars?.map(sidebar => {
            sidebarSelectOptions.push({
                label: sidebar?.name,
                value: sidebar?.id
            })
        });
        return sidebarSelectOptions;
    }

    return (
        <div>
            <Grid columns={2}>
                <ToggleControl
                    label="Show Sidebar"
                    checked={attributes?.show_sidebar}
                    onChange={(value) => {
                        setAttributes({show_sidebar: value});
                    }}
                />
            </Grid>
            {props.children}
            {attributes?.show_sidebar &&
                <Grid columns={2}>
                    <SelectControl
                        multiple={true}
                        label="Select Sidebar"
                        onChange={(value) => {
                            setAttributes({select_sidebar: value});
                        }}
                        value={attributes?.select_sidebar}
                        options={buildSidebarSelectOptions()}
                    />
                </Grid>
            }
        </div>
    );
};

export default Sidebar;

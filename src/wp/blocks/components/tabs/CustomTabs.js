import React from 'react';
import {Button, PanelBody, TabPanel} from "@wordpress/components";
import SingleTab from "./single-tab/SingleTab";

const CustomTabs = (props) => {

    const {
        data = [],
        onChange,
        reducers = null,
    } = props;

    function addTab() {
        const cloneTabs = [...data];
        cloneTabs.push({
            default_active_tab: false,
            custom_tabs_type: 'custom_carousel',
            tab_id: '',
            tab_heading: '',
            carousel_block: null,
            content_block: null,
            form_block: null,
        });
        if (typeof onChange === 'function') {
            onChange({key: 'tabs', value: cloneTabs});
        }
    }

    function formChangeHandler({key, value, index}) {
        const cloneTabs = [...data];
        cloneTabs[index][key] = value;
        if (typeof onChange === 'function') {
            onChange({key: 'tabs', value: cloneTabs});
        }
    }
    function deleteTab({index}) {
        const cloneTabs = [...data];
        cloneTabs.splice(index, 1);
        if (typeof onChange === 'function') {
            onChange({key: 'tabs', value: cloneTabs});
        }
    }

    function moveTabItem({index, item, direction}) {
        let cloneTabs = [...data];
        let newIndex = index + direction;
        if (newIndex < 0) {
            newIndex = 0;
        }
        if (newIndex > cloneTabs.length - 1) {
            newIndex = cloneTabs.length - 1;
        }
        cloneTabs.splice(index, 1);
        cloneTabs.splice(newIndex, 0, item);
        if (typeof onChange === 'function') {
            onChange({key: 'tabs', value: cloneTabs});
        }
    }
    function getSingleTabComponent(item, index) {
        SingleTab.defaultProps = {
            index,
            data: item,
            moveUp: () => {
                moveTabItem({index, item, direction: -1});
            },
            moveDown: () => {
                moveTabItem({index, item, direction: 1});
            },
            onChange: ({key, value}) => {
                formChangeHandler({key, value, index});
            },
            deleteTab: () => {
                deleteTab({index});
            }
        }
        return <SingleTab reducers={reducers} />;
    }
    return (
       <div>
           {Array.isArray(data) && data.map((item, index) => {
               return getSingleTabComponent(item, index)
           })}

           <Button
               variant="primary"
               onClick={(e) => {
                   e.preventDefault()
                   addTab();
               }}
           >
               Add Tab
           </Button>
       </div>
    );
};

export default CustomTabs;

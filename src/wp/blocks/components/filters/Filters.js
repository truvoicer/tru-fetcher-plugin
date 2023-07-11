import React from 'react';
import {Button, PanelBody, TabPanel} from "@wordpress/components";
import SingleFilter from "./SingleFilter";
import {findPostTypeIdIdentifier} from "../../../helpers/wp-helpers";

const Filters = (props) => {

    const {
        data = [],
        onChange
    } = props;

    const filterListId = findPostTypeIdIdentifier('trf_filter_list')
    function addFilter() {
        const cloneTabs = [...data];
        cloneTabs.push({
            type: false,
            name: '',
            label: '',
            source: '',
            api_endpoint: null,
            [filterListId]: null,
        });
        if (typeof onChange === 'function') {
            onChange({key: 'filters', value: cloneTabs});
        }
    }

    function formChangeHandler({key, value, index}) {
        const cloneTabs = [...data];
        cloneTabs[index][key] = value;
        if (typeof onChange === 'function') {
            onChange({key: 'filters', value: cloneTabs});
        }
    }
    function deleteTab({index}) {
        const cloneTabs = [...data];
        cloneTabs.splice(index, 1);
        if (typeof onChange === 'function') {
            onChange({key: 'filters', value: cloneTabs});
        }
    }

    function moveFilterItem({index, item, direction}) {
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
            onChange({key: 'filters', value: cloneTabs});
        }
    }
    function getSingleFilterComponent(item, index) {
        SingleFilter.defaultProps = {
            index,
            data: item,
            moveUp: () => {
                moveFilterItem({index, item, direction: -1});
            },
            moveDown: () => {
                moveFilterItem({index, item, direction: 1});
            },
            onChange: ({key, value}) => {
                formChangeHandler({key, value, index});
            },
            deleteTab: () => {
                deleteTab({index});
            }
        }
        return <SingleFilter />;
    }
    return (
       <div>
           {Array.isArray(data) && data.map((item, index) => {
               return getSingleFilterComponent(item, index)
           })}

           <Button
               variant="primary"
               onClick={(e) => {
                   e.preventDefault()
                   addFilter();
               }}
           >
               Add Filter
           </Button>
       </div>
    );
};

export default Filters;

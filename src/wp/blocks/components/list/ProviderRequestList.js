import React from 'react';
import {Button, PanelBody, TabPanel} from "@wordpress/components";
import ProviderRequestForm from "./ProviderRequestForm";

const ProviderRequestList = (props) => {

    const {
        data = [],
        onChange
    } = props;

    function addFilter() {
        const cloneTabs = [...data];
        cloneTabs.push({
            provider_name: null,
            service_request_name: null,
        });
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }

    function formChangeHandler({key, value, index}) {
        const cloneTabs = [...data];
        cloneTabs[index][key] = value;
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
        }
    }
    function deleteTab({index}) {
        const cloneTabs = [...data];
        cloneTabs.splice(index, 1);
        if (typeof onChange === 'function') {
            onChange(cloneTabs);
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
            onChange(cloneTabs);
        }
    }
    function getSingleFilterComponent(item, index) {
        ProviderRequestForm.defaultProps = {
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
        return <ProviderRequestForm />;
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
                Add Provider Request
            </Button>
        </div>
    );
};

export default ProviderRequestList;

import React from 'react';
import {useBlockProps} from '@wordpress/block-editor';
import Tabs from "../components/tabs/Tabs";

const TabsBlockEdit = (props) => {
    const {attributes, setAttributes} = props;

    function formChangeHandler({key, value}) {
        console.log(key, value);
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <div {...useBlockProps()}>
              <Tabs
                data={attributes}
                onChange={formChangeHandler}
            />
        </div>
    );
};

export default TabsBlockEdit;

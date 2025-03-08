import React from 'react';
import {useBlockProps} from '@wordpress/block-editor';
import Tabs from "../components/tabs/Tabs";
import BlockEditComponent from '../common/BlockEditComponent';

const TabsBlockEdit = (props) => {
    const {attributes, setAttributes} = props;

    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <BlockEditComponent
            {...props}
            title='Tabs Block'
        >
              <Tabs
                data={attributes}
                onChange={formChangeHandler}
            />
        </BlockEditComponent>
    );
};

export default TabsBlockEdit;

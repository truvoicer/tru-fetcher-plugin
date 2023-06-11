import React from 'react';
import {TabPanel} from "@wordpress/components";
import { useBlockProps } from '@wordpress/block-editor';
const ListingsBlockEdit = () => {
    const blockProps = useBlockProps();
    return (
        <div { ...blockProps }>
            <TabPanel
                tabs={[
                    {
                        name: 'tab1',
                        title: 'Tab 1'
                    },
                    {
                        name: 'tab2',
                        title: 'Tab 2'
                    }
                ]}
            />
        </div>
    );
};

export default ListingsBlockEdit;

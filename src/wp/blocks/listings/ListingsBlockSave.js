import React from 'react';
import { useBlockProps, RichText } from '@wordpress/block-editor';

const ListingsBlockSave = (props) => {
    const blockProps = useBlockProps.save();
    return (
        <RichText.Content
            { ...blockProps }
            tagName="p"
            value={ props.attributes.content }
        />
    );
};

export default ListingsBlockSave;

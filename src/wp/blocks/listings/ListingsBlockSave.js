import React from 'react';
import { useBlockProps, RichText } from '@wordpress/block-editor';

const ListingsBlockSave = (props) => {
    const blockProps = useBlockProps.save();
    function buildDataJson() {
        let blockData = {};
        blockData['data-json'] = JSON.stringify(props.attributes);
        return blockData;
    }
    // console.log(buildDataJson())
    return (
        <div {...blockProps}>
            <div dataJson={JSON.stringify(props.attributes)}></div>
        </div>
    );
};

export default ListingsBlockSave;

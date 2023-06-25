import React from 'react';
import { useInnerBlocksProps, InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import {getChildBlockById, getChildBlockIds} from "../../../helpers/wp-helpers";

const InnerBlocksTab = (props) => {
    const blockProps = useBlockProps();
    const formBlockConfig = getChildBlockById(props?.config, 'form_block');
    const carouselBlockConfig = getChildBlockById(props?.config, 'carousel_block');

    const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, {
        allowedBlocks: [formBlockConfig.name, carouselBlockConfig.name],
    } );
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;
    return (
        <div>
            <h3>{formBlockConfig?.title || 'Form'}</h3>
            <div {...innerBlocksProps}>
                { children }
            </div>
        </div>
    );
};

export default InnerBlocksTab;

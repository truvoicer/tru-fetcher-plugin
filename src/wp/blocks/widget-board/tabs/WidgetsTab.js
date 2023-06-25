import React from 'react';
import { useInnerBlocksProps, InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import {getChildBlockById, getChildBlockIds} from "../../../helpers/wp-helpers";

const WidgetsTab = (props) => {
    const blockProps = useBlockProps();
    const sidebarWidgetsConfig = getChildBlockById(props?.config, 'sidebar_widgets_block');
    const contentWidgetsConfig = getChildBlockById(props?.config, 'content_widgets_block');

    const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, {
        allowedBlocks: [
            sidebarWidgetsConfig.name,
            contentWidgetsConfig.name,
        ],
    } );
    return (
        <div>
            <h3>{'Widgets'}</h3>
            <div {...innerBlocksProps}>
                { children }
            </div>
        </div>
    );
};

export default WidgetsTab;

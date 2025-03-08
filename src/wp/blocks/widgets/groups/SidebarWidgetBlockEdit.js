import React from 'react';
import {store as blockEditorStore, useBlockProps, useInnerBlocksProps} from '@wordpress/block-editor';
import {getChildBlockById, getChildBlockParams} from "../../../helpers/wp-helpers";
import {useDispatch, useSelect} from '@wordpress/data';
import BlockEditComponent from '../../common/BlockEditComponent';

const SidebarWidgetBlockEdit = (props) => {
    const {attributes, setAttributes, clientId, apiConfig} = props;
    const blockProps = useBlockProps();

    const formProgressWidgetConfig = getChildBlockById(props?.config, 'form_progress_widget_block');
    const userProfileWidgetConfig = getChildBlockById(props?.config, 'user_profile_widget_block');
    const userSocialWidgetConfig = getChildBlockById(props?.config, 'user_social_widget_block');
    const userStatsWidgetConfig = getChildBlockById(props?.config, 'user_stats_widget_block');

    const { updateBlockAttributes } = useDispatch( blockEditorStore );

    const { columnsIds, hasChildBlocks, rootClientId, hasParents, getBlockAttributes, childBlockAttributes } = useSelect(
        ( select ) => {
            return getChildBlockParams({
                blockEditorStore,
                select,
                clientId,
            });
        },
        [ clientId ]
    );

    const { children, ...innerBlocksProps } = useInnerBlocksProps( blockProps, {
        allowedBlocks: [
            formProgressWidgetConfig.name,
            userProfileWidgetConfig.name,
            userSocialWidgetConfig.name,
            userStatsWidgetConfig.name,
        ],
    } );

    if (hasParents) {
        updateBlockAttributes( rootClientId, {
            [props.config.id]: attributes,
        } );
    }
    return (
        <BlockEditComponent
            {...props}
            title='Sidebar Widgets'
        >
            <h3>{'Sidebar Widgets'}</h3>
            <div {...innerBlocksProps}>
                { children }
            </div>
        </BlockEditComponent>
    );
};

export default SidebarWidgetBlockEdit;

import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import {getChildBlockParams} from "../../../helpers/wp-helpers";
import UserStats from "../../components/user-stats/UserStats";

const UserStatsBlockEdit = (props) => {

    const { updateBlockAttributes } = useDispatch( blockEditorStore );
    const {attributes, setAttributes, clientId} = props;

    const { columnsIds, hasChildBlocks, rootClientId, hasParents, parentAttributes } = useSelect(
        ( select ) => {
            return getChildBlockParams({blockEditorStore, select, clientId});
        },
        [ clientId ]
    );
    function formChangeHandler({key, value}) {
        const newAttributes = {
            ...attributes,
            [key]: value
        };
        setAttributes(newAttributes);

        if (hasParents) {
            updateBlockAttributes( rootClientId, {
                [props.config.id]: newAttributes,
            } );
        }
    }

    return (
        <div {...useBlockProps()}>
            <Panel>
                <PanelBody title="User Stats Widget Block" initialOpen={true}>
                    <UserStats
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default UserStatsBlockEdit;

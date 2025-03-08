import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import UserProfile from "../../components/user-profile/UserProfile";
import {getChildBlockParams} from "../../../helpers/wp-helpers";
import BlockEditComponent from '../../common/BlockEditComponent';

const UserProfileBlockEdit = (props) => {

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
            // const findParentWidget = parentAttributes.widgets.find(widget => widget.id === props.config.id);
            updateBlockAttributes( rootClientId, {
                [props.config.id]: newAttributes,
            } );
        }
    }

    return (
        <BlockEditComponent
            {...props}
            title='User Profile Widget Block'
        >
            <Panel>
                <PanelBody title="User Profile Widget Block" initialOpen={true}>
                    <UserProfile
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </BlockEditComponent>
    );
};

export default UserProfileBlockEdit;

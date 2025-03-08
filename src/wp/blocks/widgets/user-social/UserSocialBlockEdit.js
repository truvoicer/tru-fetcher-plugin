import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import {getChildBlockParams} from "../../../helpers/wp-helpers";
import UserSocial from "../../components/user-social/UserSocial";
import BlockEditComponent from '../../common/BlockEditComponent';

const UserSocialBlockEdit = (props) => {


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
        <BlockEditComponent
            {...props}
            title='User Social Widget Block'
        >
            <Panel>
                <PanelBody title="User Social Widget Block" initialOpen={true}>
                    <UserSocial
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </BlockEditComponent>
    );
};

export default UserSocialBlockEdit;

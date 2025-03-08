import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import FormComponent from "../components/form/FormComponent";
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import { useSelect, useDispatch } from '@wordpress/data';
import {getChildBlockParams} from "../../helpers/wp-helpers";
import BlockEditComponent from '../common/BlockEditComponent';

const FormBlockEdit = (props) => {
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
            title='Form Block'
        >
            <Panel>
                <PanelBody title="Form Block" initialOpen={true}>
                    <FormComponent
                        data={(hasParents && parentAttributes)? parentAttributes : props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </BlockEditComponent>
    );
};

export default FormBlockEdit;

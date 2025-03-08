import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import Carousel from "../components/carousel/Carousel";
import {useBlockProps, store as blockEditorStore} from '@wordpress/block-editor';
import {getChildBlockParams} from "../../helpers/wp-helpers";
import { useSelect, useDispatch } from '@wordpress/data';
import BlockEditComponent from '../common/BlockEditComponent';

const CarouselBlockEdit = (props) => {
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
            key='Carousel Block'
        >
            <Panel>
                <PanelBody title="Carousel Block" initialOpen={true}>
                    <Carousel
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </BlockEditComponent>
    );
};

export default CarouselBlockEdit;

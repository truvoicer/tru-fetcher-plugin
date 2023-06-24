import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import Carousel from "../components/carousel/Carousel";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const CarouselBlockEdit = (props) => {

    const {attributes, setAttributes} = props;

    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <div {...useBlockProps()}>
            <Panel>
                <PanelBody title="Carousel Block" initialOpen={true}>
                    <Carousel
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default CarouselBlockEdit;

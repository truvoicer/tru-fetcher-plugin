import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import Carousel from "../components/carousel/Carousel";

const CarouselBlockEdit = (props) => {

    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <Panel>
            <PanelBody title="Carousel Block" initialOpen={true}>
                <Carousel
                    data={props.attributes}
                    onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default CarouselBlockEdit;

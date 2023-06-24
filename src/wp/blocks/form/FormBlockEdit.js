import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import FormComponent from "../components/form/FormComponent";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const FormBlockEdit = (props) => {
    const {attributes, setAttributes} = props;

    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }
    console.log({attributes})
    return (
        <div {...useBlockProps()}>
            <Panel>
                <PanelBody title="Form Block" initialOpen={true}>
                    <FormComponent
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default FormBlockEdit;

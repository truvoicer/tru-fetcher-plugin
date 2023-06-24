import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import FormProgress from "../components/form-progress/FormProgress";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const FormProgressBlockEdit = (props) => {
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
                <PanelBody title="Form Progress Widget Block" initialOpen={true}>
                    <FormProgress
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default FormProgressBlockEdit;

import React from 'react';
import {Panel, PanelBody} from "@wordpress/components";
import FormComponent from "../components/form/FormComponent";

const FormBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <Panel>
            <PanelBody title="Form Block" initialOpen={true}>
                <FormComponent
                  data={props.attributes}
                  onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default FormBlockEdit;

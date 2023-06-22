import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import FormProgress from "../components/form-progress/FormProgress";

const FormProgressBlockEdit = (props) => {
    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }

    return (
        <Panel>
            <PanelBody title="Form Progress Widget Block" initialOpen={true}>
                <FormProgress
                    data={props.attributes}
                    onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default FormProgressBlockEdit;

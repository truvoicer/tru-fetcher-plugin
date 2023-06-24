import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import UserSocial from "../components/user-social/UserSocial";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const UserProfileBlockEdit = (props) => {

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
                <PanelBody title="User Profile Widget Block" initialOpen={true}>
                    <UserSocial
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default UserProfileBlockEdit;

import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import UserSocial from "../components/user-social/UserSocial";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const UserSocialBlockEdit = (props) => {

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
                <PanelBody title="User Social Widget Block" initialOpen={true}>
                    <UserSocial
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default UserSocialBlockEdit;

import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import UserStats from "../components/user-stats/UserStats";
import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const UserStatsBlockEdit = (props) => {

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
                <PanelBody title="User Stats Widget Block" initialOpen={true}>
                    <UserStats
                        data={props.attributes}
                        onChange={formChangeHandler}
                    />
                </PanelBody>
            </Panel>
        </div>
    );
};

export default UserStatsBlockEdit;

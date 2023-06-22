import React from 'react';
import {Panel, PanelBody, TabPanel} from "@wordpress/components";
import UserStats from "../components/user-stats/UserStats";

const UserStatsBlockEdit = (props) => {

    const {attributes, setAttributes} = props;
    function formChangeHandler({key, value}) {
        setAttributes({
            ...attributes,
            [key]: value
        });
    }
    return (
        <Panel>
            <PanelBody title="User Stats Widget Block" initialOpen={true}>
                <UserStats
                    data={props.attributes}
                    onChange={formChangeHandler}
                />
            </PanelBody>
        </Panel>
    );
};

export default UserStatsBlockEdit;

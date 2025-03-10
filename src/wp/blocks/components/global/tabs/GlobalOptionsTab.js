import React from 'react';
import {SelectControl, TextControl} from "@wordpress/components";
import Grid from "../../../../../components/Grid";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const GlobalOptionsTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig,
        wpMediaFrame,
    } = props;
    
    return (
        <>
            <Grid columns={2}>
                <TextControl
                    label="Title"
                    placeholder="Title"
                    value={ attributes?.title }
                    onChange={ ( value ) => {
                        setAttributes({title: value});
                    } }
                />
                <SelectControl
                    label="Access Control"
                    onChange={(value) => {
                        setAttributes({access_control: value});
                    }}
                    value={attributes?.access_control}
                    options={GutenbergBase.getSelectOptions('access_control', props)}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Position when sidebar is enabled"
                    onChange={(value) => {
                        setAttributes({sidebar_layout_position: value});
                    }}
                    value={attributes?.sidebar_layout_position}
                    options={GutenbergBase.getSelectOptions('sidebar_layout_position', props)}
                />
                <SelectControl
                    label="Block style"
                    onChange={(value) => {
                        setAttributes({block_width: value});
                    }}
                    value={attributes?.block_width}
                    options={Array.from({length: 12}, (v, i) => {
                        return {
                            label: `${i + 1}/12`,
                            value: `${i + 1}`
                        }
                    }).reverse()}
                />
            </Grid>
        </>
    );
};

export default GlobalOptionsTab;

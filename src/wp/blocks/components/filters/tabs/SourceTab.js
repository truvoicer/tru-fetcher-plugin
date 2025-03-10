import React from 'react';
import {TabPanel, Panel, Button, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import Carousel from "../../carousel/Carousel";
import {findPostTypeIdIdentifier, findPostTypeSelectOptions} from "../../../../helpers/wp-helpers";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const SourceTab = (props) => {

    const {
        data,
        onChange
    } = props;

    const filterListId = findPostTypeIdIdentifier('trf_filter_list')
    return (
        <div>
            <SelectControl
                label="Source"
                onChange={(value) => {
                    onChange({key: 'source', value: value});
                }}
                value={data?.source}
                options={GutenbergBase.getSelectOptions('source', props)}
            />
            {data?.source === 'api' &&
                <TextControl
                    label="Api Endpoint"
                    placeholder="Api Endpoint"
                    value={data?.api_endpoint}
                    onChange={(value) => {
                        onChange({key: 'api_endpoint', value: value});
                    }}
                />
            }
            {data?.source === 'wordpress' &&
                <SelectControl
                    label="Filter List"
                    onChange={(value) => {
                        onChange({key: filterListId, value: value});
                    }}
                    value={data?.[filterListId] || ''}
                    options={[
                        ...[
                            {
                                disabled: true,
                                label: 'Select an Option',
                                value: ''
                            },
                        ],
                        ...findPostTypeSelectOptions('trf_filter_list')
                    ]}
                />
            }
        </div>
    );
};

export default SourceTab;

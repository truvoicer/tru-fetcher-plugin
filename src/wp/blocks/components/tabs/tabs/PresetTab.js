import React from 'react';
import {TabPanel, Panel, RangeControl, TextControl, SelectControl, ToggleControl} from "@wordpress/components";
import { GutenbergBase } from '../../../../helpers/gutenberg/gutenberg-base';

const PresetTab = (props) => {
    const {
        data = [],
        onChange,
        showPresets = true
    } = props;

    function getPresets() {
        const tabPresets = tru_fetcher_react?.tab_presets;
        if (!Array.isArray(tabPresets)) {
            console.warn('Tab presets not found')
            return [];
        }
        return tabPresets.map(preset => {
            return {
                label: preset.name,
                value: preset.id
            }
        });
    }
    return (
        <div>
            {showPresets && (
                <SelectControl
                    label="Presets"
                    onChange={(value) => {
                        if (typeof onChange === 'function') {
                            onChange({key: 'presets', value: value});
                        }
                    }}
                    value={data?.presets}
                    options={[
                        ...GutenbergBase.getSelectOptions('presets', props),
                        ...getPresets()
                    ]}
                />
            )}
        </div>
    );
};

export default PresetTab;

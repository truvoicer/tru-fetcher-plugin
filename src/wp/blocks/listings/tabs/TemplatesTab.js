import React, { useContext, useEffect } from 'react';
import { SelectControl } from "@wordpress/components";
import { findSetting, getSettingListOptions } from "../../../helpers/wp-helpers";
import Grid from "../../../../components/Grid";
import ProviderRequestContext from '../../components/list/ProviderRequestContext';
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';

const TemplatesTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    const providerRequestContext = useContext(ProviderRequestContext);
    console.log(props)
    useEffect(() => {
        if (!Array.isArray(providerRequestContext.services) || providerRequestContext.services.length === 0) {
            return;
        }
        providerRequestContext.update({
            selectedService: providerRequestContext.services.find((service) => parseInt(service.id) === parseInt(attributes.api_listings_service))
        })
    }, [providerRequestContext.services]);

    return (
        <div>
            <Grid columns={2}>
                <SelectControl
                    label="Display As"
                    onChange={(value) => {
                        setAttributes({ display_as: value });
                    }}
                    value={attributes?.display_as}
                    options={GutenbergBase.getSelectOptions('display_as', props)}
                />
                <SelectControl
                    label="Template"
                    onChange={(value) => {
                        setAttributes({ template: value });
                    }}
                    value={attributes?.template}
                    options={[
                        ...GutenbergBase.getSelectOptions('template', props)
                    ]}
                />
            </Grid>
            <Grid columns={2}>
            <SelectControl
                    label="Style"
                    onChange={(value) => {
                        setAttributes({ style: value });
                    }}
                    value={attributes?.style}
                    options={[
                        ...GutenbergBase.getSelectOptions('style', props)
                    ]}
                />
            </Grid>
        </div>
    );
};

export default TemplatesTab;

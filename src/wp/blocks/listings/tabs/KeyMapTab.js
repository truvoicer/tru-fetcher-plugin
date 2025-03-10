import React, { useContext, useEffect } from 'react';
import { TextControl, SelectControl, RangeControl, ColorPicker } from "@wordpress/components";
import Grid from "../../../../components/Grid";
import ProviderRequestContext from '../../components/list/ProviderRequestContext';
import MediaInput from '../../../components/media/MediaInput';
import KeyMapSelector from '../../common/KeyMapSelector';
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';

const KeyMapTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    const providerRequestContext = useContext(ProviderRequestContext);

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
                    label="Sort Order"
                    onChange={(value) => {
                        setAttributes({ sort_order: value });
                    }}
                    value={attributes?.sort_order}
                    options={GutenbergBase.getSelectOptions('sort_order', props)}
                />
            </Grid>
            <Grid columns={1}>
                <KeyMapSelector
                    {...props}
                    config={[
                        {label: 'Sort By', key: 'sort_by'},
                        {label: 'Date Key', key: 'date_key'},
                        {label: 'URL Key', key: 'url_key'},
                        {label: 'Title Key', key: 'title_key'},
                        {label: 'Excerpt Key', key: 'excerpt_key'},
                        {label: 'Description Key', key: 'description_key'},
                        {label: 'Thumbnail Key', key: 'thumbnail_key'},
                    ]}
                    open={true}
                />
            </Grid>
        </div>
    );
};

export default KeyMapTab;

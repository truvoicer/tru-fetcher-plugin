import React, { useContext, useEffect } from 'react';
import { TextControl, SelectControl, RangeControl, ColorPicker } from "@wordpress/components";
import Grid from "../../../../components/Grid";
import ProviderRequestContext from '../../components/list/ProviderRequestContext';
import MediaInput from '../../../components/media/MediaInput';
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';

const DisplayTab = (props) => {
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
                <TextControl
                    label="Heading"
                    value={attributes?.heading}
                    onChange={(value) => setAttributes({ heading: value })}
                />
                <SelectControl
                    label="Load More Type"
                    onChange={(value) => {
                        setAttributes({ load_more_type: value });
                    }}
                    value={attributes?.load_more_type}
                    options={GutenbergBase.getSelectOptions('load_more_type', props)}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Select Item View Display"
                    onChange={(value) => {
                        setAttributes({ item_view_display: value });
                    }}
                    value={attributes?.item_view_display}
                    options={GutenbergBase.getSelectOptions('item_view_display', props)}
                />
                <SelectControl
                    label="Link Type"
                    onChange={(value) => {
                        setAttributes({ link_type: value });
                    }}
                    value={attributes?.link_type}
                    options={GutenbergBase.getSelectOptions('link_type', props)}
                />
            </Grid>
            <Grid columns={2}>
                <RangeControl
                    label="Posts Per Page"
                    initialPosition={50}
                    max={100}
                    min={0}
                    value={attributes?.posts_per_page}
                    onChange={(value) => setAttributes({ posts_per_page: value })}
                />
                <SelectControl
                    label="Grid Layout"
                    onChange={(value) => {
                        setAttributes({ grid_layout: value });
                    }}
                    value={attributes?.grid_layout}
                    options={GutenbergBase.getSelectOptions('grid_layout', props)}
                />
            </Grid>
        </div>
    );
};

export default DisplayTab;

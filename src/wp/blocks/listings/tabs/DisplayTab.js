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
            <Grid columns={2}>
                <SelectControl
                    label="Thumbnail type"
                    onChange={(value) => {
                        setAttributes({ thumbnail_type: value });
                    }}
                    value={attributes?.thumbnail_type}
                    options={GutenbergBase.getSelectOptions('thumbnail_type', props)}
                />
                {attributes?.thumbnail_type === 'bg' &&
                    <ColorPicker
                        color={attributes?.thumbnail_bg}
                        onChange={color => setAttributes({ thumbnail_bg: color })}
                        enableAlpha
                        defaultValue="#000"
                    />
                }
                {attributes?.thumbnail_type === 'image' &&
                    <MediaInput
                        hideDelete={true}
                        heading={`Thumbnail image`}
                        addImageText={'Add'}
                        selectedImageUrl={attributes?.thumbnail_url}
                        onChange={(value) => {
                            setAttributes({ thumbnail_url: value })
                        }}
                        onDelete={(value) => {
                            setAttributes({ thumbnail_url: null })
                        }}
                    />
                }
            </Grid>
            <Grid columns={2}>
                <RangeControl
                    label="Thumbnail width"
                    initialPosition={100}
                    max={1000}
                    min={0}
                    value={attributes?.thumbnail_width}
                    onChange={(value) => setAttributes({ thumbnail_width: value })}
                />
                <RangeControl
                    label="Thumbnail height"
                    initialPosition={100}
                    max={1000}
                    min={0}
                    value={attributes?.thumbnail_height}
                    onChange={(value) => setAttributes({ thumbnail_height: value })}
                />
            </Grid>
        </div>
    );
};

export default DisplayTab;

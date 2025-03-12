import React, { useContext, useEffect } from 'react';
import { ColorPicker, RangeControl, SelectControl } from "@wordpress/components";
import Grid from "../../../../components/Grid";
import ProviderRequestContext from '../../components/list/ProviderRequestContext';
import { GutenbergBase } from '../../../helpers/gutenberg/gutenberg-base';
import MediaInput from '../../../components/media/MediaInput';

const TemplatesTab = (props) => {
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

export default TemplatesTab;

import React, { useContext, useEffect } from 'react';
import {TextControl, SelectControl, RangeControl, ColorPicker} from "@wordpress/components";
import {findSetting} from "../../../helpers/wp-helpers";
import Grid from "../../../../components/Grid";
import ProviderRequestContext from '../../components/list/ProviderRequestContext';
import { buildSelectOptions } from '../../../../library/helpers/form-helpers';
import MediaInput from '../../../components/media/MediaInput';

const DisplayTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
    } = props;

    const providerRequestContext = useContext(ProviderRequestContext);

    function getComparisonTemplateOptions(show) {
        if (!show) {
            return [];
        }
        const comparisonTemplates = findSetting('comparison_templates')?.value;
        if (!Array.isArray(comparisonTemplates)) {
            return [];
        }

        return comparisonTemplates.map((template) => {
            return {
                label: template.name,
                value: template.value
            }
        });
    }
    
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
                    onChange={(value) => setAttributes({heading: value})}
                />
                <SelectControl
                    label="Load More Type"
                    onChange={(value) => {
                        setAttributes({load_more_type: value});
                    }}
                    value={attributes?.load_more_type}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'Pagination',
                            value: 'pagination'
                        },
                        {
                            label: 'Infinite Scroll',
                            value: 'infinite_scroll'
                        },
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Grid Layout"
                    onChange={(value) => {
                        setAttributes({grid_layout: value});
                    }}
                    value={attributes?.grid_layout}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'List',
                            value: 'list'
                        },
                        {
                            label: 'Compact',
                            value: 'compact'
                        },
                        {
                            label: 'Detailed',
                            value: 'detailed'
                        },
                    ]}
                />
                <SelectControl
                    label="Select Item View Display"
                    onChange={(value) => {
                        setAttributes({item_view_display: value});
                    }}
                    value={attributes?.item_view_display}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'Modal',
                            value: 'modal'
                        },
                        {
                            label: 'Page',
                            value: 'page'
                        },
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <RangeControl
                    label="Posts Per Page"
                    initialPosition={50}
                    max={100}
                    min={0}
                    value={attributes?.posts_per_page}
                    onChange={(value) => setAttributes({posts_per_page: value})}
                />
                <SelectControl
                    label="Display As"
                    onChange={(value) => {
                        setAttributes({display_as: value});
                    }}
                    value={attributes?.display_as}
                    options={[
                        {
                            disabled: true,
                            label: 'Select an Option',
                            value: ''
                        },
                        {
                            label: 'List',
                            value: 'list'
                        },
                        {
                            label: 'Posts List',
                            value: 'post_list'
                        },
                        {
                            label: 'Comparisons',
                            value: 'comparisons'
                        },
                        {
                            label: 'Tiles',
                            value: 'tiles'
                        },
                        {
                            label: 'Sidebar Posts',
                            value: 'sidebar_posts'
                        },
                        {
                            label: 'Sidebar List',
                            value: 'sidebar_list'
                        },
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Template"
                    onChange={(value) => {
                        setAttributes({template: value});
                    }}
                    value={attributes?.template}
                    options={[
                        {
                            label: 'Select template',
                            value: ''
                        },
                        {
                            label: 'Default',
                            value: 'default'
                        },
                        ...getComparisonTemplateOptions(attributes?.display_as === 'comparisons')
                    ]}
                />
            </Grid>
            <Grid columns={2}>
                <SelectControl
                    label="Thumbnail type"
                    onChange={(value) => {
                        setAttributes({thumbnail_type: value});
                    }}
                    value={attributes?.thumbnail_type}
                    options={[
                        {
                            disabled: false,
                            label: 'Select a type',
                            value: ''
                        },
                        {
                            disabled: false,
                            label: 'Image',
                            value: 'image'
                        },
                        {
                            disabled: false,
                            label: 'Background Color',
                            value: 'bg'
                        },
                        {
                            disabled: false,
                            label: 'Data key',
                            value: 'data_key'
                        },
                        {
                            disabled: false,
                            label: 'Disabled',
                            value: 'disabled'
                        },
                    ]}
                />
                {attributes?.thumbnail_type === 'data_key' &&
                    <SelectControl
                        label="Thumbnail Key"
                        onChange={(value) => {
                            setAttributes({thumbnail_key: value});
                        }}
                        value={attributes?.thumbnail_key}
                        options={[
                            ...[
                                {
                                    disabled: false,
                                    label: 'Select a key',
                                    value: ''
                                },
                            ],
                            ...buildSelectOptions(
                                providerRequestContext?.responseKeys,
                                'name', 
                                'name'
                            )
                        ]}
                    />
                }
                {attributes?.thumbnail_type === 'bg' &&
                    <ColorPicker
                        color={attributes?.thumbnail_bg}
                        onChange={color => setAttributes({thumbnail_bg: color})}
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
                            setAttributes({thumbnail_url: value})
                        }}
                        onDelete={(value) => {
                            setAttributes({thumbnail_url: null})
                        }}
                    />
                }

                <RangeControl
                    label="Thumbnail width"
                    initialPosition={100}
                    max={1000}
                    min={0}
                    value={attributes?.thumbnail_width}
                    onChange={(value) => setAttributes({thumbnail_width: value})}
                />
                <RangeControl
                    label="Thumbnail height"
                    initialPosition={100}
                    max={1000}
                    min={0}
                    value={attributes?.thumbnail_height}
                    onChange={(value) => setAttributes({thumbnail_height: value})}
                />
            </Grid>
        </div>
    );
};

export default DisplayTab;

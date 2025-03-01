import {useContext, useEffect} from "@wordpress/element";
import {Panel, PanelBody, SelectControl, ToggleControl, ColorPicker, RangeControl} from "@wordpress/components";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import ProviderRequestList from "../../components/list/ProviderRequestList";
import ProviderRequestContext, {providerRequestData} from "../../components/list/ProviderRequestContext";
import {buildSelectOptions} from "../../../../library/helpers/form-helpers";
import MediaInput from "../../../components/media/MediaInput";

const ApiTab = (props) => {
    const {
        attributes,
        setAttributes,
        className,
        apiConfig
    } = props;
    const providerRequestContext = useContext(ProviderRequestContext);

    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(props?.reducers?.app);
    stateMiddleware.setSessionState(props?.reducers?.session);

    useEffect(() => {
        if (!Array.isArray(providerRequestContext.services) || providerRequestContext.services.length === 0) {
            return;
        }
        providerRequestContext.update({
            selectedService: providerRequestContext.services.find((service) => parseInt(service.id) === parseInt(attributes.api_listings_service))
        })
    }, [providerRequestContext.services]);

    
    return (
        <>
            <SelectControl
                label="Api Fetch Type"
                onChange={(value) => {
                    setAttributes({api_fetch_type: value});
                }}
                value={attributes?.api_fetch_type}
                options={[
                    {
                        label: 'Database',
                        value: 'database'
                    },
                    {
                        label: 'API Direct',
                        value: 'api_direct'
                    },
                ]}
            />
            <SelectControl
                label="Api Listings Service"
                onChange={(value) => {
                    setAttributes({api_listings_service: value});
                    providerRequestContext.update({
                        selectedService: providerRequestContext.services.find((service) => parseInt(service.id) === parseInt(value))
                    })
                }}
                value={attributes?.api_listings_service}
                options={[
                    ...[
                        {
                            disabled: false,
                            label: 'Select a service',
                            value: ''
                        },
                    ],
                    ...buildSelectOptions(providerRequestContext.services, 'label', 'name')
                ]}
            />
            {Array.isArray(providerRequestContext?.providers) && providerRequestContext.providers.length > 0 &&
                <>
                    <ToggleControl
                        label="Select Providers"
                        checked={attributes?.select_providers}
                        onChange={(value) => {
                            setAttributes({select_providers: value});
                        }}
                    />
                    {attributes?.select_providers &&
                        <ProviderRequestList
                            reducers={props?.reducers}
                            data={attributes?.providers_list || []}
                            onChange={(value) => {
                                setAttributes({providers_list: value});
                            }}
                        />
                    }
                </>
            }

            <Panel>
                <PanelBody title="Key Maps" initialOpen={false}>
                    <SelectControl
                        label="Sort by"
                        onChange={(value) => {
                            setAttributes({sort_by: value});
                        }}
                        value={attributes?.sort_by}
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
                    <SelectControl
                        label="Sort order"
                        onChange={(value) => {
                            setAttributes({sort_order: value});
                        }}
                        value={attributes?.sort_order}
                        options={[
                            {
                                label: 'Descending',
                                value: 'desc'
                            },
                            {
                                label: 'Ascending',
                                value: 'asc'
                            },
                        ]}
                    />
                    <SelectControl
                        label="Date key"
                        onChange={(value) => {
                            setAttributes({date_key: value});
                        }}
                        value={attributes?.date_key}
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
                </PanelBody>
            </Panel>
        </>
    );
};

export default ApiTab;

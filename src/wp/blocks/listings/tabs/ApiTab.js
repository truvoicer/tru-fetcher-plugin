import {useContext, useEffect} from "@wordpress/element";
import {Panel, PanelBody, SelectControl, ToggleControl, ColorPicker} from "@wordpress/components";
import {StateMiddleware} from "../../../../library/api/StateMiddleware";
import ProviderRequestList from "../../components/list/ProviderRequestList";
import ProviderRequestContext, {providerRequestData} from "../../components/list/ProviderRequestContext";
import {buildSelectOptions} from "../../../../library/helpers/form-helpers";

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
                            color={color}
                            onChange={color => setAttributes({thumbnail_key: value})}
                            enableAlpha
                            defaultValue="#000"
                        />
                    }
                    {attributes?.thumbnail_type === 'image' &&
                       
                    }
                </PanelBody>
            </Panel>
        </>
    );
};

export default ApiTab;

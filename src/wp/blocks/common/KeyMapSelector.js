import { useContext, useEffect } from "@wordpress/element";
import { Panel, PanelBody, SelectControl } from "@wordpress/components";
import { StateMiddleware } from "../../../library/api/StateMiddleware";
import ProviderRequestContext from "../components/list/ProviderRequestContext";
import { buildSelectOptions } from "../../../library/helpers/form-helpers";

const KeyMapSelector = (props) => {
    const {
        attributes,
        setAttributes,
        config,
        open = false
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
        <Panel>
            <PanelBody title="Key Maps" initialOpen={open}>
                {Array.isArray(config) && config.map((item, index) => {
                    return (
                        <SelectControl
                            key={index}
                            label={item.label}
                            onChange={(value) => {
                                setAttributes({ [item.key]: value });
                            }}
                            value={attributes?.[item.key]}
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
                    );
                })}
            </PanelBody>
        </Panel>
    );
};

export default KeyMapSelector;

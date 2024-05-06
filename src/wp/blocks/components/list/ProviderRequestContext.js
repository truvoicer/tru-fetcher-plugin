import {createContext} from "@wordpress/element";
export const providerRequestData = {
    providers: [],
    services: [],
    categories: [],
    selectedService: null,
    update: () => {}
}
export default createContext(providerRequestData);

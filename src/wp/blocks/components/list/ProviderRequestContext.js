import {createContext} from "@wordpress/element";
export const providerRequestData = {
    providers: [],
    services: [],
    categories: [],
    selectedService: null,
    saveData: [],
    responseKeys: [],
    update: () => {}
}
export default createContext(providerRequestData);

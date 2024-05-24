import {createContext} from "@wordpress/element";
export const providerRequestData = {
    providers: [],
    services: [],
    categories: [],
    selectedService: null,
    saveData: [],
    update: () => {}
}
export default createContext(providerRequestData);

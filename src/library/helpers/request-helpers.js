import config from "../api/wp/config";
import fetcherApiConfig from "../api/fetcher-api/fetcherApiConfig";

export function getApiRequestConfig(appKey) {
    switch (appKey) {
        case 'wp_react':
            return config;
        case 'tru_fetcher_react':
            return fetcherApiConfig;
        default:
            return false;
    }
}

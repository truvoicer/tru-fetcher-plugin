import apiConfig from "./wp/config";
import {isNotEmpty} from "../helpers/utils-helpers";
import {
    getSessionApiUrlBaseAction,
    getSessionLocalStorage, getSessionNonceAction, getSessionUserIdAction, isLocalStorageTokenValid,
    removeLocalSession, setIsAuthenticatingAction, setSessionAuthenticatedAction,
    setSessionLocalStorage,
    setSessionState
} from "../redux/actions/session-actions";
import {SESSION_STATE, SESSION_USER_TOKEN, SESSION_USER_TOKEN_EXPIRES_AT} from "../redux/constants/session-constants";
import {getAppKeyAction, getAppNameAction} from "../redux/actions/app-actions";
import {getSignedJwt} from "../helpers/auth/jwt-helpers";
import store from "../redux/store";

const axios = require("axios");

/**
 * Initialise axios apiRequest with initial config
 * @type {AxiosInstance}
 */

function getBaseUrl(apiConfig = false) {
    if (!apiConfig?.baseUrl) {
        return false;
    }
    return apiConfig.baseUrl;
}

function getApiRequest(apiConfig = false) {
    const baseUrl = getBaseUrl(apiConfig);
    if (!baseUrl) {
        return false;
    }
    const apiRequest = axios.create({
        baseURL: baseUrl,
    });
    loadAxiosInterceptors(apiRequest);
    return apiRequest;
}

/**
 * Loads axios response interceptors
 * Redirects to login when response status is unauthorized
 */
export function loadAxiosInterceptors(apiRequest) {
    apiRequest.interceptors.request.use(function (config) {
        // Do something before request is sent
        return config;
    }, function (error) {
        // Do something with request error
        return Promise.reject(error);
    });

    apiRequest.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        switch (error?.response?.status) {
            case 401:
                handleUnauthorized();
                break;
        }
        return Promise.reject(error);
    });
}

/**
 * Build authorization header with bearer token from local storage
 * @returns {{Authorization: string}|boolean}
 */
const getAuthHeader = (apiConfig = false) => {
    //Return false if local session token is invalid
    if (!apiConfig?.token) {
        return false;
    }
    return {
        'Authorization': `Bearer ${apiConfig.token}`
    };
}


/**
 * Makes api fetch request
 * Returns false if headers are invalid
 *
 * @param config
 * @param endpoint
 * @param params
 * @param apiConfig
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export async function fetchRequest({config, endpoint, params = {}, apiConfig = false}) {
    let requestParams = params;
    const apiRequest = getApiRequest(apiConfig);
    if (!apiRequest) {
        return false;
    }
    const appKey = apiConfig?.app_key;
    const urlBase = getBaseUrl(apiConfig);
    if (!urlBase) {
        return false;
    }
    let headers = getAuthHeader(apiConfig);

    if (!headers) {
        return false;
    }
    const request = {
        method: "get",
        url: `${urlBase}${endpoint}`,
        headers,
        params: requestParams
    }
    return await apiRequest.request(request);
}

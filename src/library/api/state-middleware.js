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
import {APP_STATE} from "../redux/constants/app-constants";

const axios = require("axios");
const sprintf = require('sprintf-js').sprintf

/**
 * Initialise axios apiRequest with initial config
 * @type {AxiosInstance}
 */


function getApiRequest() {
    const apiBaseUrl = getSessionApiUrlBaseAction();
    if (!apiBaseUrl) {
        return false;
    }
    const apiRequest = axios.create({
        baseURL: apiBaseUrl,
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
const getAuthHeader = (appKey) => {
    const appState = store.getState()[APP_STATE];
    if (appKey && typeof appState?.api === 'object') {
        const findApiConfigKey = Object.keys(appState.api).find((key) => {
            if (typeof appState.api[key]['app_key'] === 'undefined') {
                return false;
            }
            return (appState.api[key]['app_key'] === appKey);
        });
        if (findApiConfigKey && isNotEmpty(appState?.api?.[findApiConfigKey]?.['token'])) {
            return {
                'Authorization': `Bearer ${appState?.api?.[findApiConfigKey]?.['token']}`
            };
        }
    }
    const sessionStorage = getSessionLocalStorage(appKey);
    let token = false;
    //Return false if local session token is invalid
    if (typeof sessionStorage[SESSION_USER_TOKEN] === 'undefined' || !isNotEmpty(sessionStorage[SESSION_USER_TOKEN])) {
        const sessionStore = store.getState()[SESSION_STATE];
        // console.log({sessionStore})
        if (!isNotEmpty(sessionStore?.user?.token)) {
            return false;
        }
        token = sessionStore.user.token;
    } else {
        token = sessionStorage[SESSION_USER_TOKEN];
        if (!validateToken(appKey)) {
            return false;
        }
    }
    return {
        'Authorization': `Bearer ${token}`
    };
}
const getUploadHeaders = () => {
    return {
        'Content-Type': 'multipart/form-data',
    };
}

/**
 * Makes api fetch request
 * Returns false if headers are invalid
 *
 * @param config
 * @param method
 * @param endpoint
 * @param params
 * @param data
 * @param upload
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export async function sendRequest({
    config = apiConfig,
    method = 'post',
    endpoint,
    params = {},
    data = {},
    upload = false,
}) {
    let requestParams = params;
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const appKey = getAppKeyAction();
    if (config?.wpRequest) {
        const apiRequestAuthData = getApiRequestAuthData(config, appKey);
        if (!apiRequestAuthData) {
            return false;
        }
        requestParams = {...requestParams, ...apiRequestAuthData};
    }
    const urlBase = getSessionApiUrlBaseAction();
    if (!urlBase) {
        return false;
    }
    let headers = getAuthHeader(appKey);
    if (!headers && config?.wpRequest) {
        return false;
    }
    if (upload) {
        headers = {...headers, ...getUploadHeaders()};
    }
    const request = {
        method,
        url: `${urlBase}${endpoint}`,
        headers,
        params: requestParams,
        data
    }
    return await apiRequest.request(request);
}

/**
 * Makes api fetch request
 * Returns false if headers are invalid
 *
 * @param config
 * @param endpoint
 * @param params
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export async function fetchRequest({ config, endpoint, params = {}}) {
    let requestParams = params;
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const appKey = getAppKeyAction(config);
    if (config?.wpRequest) {
        const apiRequestAuthData = getApiRequestAuthData(config, appKey);
        if (!apiRequestAuthData) {
            return false;
        }
        requestParams = {...requestParams, ...apiRequestAuthData};
    }
    const urlBase = getSessionApiUrlBaseAction(config);
    console.log({urlBase})
    if (!urlBase) {
        return false;
    }
    let headers = getAuthHeader(appKey);

    console.log({headers})
    if (!headers && config?.wpRequest) {
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

/**
 * Makes token refresh api request
 * Returns false if headers are invalid
 *
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export function validateToken(appKey) {
    const isTokenValid = isLocalStorageTokenValid(appKey);    //Checks token is valid and not expired
    if (!isTokenValid) {
        console.error('Token expired');
        // handleUnauthorized();
        return false;
    }
    return true
}

function buildApiRequestJwtSecret({secretType, secretApp, sessionUserId, appName}) {
    let appSecret = null;
    // if (typeof process?.env?.TRU_FETCHER_SECRET !== 'undefined' && process?.env?.TRU_FETCHER_SECRET !== '') {
    //     appSecret = process.env.TRU_FETCHER_SECRET;
    // }
    if (typeof tru_fetcher_react?.api?.wp?.secret !== 'undefined' && tru_fetcher_react?.api?.wp?.secret !== '') {
        appSecret = tru_fetcher_react.api.wp.secret;
    }
    if (!appSecret) {
        console.error('App secret is invalid');
        return false
    }
    return sprintf(
        '%s_%s_%s_%s_%s',
        secretType,
        secretApp,
        appName,
        sessionUserId,
        appSecret
    );
}

export function getApiRequestAuthData(config, appKey) {
    if (!config?.wpRequest) {
        return {};
    }
    const sessionNonce = getSessionNonceAction();
    const sessionUserId = getSessionUserIdAction();
    const appName = getAppNameAction();
    if (!sessionNonce) {
        return false;
    }
    if (!sessionUserId) {
        return false;
    }
    if (!appName) {
        return false;
    }
    const payloadSecret = buildApiRequestJwtSecret({
        config,
        secretType: 'nonce',
        secretApp: appKey,
        sessionUserId,
        appName
    });
    if (!payloadSecret) {
        return false;
    }
    const payloadJwt = getSignedJwt({
        secret: payloadSecret,
        payload: {
            nonce: sessionNonce,
        }
    })
    return {payload: payloadJwt, user_id: sessionUserId, app_key: appKey};
}

export async function generateToken({appKey, config = apiConfig}) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData(config, appKey);
    if (!apiRequestAuthData) {
        return false;
    }
    const request = {
        method: "get",
        url: `/token/generate`,
        params: {...apiRequestAuthData, app_key: appKey}
    }
    const getRequest = await apiRequest.request(request);
    if (handleTokenResponse({appKey, config, result: getRequest})) {
        return true;
    }
    return false;
}

export async function checkToken({config}) {
    const appKey = getAppKeyAction();
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData(config, appKey);
    if (!apiRequestAuthData) {
        return false;
    }
    let headers = getAuthHeader(appKey);
    if (!headers && config?.wpRequest) {
        return generateToken({appKey, config});
    }
    const request = {
        method: "get",
        url: config?.endpoints?.checkToken,
        headers,
        params: apiRequestAuthData
    }

    try {
        const getRequest = await apiRequest.request(request);
        if (getRequest?.data?.status === 'success') {
            switch (config?.tokenSource) {
                case 'env':
                    setSessionAuthenticatedAction(true);
                    setIsAuthenticatingAction(false);
                    break;
                default:
                    const sessionStorage = getSessionLocalStorage(appKey);
                    setSessionState({
                        token: sessionStorage[SESSION_USER_TOKEN],
                        expiresAt: sessionStorage[SESSION_USER_TOKEN_EXPIRES_AT],
                    })
                    break;
            }

            return true;
        }
        return false;
    } catch (e) {
        console.error(e)
        if (config?.wpRequest) {
            return tokenRefresh({appKey, config});
        }
    }
}

export async function tokenRefresh({appKey, config = apiConfig}) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData(config, appKey);
    if (!apiRequestAuthData) {
        return false;
    }
    // if (!validateToken()) {
    //     return generateToken();
    // }
    const request = {
        method: "get",
        url: `/token/refresh`,
        headers: getAuthHeader(appKey),
        params: {...apiRequestAuthData, app_key: appKey}
    }
    const getRequest = await apiRequest.request(request);
    if (handleTokenResponse({appKey, config, result: getRequest})) {
        return true;
    }
    return false;
}

export function handleCheckResponse(result) {
    setSessionAuthenticatedAction(true);
    setIsAuthenticatingAction(false);
    return false;
}

export function handleTokenResponse({result, config, appKey}) {
    if (typeof config?.tokenResponseHandler !== 'function') {
        return false;
    }
    return config.tokenResponseHandler(result, appKey);
}

export function handleUnauthorized() {
}

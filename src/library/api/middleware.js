import apiConfig from "../api/config";
import {isNotEmpty} from "../helpers/utils-helpers";
import {
    getSessionApiUrlBaseAction,
    getSessionLocalStorage, getSessionNonceAction, getSessionUserIdAction, isLocalStorageTokenValid,
    removeLocalSession, setIsAuthenticatingAction, setSessionAuthenticatedAction,
    setSessionLocalStorage,
    setSessionState
} from "../redux/actions/session-actions";
import {SESSION_USER_TOKEN} from "../redux/constants/session-constants";
import {getAppNameAction} from "../redux/actions/app-actions";
import {getSignedJwt} from "../helpers/auth/jwt-helpers";

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
const getAuthHeader = () => {
    const sessionStorage = getSessionLocalStorage();
    //Return false if local session token is invalid
    if (typeof sessionStorage[SESSION_USER_TOKEN] === 'undefined' || !isNotEmpty(sessionStorage[SESSION_USER_TOKEN])) {
        return false;
    }
    return {
        'Authorization': `Bearer ${sessionStorage[SESSION_USER_TOKEN]}`
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
 * @param method
 * @param endpoint
 * @param params
 * @param data
 * @param upload
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export async function sendRequest({
    method = 'post',
    endpoint,
    params = {},
    data = {},
    upload = false,
}) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData();
    if (!apiRequestAuthData) {
        return false;
    }
    if (!validateToken()) {
        return false;
    }
    const urlBase = getSessionApiUrlBaseAction();
    if (!urlBase) {
        return false;
    }
    let headers = getAuthHeader();
    if (upload) {
        headers = {...headers, ...getUploadHeaders()};
    }
    const request = {
        method,
        url: `${urlBase}/${endpoint}`,
        headers,
        params: {
            ...apiRequestAuthData,
            params
        },
        data
    }
    return await apiRequest.request(request);
}

/**
 * Makes api fetch request
 * Returns false if headers are invalid
 *
 * @param endpoint
 * @param params
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export async function fetchRequest({endpoint, params = {}}) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData();
    if (!apiRequestAuthData) {
        return false;
    }
    if (!validateToken()) {
        return false;
    }
    const urlBase = getSessionApiUrlBaseAction();
    if (!urlBase) {
        return false;
    }
    const request = {
        method: "get",
        url: `${urlBase}/${endpoint}`,
        headers: getAuthHeader(),
        params: {
            ...apiRequestAuthData,
            params
        }
    }
    return await apiRequest.request(request);
}

/**
 * Makes token refresh api request
 * Returns false if headers are invalid
 *
 * @returns {boolean|Promise<AxiosResponse<any>>}
 */
export function validateToken() {
    const headers = getAuthHeader();
    if (!headers) {
        console.error('Header token incorrectly set');
        // handleUnauthorized();
        return false;
    }
    const isTokenValid = isLocalStorageTokenValid();    //Checks token is valid and not expired
    if (!isTokenValid) {
        console.error('Token expired');
        // handleUnauthorized();
        return false;
    }
    return true
}

function buildApiRequestJwtSecret({secretType, secretApp, sessionUserId, appName}) {
    let appSecret = null;
    if (typeof process.env.TR_NEWS_APP_SECRET !== 'undefined' && process.env.TR_NEWS_APP_SECRET !== '') {
        appSecret = process.env.TR_NEWS_APP_SECRET;
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

export function getApiRequestAuthData() {
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
        secretType: 'nonce',
        secretApp: 'react',
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
    return {payload: payloadJwt, user_id: sessionUserId};
}

export async function generateToken(tokenType) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData();
    if (!apiRequestAuthData) {
        return false;
    }
    const request = {
        method: "get",
        url: `/token/generate`,
        params: {...apiRequestAuthData, token_type: tokenType}
    }
    const getRequest = await apiRequest.request(request);
    if (handleTokenResponse(getRequest)) {
        return true;
    }
    return false;
}

export async function checkToken(tokenType) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData();
    if (!apiRequestAuthData) {
        return false;
    }
    if (!validateToken()) {
        return generateToken(tokenType);
    }
    const request = {
        method: "get",
        url: `/token/check`,
        headers: getAuthHeader(),
        params: apiRequestAuthData
    }
    try {
        const getRequest = await apiRequest.request(request);
        if (handleCheckResponse(getRequest)) {
            return true;
        }
    } catch (e) {
        return tokenRefresh(tokenType);
    }
}

export async function tokenRefresh(tokenType) {
    const apiRequest = getApiRequest();
    if (!apiRequest) {
        return false;
    }
    const apiRequestAuthData = getApiRequestAuthData();
    if (!apiRequestAuthData) {
        return false;
    }
    // if (!validateToken()) {
    //     return generateToken();
    // }
    const request = {
        method: "get",
        url: `/token/refresh`,
        headers: getAuthHeader(),
        params: {...apiRequestAuthData, token_type: tokenType}
    }
    const getRequest = await apiRequest.request(request);
    if (handleTokenResponse(getRequest)) {
        return true;
    }
    return false;
}

export function handleCheckResponse(result) {
    setSessionAuthenticatedAction(true);
    setIsAuthenticatingAction(false);
    return false;
}

export function handleTokenResponse(result) {
    const token = result?.data?.token;
    const expiresAt = result?.data?.expiresAt;
    if (token) {
        //Set authenticated local storage data
        setSessionLocalStorage({token, expiresAt})
        //Set authenticated redux session state
        setSessionState({token, expiresAt})
        return true;
    }
    return false;
}

export function handleUnauthorized() {
}


import {
    loadAxiosInterceptors,
} from "./state-middleware";
import {APP_CURRENT_APP_KEY, APP_NAME, APP_STATE} from "../redux/constants/app-constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../helpers/utils-helpers";
import {
    SESSION_API_BASE_URL, SESSION_API_URLS,
    SESSION_NONCE,
    SESSION_STATE,
    SESSION_USER,
    SESSION_USER_ID, SESSION_USER_TOKEN, SESSION_USER_TOKEN_EXPIRES_AT
} from "../redux/constants/session-constants";
import {getSignedJwt} from "../helpers/auth/jwt-helpers";
import {
    getSessionLocalStorage
} from "../redux/actions/session-actions";
import apiConfig from "./wp/config";

const axios = require("axios");
const sprintf = require('sprintf-js').sprintf

export class StateMiddleware {
    appState = {};
    sessionState = {};
    appUpdate = null;
    sessionUpdate = null;
    setAppState(appState) {
        this.appState = appState;
    }
    setSessionState(sessionState) {
        this.sessionState = sessionState;
    }
    getAppState() {
        return this.appState;
    }
    getSessionState() {
        return this.sessionState;
    }

    onSessionUpdate(callback) {
        this.sessionUpdate = callback;
    }
    onAppUpdate(callback) {
        this.appUpdate = callback;
    }

    getUploadHeaders() {
        return {
            'Content-Type': 'multipart/form-data',
        };
    }

    buildApiRequestJwtSecret({secretType, secretApp, sessionUserId, appName}) {
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

    getSessionApiUrlBaseAction(config = null) {
        const appState = this.appState;
        if (config?.id && isNotEmpty(appState?.api?.[config.id]?.['baseUrl'])) {
            return appState?.api?.[config.id]?.['baseUrl'];
        }
        const apiUrls = this.getSessionApiUrlsAction();
        if (
            !apiUrls ||
            typeof apiUrls[SESSION_API_BASE_URL] === 'undefined' ||
            !isNotEmpty(apiUrls[SESSION_API_BASE_URL])
        ) {
            console.error('Session state api base url is not set');
            return false;
        }
        return apiUrls[SESSION_API_BASE_URL];
    }

    getApiRequest() {
        const apiBaseUrl = this.getSessionApiUrlBaseAction();
        if (!apiBaseUrl) {
            return false;
        }
        const apiRequest = axios.create({
            baseURL: apiBaseUrl,
        });
        loadAxiosInterceptors(apiRequest);
        return apiRequest;
    }

    getAppKeyAction(config = null) {
        const appState = this.appState;
        if (config?.id && isNotEmpty(appState?.api?.[config.id]?.['app_key'])) {
            return appState?.api?.[config.id]?.['app_key'];
        }
        if (
            typeof appState[APP_CURRENT_APP_KEY] === 'undefined' ||
            !isNotEmpty(appState[APP_CURRENT_APP_KEY])
        ) {
            console.error('App key state is invalid');
            return false;
        }
        return appState[APP_CURRENT_APP_KEY];
    }

    getSessionNonceAction() {
        const sessionState = this.sessionState;
        if (
            typeof sessionState[SESSION_NONCE] === 'undefined' ||
            !isNotEmpty(sessionState[SESSION_NONCE])
        ) {
            console.error('Session nonce state is invalid');
            return false;
        }
        return sessionState[SESSION_NONCE];
    }

    getSessionUserIdAction() {
        const sessionState = this.sessionState;
        if (
            typeof sessionState[SESSION_USER][SESSION_USER_ID] === 'undefined' ||
            !isNotEmpty(sessionState[SESSION_USER][SESSION_USER_ID])
        ) {
            console.error('Session user id state is invalid');
            return false;
        }
        return sessionState[SESSION_USER][SESSION_USER_ID];
    }
    getAppNameAction(appName) {
        const appState = this.appState;
        if (
            typeof appState[APP_NAME] === 'undefined' ||
            !isNotEmpty(appState[APP_NAME])
        ) {
            console.error('App name state is invalid');
            return false;
        }
        return appState[APP_NAME];
    }
    getApiRequestAuthData(config, appKey) {
        if (!config?.wpRequest) {
            return {};
        }
        const sessionNonce = this.getSessionNonceAction();
        const sessionUserId = this.getSessionUserIdAction();
        const appName = this.getAppNameAction();
        if (!sessionNonce) {
            return false;
        }
        if (!sessionUserId) {
            return false;
        }
        if (!appName) {
            return false;
        }
        const payloadSecret = this.buildApiRequestJwtSecret({
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


    getSessionApiUrlsAction() {
        const sessionState = this.sessionState;
        console.log('getSessionApiUrlsAction', sessionState)
        if (
            typeof sessionState[SESSION_API_URLS] === 'undefined' ||
            !isNotEmpty(sessionState[SESSION_API_URLS]) ||
            !isObject(sessionState[SESSION_API_URLS]) ||
            isObjectEmpty(sessionState[SESSION_API_URLS])
        ) {
            console.error('Session api urls state is invalid');
            return false;
        }
        return sessionState[SESSION_API_URLS];
    }


    isLocalStorageTokenValid(appKey) {
        const getLocalStorage = getSessionLocalStorage(appKey);
        if (typeof getLocalStorage[SESSION_USER_TOKEN] === 'undefined' || typeof getLocalStorage[SESSION_USER_TOKEN_EXPIRES_AT] === 'undefined') {
            return false;
        }

        return Date.now() < getLocalStorage[SESSION_USER_TOKEN_EXPIRES_AT];
    }
    validateToken(appKey) {
        const isTokenValid = this.isLocalStorageTokenValid(appKey);    //Checks token is valid and not expired
        if (!isTokenValid) {
            console.error('Token expired');
            // handleUnauthorized();
            return false;
        }
        return true
    }
    getAuthHeader(appKey) {
        const appState = this.appState;
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
            const sessionStore = this.sessionState;
            // console.log({sessionStore})
            if (!isNotEmpty(sessionStore?.user?.token)) {
                return false;
            }
            token = sessionStore.user.token;
        } else {
            token = sessionStorage[SESSION_USER_TOKEN];
            if (!this.validateToken(appKey)) {
                return false;
            }
        }
        return {
            'Authorization': `Bearer ${token}`
        };
    }
    async generateToken({appKey, config = apiConfig}) {
        const apiRequest = this.getApiRequest();
        if (!apiRequest) {
            return false;
        }
        const apiRequestAuthData = this.getApiRequestAuthData(config, appKey);
        if (!apiRequestAuthData) {
            return false;
        }
        const request = {
            method: "get",
            url: `/token/generate`,
            params: {...apiRequestAuthData, app_key: appKey}
        }
        const getRequest = await apiRequest.request(request);
        if (this.handleTokenResponse({appKey, config, result: getRequest})) {
            return true;
        }
        return false;
    }
    async checkToken({config}) {
        const appKey = this.getAppKeyAction();
        const apiRequest = this.getApiRequest();
        if (!apiRequest) {
            return false;
        }
        const apiRequestAuthData = this.getApiRequestAuthData(config, appKey);
        if (!apiRequestAuthData) {
            return false;
        }
        let headers = this.getAuthHeader(appKey);
        if (!headers && config?.wpRequest) {
            return this.generateToken({appKey, config});
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
                        this.setSessionAuthenticatedAction(true);
                        this.setIsAuthenticatingAction(false);
                        break;
                    default:
                        const sessionStorage = getSessionLocalStorage(appKey);

                        if (this.validateSessionUpdate()) {
                            const convertToUnixTimestamp = sessionStorage[SESSION_USER_TOKEN_EXPIRES_AT] * 1000;
                            this.sessionUpdate({
                                authenticated: true,
                                isAuthenticating: false,
                                token: sessionStorage[SESSION_USER_TOKEN],
                                expiresAt: convertToUnixTimestamp,
                            });
                        }
                        break;
                }

                return true;
            }
            return false;
        } catch (e) {
            console.error(e)
            if (config?.wpRequest) {
                return this.tokenRefresh({appKey, config});
            }
        }
    }

    validateSessionUpdate() {
        if (typeof this.sessionUpdate !== 'function') {
            console.warn('Session update callback is not set');
            return false;
        }
        return true;
    }
    setAuthenticatedSession({token, expiresAt}) {
        // setSessionAuthenticatedAction(true);
        // setIsAuthenticatingAction(false);
        // setSessionUserTokenAction(token);
        // setSessionUserTokenExpiresAtAction(convertToUnixTimestamp);
    }
    setSessionAuthenticatedAction(authenticated) {
        if (this.validateSessionUpdate()) {
            this.sessionUpdate({authenticated});
        }
        // store.dispatch(setSessionAuthenticated(authenticated));
    }

    /**
     * Sets isAuthenticating session redux state
     * @param isAuthenticating
     */
    setIsAuthenticatingAction(isAuthenticating) {
        if (this.validateSessionUpdate()) {
            this.sessionUpdate({isAuthenticating});
        }
        // store.dispatch(setSessionIsAuthenticating(isAuthenticating));
    }
    async tokenRefresh({appKey, config = apiConfig}) {
        const apiRequest = this.getApiRequest();
        if (!apiRequest) {
            return false;
        }
        const apiRequestAuthData = this.getApiRequestAuthData(config, appKey);
        if (!apiRequestAuthData) {
            return false;
        }
        // if (!validateToken()) {
        //     return generateToken();
        // }
        const request = {
            method: "get",
            url: `/token/refresh`,
            headers: this.getAuthHeader(appKey),
            params: {...apiRequestAuthData, app_key: appKey}
        }
        const getRequest = await apiRequest.request(request);
        if (this.handleTokenResponse({appKey, config, result: getRequest})) {
            return true;
        }
        return false;
    }

     handleTokenResponse({result, config, appKey}) {
        if (typeof config?.tokenResponseHandler !== 'function') {
            return false;
        }
        return config.tokenResponseHandler(result, appKey);
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
     async fetchRequest({ config, endpoint, params = {}}) {
        let requestParams = params;
        const apiRequest = this.getApiRequest();
        if (!apiRequest) {
            return false;
        }
        const appKey = this.getAppKeyAction(config);
        if (config?.wpRequest) {
            const apiRequestAuthData = this.getApiRequestAuthData(config, appKey);
            if (!apiRequestAuthData) {
                return false;
            }
            requestParams = {...requestParams, ...apiRequestAuthData};
        }
        const urlBase = this.getSessionApiUrlBaseAction(config);
        console.log({urlBase})
        if (!urlBase) {
            return false;
        }
        let headers = this.getAuthHeader(appKey);

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

    async sendRequest({
        config = apiConfig,
        method = 'post',
        endpoint,
        params = {},
        data = {},
        upload = false,
    }) {
        let requestParams = params;
        const apiRequest = this.getApiRequest();
        if (!apiRequest) {
            return false;
        }
        const appKey = this.getAppKeyAction();
        if (config?.wpRequest) {
            const apiRequestAuthData = this.getApiRequestAuthData(config, appKey);
            if (!apiRequestAuthData) {
                return false;
            }
            requestParams = {...requestParams, ...apiRequestAuthData};
        }
        const urlBase = this.getSessionApiUrlBaseAction();
        if (!urlBase) {
            return false;
        }
        let headers = this.getAuthHeader(appKey);
        if (!headers && config?.wpRequest) {
            return false;
        }
        if (upload) {
            headers = {...headers, ...this.getUploadHeaders()};
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
}

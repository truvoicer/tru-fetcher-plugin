import store from "../store"
import React from "react";
import {
    setSessionHasLoaded,
    setSessionUserToken,
    setSessionApiUrlBase,
    setSessionNonce,
    setSessionUserId,
    setSessionIsAuthenticating,
    setSessionAuthenticated,
    setSessionUserTokenExpiresAt,
    setSessionRefreshCount
} from "../reducers/session-reducer";
import {
    SESSION_USER_TOKEN, SESSION_API_BASE_URL, SESSION_USER_TOKEN_EXPIRES_AT, SESSION_API_URLS,
    SESSION_STATE,
    SESSION_NONCE, SESSION_USER, SESSION_USER_ID, SESSION_REFRESH_COUNT,
} from "../constants/session-constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../helpers/utils-helpers";
import {APP_STATE} from "../constants/app-constants";

/**
 * Sets userToken session redux state
 * @param hasLoaded
 */
export function setSessionHasLoadedAction(hasLoaded) {
    store.dispatch(setSessionHasLoaded(hasLoaded));
}
export function setSessionUserTokenAction(token) {
    store.dispatch(setSessionUserToken(token));
}

export function setSessionUserTokenExpiresAtAction(expiresAt) {
    store.dispatch(setSessionUserTokenExpiresAt(expiresAt));
}

export function setSessionUserIdAction(userId) {
    store.dispatch(setSessionUserId(userId));
}

export function setSessionNonceAction(nonce) {
    store.dispatch(setSessionNonce(nonce));
}

export function setSessionApiUrlBaseAction(apiUrlBase) {
    store.dispatch(setSessionApiUrlBase(apiUrlBase));
}

export function addToSessionRefreshCountAction() {
    const sessionRefreshCount = store.getState()[SESSION_STATE][SESSION_REFRESH_COUNT];
    store.dispatch(setSessionRefreshCount(sessionRefreshCount + 1));
}

export function getSessionRefreshCountAction() {
    return store.getState()[SESSION_STATE][SESSION_REFRESH_COUNT];
}

export function getSessionNonceAction() {
    const sessionState = store.getState()[SESSION_STATE];
    if (
        typeof sessionState[SESSION_NONCE] === 'undefined' ||
        !isNotEmpty(sessionState[SESSION_NONCE])
    ) {
        console.error('Session nonce state is invalid');
        return false;
    }
    return sessionState[SESSION_NONCE];
}

export function getSessionUserIdAction() {
    const sessionState = store.getState()[SESSION_STATE];
    if (
        typeof sessionState[SESSION_USER][SESSION_USER_ID] === 'undefined' ||
        !isNotEmpty(sessionState[SESSION_USER][SESSION_USER_ID])
    ) {
        console.error('Session user id state is invalid');
        return false;
    }
    return sessionState[SESSION_USER][SESSION_USER_ID];
}

export function getSessionApiUrlBaseAction(config = null) {
    const appState = store.getState()[APP_STATE];
    if (config?.id && isNotEmpty(appState?.api?.[config.id]?.['baseUrl'])) {
        return appState?.api?.[config.id]?.['baseUrl'];
    }
    const apiUrls = getSessionApiUrlsAction();
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

export function getSessionApiUrlsAction() {
    const sessionState = store.getState()[SESSION_STATE];
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

/**
 * Sets authenticated session redux state
 * @param authenticated
 */
export function setSessionAuthenticatedAction(authenticated) {
    store.dispatch(setSessionAuthenticated(authenticated));
}

/**
 * Sets isAuthenticating session redux state
 * @param isAuthenticating
 */
export function setIsAuthenticatingAction(isAuthenticating) {
    store.dispatch(setSessionIsAuthenticating(isAuthenticating));
}

/**
 * Sets session redux state on successful authentication
 * @param config
 */
export function setInitialSessionState(config) {
    if (config?.nonce) {
        setSessionNonceAction(config.nonce);
    }
    if (config?.baseUrl) {
        setSessionApiUrlBaseAction(config.baseUrl);
    }
    if (config?.token) {
        console.log('setInitialSessionState', config?.token)
        setSessionUserTokenAction(config?.token);
    }
    if (tru_fetcher_react?.user?.id) {
        setSessionUserIdAction(tru_fetcher_react.user.id);
    }
    return true;
}

/**
 * Sets session redux state on successful authentication
 * @param token
 * @param expiresAt
 */
export function setSessionState({token, expiresAt}) {
    const convertToUnixTimestamp = expiresAt * 1000;
    setSessionAuthenticatedAction(true);
    setIsAuthenticatingAction(false);
    setSessionUserTokenAction(token);
    setSessionUserTokenExpiresAtAction(convertToUnixTimestamp);
}

/**
 * Sets local storage items on successful login or token request
 * @param token
 * @param expires_at
 */
export function setSessionLocalStorage({token, expiresAt, appKey}) {
    const convertToUnixTimestamp = JSON.stringify(expiresAt * 1000);
    localStorage.setItem(`${appKey}_${SESSION_USER_TOKEN}`, token);
    localStorage.setItem(`${appKey}_${SESSION_USER_TOKEN_EXPIRES_AT}`, convertToUnixTimestamp);
    // navigate to the home route
}

/**
 * Resets local session storage
 */
export function removeLocalSession() {
    // Clear access token and ID token from local storage
    localStorage.removeItem(SESSION_USER_TOKEN);
    localStorage.removeItem(SESSION_USER_TOKEN_EXPIRES_AT);
}

/**
 * CHecks if token is past expiry date
 * @returns {boolean}
 */
export function isLocalStorageTokenValid(appKey) {
    const getLocalStorage = getSessionLocalStorage(appKey);
    if (typeof getLocalStorage[SESSION_USER_TOKEN] === 'undefined' || typeof getLocalStorage[SESSION_USER_TOKEN_EXPIRES_AT] === 'undefined') {
        return false;
    }

    return Date.now() < getLocalStorage[SESSION_USER_TOKEN_EXPIRES_AT];
}

/**
 * Returns local session storage object
 * @returns {{access_token: string, expires_at: any}}
 */
export function getSessionLocalStorage(appKey) {
    return {
        [SESSION_USER_TOKEN]: localStorage.getItem(`${appKey}_${SESSION_USER_TOKEN}`),
        [SESSION_USER_TOKEN_EXPIRES_AT]: JSON.parse(localStorage.getItem(`${appKey}_${SESSION_USER_TOKEN_EXPIRES_AT}`))
    }
}

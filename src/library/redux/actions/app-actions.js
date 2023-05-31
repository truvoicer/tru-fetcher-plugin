import store from "../store"
import React from "react";
import {
    setAppHasLoaded, setAppName, setActiveMenuItem
} from "../reducers/app-reducer";
import {setSessionApiUrlBaseAction, setSessionNonceAction, setSessionUserIdAction} from "./session-actions";
import {SESSION_API_URLS, SESSION_STATE} from "../constants/session-constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../helpers/utils-helpers";
import {APP_NAME, APP_STATE} from "../constants/app-constants";

/**
 * Sets appHasLoaded state
 * @param appHasLoaded
 */
export function setAppHasLoadedAction(appHasLoaded) {
    store.dispatch(setAppHasLoaded(appHasLoaded));
}

export function setAppNameAction(appName) {
    store.dispatch(setAppName(appName));
}

export function setActiveMenuItemAction(activeMenuItem) {
    store.dispatch(setActiveMenuItem(activeMenuItem));
}

export function getAppNameAction(appName) {
    const appState = store.getState()[APP_STATE];
    if (
        typeof appState[APP_NAME] === 'undefined' ||
        !isNotEmpty(appState[APP_NAME])
    ) {
        console.error('App name state is invalid');
        return false;
    }
    return appState[APP_NAME];
}

/**
 * Sets session redux state on successful authentication
 * @param token
 */
export function setInitialAppState() {
    if (tr_news_app_react?.apiConfig?.app_name) {
        setAppNameAction(tr_news_app_react.apiConfig.app_name);
    }
    return true;
}

import store from "../store"
import React from "react";
import {
    setAppHasLoaded,
    setAppName,
    setActiveMenuItem,
    setAppCurrentScreen,
    setAppApi,
    setAppMetaFields,
    setAppCurrentAppKey
} from "../reducers/app-reducer";
import {setSessionApiUrlBaseAction, setSessionNonceAction, setSessionUserIdAction} from "./session-actions";
import {SESSION_API_URLS, SESSION_STATE} from "../constants/session-constants";
import {isNotEmpty, isObject, isObjectEmpty} from "../../helpers/utils-helpers";
import {APP_API, APP_CURRENT_APP_KEY, APP_NAME, APP_STATE} from "../constants/app-constants";

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

export function setAppCurrentScreenAction(currentScreen) {
    store.dispatch(setAppCurrentScreen(currentScreen));
}
export function setAppCurrentAppKeyAction(appKey) {
    store.dispatch(setAppCurrentAppKey(appKey));
}
export function setAppApiAction(apiConfig) {
    store.dispatch(setAppApi(apiConfig));
}
export function setAppMetaFieldsAction(metaFields) {
    store.dispatch(setAppMetaFields(metaFields));
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

export function getAppApiAction() {
    const apiConfigs = store.getState()[APP_STATE][APP_API];
    if (!isObject(apiConfigs) || isObjectEmpty(apiConfigs)) {
        console.error('App api state is invalid');
        return false;
    }
    return apiConfigs;
}

export function getAppKeyAction() {
    const appState = store.getState()[APP_STATE];
    if (
        typeof appState[APP_CURRENT_APP_KEY] === 'undefined' ||
        !isNotEmpty(appState[APP_CURRENT_APP_KEY])
    ) {
        console.error('App key state is invalid');
        return false;
    }
    return appState[APP_CURRENT_APP_KEY];
}
export function getCurrentApiConfigAction() {
    const appKey = getAppKeyAction();
    if (!appKey) {
        return false;
    }
    const apiConfigs = getAppApiAction();
    if (!apiConfigs) {
        return false;
    }
    const findApiConfigKey = Object.keys(apiConfigs).find((key) => {
        if (typeof apiConfigs[key]['app_key'] === 'undefined') {
            return false;
        }
        return (apiConfigs[key]['app_key'] === appKey);
    });
    if (!findApiConfigKey) {
        console.error(`App api config for app key: ${appKey} is invalid`);
        return false;
    }

    return apiConfigs[findApiConfigKey];
}

/**
 * Sets session redux state on successful authentication
 * @param token
 */
export function setInitialAppState(apiConfig) {
    if (tru_fetcher_react?.app_name) {
        setAppNameAction(tru_fetcher_react.app_name);
    }
    if (tru_fetcher_react?.currentScreen) {
        setAppCurrentScreenAction(tru_fetcher_react.currentScreen);
    }
    if (apiConfig?.app_key) {
        setAppCurrentAppKeyAction(apiConfig.app_key);
    }
    if (tru_fetcher_react?.api) {
        setAppApiAction(tru_fetcher_react.api);
    }
    if (tru_fetcher_react?.meta?.metaFields) {
        setAppMetaFieldsAction(tru_fetcher_react.meta.metaFields);
    }
    return true;
}

// AUTH STATE
import {createSlice} from "@reduxjs/toolkit";
import {
    APP_ACTIVE_MENU_ITEM, APP_API, APP_CURRENT_APP_KEY,
    APP_CURRENT_SCREEN,
    APP_HAS_LOADED, APP_META, APP_META_META_BOXES, APP_META_META_FIELDS,
    APP_NAME,
    APP_STATE
} from "../constants/app-constants";

const defaultState = {
    [APP_HAS_LOADED]: false,
    [APP_NAME]: null,
    [APP_ACTIVE_MENU_ITEM]: 'dashboard',
    [APP_CURRENT_SCREEN]: {},
    [APP_CURRENT_APP_KEY]: null,
    [APP_API]: {},
    [APP_META]: {
        [APP_META_META_FIELDS]: [],
        [APP_META_META_BOXES]: []
    }
};
const defaultReducers = {
    setAppHasLoaded: (state, action) => {
        state[APP_HAS_LOADED] = action.payload;
    },
    setAppName: (state, action) => {
        state[APP_NAME] = action.payload;
    },
    setActiveMenuItem: (state, action) => {
        state[APP_ACTIVE_MENU_ITEM] = action.payload;
    },
    setAppCurrentScreen: (state, action) => {
        state[APP_CURRENT_SCREEN] = action.payload;
    },
    setAppCurrentAppKey: (state, action) => {
        state[APP_CURRENT_APP_KEY] = action.payload;
    },
    setAppApi: (state, action) => {
        state[APP_API] = action.payload;
    },
    setAppMetaFields: (state, action) => {
        state[APP_META][APP_META_META_FIELDS] = action.payload;
    },
    setAppMetaBoxes: (state, action) => {
        state[APP_META][APP_META_META_BOXES] = action.payload;
    },
};

export const appSlice = createSlice({
    name: APP_STATE,
    initialState: defaultState,
    reducers: defaultReducers,
});

export const appReducer = appSlice.reducer;
export const {
    setAppHasLoaded,
    setAppName,
    setActiveMenuItem,
    setAppCurrentScreen,
    setAppCurrentAppKey,
    setAppApi,
    setAppMetaFields,
    setAppMetaBoxes
} = appSlice.actions;

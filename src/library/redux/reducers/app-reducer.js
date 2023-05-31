// AUTH STATE
import {createSlice} from "@reduxjs/toolkit";
import {APP_ACTIVE_MENU_ITEM, APP_HAS_LOADED, APP_NAME, APP_STATE} from "../constants/app-constants";

const defaultState = {
    [APP_HAS_LOADED]: false,
    [APP_NAME]: null,
    [APP_ACTIVE_MENU_ITEM]: 'dashboard',
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
};

export const appSlice = createSlice({
    name: APP_STATE,
    initialState: defaultState,
    reducers: defaultReducers,
});

export const appReducer = appSlice.reducer;
export const {setAppHasLoaded, setAppName, setActiveMenuItem} = appSlice.actions;

// AUTH STATE
import {createSlice} from "@reduxjs/toolkit";
import {
    SESSION_API_BASE_URL,
    SESSION_API_URLS,
    SESSION_AUTHENTICATED, SESSION_HAS_LOADED,
    SESSION_IS_AUTHENTICATING, SESSION_NONCE,
    SESSION_REDIRECT_URL, SESSION_REFRESH_COUNT, SESSION_REFRESH_LIMIT,
    SESSION_STATE,
    SESSION_USER, SESSION_USER_ID,
    SESSION_USER_TOKEN, SESSION_USER_TOKEN_EXPIRES_AT
} from "../constants/session-constants";

const defaultState = {
    [SESSION_HAS_LOADED]: false,
    [SESSION_USER]: {
        [SESSION_USER_TOKEN]: null,
        [SESSION_USER_TOKEN_EXPIRES_AT]: null,
        [SESSION_USER_ID]: null,
    },
    [SESSION_NONCE]: null,
    [SESSION_API_URLS]: {
        [SESSION_API_BASE_URL]: null,
    },
    [SESSION_AUTHENTICATED]: false,
    [SESSION_IS_AUTHENTICATING]: true,
    [SESSION_REDIRECT_URL]: null,
    [SESSION_REFRESH_COUNT]: 0,
};

const defaultReducers = {
    setSessionHasLoaded: (state, action) => {
        state[SESSION_HAS_LOADED] = action.payload;
    },
    setSessionUserToken: (state, action) => {
        state[SESSION_USER][SESSION_USER_TOKEN] = action.payload;
    },
    setSessionUserTokenExpiresAt: (state, action) => {
        state[SESSION_USER][SESSION_USER_TOKEN_EXPIRES_AT] = action.payload;
    },
    setSessionUserId: (state, action) => {
        state[SESSION_USER][SESSION_USER_ID] = action.payload;
    },
    setSessionAuthenticated: (state, action) => {
        state[SESSION_AUTHENTICATED] = action.payload;
    },
    setSessionIsAuthenticating: (state, action) => {
        state[SESSION_IS_AUTHENTICATING] = action.payload;
    },
    setSessionRedirectUrl: (state, action) => {
        state[SESSION_REDIRECT_URL] = action.payload;
    },
    setSessionNonce: (state, action) => {
        state[SESSION_NONCE] = action.payload;
    },
    setSessionApiUrlBase: (state, action) => {
        state[SESSION_API_URLS][SESSION_API_BASE_URL] = action.payload;
    },
    setSessionApiUrls: (state, action) => {
        state[SESSION_API_URLS] = action.payload;
    },
    setSessionRefreshCount: (state, action) => {
        state[SESSION_REFRESH_COUNT] = action.payload;
    },
};

export const sessionSlice = createSlice({
    name: SESSION_STATE,
    initialState: defaultState,
    reducers: defaultReducers,
});

export const sessionReducer = sessionSlice.reducer;

export const {
    setSessionHasLoaded,
    setSessionUserToken,
    setSessionUserTokenExpiresAt,
    setSessionUserId,
    setSessionNonce,
    setSessionApiUrls,
    setSessionApiUrlBase,
    setSessionAuthenticated,
    setSessionIsAuthenticating,
    setSessionRefreshCount
} = sessionSlice.actions;

import {
    configureStore,
} from "@reduxjs/toolkit";

import {sessionReducer} from "../reducers/session-reducer";
import {SESSION_STATE} from "../constants/session-constants";
import {appReducer} from "../reducers/app-reducer";
import {APP_STATE} from "../constants/app-constants";

const store = configureStore({
    reducer: {
        [SESSION_STATE]: sessionReducer,
        [APP_STATE]: appReducer,
    },
});

export default store;

import React, {useEffect} from 'react';
import {
    setInitialSessionState, setIsAuthenticatingAction,
    setSessionAuthenticatedAction,
    setSessionHasLoadedAction, setSessionUserTokenAction, setSessionUserTokenExpiresAtAction
} from "../../library/redux/actions/session-actions";
import {
    SESSION_AUTHENTICATED, SESSION_HAS_LOADED,
    SESSION_IS_AUTHENTICATING,
    SESSION_STATE, SESSION_USER_TOKEN
} from "../../library/redux/constants/session-constants";
import {APP_HAS_LOADED, APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from "react-redux";
import {
    getAppKeyAction,
    getCurrentApiConfigAction,
    setAppHasLoadedAction,
    setInitialAppState
} from "../../library/redux/actions/app-actions";
import {setSessionIsAuthenticating} from "../../library/redux/reducers/session-reducer";
import Loader from "../Loader";
import fetcherApiConfig from "../../library/api/fetcher-api/fetcherApiConfig";
import {getApiRequestConfig} from "../../library/helpers/request-helpers";
import {StateMiddleware} from "../../library/api/StateMiddleware";

function Auth({children, app, session}) {
    const stateMiddleware = new StateMiddleware();
    stateMiddleware.setAppState(app);
    stateMiddleware.setSessionState(session);
    stateMiddleware.onSessionUpdate((sessionData) => {
        if (sessionData.hasOwnProperty('authenticated')) {
            setSessionAuthenticatedAction(sessionData.authenticated);
        }
        if (sessionData.hasOwnProperty('isAuthenticating')) {
            setIsAuthenticatingAction(sessionData.isAuthenticating);
        }
        if (sessionData.hasOwnProperty('token')) {
            setSessionUserTokenAction(sessionData.token);
        }
        if (sessionData.hasOwnProperty('expiresAt')) {
            setSessionUserTokenExpiresAtAction(sessionData.expiresAt);
        }
    })

    async function validateSession() {
        setSessionIsAuthenticating(true);
        const appKey = stateMiddleware.getAppKeyAction();
        const apiConfig = getApiRequestConfig(appKey)
        if (!apiConfig) {
            return;
        }
        const checkTokenResults = await stateMiddleware.checkToken({config: apiConfig});
        if (!checkTokenResults) {
            return;
        }

    }

    useEffect(() => {
        const config = getCurrentApiConfigAction();
        if (setInitialSessionState(config)) {
            setSessionHasLoadedAction(true)
        }
    }, []);

    useEffect(() => {
        if (!session[SESSION_HAS_LOADED]) {
            return;
        }
        validateSession()
    }, [session[SESSION_HAS_LOADED]])

    return (
        <>
            {(
                session[SESSION_HAS_LOADED] &&
                !session[SESSION_IS_AUTHENTICATING] &&
                session[SESSION_AUTHENTICATED]
            )
                ?
                children
                :
                <Loader />
            }
        </>
    );
};

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(Auth);

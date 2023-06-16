import React, {useEffect} from 'react';
import {setInitialSessionState, setSessionHasLoadedAction} from "../../library/redux/actions/session-actions";
import {
    SESSION_AUTHENTICATED, SESSION_HAS_LOADED,
    SESSION_IS_AUTHENTICATING,
    SESSION_STATE
} from "../../library/redux/constants/session-constants";
import {APP_HAS_LOADED, APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from "react-redux";
import {checkToken, loadAxiosInterceptors} from "../../library/api/state-middleware";
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

function Auth({children, app, session}) {

    async function validateSession() {
        setSessionIsAuthenticating(true);
        const appKey = getAppKeyAction();
        const apiConfig = getApiRequestConfig(appKey)
        if (!apiConfig) {
            return;
        }
        const checkTokenResults = await checkToken({config: apiConfig});
        if (!checkTokenResults) {
            return;
        }

    }

    useEffect(() => {
        const config = getCurrentApiConfigAction();
        console.log(config)
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

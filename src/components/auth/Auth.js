import React, {useEffect} from 'react';
import {setInitialSessionState} from "../../library/redux/actions/session-actions";
import {
    SESSION_AUTHENTICATED,
    SESSION_IS_AUTHENTICATING,
    SESSION_STATE
} from "../../library/redux/constants/session-constants";
import {APP_HAS_LOADED, APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from "react-redux";
import {checkToken, loadAxiosInterceptors} from "../../library/api/middleware";
import {setAppHasLoadedAction, setInitialAppState} from "../../library/redux/actions/app-actions";
import {setSessionIsAuthenticating} from "../../library/redux/reducers/session-reducer";
import Loader from "../Loader";
import fetcherApiConfig from "../../library/api/fetcher-api/fetcherApiConfig";

function Auth({children, app, session, config}) {

    async function validateSession() {
        setSessionIsAuthenticating(true);
        const checkTokenResults = await checkToken({tokenType: 'react', config: fetcherApiConfig});
        if (!checkTokenResults) {
            return;
        }
    }

    function checkAuth() {

    }

    useEffect(() => {
        const setInitialSession = setInitialSessionState(config);
        const setInitialApp = setInitialAppState(config);
        if (setInitialSession && setInitialApp) {
            setAppHasLoadedAction(true)
        }
    }, []);

    useEffect(() => {
        if (!app[APP_HAS_LOADED]) {
            return;
        }
        validateSession()
    }, [app[APP_HAS_LOADED]])
    return (
        <>
            {(
                app[APP_HAS_LOADED] &&
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

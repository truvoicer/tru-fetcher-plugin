import React, {useEffect} from 'react';
import {connect} from "react-redux";
import {APP_HAS_LOADED, APP_STATE} from "./library/redux/constants/app-constants";
import {setAppHasLoadedAction, setInitialAppState} from "./library/redux/actions/app-actions";
import Loader from "./components/Loader";


const AppLoader = ({children, app, apiConfig}) => {
    useEffect(() => {
        if (setInitialAppState(apiConfig)) {
            setAppHasLoadedAction(true)
        }
    }, []);
    return (
        <>
            {app[APP_HAS_LOADED]
                ?
                children
                :
                <Loader />
            }
        </>
    );
}
export default connect(
    (state) => {
        return {
            app: state[APP_STATE]
        }
    },
    null
)(AppLoader);

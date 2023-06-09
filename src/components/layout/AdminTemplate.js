import React from 'react'
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from 'react-redux';
import MainMenu from "./MainMenu";

function AdminTemplate({app, session, children}) {
    return (
        <>
            <MainMenu />
            <div className={'tr-news-app__content__admin'}>
                {children}
            </div>
        </>
    );
}

export default connect(
    (state) => {
        return {
            app: state[APP_STATE],
            session: state[SESSION_STATE],
        }
    },
    null
)(AdminTemplate);

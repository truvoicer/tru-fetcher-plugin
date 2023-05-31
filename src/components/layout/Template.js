import React from 'react'
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from 'react-redux';
import MainMenu from "./MainMenu";
import {Container, Divider} from 'semantic-ui-react'

function Template({app, session, children}) {
    return (
        <Container fluid>
            <div className={'tr-news-app__header'}>
                <h1>Tr News App</h1>
            </div>
            <div className={'tr-news-app__content'}>
                {children}
            </div>
        </Container>
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
)(Template);

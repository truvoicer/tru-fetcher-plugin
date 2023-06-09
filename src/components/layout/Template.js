import React from 'react'
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from 'react-redux';
import {Layout} from 'antd'
const { Header, Footer, Content } = Layout;
function Template({app, session, children}) {
    return (
        <Layout>
            <Header className={'tr-news-app__header'}>
                <h1>Tr News App</h1>
            </Header>
            <Content  className={'tr-news-app__content'}>
                {children}
            </Content >
        </Layout>
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

import React from 'react'
import {SESSION_STATE} from "../../library/redux/constants/session-constants";
import {APP_STATE} from "../../library/redux/constants/app-constants";
import {connect} from 'react-redux';
import {Layout} from 'antd'
const { Header, Footer, Content } = Layout;
function Template({app, session, children}) {
    const headerStyle = {
        textAlign: 'center',
        color: '#fff',
        height: 64,
        paddingInline: 48,
        lineHeight: '64px',
        backgroundColor: '#4096ff',
    };
    const contentStyle = {
        textAlign: 'center',
        minHeight: 120,
        lineHeight: '120px',
    };
    const siderStyle = {
        textAlign: 'center',
        lineHeight: '120px',
        color: '#fff',
        backgroundColor: '#1677ff',
    };
    const footerStyle = {
        textAlign: 'center',
        color: '#fff',
        backgroundColor: '#4096ff',
    };
    const layoutStyle = {
        borderRadius: 8,
        overflow: 'hidden',
        width: '100%'
    };
    return (
        <Layout style={layoutStyle}>
            <Header style={headerStyle} className={'tr-news-app__header'}>
                <h1>Tr News App</h1>
            </Header>
            <Content  style={contentStyle} className={'tr-news-app__content'}>
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

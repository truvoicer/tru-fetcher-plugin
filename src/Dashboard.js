import React, {useEffect} from 'react'
import {SESSION_STATE} from "./library/redux/constants/session-constants";
import {APP_ACTIVE_MENU_ITEM, APP_STATE} from "./library/redux/constants/app-constants";
import {connect} from 'react-redux';
import {useMatches, useNavigate, useParams, useRouteLoaderData} from "react-router-dom";
import {getSessionNonceAction, getSessionUserIdAction} from "./library/redux/actions/session-actions";
import {getAppNameAction} from "./library/redux/actions/app-actions";
import {getSignedJwt} from "./library/helpers/auth/jwt-helpers";

// export function getJwt() {
//     const payloadSecret = '!6rhHzO3H18C%GVdlede@Zwf';
//     const payloadJwt = getSignedJwt({
//         secret: payloadSecret,
//         payload: {
//             type: 'app',
//         }
//     })
//     return payloadJwt;
// }
const Dashboard = ({app, session}) => {
    return (
        <>
            Dash
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
)(Dashboard);

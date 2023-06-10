//Api config and endpoints
import {setSessionLocalStorage, setSessionState} from "../../redux/actions/session-actions";

export default {
    wpRequest: false,
    endpoints: {
        login: '/login',
        checkToken: '/token/login',
        tokenRefresh: '/token/refresh',
        serviceList: '/service/list',
        serviceResponseKeyList: '/service/response/key/list',
        currencyConvert: '/currency/convert',
    },
    tokenResponseHandler: (results, appKey) => {
        const token = results?.data?.data?.session?.access_token;
        const expiresAt = results?.data?.data?.session?.expires_at;
        if (token) {
            //Set authenticated local storage data
            setSessionLocalStorage({token, expiresAt, appKey})
            //Set authenticated redux session state
            setSessionState({token, expiresAt})
            return true;
        }
        return false;
    },
    tokenRefreshLimit: 1,
    tokenRefreshCount: 0
}

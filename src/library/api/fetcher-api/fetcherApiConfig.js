//Api config and endpoints
import {setSessionLocalStorage, setSessionState} from "../../redux/actions/session-actions";

export default {
    id: 'tru_fetcher',
    wpRequest: false,
    endpoints: {
        login: '/login',
        checkToken: '/backend/auth/token/user',
        tokenRefresh: '/backend/session/api-token/generate',
        service: '/backend/service',
        categories: '/backend/category/list',
        serviceResponseKeyList: '/backend/service/response-key/list',
    },
    tokenResponseHandler: (results, appKey) => {
        console.log('tokenResponseHandler', {results, appKey})
        const token = results?.data?.data?.session?.access_token;
        const expiresAt = results?.data?.data?.session?.expires_at;
        if (token) {
            console.log('tokenResponseHandler', {token, expiresAt, appKey})
            //Set authenticated local storage data
            setSessionLocalStorage({token, expiresAt, appKey})
            //Set authenticated redux session state
            setSessionState({token, expiresAt})
            return true;
        }
        return false;
    },
    tokenRefreshLimit: 1,
    tokenRefreshCount: 0,
    tokenSource: 'env',
}

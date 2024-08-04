//Api config and endpoints
import {setSessionLocalStorage, setSessionState} from "../../redux/actions/session-actions";

export default {
    id: 'wp',
    wpRequest: true,
    endpoints: {
        login: '/login',
        checkToken: '/token/check',
        tokenRefresh: '/token/refresh',
        posts: '/posts',
        settings: '/settings',
        formPresets: '/form/presets',
        tabPresets: '/tab/presets',
        listings: '/listings',
        keymap: '/keymap',
        system: '/system',
    },
    tokenResponseHandler: (results, appKey) => {

        const token = results?.data?.token;
        const expiresAt = results?.data?.expiresAt;

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

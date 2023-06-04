//Api config and endpoints
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
    tokenResponseHandler: (results) => {
        return {
            token: results?.data?.data?.session?.access_token,
            expiresAt: results?.data?.data?.session?.expires_at
        }
    },
    tokenRefreshLimit: 1,
    tokenRefreshCount: 0
}

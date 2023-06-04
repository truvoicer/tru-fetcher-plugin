//Api config and endpoints
export default {
    wpRequest: true,
    endpoints: {
        login: '/login',
        checkToken: '/token/check',
        tokenRefresh: '/token/refresh',
        currencies: '/currency/list',
        currencyConvert: '/currency/convert',
    },
    tokenResponseHandler: (results) => {
        return {
            token: results?.data?.token,
            expiresAt: results?.data?.expiresAt
        }
    },
    tokenRefreshLimit: 1,
    tokenRefreshCount: 0
}

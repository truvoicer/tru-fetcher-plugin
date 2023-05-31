//Api config and endpoints
export default {
    endpoints: {
        login: '/login',
        tokenRefresh: '/token/refresh',
        currencies: '/currency/list',
        currencyConvert: '/currency/convert',
    },
    tokenRefreshLimit: 1,
    tokenRefreshCount: 0
}

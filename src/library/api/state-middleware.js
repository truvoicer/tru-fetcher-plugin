
/**
 * Loads axios response interceptors
 * Redirects to login when response status is unauthorized
 */
export function loadAxiosInterceptors(apiRequest) {
    apiRequest.interceptors.request.use(function (config) {
        // Do something before request is sent
        return config;
    }, function (error) {
        // Do something with request error
        return Promise.reject(error);
    });

    apiRequest.interceptors.response.use(function (response) {
        return response;
    }, function (error) {
        switch (error?.response?.status) {
            case 401:
                handleUnauthorized();
                break;
        }
        return Promise.reject(error);
    });
}

export function handleUnauthorized() {
}

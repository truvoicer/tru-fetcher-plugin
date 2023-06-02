import '../assets/sass/tru-fetcher-admin.scss';
import React from 'react';
import {Provider} from "react-redux";
import store from "./library/redux/store";
import {
    createBrowserRouter,
    RouterProvider,
    Route, createHashRouter,
} from "react-router-dom";
import {buildRouterData} from "./library/helpers/route-helpers";
import routeConfig from "./library/routes/route-config";
import {loadAxiosInterceptors} from "./library/api/middleware";


const App = () => {
    const routes = buildRouterData(routeConfig);
    const router = createHashRouter(
        routes,
        {
            basename: "/",
        }
    );
    return (
        <Provider store={store}>
            <RouterProvider router={router}/>
        </Provider>
    );
}

export default App;

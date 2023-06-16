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
import {loadAxiosInterceptors} from "./library/api/state-middleware";
import AppLoader from "./AppLoader";
import Auth from "./components/auth/Auth";
import Template from "./components/layout/Template";
import SettingsContainer from "./settings/SettingsContainer";


const App = ({apiConfig}) => {
    const routes = buildRouterData(routeConfig);
    const router = createHashRouter(
        routes,
        {
            basename: "/",
        }
    );

    return (
        <Provider store={store}>
            <AppLoader apiConfig={apiConfig}>
                <Template>
                    <Auth>
                        <SettingsContainer>
                            <RouterProvider router={router}/>
                        </SettingsContainer>
                    </Auth>
                </Template>
            </AppLoader>
        </Provider>
    );
}

export default App;
